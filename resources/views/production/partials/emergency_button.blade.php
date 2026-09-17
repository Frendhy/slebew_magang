{{-- 
    Emergency Report Button & Modal — include this partial in every step page.
    Required variables in parent view:
      $spk  — the SPK model
      $step — string, current step name (e.g. 'mixing')
    
    Usage: @include('production.partials.emergency_button', ['step' => 'mixing'])
--}}

@php
    $openReport = \App\Models\EmergencyReport::where('spk_id', $spk->id)
        ->where('step', $step)
        ->where('status', '!=', 'resolved')
        ->latest()
        ->first();
    $isLocked = $openReport !== null;
@endphp

{{-- ── Lock Banner ──────────────────────────────────────────────────────────── --}}
@if($isLocked)
<div class="mb-6 bg-red-50 border-2 border-red-400 rounded-2xl p-5 flex items-start gap-4">
    <div class="w-10 h-10 rounded-xl bg-red-500 flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>
    <div class="flex-1">
        <p class="font-extrabold text-red-700 text-base">🔒 Proses Dikunci — Ada Laporan Darurat Aktif</p>
        <p class="text-sm text-red-600 mt-1">Anda telah mengirim laporan darurat untuk tahap <strong class="uppercase">{{ $step }}</strong> ini. Proses tidak dapat dilanjutkan hingga <strong>R&D</strong> memberikan izin.</p>
        <div class="mt-2 text-xs text-red-500 space-y-0.5">
            <p>📋 Dikirimkan: {{ $openReport->created_at->format('d M Y, H:i') }}</p>
            <p>Status: <span class="font-bold uppercase">{{ $openReport->status === 'acknowledged' ? '🟡 Sedang Ditinjau R&D' : '🔴 Menunggu Ditinjau' }}</span></p>
        </div>
    </div>
</div>
@if($isLocked)
<style>
    /* Prevent interaction with anything except the emergency modal itself */
    form:not(#emergencyModal form), a:not([href^="/production/spk-detail"]), button:not(#btn-emergency):not(#emergencyModal button) {
        pointer-events: none;
        opacity: 0.5;
        cursor: not-allowed !important;
    }
</style>
<script>
    // Disable inputs
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('input, select, textarea').forEach(el => {
            if(!el.closest('#emergencyModal')) el.disabled = true;
        });
    });
</script>
@endif
@endif

{{-- ── Emergency Button ─────────────────────────────────────────────────────── --}}
<button type="button" id="btn-emergency" onclick="document.getElementById('emergencyModal').style.display='flex'"
    class="flex items-center gap-2 px-5 py-3 rounded-xl bg-red-500/20 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/40 text-sm font-bold transition-all"
    title="Laporkan masalah darurat">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    Emergency Report
</button>

{{-- ── Emergency Modal ──────────────────────────────────────────────────────── --}}
<div id="emergencyModal"
     style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.6); align-items:center; justify-content:center; padding:1rem;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" style="max-height:90vh; overflow-y:auto;">
        
        {{-- Modal Header --}}
        <div class="bg-red-600 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <h3 class="text-lg font-extrabold text-white">Laporan Darurat Produksi</h3>
            </div>
            <button type="button" onclick="document.getElementById('emergencyModal').style.display='none'"
                class="text-white/70 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <form method="POST" action="{{ route('emergency.store') }}" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="spk_id" value="{{ $spk->id }}">
            <input type="hidden" name="step" value="{{ $step }}">

            {{-- Context Info --}}
            <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                <p class="text-xs font-bold text-red-600 uppercase tracking-widest mb-0.5">SPK yang Dilaporkan</p>
                <p class="font-bold text-slate-900">{{ $spk->spk_number }} — <span class="uppercase text-red-600">{{ $step }}</span></p>
            </div>

            {{-- Recipient Roles --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Kirim Laporan Kepada <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-slate-500 mb-3">Pilih satu atau lebih pihak yang perlu diberitahu.</p>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['foreman' => '👷 Foreman', 'qc' => '🔍 QC', 'rnd' => '🔬 R&D', 'supervisor' => '👔 Supervisor', 'manager' => '💼 Manager', 'admin_manufactur' => '🛡️ Admin'] as $roleVal => $roleLabel)
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-brand-400 hover:bg-brand-50 cursor-pointer transition-all has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                        <input type="checkbox" name="recipient_roles[]" value="{{ $roleVal }}"
                               class="w-4 h-4 accent-red-500">
                        <span class="text-sm font-semibold text-slate-700">{{ $roleLabel }}</span>
                    </label>
                    @endforeach
                </div>
                @error('recipient_roles') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Jelaskan Masalah <span class="text-red-500">*</span>
                </label>
                <textarea name="description" rows="4" required minlength="10"
                    placeholder="Deskripsikan masalah yang terjadi secara jelas dan detail..."
                    class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Media Upload --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Lampirkan Bukti <span class="text-slate-400 font-normal">(opsional, maks. 10 MB)</span>
                </label>
                <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-red-400 transition-colors">
                    <input type="file" name="media[]" multiple id="emergencyMedia" accept="image/*,video/*"
                           class="hidden" onchange="updateFileLabel(this)">
                    <label for="emergencyMedia" class="cursor-pointer">
                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p id="fileLabelText" class="text-sm text-slate-500 font-medium">Klik untuk upload foto atau video</p>
                        <p class="text-xs text-slate-400 mt-1">JPG, PNG, MP4, MOV, AVI — Maks. 10MB</p>
                    </label>
                </div>
                @error('media') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Warning --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3">
                <p class="text-xs font-semibold text-amber-700">⚠️ Setelah laporan dikirim, proses <strong class="uppercase">{{ $step }}</strong> akan DIKUNCI sampai R&D memberikan izin untuk melanjutkan.</p>
            </div>

            {{-- Submit --}}
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('emergencyModal').style.display='none'"
                    class="flex-1 px-4 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold text-sm hover:bg-slate-50 transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-sm transition-all shadow-lg shadow-red-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Kirim Laporan Darurat
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Flash: laporan terkirim --}}
@if(session('emergency_sent'))
<div id="emergencyToast" class="fixed bottom-6 right-6 z-50 bg-red-600 text-white px-5 py-4 rounded-2xl shadow-2xl flex items-center gap-3 max-w-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <p class="text-sm font-semibold">{{ session('emergency_sent') }}</p>
</div>
<script>setTimeout(() => { const t = document.getElementById('emergencyToast'); if(t) t.remove(); }, 6000);</script>
@endif

<script>
function updateFileLabel(input) {
    const label = document.getElementById('fileLabelText');
    if (input.files && input.files.length > 0) {
        let totalSize = 0;
        for(let i=0; i<input.files.length; i++) {
            totalSize += input.files[i].size;
        }
        const sizeMB = (totalSize / 1024 / 1024).toFixed(1);
        
        if (sizeMB > 10) {
            label.textContent = '❌ Total ukuran melebihi 10MB (' + sizeMB + ' MB)';
            label.classList.remove('text-green-600');
            label.classList.add('text-red-600');
            input.value = ''; // Reset
        } else {
            label.textContent = '✅ ' + input.files.length + ' file terpilih (' + sizeMB + ' MB)';
            label.classList.remove('text-red-600');
            label.classList.add('text-green-600');
        }
    } else {
        label.textContent = 'Klik untuk upload foto atau video';
        label.classList.remove('text-green-600', 'text-red-600');
    }
}
// Close modal on backdrop click
document.getElementById('emergencyModal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
// Move modal to body to prevent nested forms
document.addEventListener("DOMContentLoaded", function() {
    var modal = document.getElementById('emergencyModal');
    if (modal) {
        document.body.appendChild(modal);
    }
});
</script>
