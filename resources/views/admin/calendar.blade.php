@extends('layouts.admin')

@section('admin_content')
<div class="fade-in" x-data="{ viewMode: 'list' }">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 mb-1">Kalender Jadwal Produksi</h1>
            <p class="text-slate-500">Lihat jadwal seluruh SPK dalam tampilan kalender atau list detail.</p>
        </div>
        <div class="bg-slate-100 p-1 rounded-xl flex items-center border border-slate-200 shadow-inner">
            <button @click="viewMode = 'month'; setTimeout(() => cal.render(), 100)" 
                    :class="viewMode === 'month' ? 'bg-white text-brand-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all">
                Month
            </button>
            <button @click="viewMode = 'list'" 
                    :class="viewMode === 'list' ? 'bg-white text-brand-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all">
                List
            </button>
        </div>
    </div>

    <!-- Legend -->
    <div class="flex flex-wrap gap-4 mb-5" x-show="viewMode === 'month'" style="display: none;">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <span class="w-3 h-3 rounded-full bg-slate-500 inline-block"></span> Mendatang
        </div>
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span> Sedang Berjalan
        </div>
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> Selesai
        </div>
    </div>

    <!-- Calendar View -->
    <div x-show="viewMode === 'month'" style="display: none;" class="bg-white border border-slate-200 rounded-2xl p-5 shadow-2xl">
        <div id="calendar" class="text-slate-600"></div>
    </div>

    <!-- Custom List View -->
    <div x-show="viewMode === 'list'">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] uppercase tracking-wider text-slate-500 font-extrabold">
                        <th class="p-4">No. SPK & Customer</th>
                        <th class="p-4">Timeline & Pengiriman</th>
                        <th class="p-4">Metrik Kerja (Days/Mins/Delay)</th>
                        <th class="p-4">Proses (TP/MP/MD/ML) & Batch</th>
                        <th class="p-4">Keterangan & Remarks</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($spks as $spk)
                    @php
                        // Default status text & background
                        $statusText = 'RUNNING'; 
                        $statusBg = 'bg-emerald-100 text-emerald-700';

                        if ($spk->status == 'done') {
                            $statusText = 'FINISHED';
                            $statusBg = 'bg-blue-100 text-blue-700';
                        }

                        $completedBatches = $spk->batches->where('step', 'weighing')->count(); 
                        $totalBatches = $spk->materials->max('total_batches_required') ?? ($completedBatches > 0 ? $completedBatches : 10);
                        $batchPct = $totalBatches > 0 ? min(100, round(($completedBatches / $totalBatches) * 100)) : 0;
                        
                        $batchColor = 'bg-emerald-50 border-emerald-200 text-emerald-700';
                        if ($spk->status == 'done') {
                            $batchColor = 'bg-blue-50 border-blue-200 text-blue-700';
                        } elseif ($spk->status == 'upcoming') {
                            $batchColor = 'bg-amber-50 border-amber-200 text-amber-700';
                        }
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <!-- NO. SPK & CUSTOMER -->
                        <td class="p-4 align-top">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $statusBg }} mb-2 inline-block">
                                {{ $statusText }}
                            </span>
                            <div class="font-extrabold text-slate-900 text-sm mb-0.5">{{ $spk->spk_number }}</div>
                            <div class="font-bold text-slate-800 text-xs mb-1">{{ $spk->customer ?? 'PT Customer Internal' }}</div>
                            <div class="text-xs text-slate-500">{{ $spk->product_name }}</div>
                        </td>
                        
                        <!-- TIMELINE & PENGIRIMAN -->
                        <td class="p-4 align-top">
                            <div class="text-xs font-bold text-slate-800 mb-1">
                                Tanggal Kirim: {{ $spk->ship_date ? \Carbon\Carbon::parse($spk->ship_date)->format('d M Y') : 'TBA' }} <span class="text-slate-500 font-normal">(Tentatif)</span>
                            </div>
                            <div class="text-xs font-semibold text-brand-600">{{ $spk->machine ?? 'Belum Alokasi Mesin' }}</div>
                        </td>
                        
                        <!-- METRIK KERJA -->
                        <td class="p-4 align-top">
                            <div class="text-[11px] text-slate-600 mb-0.5"><span class="font-bold">Working Days:</span> {{ $spk->working_days ?? '0' }} Days</div>
                            <div class="text-[11px] text-slate-600 mb-0.5"><span class="font-bold">Working Mins:</span> {{ $spk->working_minutes ?? '0' }} Mins</div>
                            <div class="text-[11px] font-bold {{ (floatval($spk->delay_hour) > 0) ? 'text-red-600' : 'text-emerald-600' }}">
                                Delay: {{ $spk->delay_hour ?? '0.0' }} Hr {{ (floatval($spk->delay_hour) > 0) ? '(Delayed)' : '(On Time)' }}
                            </div>
                        </td>

                        <!-- PROSES & BATCH -->
                        <td class="p-4 align-top">
                            @if($spk->status == 'upcoming' && $completedBatches == 0)
                                <div class="px-3 py-1.5 rounded bg-amber-50 border border-amber-200 mb-1 inline-block">
                                    <div class="text-xs font-bold text-amber-700">Target OP: {{ $spk->target_op ?? '400 Kg / Jam' }}</div>
                                </div>
                                <div class="text-[11px] text-slate-600 mb-0.5">Batch: {{ $completedBatches }} / {{ $totalBatches }} Batch Selesai</div>
                                <div class="text-[10px] text-slate-500">Proses: Menunggu Jadwal</div>
                            @else
                                <div class="px-3 py-1.5 rounded border {{ $batchColor }} text-xs font-bold mb-2 inline-block">
                                    {{ $completedBatches }} / {{ $totalBatches }} Batch Selesai ({{ $batchPct }}%)
                                </div>
                                <div class="text-[11px] text-brand-600 font-medium">
                                    Proses Aktif: {{ ucfirst($spk->current_step ?? 'Persiapan') }}
                                </div>
                            @endif
                        </td>
                        
                        <!-- KETERANGAN & REMARKS -->
                        <td class="p-4 align-top">
                            <div class="text-[11px] text-slate-600 mb-1">Ket: {{ $spk->keterangan ?? 'Standar Produksi' }}</div>
                            <div class="text-[11px] text-slate-500">Remarks: {{ $spk->remarks ?? '-' }}</div>
                        </td>

                        <!-- AKSI -->
                        <td class="p-4 align-middle">
                            <div class="flex flex-col gap-2 items-center">
                                <a href="{{ route('admin.spk.detail', $spk->id) }}" class="flex items-center justify-center gap-1.5 w-32 px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-bold rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Detail SPK
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script>
    let cal;
    document.addEventListener('DOMContentLoaded', function() {
        @php
            $events = $spks->map(function($spk) {
                $color = '#64748b';
                if ($spk->status == 'ongoing') $color = '#f59e0b';
                if ($spk->status == 'done') $color = '#22c55e';
                $start = $spk->start_date ? \Carbon\Carbon::parse($spk->start_date) : \Carbon\Carbon::parse($spk->created_at);
                $end = $spk->due_date ? \Carbon\Carbon::parse($spk->due_date)->addDay() : $start->copy()->addDays(1);
                return [
                    'title' => $spk->spk_number . ' — ' . $spk->product_name,
                    'start' => $start->format('Y-m-d'),
                    'end'   => $end->format('Y-m-d'),
                    'color' => $color,
                    'url'   => url('/admin/spk/' . $spk->id),
                ];
            });
        @endphp

        cal = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: '' // Disable fullcalendar internal toggle button
            },
            events: {!! json_encode($events) !!},
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                window.location.href = info.event.url;
            },
            eventMouseEnter: function(info) {
                info.el.style.cursor = 'pointer';
                info.el.style.opacity = '0.85';
            },
            eventMouseLeave: function(info) {
                info.el.style.opacity = '1';
            },
            height: 'auto',
        });
        cal.render();
    });
</script>
<style>
    .fc-theme-standard td, .fc-theme-standard th, .fc-theme-standard .fc-scrollgrid { border-color: #e2e8f0; }
    .fc .fc-col-header-cell-cushion { color: #475569; font-weight: 600; }
    .fc .fc-daygrid-day-number { color: #64748b; }
    .fc .fc-button-primary { background-color: #0ea5e9; border-color: #0ea5e9; font-weight: 700; border-radius: 8px; color: #ffffff; }
    .fc .fc-button-primary:hover { background-color: #0284c7; border-color: #0284c7; }
    .fc .fc-button-primary:not(:disabled).fc-button-active { background-color: #0284c7; border-color: #0284c7; }
    .fc-day-today { background-color: #f8fafc !important; }
    .fc .fc-toolbar-title { color: #0f172a; font-weight: 800; }
    .fc .fc-daygrid-event { border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
    .fc-event { color: #ffffff !important; }
</style>
@endsection