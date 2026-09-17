@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto mt-6 fade-in">

    {{-- SPK Info Bar --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-1">{{ $spk?->spk_number ?? 'Pilih SPK' }}</p>
            <h1 class="text-3xl font-extrabold text-slate-900">Penimbangan per Batch</h1>
            <p class="text-slate-500 mt-1">{{ $spk?->product_name ?? '' }}</p>
        </div>
        <a href="/" class="flex items-center gap-2 text-slate-500 hover:text-brand-900 text-sm font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Dashboard
        </a>
    </div>

    @if($spk)
    {{-- Material Availability (Transfer vs Weighed) --}}
    <div class="bg-white border border-slate-200 p-5 rounded-xl mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Ketersediaan Material (Gudang → Produksi)</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($spk->materials as $mat)
            @php
                $transferred = $transferLogs->where('material_name', $mat->material_name)->sum('weight_kg');
                $weighed = $batches->count() * $mat->target_weight_per_batch;
                $pct = $transferred > 0 ? min(100, ($weighed / $transferred) * 100) : 0;
                $isInsufficient = $transferred < $mat->target_weight_per_batch;
            @endphp
            <div class="bg-slate-50 rounded-lg p-4 border"{{ $isInsufficient ? 'border-red-500/30' : 'border-slate-700' }}>
                <div class="flex justify-between items-end mb-1">
                    <p class="text-sm font-bold"{{ $isInsufficient ? 'text-red-400' : 'text-slate-300' }}>{{ $mat->material_name }}</p>
                    <p class="text-xs text-slate-500">
                        Ditimbang: <span id="text-weighed-{{ $mat->id }}" class="font-bold text-slate-900">{{ number_format($weighed, 2) }} kg</span> 
                        / Tersedia: <span class="font-bold text-brand-400">{{ number_format($transferred, 2) }} kg</span>
                    </p>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2 mt-2 border border-slate-300">
                    <div id="bar-{{ $mat->id }}" class="h-2 rounded-full transition-all {{ $isInsufficient ? 'bg-red-500' : 'bg-brand-500' }}" style="width: {{ $pct }}%"></div>
                </div>
                @if($isInsufficient)
                <p class="text-[10px] text-red-400 mt-1">⚠️ Material tidak cukup untuk 1 batch.</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- SPK Info + Formula --}}
    <div class="bg-amber-500/10 border border-amber-500/30 p-4 rounded-xl mb-6">
        <p class="text-sm font-bold text-amber-600 mb-3">📋 Formula Penimbangan per Batch</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach($spk->materials as $mat)
            <div class="bg-slate-50/60 rounded-lg p-3">
                <p class="text-xs text-slate-500 mb-1 truncate">{{ $mat->material_name }}</p>
                <p class="text-xl font-extrabold text-amber-600">{{ number_format($mat->target_weight_per_batch, 2) }} <span class="text-sm font-normal">kg</span></p>
                <p class="text-xs text-slate-500 mt-0.5">× {{ $mat->total_batches_required }} batch</p>
            </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            {{-- Timer + Rekam Batch --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-2xl">
                <p class="text-slate-500 text-xs uppercase tracking-widest mb-2">Waktu Batch Saat Ini (Timer)</p>
                <div class="text-7xl font-extrabold text-slate-900 font-mono tracking-tighter my-2 text-center" id="weighing-timer">00:00</div>
                
                @if($maxPossibleBatches == 0)
                <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 mt-6 text-center">
                    <p class="text-red-400 font-bold mb-2">Material Belum Lengkap!</p>
                    <p class="text-sm text-red-200 mb-4">Ada material yang belum dikirim dari gudang atau jumlahnya tidak mencukupi untuk 1 batch. Silakan kembali ke tahap Transfer untuk menambah material.</p>
                    <a href="/production/transfer?spk_id={{ $spk->id }}" class="inline-block py-2.5 px-6 rounded-lg bg-red-500 hover:bg-red-400 text-white font-bold shadow-lg transition-all">
                        ← Kembali ke Pemindahan Material
                    </a>
                </div>
                @elseif($batches->count() < $maxPossibleBatches)
                <div class="flex gap-3 mt-6" id="action-buttons">
                    <button id="btn-start" onclick="toggleTimer()" class="flex-1 py-3 rounded-xl font-bold text-lg transition-all bg-brand-500 hover:bg-brand-400 text-white shadow-[0_0_15px_rgba(14,165,233,0.3)]">Mulai Timer</button>
                    <button onclick="recordBatch()" class="flex-1 py-3 rounded-xl font-bold text-lg transition-all bg-green-500/20 border-2 border-green-500 text-green-400 hover:bg-green-500 hover:text-white">Rekam Batch</button>
                </div>
                @else
                <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-4 mt-6 text-center" id="action-buttons">
                    <p class="text-amber-600 font-bold mb-2">Batas Maksimal Batch Tercapai</p>
                    <p class="text-sm text-amber-700 mb-4">Material yang sudah ditransfer (tersedia) hanya cukup untuk memproses <strong>{{ $maxPossibleBatches }} batch</strong>.</p>
                    <a href="/production/transfer?spk_id={{ $spk->id }}" class="inline-block py-2.5 px-6 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold shadow-lg transition-all">
                        ← Kembali ke Pemindahan Material
                    </a>
                </div>
                @endif

                <p id="batch-msg" class="text-center text-sm mt-3 text-slate-500"></p>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 h-full">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Riwayat Batch</h2>
                    <span id="batch-count-badge" class="text-xs font-bold px-2.5 py-1 rounded-full bg-brand-500/20 text-brand-400 border border-brand-500/30">
                        {{ $batches->count() }} batch
                    </span>
                </div>
                <div id="batch-history" class="space-y-2 max-h-72 overflow-y-auto pr-1">
                    @forelse($batches as $b)
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 flex justify-between items-center">
                        <p class="text-sm font-bold text-slate-900">Batch #{{ $b->batch_number }}</p>
                        <div class="text-right">
                            <p class="text-sm font-bold text-brand-400 font-mono">{{ $b->duration_formatted }}</p>
                            <p class="text-xs text-green-400">✓ Tersimpan</p>
                        </div>
                    </div>
                    @empty
                    <p id="empty-msg" class="text-center text-slate-500 text-sm py-4">Belum ada batch direkam.</p>
                    @endforelse
                </div>

                <div class="mt-4 pt-4 border-t border-slate-200">
                    <p class="text-xs text-slate-500 mb-1">Total batch tersimpan</p>
                    <p id="total-batch-count" class="text-3xl font-extrabold text-slate-900">{{ $batches->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Actions --}}
    <div class="flex justify-between items-center mt-8 pt-6 border-t border-slate-200">
        <div class="flex items-center gap-3">
            <a href="/production/spk-detail?spk_id={{ $spk->id }}" class="text-slate-500 hover:text-brand-900 font-medium py-3 px-6 transition-colors">Kembali</a>
            @include('production.partials.emergency_button', ['step' => 'weighing'])
        </div>
        <a href="/production/weighing/complete?spk_id={{ $spk->id }}" id="btn-selesai">
            @php 
                $allSteps = ['cleaning', 'transfer', 'weighing', 'mixing', 'extruding'];
                $idxCur = array_search($spk->current_step, $allSteps);
                $idxReq = array_search('weighing', $allSteps);
                $isPast = $idxReq < $idxCur;
                $isDone = $batches->count() >= $spk->materials->max('total_batches_required'); 
            @endphp
            <button class="px-8 py-4 rounded-xl font-bold text-lg shadow-[0_0_20px_{{ $isDone ? 'rgba(34,197,94,0.3)' : 'rgba(245,158,11,0.3)' }}] transition-all flex items-center gap-2 {{ $isDone ? 'bg-green-500 hover:bg-green-400 text-white' : ($isPast ? 'bg-blue-500 hover:bg-blue-400 text-white' : 'bg-amber-500 hover:bg-amber-400 text-slate-900') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $isPast ? 'Simpan (Catch-up) & Kembali ke Detail' : ($isDone ? 'Selesai & Minta ACC Foreman' : 'Selesai (Partial) & Lanjut Mixing') }}
            </button>
        </a>
    </div>
    @else
    <div class="text-center py-12 text-slate-500">SPK tidak ditemukan. <a href="/" class="text-brand-400 underline">Kembali ke Dashboard</a></div>
    @endif
</div>

<script>
const SPK_ID = {{ $spk?->id ?? 'null' }};
const STEP = 'weighing';
const MAX_BATCHES = {{ $maxPossibleBatches }};
const MATERIALS = {!! json_encode($spk->materials->map(function($m) use ($transferLogs) {
    return [
        'id' => $m->id,
        'target' => $m->target_weight_per_batch,
        'transferred' => $transferLogs->where('material_name', $m->material_name)->sum('weight_kg')
    ];
})) !!};
let timerInterval = null, seconds = 0, running = false;

function toggleTimer() {
    const btn = document.getElementById('btn-start');
    if (running) {
        clearInterval(timerInterval);
        running = false;
        btn.textContent = 'Lanjutkan';
        btn.classList.replace('bg-brand-400','bg-brand-500');
    } else {
        running = true;
        btn.textContent = 'Pause';
        btn.classList.replace('bg-brand-500','bg-brand-400');
        timerInterval = setInterval(() => {
            seconds++;
            const m = String(Math.floor(seconds/60)).padStart(2,'0');
            const s = String(seconds%60).padStart(2,'0');
            document.getElementById('weighing-timer').textContent = `${m}:${s}`;
        }, 1000);
    }
}

async function recordBatch() {
    if (seconds === 0) { document.getElementById('batch-msg').textContent = '⚠️ Mulai timer dahulu!'; return; }
    clearInterval(timerInterval); running = false;
    const dur = seconds;
    seconds = 0;
    document.getElementById('weighing-timer').textContent = '00:00';
    document.getElementById('btn-start').textContent = 'Mulai Timer';

    const res = await fetch('/api/batch/save', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ spk_id: SPK_ID, step: STEP, duration_seconds: dur }),
    });
    const data = await res.json();
    if (data.success) {
        const b = data.batch;
        const mm = String(Math.floor(dur/60)).padStart(2,'0'), ss = String(dur%60).padStart(2,'0');
        document.getElementById('empty-msg')?.remove();
        document.getElementById('batch-history').insertAdjacentHTML('afterbegin', `
            <div class="bg-slate-50 p-3 rounded-lg border border-green-500/40 flex justify-between items-center animate-pulse-once">
                <p class="text-sm font-bold text-slate-900">Batch #${b.batch_number}</p>
                <div class="text-right"><p class="text-sm font-bold text-brand-400 font-mono">${mm}:${ss}</p><p class="text-xs text-green-400">✓ Tersimpan</p></div>
            </div>`);
        document.getElementById('total-batch-count').textContent = data.total;
        document.getElementById('batch-count-badge').textContent = data.total + ' batch';
        document.getElementById('batch-msg').textContent = `✓ Batch #${b.batch_number} berhasil disimpan!`;
        document.getElementById('batch-msg').classList.add('text-green-400');
        document.getElementById('batch-msg').classList.remove('text-slate-500');

        MATERIALS.forEach(m => {
            const weighed = data.total * m.target;
            let pct = m.transferred > 0 ? (weighed / m.transferred) * 100 : 0;
            if(pct > 100) pct = 100;
            const textEl = document.getElementById(`text-weighed-${m.id}`);
            const barEl = document.getElementById(`bar-${m.id}`);
            if(textEl) textEl.textContent = weighed.toFixed(2) + ' kg';
            if(barEl) barEl.style.width = pct + '%';
        });

        if (data.total >= MAX_BATCHES) {
            const actions = document.getElementById('action-buttons');
            if (actions) actions.style.display = 'none';
            
            document.getElementById('weighing-timer').insertAdjacentHTML('afterend', `
                <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-4 mt-6 text-center">
                    <p class="text-amber-600 font-bold mb-2">Batas Maksimal Batch Tercapai</p>
                    <p class="text-sm text-amber-700 mb-4">Material yang sudah ditransfer (tersedia) hanya cukup untuk memproses <strong>${MAX_BATCHES} batch</strong>.</p>
                    <a href="/production/transfer?spk_id=${SPK_ID}" class="inline-block py-2.5 px-6 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold shadow-lg transition-all">
                        ← Kembali ke Pemindahan Material
                    </a>
                </div>
            `);
        }
    }
}
</script>
@endsection
