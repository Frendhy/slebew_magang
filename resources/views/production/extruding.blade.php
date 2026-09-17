@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto mt-6 fade-in">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-1">{{ $spk?->spk_number ?? '' }}</p>
            <h1 class="text-3xl font-extrabold text-slate-900">Proses Extruding</h1>
            <p class="text-slate-500 mt-1">Monitoring mesin setiap 1 jam. Catat semua parameter.</p>
        </div>
        <a href="/" class="flex items-center gap-2 text-slate-500 hover:text-brand-900 text-sm font-semibold transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Dashboard
        </a>
    </div>

    @if($spk)
    {{-- Machine Settings Reference (Removed) --}}

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border-2 border-amber-500/70 shadow-[0_0_30px_rgba(245,158,11,0.2)] overflow-hidden">
                <div class="px-8 pt-8 pb-4">
                    <div class="flex justify-between items-center mb-4">
                        <p class="text-amber-400 font-bold uppercase tracking-widest text-sm">Countdown Monitoring Berikutnya</p>
                        <span id="session-badge" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                            Sesi #{{ $extrudingLogs->count() + 1 }}
                        </span>
                    </div>
                    <div class="flex justify-center items-center space-x-4 my-4">
                        <div class="text-center">
                            <div class="text-7xl font-extrabold text-slate-900 font-mono tracking-tighter" id="ext-hours">01</div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">Jam</p>
                        </div>
                        <div class="text-5xl text-slate-500 font-mono pb-4">:</div>
                        <div class="text-center">
                            <div class="text-7xl font-extrabold text-slate-900 font-mono tracking-tighter" id="ext-minutes">00</div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">Menit</p>
                        </div>
                        <div class="text-5xl text-slate-500 font-mono pb-4">:</div>
                        <div class="text-center">
                            <div class="text-7xl font-extrabold text-amber-400 font-mono tracking-tighter" id="ext-seconds">00</div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">Detik</p>
                        </div>
                    </div>
                    <div class="w-full bg-slate-50 rounded-full h-3 my-4">
                        <div class="bg-amber-500 h-3 rounded-full transition-all shadow-[0_0_10px_rgba(245,158,11,0.5)]" id="ext-progress" style="width:0%"></div>
                    </div>
                    <div class="flex gap-4 mt-4">
                        <button id="btn-ext-start" onclick="startExtruding()" class="flex-1 py-4 bg-amber-500 hover:bg-amber-400 text-white font-bold text-lg rounded-xl shadow-[0_0_15px_rgba(245,158,11,0.3)] transition-all">
                            Mulai Mesin Extruder
                        </button>
                        <button onclick="openMonitoringModal()" class="px-6 py-4 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold rounded-xl border border-slate-300 transition-colors">
                            Isi Form Manual
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 h-full">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Riwayat Monitoring</h2>
                    <span id="log-badge" class="text-xs font-bold px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30">{{ $extrudingLogs->count() }} sesi</span>
                </div>
                <div id="ext-log-history" class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @forelse($extrudingLogs as $log)
                    <div class="bg-green-500/10 border border-green-500/30 p-3 rounded-lg">
                        <div class="flex justify-between items-center mb-1">
                            <p class="text-xs font-bold text-green-400">Sesi #{{ $extrudingLogs->count() - $loop->index }} ✓</p>
                            <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }}</p>
                        </div>
                        @if(is_array($log->monitoring_data) && count($log->monitoring_data) > 0)
                            <div class="grid grid-cols-2 gap-1 text-[10px] mt-2">
                                @php $i = 0; @endphp
                                @foreach($log->monitoring_data as $key => $val)
                                    @if($i < 2 && !empty($val))
                                        <div><p class="text-slate-500 capitalize">{{ str_replace('_', ' ', $key) }}</p><p class="text-slate-900 font-bold">{{ $val }}</p></div>
                                        @php $i++; @endphp
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="grid grid-cols-2 gap-1 text-xs">
                                <div><p class="text-slate-500">Suhu</p><p class="text-slate-900 font-bold">{{ $log->temperature }}°C</p></div>
                                <div><p class="text-slate-500">Tekanan</p><p class="text-slate-900 font-bold">{{ $log->pressure }} Bar</p></div>
                            </div>
                        @endif
                        @if($log->notes)
                        <p class="text-xs text-slate-500 mt-1 italic">{{ $log->notes }}</p>
                        @endif
                    </div>
                    @empty
                    <p id="ext-empty-msg" class="text-center text-slate-500 text-sm py-4">Belum ada sesi monitoring.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center mt-8 pt-6 border-t border-slate-200">
        <div class="flex items-center gap-3">
            <a href="/production/spk-detail?spk_id={{ $spk->id }}" class="text-slate-500 hover:text-brand-900 font-medium py-3 px-6 transition-colors">Kembali</a>
            @include('production.partials.emergency_button', ['step' => 'extruding'])
        </div>
        <a href="/production/extruding/complete?spk_id={{ $spk->id }}">
            <button class="px-8 py-4 rounded-xl bg-green-500 hover:bg-green-400 text-white font-bold text-lg shadow-[0_0_20px_rgba(34,197,94,0.3)] transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Selesai & Minta ACC Foreman
            </button>
        </a>
    </div>
    @endif
</div>

{{-- Monitoring Modal --}}
<div id="monitoring-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm"></div>
        <div class="relative bg-slate-50 rounded-2xl shadow-2xl border border-amber-500/50 w-full max-w-lg z-10">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-900">Monitoring Mesin {{ $spk?->machine ?? '' }} — Sesi #<span id="modal-session">{{ $extrudingLogs->count() + 1 }}</span></h3>
                <p class="text-sm text-amber-400">Isi semua parameter mesin dengan benar.</p>
            </div>
            <div class="p-6 space-y-6 max-h-[60vh] overflow-y-auto" id="monitoring-fields-container">
                @php
                $machine = $spk->machine ?? 'E01'; // Default fallback

                $allFields = [
                    // ── General ──────────────────────────────────────────────────────
                    ['id'=>'output_aktual',      'label'=>'Output Aktual',          'machines'=>['E01','E03','E05','E06'], 'group'=>'General'],
                    ['id'=>'nama_produk',         'label'=>'Nama Produk',            'machines'=>['E01','E03','E05','E06'], 'group'=>'General'],
                    ['id'=>'jumlah_granule',      'label'=>'Jumlah Granule / Gram',  'machines'=>['E01','E03','E05','E06'], 'group'=>'General'],

                    // ── Kondisi Mesin (Kanan Atas) ───────────────────────────────────
                    ['id'=>'tekanan_ac_barel',    'label'=>'Tekanan Air Cooling Barel Mesin', 'machines'=>['E01','E03','E05','E06'], 'group'=>'Kondisi Mesin'],
                    ['id'=>'tekanan_oli_gearbox', 'label'=>'Tekanan Oli Gearbox',   'machines'=>['E01','E03','E05','E06'], 'group'=>'Kondisi Mesin'],
                    ['id'=>'suhu_oli_gearbox',    'label'=>'Suhu Oli Gearbox',       'machines'=>['E01','E03','E05','E06'], 'group'=>'Kondisi Mesin'],
                    ['id'=>'vacuum_bar',           'label'=>'Vacuum Bar',             'machines'=>['E01','E03','E05','E06'], 'group'=>'Kondisi Mesin'],
                    ['id'=>'tekanan_air_vacuum',  'label'=>'Tekanan Air Vacuum System','machines'=>['E01','E03','E05','E06'], 'group'=>'Kondisi Mesin'],
                    ['id'=>'suhu_ac_gearbox',     'label'=>'Suhu Air Cooling Gearbox (Cooling Tower)','machines'=>['E01','E03','E05','E06'], 'group'=>'Kondisi Mesin'],
                    ['id'=>'suhu_ac_barel_sw',    'label'=>'Suhu Air Cooling Barel (Soft Water)','machines'=>['E01','E03','E05','E06'], 'group'=>'Kondisi Mesin'],

                    // ── Extruder - Feeder ────────────────────────────────────────────
                    ['id'=>'output_mesin',        'label'=>'Output Mesin (Extruder)','machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'ampere_extruder',     'label'=>'Ampere / Torsi Extruder','machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'sei',                 'label'=>'SEI',                    'machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'rpm_extruder',        'label'=>'RPM Extruder',           'machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    // F1-F4 hanya untuk E06
                    ['id'=>'output_f1',           'label'=>'Output Feeder 1 (F1)',   'machines'=>['E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'output_f2',           'label'=>'Output Feeder 2 (F2)',   'machines'=>['E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'output_f3',           'label'=>'Output Feeder 3 (F3)',   'machines'=>['E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'output_f4',           'label'=>'Output Feeder 4 (F4)',   'machines'=>['E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'output_jotam_s50',    'label'=>'Output Jotam S-50',      'machines'=>['E06'], 'group'=>'Extruder - Feeder'],
                    // S-90 untuk semua
                    ['id'=>'output_jotam_s90_resin', 'label'=>'Output Jotam S-90 (NX-590-1 Resin)',  'machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'output_jotam_s90_aditif','label'=>'Output Jotam S-90 (NX-590-2 Aditif)', 'machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'output_liquid_feeder', 'label'=>'Output Liquid Feeder',  'machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'tekanan_liquid_feeder','label'=>'Tekanan Liquid Feeder', 'machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'rpm_feeder_rework',   'label'=>'RPM Feeder Rework',      'machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    // RPM Main Feeder: semua kecuali E06
                    ['id'=>'rpm_main_feeder',     'label'=>'RPM Main Feeder / Feeder 1','machines'=>['E01','E03','E05'], 'group'=>'Extruder - Feeder'],
                    // E06 pakai "RPM Main Feeder / Feeder 1" juga tapi versi E06
                    ['id'=>'rpm_main_feeder_e06', 'label'=>'RPM Main Feeder / Feeder 1','machines'=>['E06'], 'group'=>'Extruder - Feeder'],
                    // Ampere Main Feeder: E01, E03, E05 (Khusus E01 E02 E03 E05)
                    ['id'=>'ampere_main_feeder',  'label'=>'Ampere Main Feeder',     'machines'=>['E01','E03','E05'], 'group'=>'Extruder - Feeder'],
                    // E06 punya Ampere Main Feeder versi sendiri
                    ['id'=>'ampere_main_feeder_e06','label'=>'Ampere Main Feeder',   'machines'=>['E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'rpm_side_feeder',     'label'=>'RPM Side Feeder',        'machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'melt_temperatur',     'label'=>'Melt Temperatur',        'machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'melt_pressure',       'label'=>'Melt Pressure',          'machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    // Valve hanya E06
                    ['id'=>'valve_air_pendingin', 'label'=>'Valve Air Pendingin Zone 1 (Open/Close)','machines'=>['E06'], 'group'=>'Extruder - Feeder'],
                    // Zone 1-4 semua mesin
                    ['id'=>'zone_1',  'label'=>'Zone 1 (Set/Actual)',  'machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'zone_2',  'label'=>'Zone 2 (Set/Actual)',  'machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'zone_3',  'label'=>'Zone 3 (Set/Actual)',  'machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'zone_4',  'label'=>'Zone 4 (Set/Actual)',  'machines'=>['E01','E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    // Zone 5-10 → E03, E05, E06
                    ['id'=>'zone_5',  'label'=>'Zone 5 (Set/Actual)',  'machines'=>['E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'zone_6',  'label'=>'Zone 6 (Set/Actual)',  'machines'=>['E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'zone_7',  'label'=>'Zone 7 (Set/Actual)',  'machines'=>['E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'zone_8',  'label'=>'Zone 8 (Set/Actual)',  'machines'=>['E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'zone_9',  'label'=>'Zone 9 (Set/Actual)',  'machines'=>['E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'zone_10', 'label'=>'Zone 10 (Set/Actual)', 'machines'=>['E03','E05','E06'], 'group'=>'Extruder - Feeder'],
                    // Zone 11-12 → E05, E06
                    ['id'=>'zone_11', 'label'=>'Zone 11 (Set/Actual)', 'machines'=>['E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'zone_12', 'label'=>'Zone 12 (Set/Actual)', 'machines'=>['E05','E06'], 'group'=>'Extruder - Feeder'],
                    // Suhu 8.0 hanya E06
                    ['id'=>'suhu_8_0','label'=>'Suhu 8.0 (Set/Actual)','machines'=>['E06'], 'group'=>'Extruder - Feeder'],
                    // Screen Changer → E05, E06
                    ['id'=>'suhu_input_screen_changer','label'=>'Suhu Input Screen Changer (Set/Actual)','machines'=>['E05','E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'suhu_tsw','label'=>'Suhu TSW (Screen Changer 1 – untuk R05)','machines'=>['E06'], 'group'=>'Extruder - Feeder'],
                    ['id'=>'screen_changer_2','label'=>'Screen Changer 2 (Set/Actual – untuk E55)','machines'=>['E05'], 'group'=>'Extruder - Feeder'],

                    // ── UWP - Pelletizer ─────────────────────────────────────────────
                    ['id'=>'suhu_adaptor',  'label'=>'Suhu Adaptor (Set/Actual)',    'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'suhu_podv',     'label'=>'Suhu PoDV (Set/Actual)',       'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'suhu_die_plate','label'=>'Suhu Die Plate (Set/Actual)',  'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'suhu_air_water_tank','label'=>'Suhu Air Water Tank (Set/Actual)','machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'jenis_die_plate',    'label'=>'Jenis Die Plate',         'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'ampere_cutter',      'label'=>'Ampere / Torsi Cutter',  'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'rpm_cutter',         'label'=>'RPM Cutter',             'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'flow_air_uwp',       'label'=>'Flow Air UWP',           'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'jenis_cutter_hub',   'label'=>'Jenis Cutter Hub',       'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'setting_maju_cutter','label'=>'Setting Maju Cutter',    'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'panjang_cutter',     'label'=>'% Panjang Cutter',       'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'mesh_screen',        'label'=>'Mesh Screen',            'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'suhu_ac_tower_in',   'label'=>'Suhu Air Cooling Tower In', 'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'suhu_ac_tower_out',  'label'=>'Suhu Air Cooling Tower Out','machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'suhu_air_proses_in', 'label'=>'Suhu Air Proses In',     'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'suhu_air_proses_out','label'=>'Suhu Air Proses Out',    'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                    ['id'=>'die_hole_terbuka',   'label'=>'Die Hole Terbuka',       'machines'=>['E01','E03','E05','E06'], 'group'=>'UWP - Pelletizer'],
                ];

                // Filter and group
                $machineFields = array_filter($allFields, function($f) use ($machine) {
                    return in_array($machine, $f['machines']);
                });
                
                $groupedFields = [];
                foreach ($machineFields as $f) {
                    $groupedFields[$f['group']][] = $f;
                }
                @endphp

                @foreach($groupedFields as $groupName => $fields)
                <div class="mb-4">
                    <h4 class="text-md font-bold text-slate-800 mb-3 border-b border-slate-200 pb-1">{{ $groupName }}</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach($fields as $field)
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1" title="{{ $field['label'] }}">{{ Str::limit($field['label'], 25) }}</label>
                            <input id="m-{{ $field['id'] }}" type="text" class="monitoring-input block w-full rounded-lg border border-slate-300 bg-white text-slate-900 py-2 px-3 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none transition-colors" data-key="{{ $field['id'] }}">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1">Catatan (Opsional)</label>
                    <textarea id="m-notes" rows="2" class="block w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 py-3 px-4 focus:ring-amber-500 placeholder-slate-500" placeholder="Kondisi mesin, kendala, dll..."></textarea>
                </div>
                <p id="modal-msg" class="text-sm text-red-400 hidden"></p>
            </div>
            <div class="p-6 border-t border-slate-200 flex gap-3">
                <button onclick="saveMonitoring()" class="flex-1 py-3 bg-amber-500 hover:bg-amber-400 text-white font-bold rounded-xl transition-colors">Simpan & Reset Timer</button>
                <button onclick="closeMonitoringModal()" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold rounded-xl border border-slate-300 transition-colors">Batal</button>
            </div>
        </div>
    </div>
</div>

<script>
const SPK_ID = {{ $spk?->id ?? 'null' }};
let extTotalSeconds = 3600, extTimerInterval = null, sessionCount = {{ $extrudingLogs->count() }};

function startExtruding() {
    if (extTimerInterval) return;
    const btn = document.getElementById('btn-ext-start');
    btn.textContent = 'Mesin Berjalan...';
    btn.disabled = true;
    btn.classList.add('opacity-50','cursor-not-allowed');
    extTimerInterval = setInterval(() => {
        if (extTotalSeconds <= 0) {
            clearInterval(extTimerInterval); extTimerInterval = null;
            btn.textContent = 'Lanjutkan (Sesi Baru)'; btn.disabled = false;
            btn.classList.remove('opacity-50','cursor-not-allowed');
            btn.onclick = () => { extTotalSeconds = 3600; startExtruding(); };
            openMonitoringModal();
        } else {
            extTotalSeconds--;
            const elapsed = 3600 - extTotalSeconds;
            const pct = (elapsed/3600)*100;
            document.getElementById('ext-progress').style.width = pct + '%';
            const h = String(Math.floor(extTotalSeconds/3600)).padStart(2,'0');
            const m = String(Math.floor((extTotalSeconds%3600)/60)).padStart(2,'0');
            const s = String(extTotalSeconds%60).padStart(2,'0');
            document.getElementById('ext-hours').textContent = h;
            document.getElementById('ext-minutes').textContent = m;
            document.getElementById('ext-seconds').textContent = s;
        }
    }, 1000);
}

function openMonitoringModal() { document.getElementById('monitoring-modal').classList.remove('hidden'); }
function closeMonitoringModal() { document.getElementById('monitoring-modal').classList.add('hidden'); }

async function saveMonitoring() {
    const inputs = document.querySelectorAll('.monitoring-input');
    const monitoringData = {};
    let hasEmpty = false;
    
    inputs.forEach(input => {
        monitoringData[input.dataset.key] = input.value;
        if (!input.value) hasEmpty = true;
    });

    const notes = document.getElementById('m-notes').value;
    
    // Optional: Warn if empty, but for now we'll allow it if they just want to save partially
    // if (hasEmpty) { document.getElementById('modal-msg').textContent = '⚠️ Harap isi semua parameter!'; document.getElementById('modal-msg').classList.remove('hidden'); return; }

    const res = await fetch('/api/extruding/save', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        // Send a dummy temperature/pressure for backward compatibility, and the real data in monitoring_data
        body: JSON.stringify({ spk_id: SPK_ID, temperature: 0, pressure: 0, notes, monitoring_data: monitoringData }),
    });
    
    const data = await res.json();
    if (data.success) {
        sessionCount++;
        const now = new Date(); const hh = String(now.getHours()).padStart(2,'0'); const mm = String(now.getMinutes()).padStart(2,'0');
        document.getElementById('ext-empty-msg')?.remove();
        
        let previewHtml = '';
        let i = 0;
        for (const [key, val] of Object.entries(monitoringData)) {
            if (i >= 2) break; // just show first 2 params as preview
            if (val) previewHtml += `<div><p class="text-slate-500 capitalize">${key.replace(/_/g, ' ')}</p><p class="text-slate-900 font-bold">${val}</p></div>`;
            i++;
        }

        document.getElementById('ext-log-history').insertAdjacentHTML('afterbegin', `
            <div class="bg-green-500/10 border border-green-500/30 p-3 rounded-lg">
                <div class="flex justify-between items-center mb-1">
                    <p class="text-xs font-bold text-green-400">Sesi #${sessionCount} ✓</p>
                    <p class="text-xs text-slate-500">${hh}:${mm}</p>
                </div>
                <div class="grid grid-cols-2 gap-1 text-[10px] mt-2">
                    ${previewHtml}
                </div>
                ${notes ? `<p class="text-xs text-slate-500 mt-1 italic">${notes}</p>` : ''}
            </div>`);
            
        document.getElementById('log-badge').textContent = sessionCount + ' sesi';
        document.getElementById('session-badge').textContent = 'Sesi #' + (sessionCount+1);
        document.getElementById('modal-session').textContent = sessionCount+1;
        
        // Reset form
        inputs.forEach(input => input.value = '');
        document.getElementById('m-notes').value = '';
        document.getElementById('modal-msg').classList.add('hidden');
        
        closeMonitoringModal();
        extTotalSeconds = 3600;
        document.getElementById('ext-hours').textContent = '01';
        document.getElementById('ext-minutes').textContent = '00';
        document.getElementById('ext-seconds').textContent = '00';
        document.getElementById('ext-progress').style.width = '0%';
    }
}
</script>
@endsection
