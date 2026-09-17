<?php

namespace App\Http\Controllers;

use App\Models\WeeklySchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = WeeklySchedule::orderBy('start_date', 'desc')->get();
        return view('admin.schedules.index', compact('schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'morning_shift_team' => 'required|in:yellow,red,green',
            'afternoon_shift_team' => 'required|in:yellow,red,green',
            'night_shift_team' => 'required|in:yellow,red,green',
        ]);

        $start_date = Carbon::parse($request->start_date)->startOfWeek();
        $end_date = $start_date->copy()->endOfWeek();

        // Check if schedule already exists for this week
        if (WeeklySchedule::where('start_date', $start_date->format('Y-m-d'))->exists()) {
            return back()->with('error', 'Jadwal untuk minggu ini sudah ada.');
        }

        WeeklySchedule::create([
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $end_date->format('Y-m-d'),
            'morning_shift_team' => $request->morning_shift_team,
            'afternoon_shift_team' => $request->afternoon_shift_team,
            'night_shift_team' => $request->night_shift_team,
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $schedule = WeeklySchedule::findOrFail($id);
        
        $request->validate([
            'morning_shift_team' => 'required|in:yellow,red,green',
            'afternoon_shift_team' => 'required|in:yellow,red,green',
            'night_shift_team' => 'required|in:yellow,red,green',
        ]);

        $schedule->update([
            'morning_shift_team' => $request->morning_shift_team,
            'afternoon_shift_team' => $request->afternoon_shift_team,
            'night_shift_team' => $request->night_shift_team,
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diupdate');
    }

    public function destroy($id)
    {
        $schedule = WeeklySchedule::findOrFail($id);
        $schedule->delete();
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus');
    }
}
