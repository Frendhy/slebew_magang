<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    protected $steps = ['cleaning', 'transfer', 'weighing', 'mixing', 'extruding'];

    public function showDetail(Request $request)
    {
        $spkId = $request->query('spk_id');
        $spk   = \App\Models\Spk::with([
            'materials', 'machineSettings', 'transferLogs.worker', 'batches.worker', 
            'extrudingLogs.worker', 'cleaningLogs.worker', 'baggingLogs.worker', 'sisaMaterials'
        ])->findOrFail($spkId);

        $weighingBatches = $spk->batches->where('step', 'weighing')->sortBy('batch_number');
        $mixingBatches = $spk->batches->where('step', 'mixing')->sortBy('batch_number');
        $extrudingLogs = $spk->extrudingLogs->whereNotNull('batch_number')->sortBy('batch_number');
        
        $totalBatches = $spk->materials->max('total_batches_required') ?? 0;

        $chartData = [
            'totalBatches' => $totalBatches,
            'weighing' => [
                'completed' => $weighingBatches->count(),
                'labels' => $weighingBatches->map(fn($b) => 'B'.$b->batch_number)->values(),
                'durations' => $weighingBatches->map(fn($b) => round(($b->duration_seconds ?? 0) / 60, 1))->values(),
            ],
            'mixing' => [
                'completed' => $mixingBatches->count(),
                'labels' => $mixingBatches->map(fn($b) => 'B'.$b->batch_number)->values(),
                'durations' => $mixingBatches->map(fn($b) => round(($b->duration_seconds ?? 0) / 60, 1))->values(),
            ],
            'extruding' => [
                'completed' => $extrudingLogs->count(),
                'labels' => $extrudingLogs->map(fn($l) => 'B'.$l->batch_number)->values(),
                'temps' => $extrudingLogs->map(fn($l) => $l->temperature)->values(),
                'durations' => $extrudingLogs->map(fn($l) => round(($l->duration_seconds ?? 0) / 60, 1))->values(),
            ],
        ];

        return view('production.spk_detail', compact('spk', 'chartData'));
    }

    // ─── Show a production step page ─────────────────────────────────────────
    public function showStep($step, Request $request)
    {
        $spkId = $request->query('spk_id');
        $spk   = \App\Models\Spk::with(['materials', 'machineSettings'])->find($spkId);

        $batches      = collect();
        $extrudingLogs = collect();
        $transferLogs = collect();
        $lastApproval = null;

        if ($spk) {
            $transferLogs = \App\Models\SpkTransferLog::where('spk_id', $spk->id)
                ->orderBy('created_at', 'asc')->get();

            if (in_array($step, ['weighing', 'mixing'])) {
                $batches = \App\Models\SpkBatch::where('spk_id', $spk->id)
                    ->where('step', $step)->orderBy('batch_number')->get();
            }
            if ($step === 'extruding') {
                $extrudingLogs = \App\Models\SpkExtrudingLog::where('spk_id', $spk->id)
                    ->orderBy('created_at', 'desc')->get();
            }

            $lastApproval = DB::table('approvals')
                ->where('spk_id', $spk->id)
                ->orderBy('created_at', 'desc')->first();
        }

        $maxPossibleBatches = 0;
        if ($spk && $spk->materials->count() > 0) {
            if ($step === 'mixing') {
                $maxPossibleBatches = \App\Models\SpkBatch::where('spk_id', $spk->id)->where('step', 'weighing')->count();
            } else {
                $maxPossibleBatches = $spk->materials->min(function($mat) use ($transferLogs) {
                    $transferred = $transferLogs->where('material_name', $mat->material_name)->sum('weight_kg');
                    return $mat->target_weight_per_batch > 0 ? floor($transferred / $mat->target_weight_per_batch) : 0;
                });
            }
        }

        return view('production.' . $step, compact('spk', 'batches', 'extrudingLogs', 'transferLogs', 'lastApproval', 'maxPossibleBatches'));
    }

    // ─── Complete a step ─────────────────────────────────────────────────────
    public function completeStep($step, Request $request)
    {
        $spkId = $request->query('spk_id') ?? $request->input('spk_id');

        if ($spkId) {
            $spk = \App\Models\Spk::find($spkId);
            if ($spk) {
                $idxReq = array_search($step, $this->steps);
                $idxCur = array_search($spk->current_step, $this->steps);
                
                // Jika user menyelesaikan step masa lalu (partial catch-up),
                // jangan ubah status utama SPK, kembalikan saja ke detail.
                if ($idxReq < $idxCur) {
                    return redirect()->route('production.detail', ['spk_id' => $spk->id]);
                }

                if ($step === 'cleaning') {
                    // Cleaning (first step) auto-advances to transfer without approval
                    if ($idxReq !== false && $idxReq < count($this->steps) - 1) {
                        $spk->current_step = $this->steps[$idxReq + 1];
                        $spk->status       = 'ongoing';
                    }
                } else {
                    // All other steps need approval from Foreman/QC
                    $spk->status = 'waiting_approval';
                }
                $spk->save();
            }
        }
        return redirect()->route('dashboard');
    }

    // ─── Save pallet transfer ─────────────────────────────────────────────────
    public function saveTransfer(Request $request)
    {
        $request->validate([
            'spk_id'        => 'required|exists:spks,id',
            'material_name' => 'required|string',
            'pallet_id'     => 'required|string|unique:spk_transfer_logs,pallet_id',
            'weight_kg'     => 'required|numeric|min:0.01',
        ]);

        $log = \App\Models\SpkTransferLog::create([
            'spk_id'        => $request->spk_id,
            'worker_id'     => Auth::id(),
            'material_name' => $request->material_name,
            'pallet_id'     => strtoupper($request->pallet_id),
            'weight_kg'     => $request->weight_kg,
        ]);

        // Recompute total transferred for this material
        $totalTransferred = \App\Models\SpkTransferLog::where('spk_id', $request->spk_id)
            ->where('material_name', $request->material_name)
            ->sum('weight_kg');

        return response()->json([
            'success'          => true,
            'log'              => $log,
            'total_transferred'=> (float) $totalTransferred,
        ]);
    }

    // ─── Get all transfer logs for an SPK ────────────────────────────────────
    public function getTransferLogs(Request $request)
    {
        $logs = \App\Models\SpkTransferLog::where('spk_id', $request->spk_id)
            ->orderBy('created_at', 'asc')->get();
        return response()->json($logs);
    }

    // ─── Save batch (weighing / mixing) ──────────────────────────────────────
    public function saveBatch(Request $request)
    {
        $request->validate([
            'spk_id'           => 'required|exists:spks,id',
            'step'             => 'required|in:weighing,mixing',
            'duration_seconds' => 'required|integer|min:1',
        ]);

        $last  = \App\Models\SpkBatch::where('spk_id', $request->spk_id)
            ->where('step', $request->step)->max('batch_number') ?? 0;

        $batch = \App\Models\SpkBatch::create([
            'spk_id'           => $request->spk_id,
            'step'             => $request->step,
            'worker_id'        => Auth::id(),
            'batch_number'     => $last + 1,
            'duration_seconds' => $request->duration_seconds,
            'start_time'       => now()->subSeconds($request->duration_seconds),
            'end_time'         => now(),
        ]);

        return response()->json([
            'success' => true,
            'batch'   => $batch,
            'total'   => \App\Models\SpkBatch::where('spk_id', $request->spk_id)
                            ->where('step', $request->step)->count(),
        ]);
    }

    public function getBatches(Request $request)
    {
        return response()->json(
            \App\Models\SpkBatch::where('spk_id', $request->spk_id)
                ->where('step', $request->step)->orderBy('batch_number')->get()
        );
    }

    // ─── Save extruding monitoring log ────────────────────────────────────────
    public function saveExtrudingLog(Request $request)
    {
        $request->validate([
            'spk_id'      => 'required|exists:spks,id',
            'temperature' => 'nullable|numeric',
            'pressure'    => 'nullable|numeric',
            'monitoring_data' => 'nullable|array',
            'notes'       => 'nullable|string',
        ]);

        $log = \App\Models\SpkExtrudingLog::create([
            'spk_id'      => $request->spk_id,
            'worker_id'   => Auth::id(),
            'temperature' => $request->temperature ?? 0,
            'pressure'    => $request->pressure ?? 0,
            'monitoring_data' => $request->monitoring_data ?? null,
            'notes'       => $request->notes,
            'duration_seconds' => $request->duration_seconds ?? 0,
            'start_time'  => now()->subSeconds($request->duration_seconds ?? 0),
            'end_time'    => now(),
        ]);

        return response()->json(['success' => true, 'log' => $log]);
    }

    public function getExtrudingLogs(Request $request)
    {
        return response()->json(
            \App\Models\SpkExtrudingLog::where('spk_id', $request->spk_id)
                ->orderBy('created_at', 'desc')->get()
        );
    }

    // ─── Save cleaning log ────────────────────────────────────────────────────
    public function saveCleaningLog(Request $request)
    {
        $request->validate([
            'spk_id'               => 'required|exists:spks,id',
            'cleaning_material'    => 'required|string',
            'residual_material_kg' => 'required|numeric',
        ]);

        $log = \App\Models\SpkCleaningLog::create([
            'spk_id'               => $request->spk_id,
            'worker_id'            => Auth::id(),
            'cleaning_material'    => $request->cleaning_material,
            'residual_material_kg' => $request->residual_material_kg,
        ]);

        return response()->json(['success' => true, 'log' => $log]);
    }
}
