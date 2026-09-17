@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto pb-12">
    <div class="mb-6">
        <a href="{{ route('foreman.assign') }}" class="text-slate-500 hover:text-brand-900 flex items-center gap-2 mb-4 transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Tugaskan Pekerja
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900 mb-1">Assign Pekerja — {{ $spk->spk_number }}</h1>
        <p class="text-slate-500">{{ $spk->product_name }}</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-500/10 border border-green-500/30 text-green-700 rounded-xl px-5 py-4 font-semibold text-sm">
        ✓ {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-700 rounded-xl px-5 py-4 text-sm font-semibold">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl p-6 mb-8 shadow-sm">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Working Days</p>
                <p class="text-lg font-bold text-slate-900">{{ $spk->working_days ?? '-' }} Days</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Tenggat Waktu</p>
                <p class="text-lg font-bold text-slate-900">{{ $spk->due_date ? \Carbon\Carbon::parse($spk->due_date)->format('d M Y') : '-' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Total Shift Dibutuhkan</p>
                <p class="text-lg font-bold text-brand-600">{{ $totalShifts }} Shift</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Tim Foreman (Login)</p>
                <p class="text-lg font-bold text-amber-600 uppercase">TIM {{ $foremanTeam }}</p>
            </div>
        </div>
    </div>

    @php 
        $manAllocation = $spk->man_allocation ?? 1;
        $shiftsWeekday = [
            1 => '00:00 - 08:00',
            2 => '08:00 - 16:00',
            3 => '16:00 - 00:00'
        ];
    @endphp

    <div class="space-y-8">
        <div>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-slate-900 capitalize">Penugasan Shift Global</h2>
                
                @php $globalAssignments = $spk->assignments; @endphp
                
                @if($globalAssignments->where('assigned_by', auth()->id())->count() > 0)
                <form action="{{ route('foreman.assign.delete', $globalAssignments->where('assigned_by', auth()->id())->first()->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-500 text-red-600 hover:text-white border border-red-200 text-xs font-bold transition-all"
                        onclick="return confirm('Hapus semua penugasan yang Anda buat?')">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Kosongkan Jadwal Tim {{ ucfirst($foremanTeam) }}
                    </button>
                </form>
                @endif
            </div>

            <form action="{{ route('foreman.assign.save') }}" method="POST" x-data="alokasiApp()">
                @csrf
                <input type="hidden" name="spk_id" value="{{ $spk->id }}">

                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-6 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                        <div>
                            <h4 class="font-bold text-slate-800">Alokasi Operator Per Shift & Proses</h4>
                            <p class="text-xs text-slate-500 mt-1">Pilih operator yang bertugas untuk setiap proses di tiap shift.</p>
                        </div>
                        <div class="flex gap-2 text-[10px]">
                            <span class="px-2 py-1 bg-red-50 text-red-700 border border-red-200 rounded font-bold">Tim RED</span>
                            <span class="px-2 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded font-bold">Tim GREEN</span>
                            <span class="px-2 py-1 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded font-bold">Tim YELLOW</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <div class="flex gap-0 divide-x divide-slate-100 min-w-max pb-4">
                            @foreach($shiftGrid as $dayNum => $shifts)
                                @foreach($shifts as $shiftNum => $slot)
                                @php
                                    $teamColors = [
                                        'red' => 'bg-red-500',
                                        'green' => 'bg-emerald-500',
                                        'yellow' => 'bg-yellow-500',
                                    ];
                                    $bgColors = [
                                        'red' => 'bg-red-50 border-red-200',
                                        'green' => 'bg-emerald-50 border-emerald-200',
                                        'yellow' => 'bg-yellow-50 border-yellow-200',
                                    ];
                                    $team = strtolower($slot['team_assigned'] ?: 'default');
                                    $teamColor = $teamColors[$team] ?? 'bg-slate-500';
                                    $bgColor = $bgColors[$team] ?? 'bg-slate-50 border-slate-200';
                                    
                                    $shiftNames = [1 => 'Shift Pagi', 2 => 'Shift Siang', 3 => 'Shift Malam'];
                                    
                                    $stations = [
                                        'cleaning' => ['label' => 'Cleaning', 'need' => 1],
                                        'transfer' => ['label' => 'Transfer', 'need' => 1],
                                        'weighing' => ['label' => 'Penimbangan', 'need' => 1],
                                        'mixing'   => ['label' => 'Mixing', 'need' => 1],
                                        'extruding'=> ['label' => 'Extruder', 'need' => 2],
                                        'bagging'  => ['label' => 'Bagging', 'need' => 1],
                                        'fg'       => ['label' => 'Penyerahan (FG)', 'need' => 1]
                                    ];
                                @endphp

                                <div class="p-5 w-72 flex-shrink-0">
                                    <!-- Shift Header -->
                                    <div class="rounded-xl p-3 mb-5 text-white {{ $teamColor }} shadow-sm">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="text-xs font-bold">{{ $shiftNames[$shiftNum] }} (Hari {{ $dayNum }})</span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-white/20 uppercase">{{ $slot['team_assigned'] }} TEAM</span>
                                        </div>
                                        <p class="text-[10px] opacity-90 mt-1.5">{{ $shiftsWeekday[$shiftNum] }}</p>
                                    </div>

                                    @if($slot['is_locked'])
                                    <div class="mt-4 p-4 rounded-xl {{ $bgColor }}">
                                        <p class="text-xs font-semibold text-slate-700 text-center flex flex-col items-center gap-2">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            Shift ini ditugaskan oleh<br><span class="uppercase font-bold text-slate-900 mt-1">Tim {{ $slot['team_assigned'] }}</span>
                                        </p>
                                        
                                        <!-- Readonly Assignment dari tim lain -->
                                        @foreach($stations as $stepId => $station)
                                        @php
                                            $assignedHere = $globalAssignments->where('day_number', $dayNum)
                                                                              ->where('shift_number', $shiftNum)
                                                                              ->where('step', $stepId);
                                        @endphp
                                        @if($assignedHere->count() > 0)
                                        <div class="mt-3">
                                            <p class="text-[9px] font-bold text-slate-500 uppercase">{{ $station['label'] }}</p>
                                            <div class="mt-1 space-y-1">
                                                @foreach($assignedHere as $asg)
                                                <div class="px-2 py-1 bg-white/60 border border-slate-200 rounded text-xs text-slate-600 font-medium">
                                                    {{ $asg->worker->name ?? 'Pekerja' }}
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                    @else
                                    <input type="hidden" name="is_cadangan[{{ $dayNum }}][{{ $shiftNum }}]" value="{{ $slot['is_cadangan'] ? 1 : 0 }}">
                                    
                                    <div class="space-y-4">
                                        @foreach($stations as $stepId => $station)
                                        @php
                                            $assignedHere = $globalAssignments->where('day_number', $dayNum)
                                                                              ->where('shift_number', $shiftNum)
                                                                              ->where('step', $stepId)
                                                                              ->pluck('worker_id')->toArray();
                                        @endphp
                                        <div class="station-block" x-data="{ checkedCount: {{ count(array_intersect($assignedHere, $workers->pluck('id')->toArray())) }}, max: {{ $station['need'] }} }">
                                            <div class="flex justify-between items-center mb-2">
                                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">{{ $station['label'] }}</p>
                                                <span class="bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded text-[8px] font-bold">Butuh: {{ $station['need'] }}</span>
                                            </div>
                                            <div class="space-y-1.5">
                                                @foreach($workers as $worker)
                                                @php $isChecked = in_array($worker->id, $assignedHere); @endphp
                                                <label x-data="{ isChecked: {{ $isChecked ? 'true' : 'false' }} }"
                                                       class="flex items-center gap-2 p-2 rounded-lg border transition-all"
                                                       :class="isChecked ? 'border-brand-400 bg-brand-50 cursor-pointer' : (checkedCount >= max ? 'border-slate-100 bg-slate-50 opacity-50 cursor-not-allowed' : 'border-slate-200 bg-white cursor-pointer hover:bg-slate-50')">
                                                    <input type="checkbox"
                                                           name="assignments[{{ $dayNum }}][{{ $shiftNum }}][{{ $stepId }}][]"
                                                           value="{{ $worker->id }}"
                                                           x-model="isChecked"
                                                           :disabled="!isChecked && checkedCount >= max"
                                                           @change="isChecked ? checkedCount++ : checkedCount--; updatePreview('{{ $dayNum }}_{{ $shiftNum }}', '{{ $station['label'] }}', $el, '{{ $worker->name }}')"
                                                           class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                                    <span class="text-xs font-semibold" :class="isChecked ? 'text-brand-700' : 'text-slate-700'">{{ $worker->name }}</span>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
                    <h4 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Preview Keterangan Alokasi di Dokumen SPK
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-4 text-sm" id="preview-container">
                        @foreach($shiftGrid as $dayNum => $shifts)
                            @foreach($shifts as $shiftNum => $slot)
                            @php
                                $team = strtolower($slot['team_assigned'] ?: 'default');
                                $bgColors = [
                                    'red' => 'bg-red-50/70 border-red-200 text-red-900',
                                    'green' => 'bg-emerald-50/70 border-emerald-200 text-emerald-900',
                                    'yellow' => 'bg-yellow-50/70 border-yellow-200 text-yellow-900',
                                ];
                                $previewClass = $bgColors[$team] ?? 'bg-slate-50 border-slate-200 text-slate-900';
                            @endphp
                            <div class="rounded-xl p-4 border {{ $previewClass }}">
                                <p class="font-bold text-xs mb-1">Hari {{ $dayNum }} - {{ $shiftNames[$shiftNum] }}</p>
                                <p class="text-[10px] opacity-70 mb-3 font-bold uppercase tracking-widest">Tim {{ $slot['team_assigned'] }}</p>
                                <div class="space-y-1.5" id="preview_{{ $dayNum }}_{{ $shiftNum }}">
                                    @foreach($stations as $stepId => $station)
                                        <p class="text-xs">
                                            <span class="font-bold opacity-80">{{ $station['label'] }}:</span>
                                            <span class="opacity-100 preview-names" data-step="{{ $station['label'] }}">
                                                @if($slot['is_locked'])
                                                    @php
                                                        $asgs = $globalAssignments->where('day_number', $dayNum)
                                                                                  ->where('shift_number', $shiftNum)
                                                                                  ->where('step', $stepId);
                                                    @endphp
                                                    {{ $asgs->count() > 0 ? implode(', ', $asgs->map(fn($a) => $a->worker->name)->toArray()) : '-' }}
                                                @else
                                                    @php
                                                        $asgs = $globalAssignments->where('day_number', $dayNum)
                                                                                  ->where('shift_number', $shiftNum)
                                                                                  ->where('step', $stepId);
                                                    @endphp
                                                    {!! $asgs->count() > 0 ? implode(', ', $asgs->map(fn($a) => $a->worker->name)->toArray()) : '<i class="opacity-50">[Pilih di atas]</i>' !!}
                                                @endif
                                            </span>
                                        </p>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="flex items-center gap-2 px-6 py-3 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-sm shadow-md transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Jadwal Shift Pekerja
                    </button>
                </div>
            </form>
            
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('alokasiApp', () => ({
                        selections: {},
                        init() {
                            // Populate initial selections from checked boxes
                            document.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
                                // For alpine binding, wait a moment or just use native JS in updatePreview
                            });
                        },
                        updatePreview(shiftId, stepLabel, checkboxElem, workerName) {
                            const container = document.getElementById('preview_' + shiftId);
                            if (!container) return;
                            
                            const stepSpan = Array.from(container.querySelectorAll('.preview-names')).find(el => el.getAttribute('data-step') === stepLabel);
                            if (!stepSpan) return;
                            
                            // Get all checked workers for this specific step in this specific shift
                            const allChecked = Array.from(checkboxElem.closest('.station-block').querySelectorAll('input[type="checkbox"]:checked'));
                            
                            if (allChecked.length === 0) {
                                stepSpan.innerHTML = '<i class="opacity-50">[Pilih di atas]</i>';
                            } else {
                                stepSpan.innerHTML = allChecked.map(cb => cb.nextElementSibling.innerText).join(', ');
                            }
                        }
                    }))
                })
            </script>


        </div>
    </div>
</div>
@endsection
