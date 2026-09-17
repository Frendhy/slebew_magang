@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-10 pb-12">
    <div class="mb-8 fade-in">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-1">
            Halo, <span class="text-brand-500">{{ auth()->user()->name }}</span> 👋
        </h1>
        <p class="text-slate-500">Berikut adalah daftar tugas yang diberikan kepada Anda.</p>
        @if(auth()->user()->team)
        <span class="inline-flex items-center gap-2 mt-2 px-3 py-1 rounded-full text-xs font-bold @if(auth()->user()->team === 'yellow') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 @elseif(auth()->user()->team === 'red') bg-red-500/20 text-red-400 border border-red-500/30 @else bg-green-500/20 text-green-400 border border-green-500/30 @endif">
            <span class="w-1.5 h-1.5 rounded-full @if(auth()->user()->team === 'yellow') bg-yellow-400 @elseif(auth()->user()->team === 'red') bg-red-400 @else bg-green-400 @endif"></span>
            Tim {{ ucfirst(auth()->user()->team) }}
        </span>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl px-6 py-4 font-semibold flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    @if($myAssignments->isEmpty())
    <div class="text-center py-20 bg-white rounded-2xl border border-slate-200">
        <svg class="w-14 h-14 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-slate-500 font-semibold text-lg">Belum ada tugas yang diberikan.</p>
        <p class="text-slate-600 text-sm mt-1">Silakan hubungi Foreman Anda untuk mendapatkan assignment.</p>
    </div>
    @else
    <div class="space-y-4">
        @foreach($myAssignments as $assignment)
        @php
            $spk       = $assignment->spk;
            $step      = $assignment->step;
            $available = $assignment->is_available;
            $reason    = $assignment->reason;
            $isOverdue = $spk->due_date && \Carbon\Carbon::parse($spk->due_date)->isPast() && $spk->status !== 'done';
            $isDone    = $spk->status === 'done';
            $isWaiting = $spk->status === 'waiting_approval';

            $stepsOrder = ['cleaning', 'transfer', 'weighing', 'mixing', 'extruding'];
            $currentStepIdx = array_search($spk->current_step, $stepsOrder);
            if ($currentStepIdx === false) $currentStepIdx = -1;
            
            $myStepIdx = array_search($step, $stepsOrder);
            if ($myStepIdx === false) $myStepIdx = 99; // global = always shows
            
            $isStepDone = ($step !== 'global') && ($myStepIdx < $currentStepIdx);
            if ($spk->status === 'done') $isStepDone = true;

            $isGlobal = ($step === 'global');

            $stepColors = [
                'transfer'  => 'blue',
                'weighing'  => 'purple',
                'mixing'    => 'amber',
                'extruding' => 'orange',
                'cleaning'  => 'teal',
                'global'    => 'brand',
            ];
            $stepIcons = [
                'transfer'  => '🚛',
                'weighing'  => '⚖️',
                'mixing'    => '🔄',
                'extruding' => '🏭',
                'cleaning'  => '🧹',
                'global'    => '🔧',
            ];
        @endphp

        <div class="fade-in rounded-2xl border p-6 transition-all @if($isDone) bg-slate-50 border-green-500/20 @elseif($isWaiting) bg-slate-50 border-amber-500/30 @elseif($isOverdue) bg-red-900/10 border-red-500/40 @elseif($available) bg-white border-brand-500/30 hover:border-brand-500/60 hover:shadow-[0_0_20px_rgba(14,165,233,0.1)] @else bg-white border-slate-200 @endif">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-5">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">{{ $spk->spk_number }}</span>
                        @if($isDone || $isStepDone)
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 border border-green-200 rounded-full text-[10px] font-bold">✓ Selesai</span>
                        @elseif($isWaiting && ($isGlobal || $spk->current_step === $step))
                            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 border border-amber-200 rounded-full text-[10px] font-bold animate-pulse">⏳ Menunggu Approval</span>
                        @elseif($isOverdue)
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 border border-red-200 rounded-full text-[10px] font-bold">⚠️ Overdue</span>
                        @endif
                    </div>
                    <h2 class="text-xl font-extrabold text-slate-900">{{ $spk->product_name }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        ⏰ Tenggat: <span class="{{ $isOverdue ? 'text-red-400 font-bold' : 'text-slate-600 font-semibold' }}">
                            {{ \Carbon\Carbon::parse($spk->due_date)->format('d M Y') }}
                        </span>
                        &nbsp;·&nbsp; 🕐 Shift: <span class="text-slate-600 font-semibold">{{ $assignment->shift }}</span>
                    </p>
                </div>

                {{-- Step badge --}}
                @if($isGlobal)
                <div class="flex-shrink-0 flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-sm bg-brand-500/10 text-brand-600 border border-brand-500/20">
                    🔧 <span>Semua Tahap Produksi</span>
                </div>
                @else
                <div class="flex-shrink-0 flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-sm @if($step === 'transfer') bg-blue-500/15 text-blue-400 border border-blue-500/30 @elseif($step === 'weighing') bg-purple-500/15 text-purple-400 border border-purple-500/30 @elseif($step === 'mixing') bg-amber-500/15 text-amber-400 border border-amber-500/30 @elseif($step === 'extruding') bg-orange-500/15 text-orange-400 border border-orange-500/30 @else bg-teal-500/15 text-teal-400 border border-teal-500/30 @endif">
                    {{ $stepIcons[$step] ?? '🔧' }}
                    <span>Tugas: {{ ucfirst($step) }}</span>
                </div>
                @endif
            </div>

            {{-- Info grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                <div class="bg-slate-50/60 rounded-xl p-3">
                    <p class="text-[10px] text-slate-500 uppercase tracking-wider mb-1">Total Material</p>
                    <p class="text-sm font-bold text-slate-900">{{ $spk->materials->count() }} Jenis</p>
                </div>
                <div class="bg-slate-50/60 rounded-xl p-3">
                    <p class="text-[10px] text-slate-500 uppercase tracking-wider mb-1">Batch Ditimbang</p>
                    <p class="text-sm font-bold text-slate-900">{{ $spk->batches->where('step','weighing')->count() }} Batch</p>
                </div>
                <div class="bg-slate-50/60 rounded-xl p-3">
                    <p class="text-[10px] text-slate-500 uppercase tracking-wider mb-1">Batch Mixing</p>
                    <p class="text-sm font-bold text-slate-900">{{ $spk->batches->where('step','mixing')->count() }} Batch</p>
                </div>
                <div class="bg-slate-50/60 rounded-xl p-3">
                    <p class="text-[10px] text-slate-500 uppercase tracking-wider mb-1">Log Extruding</p>
                    <p class="text-sm font-bold text-slate-900">{{ $spk->extrudingLogs->count() }} Log</p>
                </div>
            </div>

            {{-- Action area --}}
            @if($isDone)
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-5 py-3.5">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-semibold text-green-700">SPK ini sudah selesai dikerjakan.</p>
                </div>
            @elseif($isStepDone)
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-5 py-3.5">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-semibold text-green-700">Tugas {{ ucfirst($step === 'weighing' ? 'penimbangan' : $step) }} sudah diselesaikan.</p>
                </div>
            @elseif($isWaiting && $spk->current_step === $step)
                <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-xl px-5 py-3.5">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <div>
                        <p class="text-sm font-bold text-amber-700">Menunggu Persetujuan (Approval)</p>
                        <p class="text-xs text-amber-600 mt-0.5">Tahap <span class="font-bold uppercase">{{ $step === 'weighing' ? 'penimbangan' : $step }}</span> telah selesai. Menunggu persetujuan Foreman.</p>
                    </div>
                </div>
            @elseif(!$available)
                <div class="flex items-start gap-3 bg-slate-50/80 border border-slate-200 rounded-xl px-5 py-3.5">
                    <svg class="w-5 h-5 text-slate-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-sm font-bold text-slate-600">Belum Bisa Dikerjakan</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $reason }}</p>
                    </div>
                </div>
            @else
                <a href="/production/spk-detail?spk_id={{ $spk->id }}" class="block w-full">
                    <button class="w-full flex items-center justify-between px-6 py-4 rounded-xl font-bold text-white transition-all {{ $isOverdue ? 'bg-red-600 hover:bg-red-700 shadow-[0_4px_14px_0_rgba(220,38,38,0.39)]' : 'bg-brand-600 hover:bg-brand-700 shadow-[0_4px_14px_0_rgba(2,132,199,0.39)]' }}">
                        <span>Kerjakan Sekarang →</span>
                        <span class="text-xs font-medium opacity-90 uppercase tracking-wider">{{ $step === 'weighing' ? 'penimbangan' : $step }}</span>
                    </button>
                </a>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
