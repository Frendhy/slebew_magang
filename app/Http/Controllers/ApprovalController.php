<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function approve(Request $request)
    {
        $spk = \App\Models\Spk::find($request->spk_id);
        if ($spk && $spk->status === 'waiting_approval') {
            // Log approval
            \Illuminate\Support\Facades\DB::table('approvals')->insert([
                'spk_id' => $spk->id,
                'step' => $spk->current_step,
                'approver_id' => \Illuminate\Support\Facades\Auth::id(),
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Advance step
            $steps = ['cleaning', 'transfer', 'weighing', 'mixing', 'extruding'];
            $currentIndex = array_search($spk->current_step, $steps);

            if ($currentIndex !== false && $currentIndex < count($steps) - 1) {
                $spk->current_step = $steps[$currentIndex + 1];
                $spk->status = 'ongoing';
            } else if ($currentIndex === count($steps) - 1) {
                // extruding is the last step — mark as done
                $spk->status = 'done';
            }
            $spk->save();
        }
        return back()->with('success', 'Berhasil disetujui.');
    }

    public function reject(Request $request)
    {
        $spk = \App\Models\Spk::find($request->spk_id);
        if ($spk && $spk->status === 'waiting_approval') {
            // Log rejection
            \Illuminate\Support\Facades\DB::table('approvals')->insert([
                'spk_id' => $spk->id,
                'step' => $spk->current_step,
                'approver_id' => \Illuminate\Support\Facades\Auth::id(),
                'status' => 'rejected',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Revert status to ongoing so operator can redo
            $spk->status = 'ongoing';
            $spk->save();
        }
        return back()->with('success', 'Berhasil ditolak.');
    }
}
