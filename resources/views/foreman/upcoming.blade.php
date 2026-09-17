@extends('layouts.foreman')

@section('foreman_content')
<div class="fade-in">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-1">Jadwal Mendatang</h1>
        <p class="text-slate-500">Semua SPK dan status assignment pekerja per tahap produksi.</p>
    </div>

    @if($upcomingSpks->count() === 0)
    <div class="bg-white border border-slate-200 rounded-2xl py-20 text-center">
        <svg class="w-14 h-14 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-slate-500 font-semibold">Tidak ada SPK yang tersedia.</p>
    </div>
    @else
    <div class="space-y-4">
        @foreach($upcomingSpks as $spk)
        @php
            $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($spk->due_date), false);
            $assignedSteps = $spk->assignments->pluck('step')->unique();
            $hasAnyAssignment = $spk->assignments->where('assigned_by', auth()->id())->count() > 0;
            $stepLabels = [
                'cleaning'  => 'Cleaning',
                'transfer'  => 'Transfer',
                'weighing'  => 'Weighing',
                'mixing'    => 'Mixing',
                'extruding' => 'Extruding',
                'bagging'   => 'Bagging',
                'fg'        => 'Penyerahan FG',
            ];
        @endphp
        @php $borderClass = $daysLeft <= 1 ? 'border-red-500/40' : 'border-slate-200'; @endphp
        <div class="bg-white border {{ $borderClass }} rounded-2xl p-6 hover:border-brand-500/40 transition-all group">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">{{ $spk->spk_number }}</p>
                        @if($daysLeft <= 0)
                            <span class="px-3 py-1 bg-red-500/20 text-red-500 text-[10px] font-bold rounded-full border border-red-500/30">Overdue</span>
                        @elseif($daysLeft <= 1)
                            <span class="px-3 py-1 bg-orange-500/20 text-orange-500 text-[10px] font-bold rounded-full border border-orange-500/30">Besok!</span>
                        @elseif($daysLeft <= 3)
                            <span class="px-3 py-1 bg-amber-500/20 text-amber-500 text-[10px] font-bold rounded-full border border-amber-500/30">{{ ceil($daysLeft) }} hari lagi</span>
                        @endif
                        @if($hasAnyAssignment)
                            <span class="px-3 py-1 bg-green-500/20 text-green-600 text-[10px] font-bold rounded-full border border-green-500/30">Sudah Diassign</span>
                        @else
                            <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[10px] font-bold rounded-full border border-slate-300">Belum Diassign</span>
                        @endif
                        @if(in_array($spk->status, ['ongoing','waiting_approval']))
                            <span class="px-3 py-1 bg-amber-500/20 text-amber-600 text-[10px] font-bold rounded-full border border-amber-500/30">Sedang Berjalan</span>
                        @endif
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 group-hover:text-brand-500 transition-colors mb-1">{{ $spk->product_name }}</h3>
                    <div class="flex flex-wrap gap-x-5 gap-y-1 text-xs text-slate-500">
                        <span>Mulai: {{ $spk->start_date ? \Carbon\Carbon::parse($spk->start_date)->format('d M Y') : '-' }}</span>
                        <span>Tenggat: {{ \Carbon\Carbon::parse($spk->due_date)->format('d M Y') }}</span>
                        <span>{{ $spk->man_allocation ?? 0 }} Pekerja</span>
                        <span>{{ $spk->materials->count() }} Material</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('foreman.spk.detail', $spk->id) }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-500/20 hover:bg-brand-500 text-brand-500 hover:text-white border border-brand-500/30 text-sm font-bold transition-all">
                        Detail
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2.5">Status Assignment per Tahap</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($stepLabels as $stepKey => $stepLabel)
                    @php $assigned = $spk->assignments->where('step', $stepKey)->first(); @endphp
                    @php $pillClass = $assigned ? 'bg-green-500/10 border-green-500/30 text-green-700' : 'bg-slate-100 border-slate-200 text-slate-500'; @endphp
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold border {{ $pillClass }}">
                        {{ $assigned ? 'v' : 'o' }} {{ $stepLabel }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection