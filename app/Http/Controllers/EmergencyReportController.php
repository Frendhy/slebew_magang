<?php

namespace App\Http\Controllers;

use App\Models\EmergencyReport;
use App\Models\Spk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmergencyReportController extends Controller
{
    // ── Non-worker roles that can view reports ──────────────────────────────
    const VIEWER_ROLES = ['foreman', 'qc', 'admin_manufactur', 'rnd', 'supervisor', 'manager'];

    // ── Worker submits a report ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'spk_id'          => 'required|exists:spks,id',
            'step'            => 'required|string',
            'recipient_roles' => 'required|array|min:1',
            'recipient_roles.*'=> 'in:foreman,qc,admin_manufactur,rnd,supervisor,manager',
            'description'     => 'required|string|min:10',
            'media'           => 'nullable|array',
            'media.*'         => 'file|mimes:jpg,jpeg,png,mp4,mov,avi',
        ]);

        $mediaFiles = [];
        if ($request->hasFile('media')) {
            $totalSize = collect($request->file('media'))->sum(fn($file) => $file->getSize());
            if ($totalSize > 10 * 1024 * 1024) {
                return back()->withErrors(['media' => 'Total ukuran file tidak boleh lebih dari 10 MB.']);
            }

            foreach ($request->file('media') as $file) {
                if ($file->isValid()) {
                    $mediaFiles[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'type' => str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image',
                        'path' => $file->store('emergency-media', 'public'),
                    ];
                }
            }
        }

        EmergencyReport::create([
            'spk_id'             => $request->spk_id,
            'worker_id'          => Auth::id(),
            'step'               => $request->step,
            'recipient_roles'    => $request->recipient_roles,
            'description'        => $request->description,
            'media_files'        => empty($mediaFiles) ? null : $mediaFiles,
            'status'             => 'open',
        ]);

        return redirect()
            ->back()
            ->with('emergency_sent', 'Laporan darurat berhasil dikirim. Proses ' . ucfirst($request->step) . ' dikunci hingga RnD memberikan izin.');
    }

    // ── List reports for current non-worker role ────────────────────────────
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $statusFilter = $request->query('status');

        $query = EmergencyReport::with(['spk', 'worker'])
            ->whereJsonContains('recipient_roles', $role);

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $reports = $query->orderByRaw("FIELD(status, 'open', 'acknowledged', 'resolved')")
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $openCount = EmergencyReport::whereJsonContains('recipient_roles', $role)
            ->where('status', 'open')->count();
        
        $ackCount = EmergencyReport::whereJsonContains('recipient_roles', $role)
            ->where('status', 'acknowledged')->count();
            
        $resolvedCount = EmergencyReport::whereJsonContains('recipient_roles', $role)
            ->where('status', 'resolved')->count();

        return view('emergency.index', compact('reports', 'openCount', 'ackCount', 'resolvedCount', 'statusFilter'));
    }

    // ── Show single report detail ───────────────────────────────────────────
    public function show($id)
    {
        $user   = Auth::user();
        $report = EmergencyReport::with(['spk.materials', 'worker', 'resolver'])->findOrFail($id);

        // Only recipients or rnd (who can resolve) can see the report
        if (!in_array($user->role, self::VIEWER_ROLES)) {
            abort(403);
        }

        // Mark as acknowledged if RND opens it
        if ($user->role === 'rnd' && $report->status === 'open') {
            $report->update(['status' => 'acknowledged']);
        }

        return view('emergency.show', compact('report'));
    }

    // ── RND resolves (unlocks the step) ────────────────────────────────────
    public function resolve(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'rnd') {
            abort(403, 'Hanya R&D yang dapat memberikan izin lanjut.');
        }

        $request->validate([
            'resolve_note' => 'nullable|string|max:500',
        ]);

        $report = EmergencyReport::findOrFail($id);
        $report->update([
            'status'       => 'resolved',
            'resolved_by'  => $user->id,
            'resolved_at'  => now(),
            'resolve_note' => $request->resolve_note,
        ]);

        return redirect()
            ->route('emergency.show', $id)
            ->with('success', 'Laporan diselesaikan. Worker sekarang dapat melanjutkan proses ' . ucfirst($report->step) . '.');
    }
}
