@extends('layouts.app')


@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="max-w-7xl mx-auto pb-12">
    <div class="mb-6">
        <a href="{{ route('foreman.dashboard') }}" class="text-slate-500 hover:text-brand-900 flex items-center gap-2 mb-4 transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Dasbor Foreman
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 mb-1">Detail SPK — {{ $spk->spk_number }}</h1>
                <p class="text-slate-500">{{ $spk->product_name }}</p>
            </div>
    @php
        $allSteps = ['cleaning','transfer','weighing','mixing','extruding'];
        $assignedSteps = $spk->assignments->pluck('step')->unique();
        $allAssigned = collect($allSteps)->every(fn($s) => $assignedSteps->contains($s));
    @endphp
            <div class="flex items-center gap-3">
                @if($spk->status == 'upcoming')
                    <span class="px-4 py-2 bg-slate-100 text-slate-600 rounded-full text-sm font-bold border border-slate-300">Mendatang</span>
                @elseif($spk->status == 'ongoing')
                    <span class="px-4 py-2 bg-amber-500/20 text-amber-400 rounded-full text-sm font-bold border border-amber-500/20">Ongoing · {{ ucfirst($spk->current_step) }}</span>
                @else
                    <span class="px-4 py-2 bg-green-500/20 text-green-400 rounded-full text-sm font-bold border border-green-500/20">Selesai</span>
                @endif
                <a href="{{ route('foreman.spk.assign', $spk->id) }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-500/20 hover:bg-brand-500 text-brand-400 hover:text-white border border-brand-500/30 text-sm font-bold transition-all ml-2">
                    @if($allAssigned)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Pekerja
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Tugaskan Pekerja
                    @endif
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 mb-8">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-4">Informasi SPK</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <table class="w-full text-sm">
                    <tr><td class="py-1 text-slate-500 w-2/5">Nama Customer</td><td class="py-1 font-bold text-slate-900">: {{ $spk->customer ?? '-' }}</td></tr>
                    <tr><td class="py-1 text-slate-500">Produk Akhir</td><td class="py-1 font-bold text-slate-900">: {{ $spk->product_name ?? '-' }}</td></tr>
                    <tr>
                        <td class="py-1 text-slate-500">Jumlah Batch</td>
                        <td class="py-1 font-bold text-slate-900">
                            : {{ $spk->materials->max('total_batches_required') ?? 0 }} Batch
                            @php
                                $completed = $spk->batches->where('step', 'weighing')->count();
                                $total = $spk->materials->max('total_batches_required') ?? 0;
                            @endphp
                            <span class="text-xs text-brand-600 font-bold ml-1">({{ $completed }} / {{ $total }} Batch Selesai)</span>
                        </td>
                    </tr>
                    <tr><td class="py-1 text-slate-500">Tanggal Mulai</td><td class="py-1 font-bold text-slate-900">: {{ $spk->start_date ? \Carbon\Carbon::parse($spk->start_date)->format('d M Y') : '-' }}</td></tr>
                    <tr><td class="py-1 text-slate-500">Tanggal Selesai</td><td class="py-1 font-bold text-slate-900">: {{ $spk->due_date ? \Carbon\Carbon::parse($spk->due_date)->format('d M Y') : '-' }}</td></tr>
                </table>
            </div>
            <div>
                <table class="w-full text-sm">
                    <tr><td class="py-1 text-slate-500 w-2/5">Tanggal Kirim (Tentatif)</td><td class="py-1 font-bold text-slate-900">: {{ $spk->ship_date ? \Carbon\Carbon::parse($spk->ship_date)->format('d M Y') : '-' }}</td></tr>
                    <tr><td class="py-1 text-slate-500">Target OP (Output/Hour)</td><td class="py-1 font-bold text-slate-900">: {{ $spk->target_op ?? '-' }}</td></tr>
                    <tr><td class="py-1 text-slate-500">Working Days</td><td class="py-1 font-bold text-slate-900">: {{ $spk->working_days ?? '-' }}</td></tr>
                    <tr><td class="py-1 text-slate-500">Working Minutes</td><td class="py-1 font-bold text-slate-900">: {{ $spk->working_minutes ?? '-' }}</td></tr>
                </table>
            </div>
            <div>
                <table class="w-full text-sm">
                    <tr><td class="py-1 text-slate-500 w-2/5">Mesin Alokasi</td><td class="py-1 font-bold text-slate-900">: {{ $spk->machine ?? '-' }}</td></tr>
                    <tr><td class="py-1 text-slate-500">Delay (Hour)</td><td class="py-1 font-bold text-amber-600">: {{ $spk->delay_hour ?? '0.0 Hr' }}</td></tr>
                    <tr><td class="py-1 text-slate-500">Status</td><td class="py-1 font-bold {{ $spk->status == 'done' ? 'text-green-600' : 'text-amber-600' }}">: {{ ucfirst($spk->status) }}</td></tr>
                    <tr><td class="py-1 text-slate-500">Keterangan</td><td class="py-1 font-semibold text-slate-700">: {{ $spk->keterangan ?? '-' }}</td></tr>
                    <tr><td class="py-1 text-slate-500">Remarks</td><td class="py-1 font-semibold text-slate-700">: {{ $spk->remarks ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Material & Formula -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-base font-bold text-slate-900">📋 Formula & Kebutuhan Material (BOM)</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr class="text-[11px] uppercase text-slate-500 font-bold">
                            <th class="p-4">Nama Material</th>
                            <th class="p-4 text-center">Jumlah Batch</th>
                            <th class="p-4 text-right">Berat / Batch</th>
                            <th class="p-4 text-right text-brand-700">Total Qty (Target)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($spk->materials as $m)
                        <tr>
                            <td class="p-4 font-medium text-slate-800">{{ $m->material_name }}</td>
                            <td class="p-4 text-center text-slate-500 font-bold">{{ $m->total_batches_required }}</td>
                            <td class="p-4 text-right text-slate-600">{{ number_format($m->target_weight_per_batch, 2) }} Kg</td>
                            <td class="p-4 text-right font-black text-brand-600 text-[13px]">{{ number_format($m->total_material_kg, 1) }} Kg</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
        
        <!-- Progress -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col max-h-[500px]">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-base font-bold text-slate-900">📊 Progress Produksi</h2>
            </div>
            <div class="p-5 space-y-5 overflow-y-auto">
                @php
                    $totalKg = 0; $transKg = 0;
                    foreach($spk->materials as $m) {
                        $totalKg += $m->total_material_kg;
                        $transKg += $spk->transferLogs->where('material_name', $m->material_name)->sum('weight_kg');
                    }
                    $pctT = $totalKg > 0 ? min(100, $transKg / $totalKg * 100) : 0;
                    $totalBatch = $spk->materials->max('total_batches_required') ?? 0;
                    $weighed = $spk->batches->where('step', 'weighing')->count();
                    $mixed = $spk->batches->where('step', 'mixing')->count();
                    $pctW = $totalBatch > 0 ? min(100, $weighed / $totalBatch * 100) : 0;
                    $pctM = $totalBatch > 0 ? min(100, $mixed / $totalBatch * 100) : 0;
                @endphp
                <div class="mb-5">
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="font-bold text-slate-600">1. Transfer</span>
                        <span class="text-slate-500">{{ number_format($transKg,1) }} / {{ number_format($totalKg,1) }} kg</span>
                    </div>
                    <div class="w-full bg-slate-50 rounded-full h-2.5 border border-slate-200 mb-2">
                        <div class="h-2.5 rounded-full bg-blue-500" style="width:{{ $pctT }}%"></div>
                    </div>
                    @if($spk->transferLogs->count() > 0)
                    <div class="space-y-1">
                        @foreach($spk->transferLogs->sortByDesc('created_at')->take(3) as $log)
                        <p class="text-[10px] text-slate-500">
                            <span class="font-bold text-slate-700">{{ $log->worker->name ?? 'Worker' }}</span> memindahkan material <span class="font-semibold">{{ $log->material_name }}</span> sebanyak <span class="font-semibold">{{ $log->weight_kg }} kg</span> &mdash; {{ $log->created_at->format('d M Y, H:i:s') }}
                        </p>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="mb-5">
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="font-bold text-slate-600">2. Weighing</span>
                        <span class="text-slate-500">{{ $weighed }} / {{ $totalBatch }} batch</span>
                    </div>
                    <div class="w-full bg-slate-50 rounded-full h-2.5 border border-slate-200 mb-2">
                        <div class="h-2.5 rounded-full bg-brand-500" style="width:{{ $pctW }}%"></div>
                    </div>
                    @if($spk->batches->where('step', 'weighing')->count() > 0)
                    <div class="space-y-1">
                        @foreach($spk->batches->where('step', 'weighing')->sortByDesc('created_at')->take(3) as $log)
                        <p class="text-[10px] text-slate-500">
                            <span class="font-bold text-slate-700">{{ $log->worker->name ?? 'Worker' }}</span> menimbang batch <span class="font-semibold">#{{ $log->batch_number }}</span> ({{ $log->duration_seconds }}s) &mdash; {{ $log->created_at->format('d M Y, H:i:s') }}
                        </p>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="mb-5">
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="font-bold text-slate-600">3. Mixing</span>
                        <span class="text-slate-500">{{ $mixed }} / {{ $totalBatch }} batch</span>
                    </div>
                    <div class="w-full bg-slate-50 rounded-full h-2.5 border border-slate-200 mb-2">
                        <div class="h-2.5 rounded-full bg-purple-500" style="width:{{ $pctM }}%"></div>
                    </div>
                    @if($spk->batches->where('step', 'mixing')->count() > 0)
                    <div class="space-y-1">
                        @foreach($spk->batches->where('step', 'mixing')->sortByDesc('created_at')->take(3) as $log)
                        <p class="text-[10px] text-slate-500">
                            <span class="font-bold text-slate-700">{{ $log->worker->name ?? 'Worker' }}</span> me-mixing batch <span class="font-semibold">#{{ $log->batch_number }}</span> ({{ $log->duration_seconds }}s) &mdash; {{ $log->created_at->format('d M Y, H:i:s') }}
                        </p>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-200">
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs font-bold text-slate-600 mb-1">4. Extruding</p>
                        @if($spk->extrudingLogs->count() > 0)
                            <p class="text-xs text-green-400 mb-1">✓ {{ $spk->extrudingLogs->count() }} catatan</p>
                            @php $lastEx = $spk->extrudingLogs->sortByDesc('created_at')->first(); @endphp
                            @if($lastEx)
                            <p class="text-[10px] text-slate-500"><span class="font-bold text-slate-700">{{ $lastEx->worker->name ?? 'Worker' }}</span> mencatat di {{ $lastEx->created_at->format('H:i') }}</p>
                            @endif
                        @else
                            <p class="text-xs text-slate-500">Belum dimulai</p>
                        @endif
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs font-bold text-slate-600 mb-1">5. Cleaning</p>
                        @if($spk->cleaningLogs->count() > 0)
                            <p class="text-xs text-green-400 mb-1">✓ Selesai</p>
                            @php $lastCl = $spk->cleaningLogs->sortByDesc('created_at')->first(); @endphp
                            @if($lastCl)
                            <p class="text-[10px] text-slate-500"><span class="font-bold text-slate-700">{{ $lastCl->worker->name ?? 'Worker' }}</span> membersihkan di {{ $lastCl->created_at->format('H:i') }}</p>
                            @endif
                        @else
                            <p class="text-xs text-slate-500">Belum dimulai</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End of grid-cols-2 -->

    <!-- Live Report / Progress Tabbed -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col mb-8 shadow-sm" x-data="spkReport()" x-init="init()">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-wrap gap-2 items-center justify-between">
            <div class="flex flex-wrap gap-2 items-center">
                <h2 class="text-base font-bold text-slate-900 mr-4">📊 Live Report Produksi</h2>
            
            <!-- Tabs -->
            <button @click="setTab('penimbangan')" :class="activeTab === 'penimbangan' ? 'bg-brand-50 text-brand-700 border-brand-200 shadow-sm' : 'bg-slate-50 text-slate-500 border-transparent hover:bg-slate-100'" class="px-4 py-2 text-sm font-bold rounded-xl border transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                Penimbangan
            </button>
            <button @click="setTab('mixing')" :class="activeTab === 'mixing' ? 'bg-brand-50 text-brand-700 border-brand-200 shadow-sm' : 'bg-slate-50 text-slate-500 border-transparent hover:bg-slate-100'" class="px-4 py-2 text-sm font-bold rounded-xl border transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                Mixing
            </button>
            <button @click="setTab('extruder')" :class="activeTab === 'extruder' ? 'bg-brand-50 text-brand-700 border-brand-200 shadow-sm' : 'bg-slate-50 text-slate-500 border-transparent hover:bg-slate-100'" class="px-4 py-2 text-sm font-bold rounded-xl border transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Extruder
            </button>
            <button class="px-4 py-2 text-sm font-bold rounded-xl border border-transparent bg-slate-50 text-slate-400 flex items-center gap-2 opacity-60 cursor-not-allowed">
                Bagging (Menunggu)
            </button>
            </div>
            <button onclick="document.getElementById('liveReportModal').classList.remove('hidden'); document.body.style.overflow = 'hidden';" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition-colors shadow-sm border border-blue-700">
                Show Details
            </button>
        </div>
        
        <div class="p-6 flex-1 flex flex-col bg-slate-50/50">
            <!-- Tab: Penimbangan -->
            <div x-show="activeTab === 'penimbangan'">
                @if($chartData['weighing']['completed'] === 0)
                <div class="py-12 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                    <p class="text-slate-500 font-bold">Belum ada data</p>
                    <p class="text-xs text-slate-400 mt-1">Proses penimbangan belum dimulai.</p>
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Batch Selesai</p>
                        <p class="text-2xl font-black text-brand-600">{{ $chartData['weighing']['completed'] }} <span class="text-sm font-medium text-slate-500">/ {{ $chartData['totalBatches'] }}</span></p>
                        @php $pctW = $chartData['totalBatches'] > 0 ? round(($chartData['weighing']['completed'] / $chartData['totalBatches']) * 100) : 0; @endphp
                        <p class="text-xs text-green-600 font-bold mt-1">{{ $pctW }}% Selesai</p>
                    </div>
                    <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Mesin Timbang</p>
                        <p class="text-lg font-bold text-slate-900">Timbangan A-01</p>
                        <p class="text-xs text-slate-500 mt-1">Kapasitas 500 Kg</p>
                    </div>
                    <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Rata-Rata Durasi</p>
                        @php $avgW = count($chartData['weighing']['durations']) > 0 ? round(collect($chartData['weighing']['durations'])->avg(), 1) : 0; @endphp
                        <p class="text-2xl font-black text-slate-900">{{ $avgW }} <span class="text-sm font-medium text-slate-500">menit</span></p>
                        <p class="text-xs text-slate-500 mt-1">per Batch</p>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Grafik Durasi Penimbangan</p>
                            <p class="text-sm font-bold text-slate-900">Durasi per Batch (Menit) — Proses Timbang</p>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-md">Target ≤ 12 menit</span>
                    </div>
                    <div class="h-64">
                        <canvas id="timbangChart"></canvas>
                    </div>
                </div>
                @endif
            </div>

            <!-- Tab: Mixing -->
            <div x-show="activeTab === 'mixing'" style="display: none;">
                @if($chartData['mixing']['completed'] === 0)
                <div class="py-12 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    <p class="text-slate-500 font-bold">Belum ada data</p>
                    <p class="text-xs text-slate-400 mt-1">Proses mixing belum dimulai.</p>
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Batch Selesai</p>
                        <p class="text-2xl font-black text-brand-600">{{ $chartData['mixing']['completed'] }} <span class="text-sm font-medium text-slate-500">/ {{ $chartData['totalBatches'] }}</span></p>
                        @php $pctM = $chartData['totalBatches'] > 0 ? round(($chartData['mixing']['completed'] / $chartData['totalBatches']) * 100) : 0; @endphp
                        <p class="text-xs text-green-600 font-bold mt-1">{{ $pctM }}% Selesai</p>
                    </div>
                    <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Mesin Mixing</p>
                        <p class="text-lg font-bold text-slate-900">Mixer A-01</p>
                        <p class="text-xs text-slate-500 mt-1">High-Speed Mixer</p>
                    </div>
                    <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Rata-Rata Durasi</p>
                        @php $avgM = count($chartData['mixing']['durations']) > 0 ? round(collect($chartData['mixing']['durations'])->avg(), 1) : 0; @endphp
                        <p class="text-2xl font-black text-slate-900">{{ $avgM }} <span class="text-sm font-medium text-slate-500">menit</span></p>
                        <p class="text-xs text-slate-500 mt-1">per Batch</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm">
                        <div class="mb-4">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Durasi Mixing</p>
                            <p class="text-sm font-bold text-slate-900">Durasi Mixing per Batch (Menit)</p>
                        </div>
                        <div class="h-64">
                            <canvas id="mixingTimeChart"></canvas>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Tab: Extruder -->
            <div x-show="activeTab === 'extruder'" style="display: none;">
                @if($chartData['extruding']['completed'] === 0)
                <div class="py-12 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <p class="text-slate-500 font-bold">Belum ada data</p>
                    <p class="text-xs text-slate-400 mt-1">Proses extruding belum dimulai.</p>
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Batch Selesai</p>
                        <p class="text-2xl font-black text-brand-600">{{ $chartData['extruding']['completed'] }} <span class="text-sm font-medium text-slate-500">/ {{ $chartData['totalBatches'] }}</span></p>
                        @php $pctE = $chartData['totalBatches'] > 0 ? round(($chartData['extruding']['completed'] / $chartData['totalBatches']) * 100) : 0; @endphp
                        <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 mb-1">
                            <div class="h-1.5 rounded-full bg-brand-500" style="width:{{ $pctE }}%"></div>
                        </div>
                        <p class="text-xs text-brand-600 font-bold">{{ $pctE }}% Selesai</p>
                    </div>
                    <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Mesin Extruder</p>
                        <p class="text-lg font-bold text-slate-900">Extruder E-03</p>
                        <p class="text-xs text-slate-500 mt-1">Line 1 - Screw Ø 65mm</p>
                    </div>
                    <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Output Aktual</p>
                        <p class="text-2xl font-black text-slate-900">495 <span class="text-sm font-medium text-slate-500">Kg/Jam</span></p>
                        <p class="text-xs text-slate-500 mt-1">Target: {{ $spk->target_op ?? '500 Kg/Jam' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm relative">
                        <span class="absolute top-5 right-5 px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded">Live</span>
                        <div class="mb-4">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Parameter Mesin</p>
                            <p class="text-sm font-bold text-slate-900">Grafik Suhu Extruder (°C)</p>
                        </div>
                        <div class="h-64">
                            <canvas id="extruderTempChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm">
                        <div class="mb-4">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Ketepatan Waktu</p>
                            <p class="text-sm font-bold text-slate-900">Durasi Proses / Batch (Menit)</p>
                        </div>
                        <div class="h-64">
                            <canvas id="extruderTimeChart"></canvas>
                        </div>
                    </div>
                    </div>
                </div>
                @endif
            </div>

        </div>

    </div>

    <!-- Assignment Table -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">👷 Penugasan Pekerja per Tahap</h2>
                <p class="text-xs text-slate-500 mt-0.5">Assignment yang sudah diatur untuk SPK ini.</p>
            </div>
            <a href="{{ route('foreman.spk.assign', $spk->id) }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-500/20 hover:bg-brand-500 text-brand-400 hover:text-white border border-brand-500/30 text-xs font-bold transition-all">
                ✏️ Edit Assignment
            </a>
        </div>
        @if($spk->assignments->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-slate-600">
                <thead class="text-xs text-slate-500 uppercase bg-slate-100/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Tahap</th>
                        <th class="px-6 py-3 text-left font-semibold">Pekerja</th>
                        <th class="px-6 py-3 text-left font-semibold">Shift</th>
                        <th class="px-6 py-3 text-left font-semibold">Ditugaskan Oleh</th>
                        <th class="px-6 py-3 text-left font-semibold">Diassign Pada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @foreach($spk->assignments->sortBy('step') as $a)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-3 font-bold text-brand-400 uppercase">{{ $a->step }}</td>
                        <td class="px-6 py-3 font-bold text-slate-900">{{ $a->worker->name ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $a->shift }}</td>
                        <td class="px-6 py-3">{{ $a->assignedBy->name ?? '-' }}</td>
                        <td class="px-6 py-3 text-slate-500 text-xs">{{ $a->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-12 text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <p class="text-slate-500">Belum ada pekerja yang di-assign untuk SPK ini.</p>
            <a href="{{ route('foreman.spk.assign', $spk->id) }}" class="inline-block mt-3 px-5 py-2 rounded-xl bg-brand-500 hover:bg-brand-400 text-white font-bold text-sm transition-all">
                + Tugaskan Sekarang
            </a>
        </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    const serverChartData = @json($chartData);
    
    Alpine.data('spkReport', () => {
        let chartInstances = {}; // Keep instances outside reactive proxy
        
        return {
            activeTab: 'penimbangan',
            chartData: serverChartData,
            
            init() {
                // Render first tab
                this.$nextTick(() => {
                    this.initStepChart('penimbangan');
                });
                
                // Watch for tab changes
                this.$watch('activeTab', value => {
                    this.$nextTick(() => {
                        this.initStepChart(value);
                    });
                });
            },
            
            setTab(tab) {
                this.activeTab = tab;
            },

            initStepChart(step) {
                // Destroy existing charts to prevent memory leaks and display bugs
                Object.values(chartInstances).forEach(chart => {
                    if(chart) chart.destroy();
                });
                chartInstances = {};

            const defaultLineOpts = {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                elements: { line: { tension: 0.4 }, point: { radius: 3 } },
                scales: { 
                    x: { grid: { display: false } },
                    y: { grid: { color: '#f1f5f9' }, border: { dash: [4, 4] } }
                }
            };
            
            const defaultBarOpts = {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    x: { grid: { display: false } },
                    y: { grid: { color: '#f1f5f9' }, border: { dash: [4, 4] } }
                }
            };

            if (step === 'penimbangan' && this.chartData.weighing.completed > 0) {
                const ctx = document.getElementById('timbangChart');
                if (ctx) {
                    chartInstances.timbang = new Chart(ctx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: this.chartData.weighing.labels,
                            datasets: [{
                                label: 'Durasi (mnt)',
                                data: this.chartData.weighing.durations,
                                backgroundColor: (ctx) => ctx.raw > 12 ? '#f59e0b' : '#10b981', // Amber if exceed 12m, else Emerald
                                borderRadius: 4,
                                barPercentage: 0.6
                            }, {
                                label: 'Target',
                                data: Array(this.chartData.weighing.labels.length).fill(12),
                                type: 'line',
                                borderColor: '#ef4444',
                                borderDash: [5,5],
                                borderWidth: 2,
                                pointRadius: 0,
                                fill: false
                            }]
                        },
                        options: { ...defaultBarOpts, scales: { ...defaultBarOpts.scales, y: { ...defaultBarOpts.scales.y, min: 0 } } }
                    });
                }
            }

            if (step === 'mixing' && this.chartData.mixing.completed > 0) {
                const ctxMT = document.getElementById('mixingTimeChart');
                if (ctxMT) {
                    chartInstances.mixTime = new Chart(ctxMT.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: this.chartData.mixing.labels,
                            datasets: [{
                                label: 'Durasi (mnt)',
                                data: this.chartData.mixing.durations,
                                backgroundColor: '#8b5cf6', // Violet
                                borderRadius: 4,
                                barPercentage: 0.6
                            }]
                        },
                        options: { ...defaultBarOpts, scales: { ...defaultBarOpts.scales, y: { ...defaultBarOpts.scales.y, min: 0 } } }
                    });
                }
            }
            if (step === 'extruder' && this.chartData.extruding.completed > 0) {
                const ctxET = document.getElementById('extruderTempChart');
                if (ctxET) {
                    chartInstances.extTemp = new Chart(ctxET.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: this.chartData.extruding.labels,
                            datasets: [{
                                label: 'Suhu Aktual (°C)',
                                data: this.chartData.extruding.temps,
                                borderColor: '#0ea5e9', backgroundColor: 'rgba(14,165,233,0.1)', fill: true, borderWidth: 2
                            }]
                        },
                        options: { ...defaultLineOpts, scales: { ...defaultLineOpts.scales, y: { ...defaultLineOpts.scales.y, min: 0 } } }
                    });
                }

                const ctxETime = document.getElementById('extruderTimeChart');
                if (ctxETime) {
                    chartInstances.extTime = new Chart(ctxETime.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: this.chartData.extruding.labels,
                            datasets: [{
                                label: 'Durasi (mnt)',
                                data: this.chartData.extruding.durations,
                                backgroundColor: '#10b981', // Emerald
                                borderRadius: 4,
                                barPercentage: 0.6
                            }]
                        },
                        options: { ...defaultBarOpts, scales: { ...defaultBarOpts.scales, y: { ...defaultBarOpts.scales.y, min: 0 } } }
                    });
                }
            }
        }
    };
    });
});
</script>
@include('production.partials.live_report_modal')
@endsection
