@extends('layouts.foreman')

@section('foreman_content')
<div class="fade-in">
    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-1">Dasbor Foreman</h1>
        <p class="text-slate-500">Cari dan pantau progress setiap SPK yang sedang berjalan.</p>
    </div>

    <!-- Search Bar -->
    <div class="relative mb-4">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input id="search-input" type="text" placeholder="Cari berdasarkan nomor SPK atau nama produk..."
            class="w-full bg-white border border-slate-300 rounded-2xl pl-12 pr-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all text-sm"/>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 mb-6 flex-wrap">
        @php
            $tabs = [
                'all'      => ['label' => 'Semua',          'count' => $allSpks->count()],
                'upcoming' => ['label' => 'Mendatang',       'count' => $allSpks->where('status','upcoming')->count()],
                'ongoing'  => ['label' => 'Sedang Berjalan', 'count' => $allSpks->whereIn('status',['ongoing','waiting_approval'])->count()],
                'done'     => ['label' => 'Selesai',         'count' => $allSpks->where('status','done')->count()],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
        <button id="tab-{{ $key }}" onclick="filterByStatus('{{ $key }}')"
            class="tab-btn flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition-all {{ $key === 'all' ? 'bg-brand-500 text-white border-brand-500 shadow-[0_0_12px_rgba(14,165,233,0.3)]' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-400' }}">
            {{ $tab['label'] }}
            <span class="text-xs px-1.5 py-0.5 rounded-full font-bold {{ $key === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $tab['count'] }}</span>
        </button>
        @endforeach
    </div>

    <!-- SPK Cards -->
    <div id="spk-list" class="space-y-4">
        @forelse($allSpks as $spk)
        @php
            $isOverdue = $spk->status != 'done' && $spk->due_date && \Carbon\Carbon::parse($spk->due_date)->isPast();
            $filterStatus = in_array($spk->status, ['ongoing','waiting_approval']) ? 'ongoing' : $spk->status;

            // Total assigned workers (unique workers across all assignments for this SPK)
            $totalAssignedWorkers = $spk->assignments->pluck('worker_id')->unique()->count();

            // Transfer progress
            $totalKg = $spk->materials->sum('total_material_kg');
            $transferredKg = 0;
            foreach ($spk->materials as $m) {
                $transferredKg += $spk->transferLogs->where('material_name', $m->material_name)->sum('weight_kg');
            }
            $transferPct = $totalKg > 0 ? min(100, round(($transferredKg / $totalKg) * 100)) : 0;

            // Weighing progress
            $weighedBatches = $spk->batches->where('step', 'weighing')->count();
            $totalBatches   = $spk->materials->max('total_batches_required') ?? 0;
            $weighingPct    = $totalBatches > 0 ? min(100, round(($weighedBatches / $totalBatches) * 100)) : 0;

            // Mixing progress
            $mixedBatches  = $spk->batches->where('step', 'mixing')->count();
            $mixingPct     = $totalBatches > 0 ? min(100, round(($mixedBatches / $totalBatches) * 100)) : 0;

            // Extruding progress — based on extrudingLogs
            $extrudingDone = $spk->extrudingLogs->count() > 0 ? 100 : 0;

            // Cleaning progress — based on cleaningLogs
            $cleaningDone  = $spk->cleaningLogs->count() > 0 ? 100 : 0;

            // Bagging & FG: check if SPK is at those stages
            $baggingDone   = in_array($spk->current_step, ['bagging','fg','done']) ? 100 : 0;
            $fgDone        = in_array($spk->current_step, ['fg','done']) ? 100 : 0;

            $processes = [
                'Transfer'        => $transferPct,
                'Penimbangan'     => $weighingPct,
                'Mixing'          => $mixingPct,
                'Extruding'       => $extrudingDone,
                'Cleaning'        => $cleaningDone,
                'Bagging'         => $baggingDone,
                'Penyerahan FG'   => $fgDone,
            ];
        @endphp

        <div class="spk-card bg-white border {{ $isOverdue ? 'border-red-500/40' : 'border-slate-200' }} rounded-2xl p-5 hover:border-brand-500/60 hover:shadow-[0_0_20px_rgba(14,165,233,0.12)] transition-all group cursor-pointer"
             data-title="{{ strtolower($spk->spk_number . ' ' . $spk->product_name) }}"
             data-status="{{ $filterStatus }}"
             onclick="window.location='{{ route('foreman.spk.detail', $spk->id) }}'">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">{{ $spk->spk_number }}</p>
                        @if($spk->status == 'upcoming')
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold border border-slate-300">Mendatang</span>
                        @elseif(in_array($spk->status, ['ongoing', 'waiting_approval']))
                            <span class="px-3 py-1 bg-amber-500/20 text-amber-600 rounded-full text-[10px] font-bold border border-amber-500/20">
                                {{ $spk->status == 'waiting_approval' ? 'Menunggu ACC' : 'Ongoing' }} &middot; {{ ucfirst($spk->current_step) }}
                            </span>
                        @else
                            <span class="px-3 py-1 bg-green-500/20 text-green-600 rounded-full text-[10px] font-bold border border-green-500/20">Selesai</span>
                        @endif
                        @if($isOverdue)
                            <span class="px-3 py-1 bg-red-500/20 text-red-500 rounded-full text-[10px] font-bold border border-red-500/20">Overdue</span>
                        @endif
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900 group-hover:text-brand-500 transition-colors">{{ $spk->product_name }}</h3>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1.5 text-xs text-slate-500">
                        <span>Mulai: {{ $spk->start_date ? \Carbon\Carbon::parse($spk->start_date)->format('d M Y') : '-' }}</span>
                        <span>Due: {{ $spk->due_date ? \Carbon\Carbon::parse($spk->due_date)->format('d M Y') : '-' }}</span>
                        <span>{{ $totalAssignedWorkers }} Pekerja Terassign</span>
                        <span>{{ $spk->materials->count() }} Material</span>
                    </div>
                </div>
                <svg class="w-5 h-5 text-slate-300 group-hover:text-brand-500 transition-colors flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>

            <!-- Progress Bars for all 7 processes -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-3 pt-3 border-t border-slate-100">
                @foreach($processes as $label => $pct)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $label }}</span>
                        <span class="text-[10px] font-black text-slate-700">{{ $pct }}%</span>
                    </div>
                    <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all {{ $pct >= 100 ? 'bg-green-500' : 'bg-brand-500' }}" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="py-16 text-center text-slate-500">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            Belum ada SPK yang ditemukan.
        </div>
        @endforelse
    </div>
</div>

<script>
    const searchInput = document.getElementById('search-input');
    let currentStatusFilter = 'all';

    function filterByStatus(status) {
        currentStatusFilter = status;
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-brand-500','text-white','border-brand-500','shadow-[0_0_12px_rgba(14,165,233,0.3)]');
            btn.classList.add('bg-white','text-slate-600','border-slate-200');
            const badge = btn.querySelector('span');
            if (badge) { badge.classList.remove('bg-white/20','text-white'); badge.classList.add('bg-slate-100','text-slate-500'); }
        });
        const activeBtn = document.getElementById('tab-' + status);
        if (activeBtn) {
            activeBtn.classList.remove('bg-white','text-slate-600','border-slate-200');
            activeBtn.classList.add('bg-brand-500','text-white','border-brand-500','shadow-[0_0_12px_rgba(14,165,233,0.3)]');
            const activeBadge = activeBtn.querySelector('span');
            if (activeBadge) { activeBadge.classList.remove('bg-slate-100','text-slate-500'); activeBadge.classList.add('bg-white/20','text-white'); }
        }
        applyFilters();
    }

    function applyFilters() {
        const query = searchInput.value.toLowerCase();
        document.querySelectorAll('.spk-card').forEach(card => {
            const title = card.getAttribute('data-title');
            const status = card.getAttribute('data-status');
            const matchesQuery = title.includes(query);
            const matchesStatus = currentStatusFilter === 'all' || status === currentStatusFilter;
            card.style.display = (matchesQuery && matchesStatus) ? 'block' : 'none';
        });
    }

    searchInput.addEventListener('input', applyFilters);
</script>
@endsection