@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto mt-6 fade-in">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-1">{{ $spk?->spk_number ?? '' }}</p>
            <h1 class="text-3xl font-extrabold text-slate-900">Proses Mixing (Pencampuran)</h1>
            <p class="text-slate-500 mt-1">{{ $spk?->product_name ?? '' }}</p>
        </div>
        <a href="/" class="flex items-center gap-2 text-slate-500 hover:text-brand-900 text-sm font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Dashboard
        </a>
    </div>

    @if($spk)
    {{-- Batch Availability (Weighing vs Mixed) --}}
    <div class="bg-white border border-slate-200 p-5 rounded-xl mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Ketersediaan Batch (Penimbangan → Pencampuran)</h2>
        </div>
        @php
            $weighedBatches = $maxPossibleBatches;
            $mixedBatches = $batches->count();
            $pctMix = $weighedBatches > 0 ? min(100, ($mixedBatches / $weighedBatches) * 100) : 0;
            $isDoneMix = $mixedBatches >= $weighedBatches && $weighedBatches > 0;
        @endphp
        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
            <div class="flex justify-between items-end mb-1">
                <p class="text-sm font-bold text-slate-600">Total Batch</p>
                <p class="text-xs text-slate-500">
                    Selesai Mixing: <span id="text-mixed" class="font-bold text-slate-900">{{ $mixedBatches }} batch</span> 
                    / Dari Weighing: <span class="font-bold text-brand-400">{{ $weighedBatches }} batch</span>
                </p>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-2 mt-2 border border-slate-300">
                <div id="bar-mix" class="h-2 rounded-full transition-all {{ $isDoneMix ? 'bg-green-500' : 'bg-brand-500' }}" style="width: {{ $pctMix }}%"></div>
            </div>
        </div>
    </div>

    {{-- Approval Notice --}}
    @if($lastApproval && $lastApproval->status === 'approved')
    <div class="bg-green-500/10 border border-green-500/30 p-4 rounded-xl flex items-start mb-6">
        <svg class="w-6 h-6 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <p class="text-sm font-bold text-green-700">Tahap Penimbangan telah disetujui Foreman!</p>
            <p class="text-sm text-green-600 mt-1">Anda dapat mulai proses mixing. Total batch yang ditimbang: <strong>{{ $weighedBatches }}</strong>.</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            {{-- Big Timer --}}
            <div class="bg-white rounded-2xl border-2 border-brand-500 shadow-[0_0_30px_rgba(14,165,233,0.2)] overflow-hidden">
                <div class="p-8 text-center">
                    <p class="text-slate-500 text-xs uppercase tracking-widest mb-1">Batch Saat Ini</p>
                    <div class="text-7xl font-extrabold text-slate-900 font-mono tracking-tighter my-2" id="mixing-timer">00:00</div>
                    @if($batches->count() < $maxPossibleBatches)
                    <div class="flex justify-center gap-4 mt-6" id="action-buttons-mix">
                        <button id="btn-mix-start" onclick="toggleMixTimer()" class="px-8 py-4 rounded-xl bg-brand-500 hover:bg-brand-400 text-white font-bold text-lg shadow-[0_0_15px_rgba(14,165,233,0.3)] transition-all">Mulai Mixing</button>
                        <button onclick="recordMixBatch()" class="px-8 py-4 rounded-xl bg-green-500/20 border-2 border-green-500 text-green-400 font-bold text-lg hover:bg-green-500 hover:text-white transition-all">Rekam Batch</button>
                    </div>
                    @else
                    <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-4 mt-6 text-center">
                        <p class="text-amber-600 font-bold mb-2">Batas Batch Tercapai</p>
                        <p class="text-sm text-amber-700">Anda telah menyelesaikan proses Mixing untuk semua batch yang ditimbang (<strong>{{ $maxPossibleBatches }} batch</strong>).</p>
                    </div>
                    @endif
                    <p id="mix-msg" class="text-center text-sm mt-3 text-slate-500"></p>
                </div>
                <div class="grid grid-cols-3 divide-x divide-slate-700/50 border-t border-slate-200">
                    <div class="p-4 text-center">
                        <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Batch Selesai</p>
                        <p class="text-2xl font-bold text-slate-900" id="mix-done-count">{{ $batches->count() }}</p>
                    </div>
                    <div class="p-4 text-center">
                        <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Waktu</p>
                        <p class="text-2xl font-bold text-amber-400 font-mono" id="mix-total-time">
                            @php $totalSec = $batches->sum('duration_seconds'); $tm = str_pad(intdiv($totalSec,60),2,'0',STR_PAD_LEFT); $ts = str_pad($totalSec%60,2,'0',STR_PAD_LEFT); @endphp
                            {{ $tm }}:{{ $ts }}
                        </p>
                    </div>
                    <div class="p-4 text-center">
                        <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Rata-rata/Batch</p>
                        <p class="text-2xl font-bold text-brand-400 font-mono" id="mix-avg-time">
                            @php $avg = $batches->count() > 0 ? intdiv($totalSec,$batches->count()) : 0; $am = str_pad(intdiv($avg,60),2,'0',STR_PAD_LEFT); $as = str_pad($avg%60,2,'0',STR_PAD_LEFT); @endphp
                            {{ $am }}:{{ $as }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 h-full">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Riwayat Batch Mixing</h2>
                    <span id="mix-badge" class="text-xs font-bold px-2.5 py-1 rounded-full bg-brand-500/20 text-brand-400 border border-brand-500/30">{{ $batches->count() }} batch</span>
                </div>
                <div id="mix-history" class="space-y-2 max-h-80 overflow-y-auto pr-1">
                    @forelse($batches as $b)
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 flex justify-between items-center">
                        <p class="text-sm font-bold text-slate-900">Batch #{{ $b->batch_number }}</p>
                        <div class="text-right"><p class="text-sm font-bold text-brand-400 font-mono">{{ $b->duration_formatted }}</p><p class="text-xs text-green-400">✓ Selesai</p></div>
                    </div>
                    @empty
                    <p id="mix-empty-msg" class="text-center text-slate-500 text-sm py-4">Belum ada batch direkam.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center mt-8 pt-6 border-t border-slate-200">
        <div class="flex items-center gap-3">
            <a href="/production/spk-detail?spk_id={{ $spk->id }}" class="text-slate-500 hover:text-brand-900 font-medium py-3 px-6 transition-colors">Kembali</a>
            @include('production.partials.emergency_button', ['step' => 'mixing'])
        </div>
        <a href="/production/mixing/complete?spk_id={{ $spk->id }}">
            @php 
                $allSteps = ['cleaning', 'transfer', 'weighing', 'mixing', 'extruding'];
                $idxCur = array_search($spk->current_step, $allSteps);
                $idxReq = array_search('mixing', $allSteps);
                $isPast = $idxReq < $idxCur;
                $isDone = $batches->count() >= $spk->materials->max('total_batches_required'); 
            @endphp
            <button class="px-8 py-4 rounded-xl font-bold text-lg transition-all flex items-center gap-2 {{ $isDone ? 'bg-green-500 hover:bg-green-400 shadow-[0_0_20px_rgba(34,197,94,0.3)] text-white' : 'bg-amber-500 hover:bg-amber-400 shadow-[0_0_20px_rgba(245,158,11,0.3)] text-slate-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $isPast ? 'Simpan (Catch-up) & Kembali ke Detail' : ($isDone ? 'Selesai & Minta ACC Foreman' : 'Selesai (Partial) & Lanjut Extruding') }}
            </button>
        </a>
    </div>
    @else
    <div class="text-center py-12 text-slate-500">SPK tidak ditemukan. <a href="/" class="text-brand-400 underline">Kembali ke Dashboard</a></div>
    @endif
</div>

<script>
const SPK_ID = {{ $spk?->id ?? 'null' }};
const STEP = 'mixing';
const MAX_BATCHES = {{ $maxPossibleBatches }};
let timerInterval = null, seconds = 0, running = false;
let totalSecondsDB = {{ $batches->sum('duration_seconds') }};
let totalBatches = {{ $batches->count() }};

function toggleMixTimer() {
    const btn = document.getElementById('btn-mix-start');
    if (running) {
        clearInterval(timerInterval); running = false; btn.textContent = 'Lanjutkan';
    } else {
        running = true; btn.textContent = 'Pause';
        timerInterval = setInterval(() => {
            seconds++;
            const m = String(Math.floor(seconds/60)).padStart(2,'0');
            const s = String(seconds%60).padStart(2,'0');
            document.getElementById('mixing-timer').textContent = `${m}:${s}`;
        }, 1000);
    }
}

async function recordMixBatch() {
    if (seconds === 0) { document.getElementById('mix-msg').textContent = '⚠️ Mulai timer dahulu!'; return; }
    clearInterval(timerInterval); running = false;
    const dur = seconds; seconds = 0;
    document.getElementById('mixing-timer').textContent = '00:00';
    document.getElementById('btn-mix-start').textContent = 'Mulai Mixing';

    const res = await fetch('/api/batch/save', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ spk_id: SPK_ID, step: STEP, duration_seconds: dur }),
    });
    const data = await res.json();
    if (data.success) {
        const b = data.batch;
        totalBatches++; totalSecondsDB += dur;
        const mm = String(Math.floor(dur/60)).padStart(2,'0'), ss = String(dur%60).padStart(2,'0');
        document.getElementById('mix-empty-msg')?.remove();
        document.getElementById('mix-history').insertAdjacentHTML('afterbegin', `
            <div class="bg-slate-50 p-3 rounded-lg border border-green-500/40 flex justify-between items-center">
                <p class="text-sm font-bold text-slate-900">Batch #${b.batch_number}</p>
                <div class="text-right"><p class="text-sm font-bold text-brand-400 font-mono">${mm}:${ss}</p><p class="text-xs text-green-400">✓ Tersimpan</p></div>
            </div>`);
        document.getElementById('mix-done-count').textContent = totalBatches;
        document.getElementById('mix-badge').textContent = totalBatches + ' batch';
        const tm = String(Math.floor(totalSecondsDB/60)).padStart(2,'0'), ts = String(totalSecondsDB%60).padStart(2,'0');
        document.getElementById('mix-total-time').textContent = `${tm}:${ts}`;
        const avg = totalBatches > 0 ? Math.floor(totalSecondsDB/totalBatches) : 0;
        const am = String(Math.floor(avg/60)).padStart(2,'0'), as_ = String(avg%60).padStart(2,'0');
        document.getElementById('mix-avg-time').textContent = `${am}:${as_}`;
        document.getElementById('mix-msg').textContent = `✓ Batch #${b.batch_number} berhasil disimpan!`;
        document.getElementById('mix-msg').className = 'text-center text-sm mt-3 text-green-400';

        // Update progress bar
        let pct = MAX_BATCHES > 0 ? (totalBatches / MAX_BATCHES) * 100 : 0;
        if(pct > 100) pct = 100;
        const textMixed = document.getElementById('text-mixed');
        const barMix = document.getElementById('bar-mix');
        if(textMixed) textMixed.textContent = totalBatches + ' batch';
        if(barMix) {
            barMix.style.width = pct + '%';
            if (totalBatches >= MAX_BATCHES) {
                barMix.classList.replace('bg-brand-500', 'bg-green-500');
            }
        }

        if (totalBatches >= MAX_BATCHES) {
            const actions = document.getElementById('action-buttons-mix');
            if (actions) actions.style.display = 'none';
            document.getElementById('mixing-timer').insertAdjacentHTML('afterend', `
                <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-4 mt-6 text-center">
                    <p class="text-amber-600 font-bold mb-2">Batas Batch Tercapai</p>
                    <p class="text-sm text-amber-700">Anda telah menyelesaikan proses Mixing untuk semua batch yang ditimbang (<strong>${MAX_BATCHES} batch</strong>).</p>
                </div>
            `);
        }
    }
}
</script>
@endsection
