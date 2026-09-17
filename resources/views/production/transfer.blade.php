@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto mt-6 fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-bold uppercase tracking-wider mb-2">
                <a href="/production/spk-detail?spk_id={{ $spk?->id }}" class="hover:text-slate-300 transition-colors">{{ $spk?->spk_number }}</a>
                <span>›</span>
                <span class="text-brand-400">1. Pemindahan Material</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900">Pemindahan Pallet / Material</h1>
            <p class="text-slate-500 mt-1">Catat setiap pallet yang dipindahkan dari Gudang ke Area Produksi.</p>
        </div>
        <a href="/" class="flex items-center gap-2 text-slate-500 hover:text-brand-900 text-sm font-semibold transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Dashboard
        </a>
    </div>

    @if($spk)
    {{-- Kebutuhan Material Info Bar --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        @foreach($spk->materials as $mat)
        @php
            $transferred = $transferLogs->where('material_name', $mat->material_name)->sum('weight_kg');
            $pct = $mat->total_kg > 0 ? min(100, ($transferred / $mat->total_kg) * 100) : 0;
        @endphp
        <div id="mat-card-{{ Str::slug($mat->material_name) }}" class="bg-white rounded-xl border border-slate-200 p-4">
            <p class="text-xs font-bold text-slate-500 mb-1 truncate">{{ $mat->material_name }}</p>
            <p class="text-lg font-extrabold text-slate-900">
                <span id="transferred-{{ Str::slug($mat->material_name) }}">{{ number_format($transferred, 0, '.', '') }}</span> 
                <span class="text-sm text-slate-500 font-normal">/ <span id="req-{{ Str::slug($mat->material_name) }}">{{ number_format($mat->total_kg, 0, '.', '') }}</span> kg</span>
            </p>
            <div class="w-full bg-slate-200 rounded-full h-1.5 mt-2 border border-slate-300">
                <div id="bar-{{ Str::slug($mat->material_name) }}" class="h-1.5 rounded-full transition-all {{ $pct >= 100 ? 'bg-green-500' : 'bg-brand-500' }}" style="width: {{ $pct }}%"></div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
        {{-- Left: Input Form --}}
        <div class="md:col-span-3">
            <div class="bg-white rounded-2xl border border-brand-500/20 p-6">
                <h2 class="text-lg font-bold text-slate-900 mb-5">Catat Pemindahan Pallet</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">Material (Dari SPK)</label>
                        <select id="select-material" class="block w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 py-3 px-4 focus:ring-brand-500 focus:border-brand-500">
                            @foreach($spk->materials as $mat)
                            <option value="{{ $mat->material_name }}" data-total="{{ $mat->total_kg }}">
                                {{ $mat->material_name }} (Dibutuhkan: {{ number_format($mat->total_kg, 0) }} kg)
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-2">ID Pallet / Barcode</label>
                            <input id="input-pallet-id" type="text" class="block w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 py-3 px-4 focus:ring-brand-500 focus:border-brand-500 uppercase" placeholder="PLT-0001">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-2">Berat Pallet (kg)</label>
                            <input id="input-weight" type="number" step="0.01" min="0.01" class="block w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 py-3 px-4 focus:ring-brand-500 focus:border-brand-500" placeholder="1000">
                        </div>
                    </div>
                    <p id="add-msg" class="text-sm text-slate-500 min-h-[20px]"></p>
                    <button onclick="addPallet()" class="w-full py-4 rounded-xl bg-brand-500 hover:bg-brand-400 text-white font-bold text-lg shadow-[0_0_15px_rgba(14,165,233,0.3)] transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        TAMBAH PALLET
                    </button>
                </div>
            </div>
        </div>

        {{-- Right: Pallet List --}}
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 h-full flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Daftar Pallet</h2>
                    <span id="pallet-count-badge" class="text-xs font-bold px-2.5 py-1 rounded-full bg-brand-500/20 text-brand-400 border border-brand-500/30">
                        {{ $transferLogs->count() }} pallet
                    </span>
                </div>

                <div id="pallet-list" class="space-y-2 overflow-y-auto flex-1 max-h-72 pr-1">
                    @forelse($transferLogs as $log)
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 flex justify-between items-center">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $log->pallet_id }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $log->material_name }}</p>
                        </div>
                        <span class="text-brand-400 font-extrabold text-sm">{{ number_format($log->weight_kg, 0) }} kg</span>
                    </div>
                    @empty
                    <div id="empty-pallet-msg" class="text-center py-8 text-slate-500 text-sm">
                        <svg class="w-10 h-10 mx-auto mb-2 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Belum ada pallet ditambahkan.
                    </div>
                    @endforelse
                </div>

                <div class="mt-4 pt-4 border-t border-slate-200">
                    <p class="text-xs text-slate-500 mb-1">Total Dipindahkan (semua material)</p>
                    <p id="total-weight-display" class="text-3xl font-extrabold text-brand-500">{{ number_format($transferLogs->sum('weight_kg'), 0) }} kg</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Action --}}
    <div class="flex justify-between items-center mt-8 pt-6 border-t border-slate-200">
        <div class="flex items-center gap-3">
            <a href="/production/spk-detail?spk_id={{ $spk->id }}" class="text-slate-500 hover:text-brand-900 font-medium py-3 px-6 transition-colors">Kembali</a>
            @include('production.partials.emergency_button', ['step' => 'transfer'])
        </div>
        <a href="/production/transfer/complete?spk_id={{ $spk->id }}">
            @php 
                $allSteps = ['cleaning', 'transfer', 'weighing', 'mixing', 'extruding'];
                $idxCur = array_search($spk->current_step, $allSteps);
                $idxReq = array_search('transfer', $allSteps);
                $isPast = $idxReq < $idxCur;
            @endphp
            <button class="flex items-center gap-3 px-8 py-4 rounded-xl font-bold text-lg transition-all shadow-[0_0_20px_rgba(245,158,11,0.3)] bg-amber-500 hover:bg-amber-400 text-slate-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $isPast ? 'Simpan Catch-up & Kembali' : 'Selesai & Lanjut ke Penimbangan' }}
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </a>
    </div>
    @else
    <div class="text-center py-12 text-slate-500">SPK tidak ditemukan. <a href="/" class="text-brand-400 underline">Kembali</a></div>
    @endif
</div>

<script>
const SPK_ID = {{ $spk?->id ?? 'null' }};
let totalWeight = {{ $transferLogs->sum('weight_kg') }};
let palletCount = {{ $transferLogs->count() }};

async function addPallet() {
    const material = document.getElementById('select-material').value;
    const palletId = document.getElementById('input-pallet-id').value.trim().toUpperCase();
    const weight   = parseFloat(document.getElementById('input-weight').value);
    const msgEl    = document.getElementById('add-msg');

    if (!palletId) { msgEl.textContent = '⚠️ ID Pallet tidak boleh kosong!'; msgEl.className = 'text-sm text-red-400'; return; }
    if (!weight || weight <= 0) { msgEl.textContent = '⚠️ Masukkan berat yang valid!'; msgEl.className = 'text-sm text-red-400'; return; }

    const slug = material.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
    const reqKg = parseFloat(document.getElementById('req-' + slug).textContent);
    const currTransferred = parseFloat(document.getElementById('transferred-' + slug).textContent);
    
    if (currTransferred >= reqKg) {
        msgEl.textContent = '⚠️ Kebutuhan material ini sudah terpenuhi! Tidak bisa ditambah lagi.'; 
        msgEl.className = 'text-sm text-red-400';
        return;
    }

    if (currTransferred + weight > reqKg) {
         msgEl.textContent = `⚠️ Peringatan: Berat melebihi kebutuhan. Maksimal sisa: ${reqKg - currTransferred} kg.`; 
         msgEl.className = 'text-sm text-amber-400';
         // Allow them to read warning, or you could strictly block. I'll strictly block for simplicity.
         return;
    }

    msgEl.textContent = 'Menyimpan...'; msgEl.className = 'text-sm text-slate-400';

    const res = await fetch('/api/transfer/save', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ spk_id: SPK_ID, material_name: material, pallet_id: palletId, weight_kg: weight }),
    });

    if (!res.ok) {
        if (res.status === 422) {
            const errData = await res.json();
            if (errData.errors && errData.errors.pallet_id) {
                msgEl.textContent = '⚠️ ID Pallet sudah digunakan! Silakan gunakan ID lain.';
            } else {
                msgEl.textContent = '⚠️ Data tidak valid. Periksa kembali input Anda.';
            }
        } else {
            msgEl.textContent = '✗ Terjadi kesalahan pada server.';
        }
        msgEl.className = 'text-sm text-red-400';
        return;
    }

    const data = await res.json();

    if (data.success) {
        palletCount++;
        totalWeight += weight;

        // Remove empty message if exists
        document.getElementById('empty-pallet-msg')?.remove();

        // Add pallet row
        document.getElementById('pallet-list').insertAdjacentHTML('afterbegin', `
            <div class="bg-slate-50 p-3 rounded-xl border border-green-500/30 flex justify-between items-center animate-pulse-once">
                <div>
                    <p class="text-sm font-bold text-slate-900">${palletId}</p>
                    <p class="text-xs text-slate-500 mt-0.5">${material}</p>
                </div>
                <span class="text-brand-400 font-extrabold text-sm">${weight.toLocaleString()} kg</span>
            </div>`);

        // Update totals
        document.getElementById('total-weight-display').textContent = totalWeight.toLocaleString() + ' kg';
        document.getElementById('pallet-count-badge').textContent = palletCount + ' pallet';

        // Clear inputs
        document.getElementById('input-pallet-id').value = '';
        document.getElementById('input-weight').value = '';

        // Update the progress bar for this material
        updateProgressBar(material, data.total_transferred);

        msgEl.textContent = `✓ Pallet ${palletId} berhasil ditambahkan!`;
        msgEl.className = 'text-sm text-green-400';
    } else {
        msgEl.textContent = '✗ Gagal menyimpan. Coba lagi.';
        msgEl.className = 'text-sm text-red-400';
    }
}

function updateProgressBar(materialName, totalTransferred) {
    const slug = materialName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
    const reqKg = parseFloat(document.getElementById('req-' + slug).textContent);
    
    document.getElementById('transferred-' + slug).textContent = totalTransferred.toLocaleString('en-US', {maximumFractionDigits:2});
    
    let pct = reqKg > 0 ? (totalTransferred / reqKg) * 100 : 0;
    if (pct > 100) pct = 100;
    
    const bar = document.getElementById('bar-' + slug);
    bar.style.width = pct + '%';
    
    if (pct >= 100) {
        bar.classList.remove('bg-brand-500');
        bar.classList.add('bg-green-500');
    }
}

// Enter key support
document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && (document.activeElement.id === 'input-pallet-id' || document.activeElement.id === 'input-weight')) {
        addPallet();
    }
});
</script>
@endsection
