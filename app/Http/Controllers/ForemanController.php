<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use App\Models\SpkAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class ForemanController extends Controller
{
    public function listSpk()
    {
        $upcomingSpks = Spk::where('status', 'upcoming')->orderBy('due_date')->get();
        $ongoingSpks  = Spk::with(['transferLogs.worker', 'batches.worker', 'extrudingLogs.worker', 'cleaningLogs.worker'])
            ->whereIn('status', ['ongoing', 'waiting_approval'])
            ->orderBy('due_date')->get();
        $doneSpks     = Spk::where('status', 'done')->orderBy('due_date', 'desc')->get();
        return view('foreman.list', compact('upcomingSpks', 'ongoingSpks', 'doneSpks'));
    }

    public function dashboard()
    {
        // Load SPKs with their relations to calculate progress
        $allSpks = Spk::with(['materials', 'assignments.worker', 'transferLogs', 'batches', 'extrudingLogs', 'cleaningLogs'])
            ->orderByRaw("CASE status WHEN 'ongoing' THEN 0 WHEN 'waiting_approval' THEN 1 WHEN 'upcoming' THEN 2 ELSE 3 END")
            ->orderBy('due_date')
            ->get();
            
        return view('foreman.dashboard', compact('allSpks'));
    }

    public function upcoming()
    {
        $upcomingSpks = Spk::with(['materials', 'assignments.worker'])
            ->whereIn('status', ['upcoming', 'ongoing', 'waiting_approval'])
            ->orderBy('due_date')
            ->get();
        return view('foreman.upcoming', compact('upcomingSpks'));
    }

    public function assign()
    {
        $allSpks = Spk::with(['materials', 'assignments'])
            ->orderByRaw("CASE status WHEN 'ongoing' THEN 0 WHEN 'upcoming' THEN 1 ELSE 2 END")
            ->orderBy('due_date')
            ->get();
        return view('foreman.assign', compact('allSpks'));
    }

    public function spkAssign($id)
    {
        $spk     = Spk::with(['assignments.worker', 'materials'])->findOrFail($id);
        $foreman = auth()->user();
        $workers = User::where('role', 'worker')->where('team', $foreman->team)->orderBy('name')->get();
        
        $workingDays = floatval($spk->working_days ?? 1);
        $totalShifts = ceil($workingDays * 3);
        $totalShiftsWithBackup = $totalShifts + 1;
        
        // Cek Weekly Schedule berdasarkan due_date SPK (atau default jika tak ada)
        $targetDate = $spk->due_date ? \Carbon\Carbon::parse($spk->due_date) : now();
        $schedule = \App\Models\WeeklySchedule::whereDate('start_date', '<=', $targetDate)
                                              ->whereDate('end_date', '>=', $targetDate)
                                              ->first();
                                              
        $shiftTeams = [
            1 => $schedule ? strtolower($schedule->morning_shift_team) : null,
            2 => $schedule ? strtolower($schedule->afternoon_shift_team) : null,
            3 => $schedule ? strtolower($schedule->night_shift_team) : null,
        ];
        
        $foremanTeam = strtolower($foreman->team);

        $shiftGrid = [];
        $currentShiftCount = 0;
        $daysCount = ceil($totalShiftsWithBackup / 3);
        
        for ($d = 1; $d <= $daysCount; $d++) {
            $shiftGrid[$d] = [];
            for ($s = 1; $s <= 3; $s++) {
                $currentShiftCount++;
                if ($currentShiftCount > $totalShiftsWithBackup) {
                    break;
                }
                $isCadangan = ($currentShiftCount == $totalShiftsWithBackup);
                $isLocked = $shiftTeams[$s] && $shiftTeams[$s] !== $foremanTeam;
                
                $shiftGrid[$d][$s] = [
                    'day' => $d,
                    'shift' => $s,
                    'is_cadangan' => $isCadangan,
                    'is_locked' => $isLocked,
                    'team_assigned' => $shiftTeams[$s] ?? 'Belum ada jadwal'
                ];
            }
        }

        return view('foreman.spk_assign', compact('spk', 'workers', 'shiftGrid', 'totalShifts', 'totalShiftsWithBackup', 'foremanTeam'));
    }

    public function saveAssign(Request $request)
    {
        $request->validate([
            'spk_id'    => 'required|exists:spks,id',
            'assignments' => 'nullable|array', 
        ]);

        $spkId = $request->spk_id;
        $foremanTeam = strtolower(auth()->user()->team);

        // Hanya delete assignment yang belong ke tim foreman ini (di-derive dari shift di grid)
        // Sebenarnya aman jika kita hapus semua assignment dari assigned_by foreman ini untuk SPK ini
        SpkAssignment::where('spk_id', $spkId)
            ->where('assigned_by', auth()->id())
            ->delete();

        $shiftsWeekday = [
            1 => 'Shift 1 (00:00 - 08:00)',
            2 => 'Shift 2 (08:00 - 16:00)',
            3 => 'Shift 3 (16:00 - 00:00)'
        ];

        if ($request->has('assignments')) {
            foreach ($request->assignments as $day => $shifts) {
                foreach ($shifts as $shiftNum => $steps) {
                    $isCadangan = isset($request->is_cadangan[$day][$shiftNum]) && $request->is_cadangan[$day][$shiftNum] == 1;

                    foreach ($steps as $step => $workerIds) {
                        foreach ($workerIds as $workerId) {
                            if (!$workerId) continue;
                            
                            SpkAssignment::create([
                                'spk_id'      => $spkId,
                                'worker_id'   => $workerId,
                                'step'        => $step,
                                'shift'       => 'Hari ' . $day . ' - ' . $shiftsWeekday[$shiftNum],
                                'day_number'  => $day,
                                'shift_number'=> $shiftNum,
                                'is_cadangan' => $isCadangan,
                                'assigned_by' => auth()->id(),
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->route('foreman.spk.assign', $spkId)
            ->with('success', 'Assignment pekerja berhasil disimpan!');
    }

    public function deleteAssign($id)
    {
        $assignment = SpkAssignment::findOrFail($id);
        $spkId = $assignment->spk_id;
        $assignment->delete();
        return redirect()->route('foreman.spk.assign', $spkId)
            ->with('success', 'Pekerja berhasil dihapus dari jadwal.');
    }

    public function spkDetail($id)
    {
        $spk = Spk::with([
            'materials', 'batches.worker', 'transferLogs.worker',
            'extrudingLogs.worker', 'cleaningLogs.worker',
            'assignments.worker', 'assignments.assignedBy', 'machineSettings',
        ])->findOrFail($id);

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

        return view('foreman.spk_detail', compact('spk', 'chartData'));
    }
}
