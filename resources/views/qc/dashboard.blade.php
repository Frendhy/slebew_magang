@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto fade-in">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 mb-1">Dashboard Quality Control (QC)</h1>
            <p class="text-slate-500">Berikan persetujuan untuk tahap Extruding (Tahap Akhir).</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl px-6 py-4 font-semibold">{{ session('success') }}</div>
    @endif

    {{-- SPKs Waiting for QC Extruding Approval --}}
    @php $waitingQC = $spks->where('status','waiting_approval')->where('current_step','extruding'); @endphp
    @if($waitingQC->isNotEmpty())
    <div class="mb-6 bg-amber-500/10 border border-amber-500/30 text-amber-400 rounded-xl px-6 py-4 font-semibold flex items-center gap-3">
        <svg class="w-5 h-5 animate-pulse flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Ada <strong>{{ $waitingQC->count() }} SPK</strong> yang menunggu persetujuan Akhir (Extruding) dari Anda!
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($spks as $spk)
        @php
            $needsQcApproval = $spk->status === 'waiting_approval' && $spk->current_step === 'extruding';
        @endphp
        <div class="rounded-2xl border p-6 transition-all"{{ $needsQcApproval ? 'border-amber-500/60 bg-amber-500/5 shadow-[0_0_20px_rgba(245,158,11,0.1)]' : 'border-slate-700 bg-slate-800/60 hover:border-slate-600' }}>
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">{{ $spk->spk_number }}</p>
                    <h2 class="text-xl font-bold text-slate-900">{{ $spk->product_name }}</h2>
                </div>
                @if($needsQcApproval)
                <span class="flex-shrink-0 inline-flex items-center gap-1.5 text-xs font-bold text-amber-400 bg-amber-500/10 border border-amber-500/30 px-3 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Butuh ACC QC
                </span>
                @elseif($spk->status === 'done')
                <span class="flex-shrink-0 inline-flex items-center gap-1.5 text-xs font-bold text-green-400 bg-green-500/10 border border-green-500/30 px-3 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg> Selesai
                </span>
                @else
                <span class="flex-shrink-0 text-xs font-bold text-slate-500 bg-slate-100 border border-slate-300 px-3 py-1 rounded-full uppercase">{{ str_replace('_',' ',$spk->current_step) }}</span>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-3 mb-5 bg-slate-50/60 rounded-xl p-4">
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Tenggat Waktu</p>
                    <p class="text-sm font-bold text-slate-900">{{ \Carbon\Carbon::parse($spk->due_date)->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Tahap Saat Ini</p>
                    <p class="text-sm font-bold text-brand-400 uppercase">{{ str_replace('_',' ',$spk->current_step) }}</p>
                </div>
            </div>

            @if($needsQcApproval)
            <div class="flex gap-3">
                <form action="{{ route('approval.approve') }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="spk_id" value="{{ $spk->id }}">
                    <button type="submit" class="w-full py-3 rounded-xl bg-green-500 hover:bg-green-400 text-white font-bold shadow-lg shadow-green-500/20 transition-all">
                        ✓ Setujui Extruding (ACC)
                    </button>
                </form>
                <form action="{{ route('approval.reject') }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="spk_id" value="{{ $spk->id }}">
                    <button type="submit" class="w-full py-3 rounded-xl bg-red-500/20 border border-red-500/50 text-red-400 hover:bg-red-500 hover:text-white font-bold transition-all">
                        ✗ Tolak (Redo)
                    </button>
                </form>
            </div>
            @elseif($spk->status !== 'done')
            <div class="bg-slate-700/40 text-slate-500 text-xs rounded-xl px-4 py-3 text-center font-medium border border-slate-200">
                Belum memasuki tahap Extruding (akhir). Saat ini di tahap: <span class="text-slate-600 font-bold uppercase">{{ $spk->current_step }}</span>
            </div>
            @else
            <div class="bg-green-500/10 text-green-400 text-xs rounded-xl px-4 py-3 text-center font-medium border border-green-500/20">
                ✓ Telah disetujui dan diselesaikan.
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-2 text-center py-16 text-slate-500 text-lg font-medium bg-white rounded-2xl border border-slate-200">
            Belum ada SPK yang aktif untuk direview.
        </div>
        @endforelse
    </div>
</div>
@endsection
