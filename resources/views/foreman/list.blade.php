@extends('layouts.foreman')

@section('foreman_content')
<div class="fade-in" x-data="{ 
    view: 'card', 
    search: '',
    matches(num, name) {
        return this.search === '' || 
               num.toLowerCase().includes(this.search.toLowerCase()) || 
               name.toLowerCase().includes(this.search.toLowerCase());
    }
}">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 mb-1">Dashboard Foreman</h1>
            <p class="text-slate-500">Pantau status SPK dan berikan persetujuan (ACC) setiap tahapan produksi.</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="search" placeholder="Cari SPK..." class="pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 w-64 transition-all">
            </div>
            <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                <button @click="view = 'card'" :class="view === 'card' ? 'bg-white shadow-sm text-brand-600' : 'text-slate-500 hover:text-slate-700'" class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-all">
                    Card
                </button>
                <button @click="view = 'table'" :class="view === 'table' ? 'bg-white shadow-sm text-brand-600' : 'text-slate-500 hover:text-slate-700'" class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-all">
                    List
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl px-5 py-4 font-semibold text-sm">
        ✓ {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-show="view === 'card'">

        {{-- UPCOMING --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200">
            <div class="flex items-center justify-between mb-5 pb-4 border-b border-slate-200">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span> Mendatang
                </h2>
                <span class="bg-slate-50 text-slate-600 text-xs font-bold px-2.5 py-1 rounded-full">{{ $upcomingSpks->count() }}</span>
            </div>
            @forelse($upcomingSpks as $spk)
            <div class="mb-3 p-4 rounded-xl border border-slate-200 bg-slate-50/60 hover:border-slate-500 transition-colors" x-show="matches('{{ $spk->spk_number }}', '{{ addslashes($spk->product_name) }}')">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1">{{ $spk->spk_number }}</p>
                <h3 class="text-sm font-bold text-slate-900 mb-1">{{ $spk->product_name }}</h3>
                <p class="text-xs text-slate-500">Tenggat: <span class="text-slate-900 font-semibold">{{ \Carbon\Carbon::parse($spk->due_date)->format('d M Y') }}</span></p>
                <a href="{{ route('foreman.spk.detail', $spk->id) }}" class="mt-3 flex items-center gap-1 text-xs font-bold text-brand-400 hover:text-brand-300 transition-colors">
                    Lihat Detail <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            @empty
            <p class="text-center py-6 text-slate-500 text-sm">Tidak ada SPK mendatang.</p>
            @endforelse
        </div>

        {{-- ONGOING / WAITING APPROVAL --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200 border-t-4 border-t-amber-500">
            <div class="flex items-center justify-between mb-5 pb-4 border-b border-slate-200">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span> Sedang Berjalan
                </h2>
                <span class="bg-amber-500/20 text-amber-400 border border-amber-500/20 text-xs font-bold px-2.5 py-1 rounded-full">{{ $ongoingSpks->count() }}</span>
            </div>
            @forelse($ongoingSpks as $spk)
            <div class="mb-4 p-4 rounded-xl border {{ $spk->status === 'waiting_approval' ? 'border-amber-500/60 bg-amber-50' : 'border-slate-200 bg-slate-50/60' }} hover:border-amber-400/40 transition-colors" x-show="matches('{{ $spk->spk_number }}', '{{ addslashes($spk->product_name) }}')">
                <div class="flex justify-between items-start mb-2">
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">{{ $spk->spk_number }}</p>
                    @if($spk->status === 'waiting_approval')
                    <span class="flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-100 border border-amber-200 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Butuh ACC
                    </span>
                    @else
                    <span class="text-[10px] font-bold text-slate-600 bg-slate-200 border border-slate-300 px-2 py-0.5 rounded-full">Berjalan</span>
                    @endif
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1">{{ $spk->product_name }}</h3>
                <p class="text-xs text-slate-500 mb-1">Tenggat: <span class="text-slate-900 font-semibold">{{ \Carbon\Carbon::parse($spk->due_date)->format('d M Y') }}</span></p>
                <p class="text-xs text-slate-500 mb-3">Tahap: <span class="text-brand-500 font-bold uppercase">{{ str_replace('_', ' ', $spk->current_step) }}</span></p>

                @if($spk->status === 'waiting_approval' && $spk->current_step !== 'cleaning')
                @php
                    $lastLog = null;
                    if ($spk->current_step === 'transfer') $lastLog = $spk->transferLogs->last();
                    elseif (in_array($spk->current_step, ['weighing', 'mixing'])) $lastLog = $spk->batches->where('step', $spk->current_step)->last();
                    elseif ($spk->current_step === 'extruding') $lastLog = $spk->extrudingLogs->first();
                @endphp
                @if($lastLog && $lastLog->worker)
                <div class="bg-white border border-slate-200 rounded-lg p-2 mb-3 text-xs text-slate-600">
                    <span class="font-bold text-slate-800">{{ $lastLog->worker->name }}</span> meminta ACC pada 
                    <span class="font-semibold">{{ $lastLog->created_at->format('d M Y, H:i') }}</span>
                </div>
                @endif
                <div class="flex gap-2 mb-2">
                    <form action="{{ route('approval.approve') }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="spk_id" value="{{ $spk->id }}">
                        <button type="submit" class="w-full py-2 rounded-lg bg-green-500 hover:bg-green-400 text-white font-bold text-xs shadow-lg shadow-green-500/20 transition-all">✓ Setujui</button>
                    </form>
                    <form action="{{ route('approval.reject') }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="spk_id" value="{{ $spk->id }}">
                        <button type="submit" class="w-full py-2 rounded-lg bg-red-500/20 border border-red-500/50 text-red-400 hover:bg-red-500 hover:text-white font-bold text-xs transition-all">✗ Tolak</button>
                    </form>
                </div>
                @elseif($spk->status === 'waiting_approval' && $spk->current_step === 'cleaning')
                <div class="bg-slate-100 text-slate-600 text-xs rounded-lg px-3 py-2 text-center font-medium border border-slate-300 mb-2">
                    🔍 Persetujuan oleh QC
                </div>
                @endif

                <a href="{{ route('foreman.spk.detail', $spk->id) }}" class="flex items-center gap-1 text-xs font-bold text-brand-400 hover:text-brand-300 transition-colors">
                    Lihat Detail <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            @empty
            <p class="text-center py-6 text-slate-500 text-sm">Tidak ada SPK sedang berjalan.</p>
            @endforelse
        </div>

        {{-- DONE --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200 border-t-4 border-t-green-500">
            <div class="flex items-center justify-between mb-5 pb-4 border-b border-slate-200">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Selesai
                </h2>
                <span class="bg-green-500/20 text-green-400 border border-green-500/20 text-xs font-bold px-2.5 py-1 rounded-full">{{ $doneSpks->count() }}</span>
            </div>
            @forelse($doneSpks as $spk)
            <div class="mb-3 p-4 rounded-xl border border-green-500/20 bg-green-500/5" x-show="matches('{{ $spk->spk_number }}', '{{ addslashes($spk->product_name) }}')">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1">{{ $spk->spk_number }}</p>
                <h3 class="text-sm font-bold text-slate-900">{{ $spk->product_name }}</h3>
                <span class="inline-flex items-center gap-1 text-xs font-bold text-green-400 mt-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Selesai
                </span>
            </div>
            @empty
            <p class="text-center py-10 text-slate-500 text-sm">Belum ada SPK selesai.</p>
            @endforelse
        </div>
    </div>

    <!-- Table/List View -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mt-6" x-show="view === 'table'" style="display: none;">
        @php
            $allCombinedSpks = collect()->concat($upcomingSpks)->concat($ongoingSpks)->concat($doneSpks);
        @endphp
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-4 px-5">Nomor SPK</th>
                        <th class="py-4 px-5">Produk</th>
                        <th class="py-4 px-5">Tenggat Waktu</th>
                        <th class="py-4 px-5 text-center">Status</th>
                        <th class="py-4 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($allCombinedSpks as $spk)
                    <tr class="hover:bg-slate-50 transition-colors" x-show="matches('{{ $spk->spk_number }}', '{{ addslashes($spk->product_name) }}')">
                        <td class="py-4 px-5 font-bold text-slate-700">{{ $spk->spk_number }}</td>
                        <td class="py-4 px-5">
                            <p class="font-bold text-slate-900">{{ $spk->product_name }}</p>
                        </td>
                        <td class="py-4 px-5 text-slate-500">
                            {{ $spk->due_date ? \Carbon\Carbon::parse($spk->due_date)->format('d M Y') : '-' }}
                        </td>
                        <td class="py-4 px-5 text-center">
                            @if($spk->status == 'upcoming') 
                                <span class="px-2 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-full">Mendatang</span>
                            @elseif($spk->status == 'ongoing' || $spk->status == 'waiting_approval') 
                                <span class="px-2 py-1 bg-amber-500/20 text-amber-500 text-[10px] font-bold rounded-full">Sedang Berjalan</span>
                            @else 
                                <span class="px-2 py-1 bg-green-500/20 text-green-500 text-[10px] font-bold rounded-full">Selesai</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-right">
                            <a href="{{ route('foreman.spk.detail', $spk->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-50 text-brand-600 hover:bg-brand-500 hover:text-white border border-brand-200 hover:border-brand-500 text-xs font-bold transition-colors">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-500">Belum ada SPK.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
