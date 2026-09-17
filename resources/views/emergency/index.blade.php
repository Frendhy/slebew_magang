@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto pb-12 fade-in">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : (auth()->user()->role === 'admin' ? route('admin.dashboard') : route('foreman.dashboard')) }}" class="text-slate-500 hover:text-brand-900 flex items-center gap-2 mb-4 transition-colors text-sm font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <h1 class="text-3xl font-extrabold text-slate-900 mb-1">🚨 Emergency Reports</h1>
        <p class="text-slate-500">Laporan darurat dari operator produksi yang membutuhkan tindakan segera.</p>
    </div>

    {{-- Success Flash --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <p class="text-sm font-semibold text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-red-500 mb-1">Belum Ditangani</p>
            <p class="text-3xl font-extrabold text-red-600">{{ $openCount }}</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-1">Sedang Ditinjau</p>
            <p class="text-3xl font-extrabold text-amber-600">{{ $ackCount }}</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-green-500 mb-1">Selesai</p>
            <p class="text-3xl font-extrabold text-green-600">{{ $resolvedCount }}</p>
        </div>
    </div>

    {{-- Pill Filters --}}
    @php
        $totalCount = $openCount + $ackCount + $resolvedCount;
        $tabs = [
            'all' => ['label' => 'Semua', 'count' => $totalCount],
            'open' => ['label' => 'Belum Ditangani', 'count' => $openCount],
            'acknowledged' => ['label' => 'Sedang Ditinjau', 'count' => $ackCount],
            'resolved' => ['label' => 'Selesai', 'count' => $resolvedCount],
        ];
        $currentTab = $statusFilter ?: 'all';
    @endphp
    <div class="flex flex-wrap gap-3 mb-6">
        @foreach($tabs as $key => $tab)
        <a href="{{ $key === 'all' ? route('emergency.index') : route('emergency.index', ['status' => $key]) }}"
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition-all {{ $currentTab === $key ? 'bg-brand-500 text-white border-brand-500 shadow-[0_0_12px_rgba(14,165,233,0.3)]' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-400' }}">
            {{ $tab['label'] }}
            <span class="text-xs px-1.5 py-0.5 rounded-full font-bold {{ $currentTab === $key ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                {{ $tab['count'] }}
            </span>
        </a>
        @endforeach
    </div>

    {{-- Report List --}}
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-base font-bold text-slate-900">Daftar Laporan</h2>
        </div>

        @if($reports->count() === 0)
        <div class="py-16 text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-slate-500 font-semibold">Tidak ada laporan darurat untuk Anda.</p>
            <p class="text-slate-400 text-sm mt-1">Semua proses berjalan normal.</p>
        </div>
        @else
        <div class="divide-y divide-slate-100">
            @foreach($reports as $report)
            @php
                $statusColor = match($report->status) {
                    'open'         => 'bg-red-100 text-red-700 border-red-200',
                    'acknowledged' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'resolved'     => 'bg-green-100 text-green-700 border-green-200',
                    default        => 'bg-slate-100 text-slate-600'
                };
                $statusLabel = match($report->status) {
                    'open'         => '🔴 Belum Ditangani',
                    'acknowledged' => '🟡 Sedang Ditinjau',
                    'resolved'     => '🟢 Selesai',
                    default        => $report->status
                };
            @endphp
            <div class="px-6 py-5 hover:bg-slate-50 transition-colors {{ $report->status === 'open' ? 'border-l-4 border-l-red-500' : '' }}">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="text-xs font-bold uppercase tracking-widest text-slate-500">{{ $report->spk->spk_number ?? '-' }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $statusColor }}">{{ $statusLabel }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 border border-blue-200 uppercase">{{ $report->step }}</span>
                        </div>
                        <p class="text-sm font-semibold text-slate-800 mb-1">{{ Str::limit($report->description, 120) }}</p>
                        <div class="flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-slate-500 mt-1">
                            <span>👷 {{ $report->worker->name ?? '-' }}</span>
                            <span>🕐 {{ $report->created_at->format('d M Y, H:i') }}</span>
                            @if(!empty($report->media_files))
                            <span>📎 {{ count($report->media_files) }} Lampiran</span>
                            @elseif($report->media_path)
                            <span>📎 {{ $report->media_type === 'video' ? '🎥 Video' : '📷 Foto' }}</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('emergency.show', $report->id) }}"
                       class="flex-shrink-0 flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-500/20 hover:bg-brand-500 text-brand-400 hover:text-white border border-brand-500/30 text-sm font-bold transition-all">
                        Lihat Detail
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($reports->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $reports->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
