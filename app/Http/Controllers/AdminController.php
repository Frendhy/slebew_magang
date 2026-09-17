<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function allSpks()
    {
        return \App\Models\Spk::with(['materials', 'assignments.worker', 'transferLogs', 'batches', 'extrudingLogs', 'cleaningLogs'])
            ->orderByRaw("CASE status WHEN 'ongoing' THEN 0 WHEN 'waiting_approval' THEN 1 WHEN 'upcoming' THEN 2 ELSE 3 END")
            ->orderBy('due_date')
            ->get();
    }

    public function dashboard()
    {
        $spks = $this->allSpks();
        return view('admin.dashboard', compact('spks'));
    }

    public function calendar()
    {
        $spks = $this->allSpks();
        return view('admin.calendar', compact('spks'));
    }

    public function search()
    {
        $spks = $this->allSpks();
        return view('admin.search', compact('spks'));
    }

    public function spkDetail($id)
    {
        $spk = \App\Models\Spk::with([
            'materials', 'batches', 'extrudingLogs', 'cleaningLogs', 
            'transferLogs', 'assignments.worker', 'assignments.assignedBy', 
            'machineSettings', 'approvals'
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

        return view('admin.spk_detail', compact('spk', 'chartData'));
    }
}
