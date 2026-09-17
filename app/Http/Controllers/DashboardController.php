<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin_manufactur') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'foreman') {
            return redirect()->route('foreman.dashboard');
        }

        if ($user->role === 'qc') {
            $spks = \App\Models\Spk::whereIn('status', ['ongoing', 'waiting_approval', 'done'])->orderBy('due_date', 'asc')->get();
            return view('qc.dashboard', compact('spks'));
        }

        if (in_array($user->role, ['rnd', 'supervisor', 'manager'])) {
            return redirect()->route('emergency.index');
        }

        // Default to worker
        $user = auth()->user();

        // Fetch all assignments for this worker, with eager-loaded SPK data
        $assignments = \App\Models\SpkAssignment::where('worker_id', $user->id)
            ->with([
                'spk.materials',
                'spk.batches',
                'spk.transferLogs',
                'spk.extrudingLogs',
                'spk.cleaningLogs',
            ])
            ->get();

        // For global assignments, group by SPK so we show one card per SPK
        // Pick the first assignment per SPK (earliest day/shift)
        $seenSpkIds = [];
        $myAssignments = $assignments->filter(function ($assignment) use (&$seenSpkIds) {
            if (in_array($assignment->spk_id, $seenSpkIds)) return false;
            $seenSpkIds[] = $assignment->spk_id;
            return true;
        })->map(function ($assignment) {
            $spk  = $assignment->spk;
            $step = $assignment->step;

            // For global assignments, the worker can always start work on the SPK
            // The SPK page itself will show what steps are available
            if ($step === 'global') {
                $isAvailable = in_array($spk->status, ['upcoming', 'ongoing', 'waiting_approval']);
                $reason = $isAvailable ? null : 'SPK sudah selesai.';
                $assignment->is_available = $isAvailable;
                $assignment->reason       = $reason;
                return $assignment;
            }

            $transferCount  = $spk->transferLogs->count();
            $weighingCount  = $spk->batches->where('step', 'weighing')->count();
            $mixingCount    = $spk->batches->where('step', 'mixing')->count();

            $isAvailable = false;
            $reason      = null;

            switch ($step) {
                case 'cleaning':
                    $isAvailable = true;
                    break;
                case 'transfer':
                    $stepsOrder = ['cleaning', 'transfer', 'weighing', 'mixing', 'extruding'];
                    $currentIdx = array_search($spk->current_step, $stepsOrder);
                    $isAvailable = $spk->cleaningLogs->count() > 0 || $currentIdx >= 1;
                    $reason = $isAvailable ? null : 'Menunggu proses Cleaning (persiapan mesin) selesai terlebih dahulu.';
                    break;
                case 'weighing':
                    $isAvailable = $transferCount > 0;
                    $reason = $isAvailable ? null : 'Menunggu proses Transfer selesai terlebih dahulu.';
                    break;
                case 'mixing':
                    $isAvailable = $weighingCount > 0;
                    $reason = $isAvailable ? null : 'Menunggu proses Weighing menghasilkan minimal 1 batch.';
                    break;
                case 'extruding':
                    $isAvailable = $mixingCount > 0;
                    $reason = $isAvailable ? null : 'Menunggu proses Mixing menghasilkan minimal 1 batch.';
                    break;
            }

            $assignment->is_available = $isAvailable;
            $assignment->reason       = $reason;
            return $assignment;
        });

        return view('dashboard', compact('myAssignments'));
    }
}
