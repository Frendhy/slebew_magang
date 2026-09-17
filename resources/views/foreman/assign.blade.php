@extends('layouts.foreman')

@section('foreman_content')
<div class="fade-in">
    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-1">Penugasan Pekerja</h1>
        <p class="text-slate-500">Pilih SPK, lalu tentukan siapa mengerjakan proses apa dan di shift mana.</p>
    </div>

    <!-- Search Bar -->
    <div class="relative mb-4">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input id="assign-search" type="text" placeholder="Cari berdasarkan nomor SPK atau nama produk..."
            class="w-full bg-white border border-slate-300 rounded-2xl pl-12 pr-5 py-3.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all text-sm"/>
    </div>

    <!-- Filter Tabs -->
    @php
        $requiredStepsGlobal = ['cleaning', 'transfer', 'weighing', 'mixing', 'extruding', 'bagging', 'fg'];
        $foremanTeam = strtolower(auth()->user()->team);
        
        $checkStatus = function($spk) use ($requiredStepsGlobal, $foremanTeam) {
            $workingDays = $spk->working_days ?? 1;
            $totalShifts = ceil($workingDays * 3);
            $totalShiftsWithBackup = $totalShifts + 1;
            $daysCount = ceil($totalShiftsWithBackup / 3);
            
            $targetDate = $spk->due_date ? \Carbon\Carbon::parse($spk->due_date) : now();
            $schedule = \App\Models\WeeklySchedule::whereDate('start_date', '<=', $targetDate)
                                                  ->whereDate('end_date', '>=', $targetDate)
                                                  ->first();
            $expectedSlotsCount = 0;
            $currentShiftCount = 0;
            
            for ($d = 1; $d <= $daysCount; $d++) {
                for ($s = 1; $s <= 3; $s++) {
                    $currentShiftCount++;
                    if ($currentShiftCount > $totalShiftsWithBackup) break;
                    
                    $teamForThisShift = null;
                    if ($s == 1) $teamForThisShift = $schedule ? strtolower($schedule->morning_shift_team) : null;
                    if ($s == 2) $teamForThisShift = $schedule ? strtolower($schedule->afternoon_shift_team) : null;
                    if ($s == 3) $teamForThisShift = $schedule ? strtolower($schedule->night_shift_team) : null;
                    
                    if ($teamForThisShift === $foremanTeam) {
                        $expectedSlotsCount++;
                    }
                }
            }
            
            $myAssignments = $spk->assignments->where('assigned_by', auth()->id());
            $anyAssignmentCount = $spk->assignments->count();

            if ($expectedSlotsCount === 0) {
                return 'lengkap'; // No slots for this team
            }

            if ($myAssignments->count() === 0) {
                return ($anyAssignmentCount > 0) ? 'belum-lengkap' : 'belum-assign';
            }
            
            $bySlot = $myAssignments->groupBy(fn($a) => $a->day_number . '_' . $a->shift_number);
            
            if ($bySlot->count() < $expectedSlotsCount) {
                return 'belum-lengkap';
            }

            $allComplete = $bySlot->every(function($slot) use ($requiredStepsGlobal) {
                $steps = $slot->pluck('step')->unique()->values();
                return collect($requiredStepsGlobal)->every(fn($st) => $steps->contains($st));
            });
            
            return $allComplete ? 'lengkap' : 'belum-lengkap';
        };

        $totalCount   = $allSpks->count();
        $lengkapCount = 0;
        $belumLengkap = 0;
        $belumAssign  = 0;
        
        $spkStatuses = [];
        foreach($allSpks as $spk) {
            $st = $checkStatus($spk);
            $spkStatuses[$spk->id] = $st;
            if ($st === 'lengkap') $lengkapCount++;
            elseif ($st === 'belum-lengkap') $belumLengkap++;
            elseif ($st === 'belum-assign') $belumAssign++;
        }
    @endphp
    
    <div class="flex items-center gap-2 mb-6 flex-wrap">
        <button id="tab-all" onclick="assignFilter('all')"
            class="assign-tab flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition-all bg-brand-500 text-white border-brand-500 shadow-[0_0_12px_rgba(14,165,233,0.3)]">
            Semua <span class="text-xs px-1.5 py-0.5 rounded-full font-bold bg-white/20 text-white">{{ $totalCount }}</span>
        </button>
        <button id="tab-lengkap" onclick="assignFilter('lengkap')"
            class="assign-tab flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition-all bg-white text-slate-600 border-slate-200 hover:border-slate-400">
            Lengkap <span class="text-xs px-1.5 py-0.5 rounded-full font-bold bg-slate-100 text-slate-500">{{ $lengkapCount }}</span>
        </button>
        <button id="tab-belum-lengkap" onclick="assignFilter('belum-lengkap')"
            class="assign-tab flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition-all bg-white text-slate-600 border-slate-200 hover:border-slate-400">
            Belum Lengkap <span class="text-xs px-1.5 py-0.5 rounded-full font-bold bg-slate-100 text-slate-500">{{ $belumLengkap }}</span>
        </button>
        <button id="tab-belum-assign" onclick="assignFilter('belum-assign')"
            class="assign-tab flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition-all bg-white text-slate-600 border-slate-200 hover:border-slate-400">
            Belum Diassign <span class="text-xs px-1.5 py-0.5 rounded-full font-bold bg-slate-100 text-slate-500">{{ $belumAssign }}</span>
        </button>
    </div>

    <!-- SPK List -->
    <div id="assign-list" class="space-y-3">
        @forelse($allSpks as $spk)
        @php
            $assignStatus = $spkStatuses[$spk->id];
        @endphp
        <div class="assign-card bg-white border border-slate-200 rounded-2xl p-5 hover:border-brand-500/30 transition-all group"
             data-title="{{ strtolower($spk->spk_number . ' ' . $spk->product_name) }}"
             data-assign="{{ $assignStatus }}">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500">{{ $spk->spk_number }}</p>
                        @if($spk->status == 'upcoming')
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-full">Mendatang</span>
                        @elseif(in_array($spk->status, ['ongoing','waiting_approval']))
                            <span class="px-2 py-0.5 bg-amber-500/20 text-amber-500 text-[10px] font-bold rounded-full">Sedang Berjalan</span>
                        @else
                            <span class="px-2 py-0.5 bg-green-500/20 text-green-500 text-[10px] font-bold rounded-full">Selesai</span>
                        @endif
                        @if($assignStatus === 'lengkap')
                            <span class="px-2 py-0.5 bg-green-500/20 text-green-600 text-[10px] font-bold rounded-full border border-green-500/30">Lengkap</span>
                        @elseif($assignStatus === 'belum-lengkap')
                            <span class="px-2 py-0.5 bg-orange-500/20 text-orange-500 text-[10px] font-bold rounded-full border border-orange-500/30">Belum Lengkap</span>
                        @else
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-bold rounded-full border border-slate-300">Belum Diassign</span>
                        @endif
                    </div>
                    <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-500 transition-colors">{{ $spk->product_name }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $spk->assignments->count() }} pekerja terassign &middot; Tenggat: {{ $spk->due_date ? \Carbon\Carbon::parse($spk->due_date)->format('d M Y') : '-' }}
                    </p>
                </div>
                <a href="{{ route('foreman.spk.assign', $spk->id) }}"
                   class="flex-shrink-0 flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-500/20 hover:bg-brand-500 text-brand-500 hover:text-white border border-brand-500/30 text-sm font-bold transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Kelola Assignment
                </a>
            </div>
        </div>
        @empty
        <div class="py-16 text-center text-slate-500">Belum ada SPK.</div>
        @endforelse
    </div>
</div>

<script>
    let currentAssignFilter = 'all';
    const assignSearch = document.getElementById('assign-search');

    function assignFilter(status) {
        currentAssignFilter = status;
        document.querySelectorAll('.assign-tab').forEach(btn => {
            btn.classList.remove('bg-brand-500','text-white','border-brand-500','shadow-[0_0_12px_rgba(14,165,233,0.3)]');
            btn.classList.add('bg-white','text-slate-600','border-slate-200');
            const badge = btn.querySelector('span');
            if (badge) { badge.classList.remove('bg-white/20','text-white'); badge.classList.add('bg-slate-100','text-slate-500'); }
        });
        const active = document.getElementById('tab-' + status);
        if (active) {
            active.classList.remove('bg-white','text-slate-600','border-slate-200');
            active.classList.add('bg-brand-500','text-white','border-brand-500','shadow-[0_0_12px_rgba(14,165,233,0.3)]');
            const badge = active.querySelector('span');
            if (badge) { badge.classList.remove('bg-slate-100','text-slate-500'); badge.classList.add('bg-white/20','text-white'); }
        }
        applyAssignFilters();
    }

    function applyAssignFilters() {
        const query = assignSearch.value.toLowerCase();
        document.querySelectorAll('.assign-card').forEach(card => {
            const title = card.getAttribute('data-title');
            const asgn = card.getAttribute('data-assign');
            const matchQ = title.includes(query);
            const matchF = currentAssignFilter === 'all' || asgn === currentAssignFilter;
            card.style.display = (matchQ && matchF) ? 'block' : 'none';
        });
    }

    assignSearch.addEventListener('input', applyAssignFilters);
</script>
@endsection