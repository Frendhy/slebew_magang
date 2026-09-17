<!-- Live Report Full Screen Modal -->
<div id="liveReportModal" x-data="liveReport()" x-init="initLr()" class="fixed inset-0 z-[100] hidden flex-col items-center justify-center bg-slate-900/80 backdrop-blur-sm transition-opacity">
    <div class="w-[98vw] h-[98vh] bg-slate-50 rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">
        
        <!-- Header -->
        <div class="bg-[#1e293b] text-white px-6 py-4 flex justify-between items-center shrink-0">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="px-2 py-0.5 bg-slate-700 text-slate-300 text-[10px] font-bold tracking-widest rounded border border-slate-600">LIVE REPORT</span>
                    <h2 class="text-xl font-bold tracking-wide">{{ $spk->spk_number }}</h2>
                </div>
                <p class="text-xs text-slate-400">{{ $spk->product_name }} - {{ $spk->customer }}</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-px h-8 bg-slate-700 mx-2"></div>
                <button onclick="document.getElementById('liveReportModal').classList.add('hidden'); document.body.style.overflow = '';" class="p-2 text-slate-400 hover:text-white hover:bg-slate-700 rounded-full transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Task Banner -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-2.5 flex justify-between items-center text-xs font-semibold shrink-0">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 bg-white/20 rounded font-bold tracking-widest text-[10px]">STACKED TASK</span>
                <span>SPK ini memiliki jadwal cleaning pasca-produksi: 🧹 Cleaning {{ $spk->machine }} (Ter-stack Pasca {{ $spk->spk_number }})</span>
            </div>
            <span class="text-white/80 font-medium">Pembersihan mesin & feeder terintegrasi</span>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white border-b border-slate-200 px-2 flex items-center justify-between shrink-0">
            <div class="flex items-center overflow-x-auto no-scrollbar">
                <template x-for="tab in tabs" :key="tab.id">
                    <button @click="lrTab = tab.id" 
                            :class="lrTab === tab.id ? 'border-brand-500 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                            class="px-6 py-4 border-b-2 font-bold text-sm transition-colors whitespace-nowrap flex items-center gap-2">
                        <span x-show="tab.done" class="w-4 h-4 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-[10px]">✓</span>
                        <span x-text="tab.name"></span>
                        <span x-show="tab.done" class="text-[10px] text-green-500 uppercase tracking-widest ml-1">Selesai</span>
                    </button>
                </template>
            </div>
            <div class="px-6 flex items-center gap-4">
                @php 
                    $total = $chartData['totalBatches'];
                    $completedWeighing = $chartData['weighing']['completed'];
                    $overallPct = $total > 0 ? min(100, round(($completedWeighing / $total) * 100)) : 0;
                @endphp
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Overall</span>
                <div class="w-48 h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full" style="width: {{ $overallPct }}%;"></div>
                </div>
                <span class="text-xs font-bold text-slate-700">{{ $overallPct }}% <span class="text-slate-400 font-normal">{{ $completedWeighing }}/{{ $total }} Batch</span></span>
            </div>
        </div>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-6 relative bg-slate-50/50">
            
            <!-- CLEANING TAB -->
            <div x-show="lrTab === 'cleaning'" style="display: none;" class="space-y-6">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-green-50 text-green-500 rounded-xl flex items-center justify-center border border-green-100">✓</div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">FORM CLEANING MESIN - C.PRO.001-24</p>
                            <h3 class="text-lg font-bold text-slate-900">Pembersihan Mesin Pra-Produksi</h3>
                            <p class="text-xs text-slate-500">SPK Induk: {{ $spk->spk_number }}</p>
                        </div>
                        <div class="ml-auto flex items-center gap-2 px-4 py-1.5 bg-green-50 border border-green-200 text-green-700 rounded-full text-xs font-bold">
                            ✓ Cleaning Selesai - Diverifikasi QC
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-3 border-b border-slate-200 pb-2">Informasi Pelaksanaan <span class="text-slate-500 font-normal">(Diisi oleh Operator Cleaning)</span></p>
                        <div class="grid grid-cols-3 gap-6 bg-slate-50 p-5 rounded-xl border border-slate-100">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nama Operator</p>
                                <p class="text-sm font-semibold text-slate-800 bg-white px-3 py-2 border border-slate-200 rounded-md">Budi Santoso</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Shift / Group</p>
                                <p class="text-sm font-semibold text-slate-800 bg-white px-3 py-2 border border-slate-200 rounded-md">Shift 1 - Group A</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Mesin Yang Di-Cleaning</p>
                                <p class="text-sm font-semibold text-slate-800 bg-white px-3 py-2 border border-slate-200 rounded-md">Mixer A-01, Extruder E-03</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Tanggal</p>
                                <p class="text-sm font-semibold text-slate-800 bg-white px-3 py-2 border border-slate-200 rounded-md">2026-09-08</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Jam Mulai</p>
                                <p class="text-sm font-semibold text-slate-800 bg-white px-3 py-2 border border-slate-200 rounded-md">06:00</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Jam Selesai</p>
                                <p class="text-sm font-semibold text-slate-800 bg-white px-3 py-2 border border-slate-200 rounded-md">09:30</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3 border-b border-slate-200 pb-2">Material Pendukung Yang Digunakan</p>
                            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                                <table class="w-full text-sm">
                                    <tbody>
                                        <tr class="border-b border-slate-100"><td class="py-2 px-4 text-slate-600">Kain Majun</td><td class="py-2 px-4 text-right font-medium">3 Lembar</td></tr>
                                        <tr class="border-b border-slate-100"><td class="py-2 px-4 text-slate-600">Thinner</td><td class="py-2 px-4 text-right font-medium">1 Liter</td></tr>
                                        <tr class="border-b border-slate-100"><td class="py-2 px-4 text-slate-600">Plastik</td><td class="py-2 px-4 text-right font-medium">2 Pcs</td></tr>
                                        <tr class="border-b border-slate-100"><td class="py-2 px-4 text-slate-600">Kabel Ties</td><td class="py-2 px-4 text-right font-medium">10 Pcs</td></tr>
                                        <tr class="border-b border-slate-100"><td class="py-2 px-4 text-slate-600">Grade B PP</td><td class="py-2 px-4 text-right font-medium">25 Kg</td></tr>
                                        <tr class="border-b border-slate-100"><td class="py-2 px-4 text-slate-600">Grade B PE</td><td class="py-2 px-4 text-right font-medium">15 Kg</td></tr>
                                        <tr class="border-b border-slate-100"><td class="py-2 px-4 text-slate-600">Resin PP</td><td class="py-2 px-4 text-right font-medium">50 Kg</td></tr>
                                        <tr class="border-b border-slate-100"><td class="py-2 px-4 text-slate-600">Resin PE</td><td class="py-2 px-4 text-right font-medium">25 Kg</td></tr>
                                        <tr class="border-b border-slate-100"><td class="py-2 px-4 text-slate-600">Stearic Acid</td><td class="py-2 px-4 text-right font-medium">2 Kg</td></tr>
                                        <tr><td class="py-2 px-4 text-slate-600">Parawax</td><td class="py-2 px-4 text-right font-medium">1 Kg</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3 border-b border-slate-200 pb-2">Material Hasil Cleaning</p>
                            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                                <table class="w-full text-sm">
                                    <tbody>
                                        <tr class="border-b border-slate-100"><td class="py-2 px-4 text-slate-600">Titan</td><td class="py-2 px-4 text-right font-medium">5 Kg</td></tr>
                                        <tr class="border-b border-slate-100"><td class="py-2 px-4 text-slate-600">Resin</td><td class="py-2 px-4 text-right font-medium">20 Kg</td></tr>
                                        <tr class="border-b border-slate-100"><td class="py-2 px-4 text-slate-600">Grade B</td><td class="py-2 px-4 text-right font-medium">45 Kg</td></tr>
                                        <tr class="border-b border-slate-100"><td class="py-2 px-4 text-slate-600">Gumpalan</td><td class="py-2 px-4 text-right font-medium">12 Kg</td></tr>
                                        <tr class="border-b border-slate-100"><td class="py-2 px-4 text-slate-600">Sapuan (debu, granule)</td><td class="py-2 px-4 text-right font-medium">3 Kg</td></tr>
                                        <tr><td class="py-2 px-4 text-slate-600">Limbah B3 (majun, dll)</td><td class="py-2 px-4 text-right font-medium">2.5 Kg</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-4">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Riwayat Pengecekan QC</p>
                            <span class="px-2 py-1 bg-green-50 text-green-600 rounded text-[10px] font-bold flex items-center gap-1 border border-green-200">
                                <span class="w-4 h-4 bg-green-200 rounded-full flex items-center justify-center text-green-700">QC</span> Hendra (QC)
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mb-4">Pemantauan hasil pengecekan kebersihan mesin (Read-Only)</p>
                        
                        <div class="space-y-4">
                            <!-- Check 1 -->
                            <div class="border border-red-200 rounded-xl overflow-hidden bg-red-50/30">
                                <div class="flex justify-between items-center px-4 py-3 border-b border-red-100 bg-red-50">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-red-700">CHECK 1</span>
                                        <span class="px-2 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded">Tidak Lolos</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400">Waktu Cek: 08:30</span>
                                </div>
                                <div class="p-4">
                                    <p class="text-[10px] font-bold text-red-600 uppercase tracking-widest mb-1">Catatan Penolakan QC</p>
                                    <p class="text-sm text-slate-700 mb-3">Sisa material PVC masih menempel pada ujung screw extruder. Saringan centrifugal dryer belum dibersihkan maksimal. Mohon dilakukan pembersihan ulang.</p>
                                    
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Tindak Lanjut Operator</p>
                                    <p class="text-sm text-slate-600">Akan dilakukan pembersihan ulang menggunakan cairan pelarut (Thinner) dan disikat ulang pada area yang disebutkan.</p>
                                </div>
                            </div>

                            <!-- Check 2 -->
                            <div class="border border-green-200 rounded-xl overflow-hidden bg-green-50/30">
                                <div class="flex justify-between items-center px-4 py-3 border-b border-green-100 bg-green-50">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-green-700">CHECK 2</span>
                                        <span class="px-2 py-0.5 bg-green-500 text-white text-[10px] font-bold rounded">Lolos (OK)</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400">Waktu Cek: 09:15</span>
                                </div>
                                <div class="p-4">
                                    <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mb-1">Catatan QC</p>
                                    <p class="text-sm text-slate-700 mb-3">Semua mesin sudah bersih sesuai standar kebersihan mesin (C.PRO.001-24). Area screw dan saringan dipastikan bebas kontaminasi. Siap digunakan untuk SPK selanjutnya.</p>
                                    
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Tindak Lanjut Operator</p>
                                    <p class="text-sm text-slate-600">Pembersihan selesai dan siap diserahterimakan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 border-b border-slate-200 pb-2">Tanda Tangan & Approval</p>
                        <div class="grid grid-cols-3 gap-6">
                            <div class="border border-slate-200 rounded-xl p-4 text-center bg-slate-50">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-6">Dibuat Oleh</p>
                                <p class="text-sm font-bold text-slate-800">Budi Santoso</p>
                                <div class="w-full h-px bg-slate-200 my-2"></div>
                                <p class="text-xs text-green-600 font-medium flex justify-center items-center gap-1">Operator <span class="text-green-500">✓</span></p>
                            </div>
                            <div class="border border-slate-200 rounded-xl p-4 text-center bg-slate-50">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-6">Diperiksa Oleh</p>
                                <p class="text-sm font-bold text-slate-800">Agus Setiawan</p>
                                <div class="w-full h-px bg-slate-200 my-2"></div>
                                <p class="text-xs text-green-600 font-medium flex justify-center items-center gap-1">Foreman <span class="text-green-500">✓</span></p>
                            </div>
                            <div class="border border-green-200 rounded-xl p-4 text-center bg-green-50">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-6">Verifikasi Oleh</p>
                                <p class="text-sm font-bold text-slate-800">Hendra (QC)</p>
                                <div class="w-full h-px bg-green-200 my-2"></div>
                                <p class="text-xs text-green-600 font-medium flex justify-center items-center gap-1">QC <span class="text-green-500">✓</span> Lolos</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- PENIMBANGAN TAB -->
            <div x-show="lrTab === 'penimbangan'" style="display: none;" class="space-y-6">
                <!-- Top Stats -->
                <div class="grid grid-cols-6 gap-4">
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm col-span-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Batch Selesai</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-brand-600">50</span>
                            <span class="text-sm font-bold text-slate-400">/ 50</span>
                        </div>
                        <div class="mt-2 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-brand-500 rounded-full" style="width: 100%"></div>
                        </div>
                        <p class="text-[10px] font-bold text-green-500 mt-2">100% Selesai</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Mesin Timbang</p>
                        <p class="text-base font-bold text-slate-800">Timbangan A-01</p>
                        <p class="text-xs text-slate-400 mt-1">Kapasitas 500 Kg</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Rata-Rata Durasi</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-slate-800">12</span>
                            <span class="text-sm font-medium text-slate-500">menit</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">per Batch</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Ketepatan Waktu</p>
                        <p class="text-2xl font-black text-green-500">On Time</p>
                        <p class="text-xs text-slate-400 mt-1">Total Delay: 0 menit</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Shift Kerja</p>
                        <p class="text-sm font-bold text-slate-800">Shift 1 · Team RED</p>
                        <p class="text-xs text-slate-400 mt-1">Budi, Mia, Ayu</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Tanggal Penimbangan</p>
                        <p class="text-sm font-bold text-slate-800">2026-09-15</p>
                        <p class="text-xs text-slate-400 mt-1">Jam Mulai: 06:00</p>
                    </div>
                </div>

                <!-- Bar Chart: Durasi per Batch -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Grafik Durasi Penimbangan</p>
                            <p class="text-sm font-bold text-slate-800">Durasi per Batch (Menit) — Proses Timbang</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-full">Semua Shift</span>
                            <span class="text-[10px] font-bold text-green-700 bg-green-50 border border-green-200 px-3 py-1 rounded-full">Target ≤ 15 menit</span>
                        </div>
                    </div>
                    <!-- Dummy Bar Chart -->
                    <div class="relative h-40 flex items-end gap-6 px-4 border-b border-slate-200 pb-0">
                        <!-- Y axis label -->
                        <div class="absolute left-0 top-0 bottom-0 flex flex-col justify-between text-[8px] text-slate-400 pb-2">
                            <span>20</span><span>16</span><span>12</span><span>8</span><span>4</span><span>0</span>
                        </div>
                        <!-- Target dashed line at 15 mnt = 75% height -->
                        <div class="absolute left-8 right-0 border-t-2 border-dashed border-red-300" style="bottom: 75%;"></div>
                        <!-- Bars -->
                        <div class="flex-1 flex items-end justify-center gap-6 pl-4">
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-12 bg-green-400 rounded-t" style="height: 55px;" title="B46: 11 mnt"></div>
                                <span class="text-[9px] text-slate-500">B46</span>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-12 bg-green-400 rounded-t" style="height: 60px;" title="B47: 12 mnt"></div>
                                <span class="text-[9px] text-slate-500">B47</span>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-12 bg-amber-400 rounded-t" style="height: 70px;" title="B48: 14 mnt"></div>
                                <span class="text-[9px] text-slate-500">B48</span>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-12 bg-green-400 rounded-t" style="height: 60px;" title="B49: 12 mnt"></div>
                                <span class="text-[9px] text-slate-500">B49</span>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-12 bg-amber-400 rounded-t" style="height: 75px;" title="B50: 15 mnt"></div>
                                <span class="text-[9px] text-slate-500">B50</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2 italic">Arahkan kursor ke batang grafik untuk melihat operator, shift, dan jam mulai penimbangan.</p>
                </div>

                <!-- Detail Batch Table -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="flex justify-between items-center px-5 py-4 border-b border-slate-100">
                        <p class="text-sm font-bold text-slate-800">Rincian Batch Terakhir</p>
                        <span class="text-[10px] text-slate-400">Menampilkan 5 batch terakhir</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-500 uppercase tracking-widest font-bold">
                                    <th class="py-3 px-4 text-left">Batch</th>
                                    <th class="py-3 px-4 text-left">Operator</th>
                                    <th class="py-3 px-4 text-left">Shift</th>
                                    <th class="py-3 px-4">Mulai</th>
                                    <th class="py-3 px-4">Selesai</th>
                                    <th class="py-3 px-4">Durasi</th>
                                    <th class="py-3 px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700">Batch #50</td>
                                    <td class="py-3 px-4 text-slate-600">Mia</td>
                                    <td class="py-3 px-4 text-slate-500">Shift 1</td>
                                    <td class="py-3 px-4 text-center">08:00</td>
                                    <td class="py-3 px-4 text-center">08:13</td>
                                    <td class="py-3 px-4 text-center font-semibold">13 mnt</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-1 bg-green-50 text-green-600 border border-green-200 text-[10px] font-bold rounded">On Time</span></td>
                                </tr>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700">Batch #49</td>
                                    <td class="py-3 px-4 text-slate-600">Ayu</td>
                                    <td class="py-3 px-4 text-slate-500">Shift 1</td>
                                    <td class="py-3 px-4 text-center">07:47</td>
                                    <td class="py-3 px-4 text-center">07:59</td>
                                    <td class="py-3 px-4 text-center font-semibold">12 mnt</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-1 bg-green-50 text-green-600 border border-green-200 text-[10px] font-bold rounded">On Time</span></td>
                                </tr>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700">Batch #48</td>
                                    <td class="py-3 px-4 text-slate-600">Mia</td>
                                    <td class="py-3 px-4 text-slate-500">Shift 1</td>
                                    <td class="py-3 px-4 text-center">07:31</td>
                                    <td class="py-3 px-4 text-center">07:45</td>
                                    <td class="py-3 px-4 text-center font-bold text-amber-600">14 mnt</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-1 bg-amber-50 text-amber-600 border border-amber-200 text-[10px] font-bold rounded">Lambat</span></td>
                                </tr>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700">Batch #47</td>
                                    <td class="py-3 px-4 text-slate-600">Fitri</td>
                                    <td class="py-3 px-4 text-slate-500">Shift 2</td>
                                    <td class="py-3 px-4 text-center">07:19</td>
                                    <td class="py-3 px-4 text-center">07:30</td>
                                    <td class="py-3 px-4 text-center font-semibold">11 mnt</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-1 bg-green-50 text-green-600 border border-green-200 text-[10px] font-bold rounded">On Time</span></td>
                                </tr>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700">Batch #46</td>
                                    <td class="py-3 px-4 text-slate-600">Hana</td>
                                    <td class="py-3 px-4 text-slate-500">Shift 2</td>
                                    <td class="py-3 px-4 text-center">07:07</td>
                                    <td class="py-3 px-4 text-center">07:18</td>
                                    <td class="py-3 px-4 text-center font-semibold">11 mnt</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-1 bg-green-50 text-green-600 border border-green-200 text-[10px] font-bold rounded">On Time</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- MIXING TAB -->
            <div x-show="lrTab === 'mixing'" style="display: none;" class="space-y-6">
                <!-- Top Stats -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Batch Selesai</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-brand-600">50</span>
                            <span class="text-sm font-bold text-slate-400">/ 50</span>
                        </div>
                        <div class="mt-2 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-brand-500 rounded-full" style="width: 100%"></div>
                        </div>
                        <p class="text-[10px] font-bold text-green-500 mt-2">100% Selesai</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Mesin Mixing</p>
                        <p class="text-base font-bold text-slate-800">Mixer A-01</p>
                        <p class="text-xs text-slate-400 mt-1">High-Speed Mixer</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Rata-Rata Durasi</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-slate-800">20</span>
                            <span class="text-sm font-medium text-slate-500">menit</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">per Batch (HP+ML)</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Ketepatan Waktu</p>
                        <p class="text-2xl font-black text-green-500">On Time</p>
                        <p class="text-xs text-slate-400 mt-1">Total Delay: 5 menit</p>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Suhu Mixing Line Chart -->
                    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Parameter Suhu Mixing</p>
                        <p class="text-sm font-bold text-slate-800 mb-4">Grafik Suhu Mixer (&deg;C) per Batch</p>
                        <div class="relative h-36 bg-slate-50 rounded-lg border border-slate-100 overflow-hidden">
                            <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                                <line x1="0" y1="20" x2="100" y2="20" stroke="#e2e8f0" stroke-width="0.5"/>
                                <line x1="0" y1="40" x2="100" y2="40" stroke="#e2e8f0" stroke-width="0.5"/>
                                <line x1="0" y1="60" x2="100" y2="60" stroke="#e2e8f0" stroke-width="0.5"/>
                                <line x1="0" y1="80" x2="100" y2="80" stroke="#e2e8f0" stroke-width="0.5"/>
                                <!-- Hot Mix line (high ~120°C) -->
                                <path d="M0,10 L20,11 L40,10 L60,12 L80,11 L100,11" fill="none" stroke="#ef4444" stroke-width="1.5"/>
                                <path d="M0,10 L20,11 L40,10 L60,12 L80,11 L100,11 L100,100 L0,100 Z" fill="#ef4444" fill-opacity="0.05"/>
                                <!-- Cold Mix line (low ~50°C) -->
                                <path d="M0,55 L20,56 L40,54 L60,57 L80,55 L100,56" fill="none" stroke="#3b82f6" stroke-width="1.5"/>
                                <path d="M0,55 L20,56 L40,54 L60,57 L80,55 L100,56 L100,100 L0,100 Z" fill="#3b82f6" fill-opacity="0.05"/>
                            </svg>
                            <div class="absolute bottom-1 left-2 text-[7px] text-slate-400">B1</div>
                            <div class="absolute bottom-1 right-2 text-[7px] text-slate-400">B60</div>
                        </div>
                        <div class="flex gap-4 mt-2 text-[9px]">
                            <span class="flex items-center gap-1"><span class="w-3 h-0.5 bg-red-400 inline-block"></span> Hot Mix (°C)</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-0.5 bg-blue-400 inline-block"></span> Cold Mix (°C)</span>
                        </div>
                    </div>

                    <!-- Durasi Mixing Bar Chart -->
                    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Durasi Mixing</p>
                        <p class="text-sm font-bold text-slate-800 mb-4">Durasi Mixing per Batch (Menit)</p>
                        <div class="relative h-36 flex items-end gap-4 border-b border-slate-200 px-4">
                            <div class="absolute left-0 top-0 bottom-0 flex flex-col justify-between text-[8px] text-slate-400 pb-0">
                                <span>30</span><span>20</span><span>10</span><span>0</span>
                            </div>
                            <div class="flex-1 flex items-end justify-center gap-3 pl-4">
                                <div class="flex flex-col items-center gap-1"><div class="w-10 bg-violet-400 rounded-t" style="height:64px"></div><span class="text-[8px] text-slate-500">B46</span></div>
                                <div class="flex flex-col items-center gap-1"><div class="w-10 bg-violet-400 rounded-t" style="height:64px"></div><span class="text-[8px] text-slate-500">B47</span></div>
                                <div class="flex flex-col items-center gap-1"><div class="w-10 bg-violet-400 rounded-t" style="height:64px"></div><span class="text-[8px] text-slate-500">B48</span></div>
                                <div class="flex flex-col items-center gap-1"><div class="w-10 bg-violet-400 rounded-t" style="height:64px"></div><span class="text-[8px] text-slate-500">B49</span></div>
                                <div class="flex flex-col items-center gap-1"><div class="w-10 bg-violet-400 rounded-t" style="height:64px"></div><span class="text-[8px] text-slate-500">B50</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rincian Batch Table -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="flex justify-between items-center px-5 py-4 border-b border-slate-100">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Rincian Batch Mixing</p>
                            <p class="text-xs text-slate-400">Detail tiap batch: operator, shift, jam mulai, jam selesai, dan status</p>
                        </div>
                        <div class="flex gap-2 text-[10px] font-bold">
                            <span class="px-2 py-1 bg-green-50 text-green-600 border border-green-200 rounded">Lebih Cepat</span>
                            <span class="px-2 py-1 bg-blue-50 text-blue-600 border border-blue-200 rounded">Tepat Waktu</span>
                            <span class="px-2 py-1 bg-red-50 text-red-600 border border-red-200 rounded">Lambat</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-500 uppercase tracking-widest font-bold">
                                    <th class="py-3 px-4 text-left">Batch</th>
                                    <th class="py-3 px-4 text-left">Operator</th>
                                    <th class="py-3 px-4 text-left">Shift</th>
                                    <th class="py-3 px-4">Jam Mulai</th>
                                    <th class="py-3 px-4">Jam Selesai</th>
                                    <th class="py-3 px-4">Durasi</th>
                                    <th class="py-3 px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700">Batch #50</td>
                                    <td class="py-3 px-4 text-slate-600">Budi, Anggun</td>
                                    <td class="py-3 px-4 text-slate-500">Shift 1</td>
                                    <td class="py-3 px-4 text-center">08:15</td>
                                    <td class="py-3 px-4 text-center">08:33</td>
                                    <td class="py-3 px-4 text-center font-semibold text-green-600">18 mnt</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-1 bg-green-50 text-green-600 border border-green-200 text-[10px] font-bold rounded">Lebih Cepat</span></td>
                                </tr>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700">Batch #49</td>
                                    <td class="py-3 px-4 text-slate-600">Anggun</td>
                                    <td class="py-3 px-4 text-slate-500">Shift 1</td>
                                    <td class="py-3 px-4 text-center">07:55</td>
                                    <td class="py-3 px-4 text-center">08:14</td>
                                    <td class="py-3 px-4 text-center font-semibold">19 mnt</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-1 bg-blue-50 text-blue-600 border border-blue-200 text-[10px] font-bold rounded">Tepat Waktu</span></td>
                                </tr>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700">Batch #48</td>
                                    <td class="py-3 px-4 text-slate-600">Budi</td>
                                    <td class="py-3 px-4 text-slate-500">Shift 1</td>
                                    <td class="py-3 px-4 text-center">07:30</td>
                                    <td class="py-3 px-4 text-center">07:54</td>
                                    <td class="py-3 px-4 text-center font-bold text-red-600">24 mnt</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-1 bg-red-50 text-red-600 border border-red-200 text-[10px] font-bold rounded">Lambat</span></td>
                                </tr>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700">Batch #47</td>
                                    <td class="py-3 px-4 text-slate-600">Bagas, Rudi</td>
                                    <td class="py-3 px-4 text-slate-500">Shift 2</td>
                                    <td class="py-3 px-4 text-center">07:07</td>
                                    <td class="py-3 px-4 text-center">07:25</td>
                                    <td class="py-3 px-4 text-center font-semibold text-green-600">18 mnt</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-1 bg-green-50 text-green-600 border border-green-200 text-[10px] font-bold rounded">Lebih Cepat</span></td>
                                </tr>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700">Batch #46</td>
                                    <td class="py-3 px-4 text-slate-600">Rudi</td>
                                    <td class="py-3 px-4 text-slate-500">Shift 2</td>
                                    <td class="py-3 px-4 text-center">06:44</td>
                                    <td class="py-3 px-4 text-center">07:05</td>
                                    <td class="py-3 px-4 text-center font-semibold">21 mnt</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2 py-1 bg-blue-50 text-blue-600 border border-blue-200 text-[10px] font-bold rounded">Tepat Waktu</span></td>
                                </tr>
                                <tr class="bg-slate-50">
                                    <td colspan="5" class="py-3 px-4 font-bold text-slate-500 text-[10px] uppercase tracking-widest">Rata-Rata Durasi</td>
                                    <td class="py-3 px-4 text-center font-black text-slate-800">20 MNT</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- EXTRUDER TAB -->
            <div x-show="lrTab === 'extruder'" style="display: none;" class="space-y-6">
                
                <!-- TOP CARDS -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex flex-col justify-between">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Batch Selesai</p>
                        <div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-3xl font-black text-brand-600">28</span>
                                <span class="text-sm font-bold text-slate-400">/ 50</span>
                            </div>
                            <div class="mt-2 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-brand-500 rounded-full" style="width: 56%"></div>
                            </div>
                            <p class="text-[10px] font-bold text-brand-600 mt-2">56% - Sedang Berjalan</p>
                        </div>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Mesin Extruder</p>
                        <p class="text-lg font-bold text-slate-800">Extruder E-03</p>
                        <p class="text-xs text-slate-400 mt-1">Line 1 - Screw Ø 65mm</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Output Aktual</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-slate-800">495</span>
                            <span class="text-sm font-medium text-slate-500">Kg/Jam</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Target: 500 Kg/Jam</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Ketepatan Waktu</p>
                        <p class="text-2xl font-black text-green-500">On Time</p>
                        <p class="text-xs text-slate-400 mt-1">Delay: 0.0 jam</p>
                    </div>
                </div>

                <!-- LAPORAN & RINGKASAN -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mt-6">
                    <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Laporan Parameter Extruder - Per Jam</p>
                        </div>
                    </div>

                    <div class="bg-slate-900 px-4 py-2">
                        <p class="text-xs font-bold text-white uppercase tracking-widest">Ringkasan Parameter Aktual - Shift Terakhir</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50">
                                    <th class="py-2 px-4 font-bold text-slate-500 text-[10px] uppercase tracking-wider">Parameter</th>
                                    <th class="py-2 px-4 font-bold text-slate-500 text-[10px] uppercase tracking-wider">Aktual</th>
                                    <th class="py-2 px-4 font-bold text-slate-500 text-[10px] uppercase tracking-wider">Target</th>
                                    <th class="py-2 px-4 font-bold text-slate-500 text-[10px] uppercase tracking-wider text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-slate-100">
                                    <td class="py-2 px-4 font-medium text-slate-700">Suhu Zone 1</td>
                                    <td class="py-2 px-4 font-bold text-slate-900">160 &deg;C</td>
                                    <td class="py-2 px-4 text-slate-500">155-165 &deg;C</td>
                                    <td class="py-2 px-4 text-right"><span class="text-green-500 font-bold text-[10px]">Normal</span></td>
                                </tr>
                                <tr class="border-b border-slate-100">
                                    <td class="py-2 px-4 font-medium text-slate-700">Suhu Zone 2</td>
                                    <td class="py-2 px-4 font-bold text-slate-900">170 &deg;C</td>
                                    <td class="py-2 px-4 text-slate-500">165-175 &deg;C</td>
                                    <td class="py-2 px-4 text-right"><span class="text-green-500 font-bold text-[10px]">Normal</span></td>
                                </tr>
                                <tr class="border-b border-slate-100">
                                    <td class="py-2 px-4 font-medium text-slate-700">Suhu Zone 3</td>
                                    <td class="py-2 px-4 font-bold text-slate-900">175 &deg;C</td>
                                    <td class="py-2 px-4 text-slate-500">170-180 &deg;C</td>
                                    <td class="py-2 px-4 text-right"><span class="text-green-500 font-bold text-[10px]">Normal</span></td>
                                </tr>
                                <tr class="border-b border-slate-100">
                                    <td class="py-2 px-4 font-medium text-slate-700">Suhu Zone 4 (Die)</td>
                                    <td class="py-2 px-4 font-bold text-slate-900">178 &deg;C</td>
                                    <td class="py-2 px-4 text-slate-500">175-182 &deg;C</td>
                                    <td class="py-2 px-4 text-right"><span class="text-green-500 font-bold text-[10px]">Normal</span></td>
                                </tr>
                                <tr class="border-b border-slate-100">
                                    <td class="py-2 px-4 font-medium text-slate-700">RPM Screw</td>
                                    <td class="py-2 px-4 font-bold text-slate-900">35 RPM</td>
                                    <td class="py-2 px-4 text-slate-500">30-40 RPM</td>
                                    <td class="py-2 px-4 text-right"><span class="text-green-500 font-bold text-[10px]">Normal</span></td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-4 font-medium text-slate-700">Ampere</td>
                                    <td class="py-2 px-4 font-bold text-amber-600">42 A</td>
                                    <td class="py-2 px-4 text-slate-500">&le;40 A</td>
                                    <td class="py-2 px-4 text-right"><span class="text-amber-500 font-bold text-[10px] bg-amber-50 px-2 py-0.5 rounded">Perhatian</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- GRAPHS (DUMMY PLACEHOLDERS) -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Zone 1 -->
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm h-48 flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Suhu Zone 1</p>
                                <p class="text-[10px] text-slate-400">Grafik per Jam (&deg;C)</p>
                            </div>
                            <span class="text-[10px] font-bold text-slate-700">Target: 155-165 &deg;C</span>
                        </div>
                        <div class="w-full h-full bg-slate-50 rounded border border-slate-100 flex items-end relative overflow-hidden">
                            <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                                <path d="M0,50 L20,48 L40,52 L60,45 L80,55 L100,50" fill="none" stroke="#8b5cf6" stroke-width="2"/>
                                <path d="M0,50 L20,48 L40,52 L60,45 L80,55 L100,50 L100,100 L0,100 Z" fill="#8b5cf6" fill-opacity="0.1"/>
                                <line x1="0" y1="30" x2="100" y2="30" stroke="#ef4444" stroke-width="1" stroke-dasharray="2,2"/>
                            </svg>
                            <div class="absolute bottom-1 left-2 text-[8px] text-slate-400">06:00</div>
                            <div class="absolute bottom-1 right-2 text-[8px] text-slate-400">12:00</div>
                        </div>
                    </div>
                    <!-- Zone 2 -->
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm h-48 flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Suhu Zone 2</p>
                                <p class="text-[10px] text-slate-400">Grafik per Jam (&deg;C)</p>
                            </div>
                            <span class="text-[10px] font-bold text-slate-700">Target: 165-175 &deg;C</span>
                        </div>
                        <div class="w-full h-full bg-slate-50 rounded border border-slate-100 flex items-end relative overflow-hidden">
                            <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                                <path d="M0,40 L20,38 L40,42 L60,35 L80,45 L100,40" fill="none" stroke="#0ea5e9" stroke-width="2"/>
                                <path d="M0,40 L20,38 L40,42 L60,35 L80,45 L100,40 L100,100 L0,100 Z" fill="#0ea5e9" fill-opacity="0.1"/>
                                <line x1="0" y1="20" x2="100" y2="20" stroke="#ef4444" stroke-width="1" stroke-dasharray="2,2"/>
                            </svg>
                            <div class="absolute bottom-1 left-2 text-[8px] text-slate-400">06:00</div>
                            <div class="absolute bottom-1 right-2 text-[8px] text-slate-400">12:00</div>
                        </div>
                    </div>
                    <!-- Zone 3 -->
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm h-48 flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Suhu Zone 3</p>
                                <p class="text-[10px] text-slate-400">Grafik per Jam (&deg;C)</p>
                            </div>
                            <span class="text-[10px] font-bold text-blue-600">Target: 170-180 &deg;C</span>
                        </div>
                        <div class="w-full h-full bg-slate-50 rounded border border-slate-100 flex items-end relative overflow-hidden">
                            <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                                <path d="M0,45 L20,40 L40,46 L60,42 L80,38 L100,41" fill="none" stroke="#10b981" stroke-width="2"/>
                                <path d="M0,45 L20,40 L40,46 L60,42 L80,38 L100,41 L100,100 L0,100 Z" fill="#10b981" fill-opacity="0.1"/>
                                <line x1="0" y1="35" x2="100" y2="35" stroke="#ef4444" stroke-width="1" stroke-dasharray="2,2"/>
                            </svg>
                            <div class="absolute bottom-1 left-2 text-[8px] text-slate-400">06:00</div>
                            <div class="absolute bottom-1 right-2 text-[8px] text-slate-400">12:00</div>
                        </div>
                    </div>
                    <!-- Zone 4 (Die) -->
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm h-48 flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Suhu Zone 4 (Die Head)</p>
                                <p class="text-[10px] text-slate-400">Grafik per Jam (&deg;C)</p>
                            </div>
                            <span class="text-[10px] font-bold text-blue-600">Target: 175-182 &deg;C</span>
                        </div>
                        <div class="w-full h-full bg-slate-50 rounded border border-slate-100 flex items-end relative overflow-hidden">
                            <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                                <path d="M0,55 L20,50 L40,58 L60,52 L80,50 L100,53" fill="none" stroke="#3b82f6" stroke-width="2"/>
                                <path d="M0,55 L20,50 L40,58 L60,52 L80,50 L100,53 L100,100 L0,100 Z" fill="#3b82f6" fill-opacity="0.1"/>
                                <line x1="0" y1="45" x2="100" y2="45" stroke="#ef4444" stroke-width="1" stroke-dasharray="2,2"/>
                            </svg>
                            <div class="absolute bottom-1 left-2 text-[8px] text-slate-400">06:00</div>
                            <div class="absolute bottom-1 right-2 text-[8px] text-slate-400">12:00</div>
                        </div>
                    </div>
                    <!-- RPM Screw -->
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm h-48 flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">RPM Screw</p>
                                <p class="text-[10px] text-slate-400">Grafik per Jam</p>
                            </div>
                            <span class="text-[10px] font-bold text-slate-700">Target: 30-40 RPM</span>
                        </div>
                        <div class="w-full h-full bg-slate-50 rounded border border-slate-100 flex items-end relative overflow-hidden">
                            <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                                <path d="M0,60 L20,58 L40,62 L60,57 L80,63 L100,60" fill="none" stroke="#a855f7" stroke-width="2"/>
                                <path d="M0,60 L20,58 L40,62 L60,57 L80,63 L100,60 L100,100 L0,100 Z" fill="#a855f7" fill-opacity="0.1"/>
                                <line x1="0" y1="50" x2="100" y2="50" stroke="#ef4444" stroke-width="1" stroke-dasharray="2,2"/>
                            </svg>
                            <div class="absolute bottom-1 left-2 text-[8px] text-slate-400">06:00</div>
                            <div class="absolute bottom-1 right-2 text-[8px] text-slate-400">12:00</div>
                        </div>
                    </div>
                    <!-- Ampere -->
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm h-48 flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Ampere</p>
                                <p class="text-[10px] text-slate-400">Grafik per Jam (A)</p>
                            </div>
                            <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">Target: &le;40 A</span>
                        </div>
                        <div class="w-full h-full bg-slate-50 rounded border border-slate-100 flex items-end relative overflow-hidden">
                            <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                                <path d="M0,35 L20,33 L40,25 L60,30 L80,22 L100,32" fill="none" stroke="#f59e0b" stroke-width="2"/>
                                <path d="M0,35 L20,33 L40,25 L60,30 L80,22 L100,32 L100,100 L0,100 Z" fill="#f59e0b" fill-opacity="0.1"/>
                                <line x1="0" y1="40" x2="100" y2="40" stroke="#ef4444" stroke-width="1" stroke-dasharray="2,2"/>
                            </svg>
                            <div class="absolute bottom-1 left-2 text-[8px] text-slate-400">06:00</div>
                            <div class="absolute bottom-1 right-2 text-[8px] text-slate-400">12:00</div>
                        </div>
                    </div>
                </div>

                <!-- DETAIL RINCIAN DATA TABLE & FORM -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm mt-6">
                    <div class="p-5 border-b border-slate-100">
                        <p class="text-sm font-bold text-slate-800">Detail Rincian Data Extruder per Jam</p>
                        <p class="text-xs text-slate-400">Pencatatan aktual Operator, Suhu No. 1 (Zone 1-4), Target Suhu, Hasil Pengecekan, dan Kesimpulan</p>
                        
                        <!-- Form Inputs -->
                        </div>

                        
                        <!-- MULTILINE CHART (Grafik Suhu Extruder per Jam (°C) - Realtime) -->
                        <div class="mt-8 mb-4">
                            <p class="text-xs font-bold text-slate-800 mb-3">Grafik Suhu Extruder per Jam (&deg;C) - Realtime</p>
                            <div class="flex items-center justify-center gap-4 mb-3 text-[10px] font-bold text-slate-500">
                                <div class="flex items-center gap-1.5"><span class="w-5 h-1 bg-slate-400 inline-block rounded"></span> Zone 1</div>
                                <div class="flex items-center gap-1.5"><span class="w-5 h-1 bg-blue-400 inline-block rounded"></span> Zone 2</div>
                                <div class="flex items-center gap-1.5"><span class="w-5 h-1 bg-green-400 inline-block rounded"></span> Zone 3</div>
                                <div class="flex items-center gap-1.5"><span class="w-5 h-1 bg-amber-400 inline-block rounded"></span> Zone 4 (Die)</div>
                            </div>
                            <div class="relative pl-8 pb-5 h-52 bg-white border border-slate-100 rounded-xl overflow-hidden">
                                <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100" style="left:2rem; width:calc(100% - 2rem); top:0; height:calc(100% - 1.25rem);">
                                    <!-- Grid horizontal lines -->
                                    <line x1="0" y1="0" x2="100" y2="0" stroke="#e2e8f0" stroke-width="0.5"/>
                                    <line x1="0" y1="20" x2="100" y2="20" stroke="#e2e8f0" stroke-width="0.5"/>
                                    <line x1="0" y1="40" x2="100" y2="40" stroke="#e2e8f0" stroke-width="0.5"/>
                                    <line x1="0" y1="60" x2="100" y2="60" stroke="#e2e8f0" stroke-width="0.5"/>
                                    <line x1="0" y1="80" x2="100" y2="80" stroke="#e2e8f0" stroke-width="0.5"/>
                                    <line x1="0" y1="100" x2="100" y2="100" stroke="#e2e8f0" stroke-width="0.5"/>
                                    <!-- Reactive paths from Alpine state -->
                                    <path :d="zone1Path" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linejoin="round"/>
                                    <path :d="zone2Path" fill="none" stroke="#60a5fa" stroke-width="1.5" stroke-linejoin="round"/>
                                    <path :d="zone3Path" fill="none" stroke="#4ade80" stroke-width="1.5" stroke-linejoin="round"/>
                                    <path :d="zone4Path" fill="none" stroke="#fbbf24" stroke-width="1.5" stroke-linejoin="round"/>
                                    <!-- Dots for Zone 3 (the topmost line) -->
                                    <template x-for="(log, i) in extruderLogs" :key="i">
                                        <circle :cx="getXPos(i)" :cy="getYPos(log, 'z3')" r="1.5" fill="#fff" stroke="#4ade80" stroke-width="1.5"/>
                                    </template>
                                </svg>
                                <!-- Y Axis Labels -->
                                <div class="absolute text-[8px] text-slate-400" style="top:0; left:2px;">200</div>
                                <div class="absolute text-[8px] text-slate-400" style="top:20%; left:2px; transform:translateY(-50%);">190</div>
                                <div class="absolute text-[8px] text-slate-400" style="top:40%; left:2px; transform:translateY(-50%);">180</div>
                                <div class="absolute text-[8px] text-slate-400" style="top:60%; left:2px; transform:translateY(-50%);">170</div>
                                <div class="absolute text-[8px] text-slate-400" style="top:80%; left:2px; transform:translateY(-50%);">160</div>
                                <div class="absolute text-[8px] text-slate-400" style="bottom:1.25rem; left:2px;">150</div>
                                <!-- X Axis Labels - dynamic -->
                                <template x-for="(log, i) in extruderLogs" :key="'xl'+i">
                                    <div class="absolute text-[8px] text-slate-400" style="bottom:0;"
                                         :style="`left: calc(2rem + ${getXPos(i)}%); transform: translateX(-50%);`"
                                         x-text="log.jam"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Reactive Log Table -->
                    <div class="overflow-x-auto border-t border-slate-100">
                        <table class="w-full text-center text-xs whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-500 uppercase tracking-widest font-bold">
                                    <th class="py-3 px-4 text-left">Jam</th>
                                    <th class="py-3 px-4 text-left">Dikerjakan Oleh</th>
                                    <th class="py-3 px-2">Zone 1</th>
                                    <th class="py-3 px-2">Zone 2</th>
                                    <th class="py-3 px-2">Zone 3</th>
                                    <th class="py-3 px-2">Zone 4 (Die)</th>
                                    <th class="py-3 px-2">Target Suhu</th>
                                    <th class="py-3 px-4">Hasil Pengecekan</th>
                                    <th class="py-3 px-4">Kesimpulan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(log, i) in extruderLogs" :key="i">
                                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                        <td class="py-2 px-4 text-left font-bold text-slate-700" x-text="log.jam"></td>
                                        <td class="py-2 px-4 text-left text-slate-600" x-text="log.operator"></td>
                                        <td class="py-2 px-2" x-text="log.z1 + '°C'"></td>
                                        <td class="py-2 px-2" x-text="log.z2 + '°C'"></td>
                                        <td class="py-2 px-2" x-text="log.z3 + '°C'"></td>
                                        <td class="py-2 px-2 font-bold text-blue-600" x-text="log.z4 + '°C'"></td>
                                        <td class="py-2 px-2 text-slate-400" x-text="log.target"></td>
                                        <td class="py-2 px-4">
                                            <span x-text="log.hasil"
                                                  :class="log.hasil && log.hasil.includes('Tidak') ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-amber-50 text-amber-600 border border-amber-200'"
                                                  class="px-2 py-0.5 rounded text-[10px] font-bold inline-block"></span>
                                        </td>
                                        <td class="py-2 px-4 text-slate-500" x-text="log.kesimpulan"></td>
                                    </tr>
                                </template>
                                <!-- Rata-rata row -->
                                <tr class="bg-blue-50/50 border-t-2 border-blue-100">
                                    <td colspan="2" class="py-3 px-4 text-left font-bold text-blue-800 text-[10px] uppercase tracking-widest">Rata-Rata Suhu &amp; Parameter</td>
                                    <td class="py-3 px-2 font-bold text-blue-800"
                                        x-text="(extruderLogs.reduce((s,l)=>s+parseFloat(l.z1||0),0)/extruderLogs.length).toFixed(1)+'°C'"></td>
                                    <td class="py-3 px-2 font-bold text-blue-800"
                                        x-text="(extruderLogs.reduce((s,l)=>s+parseFloat(l.z2||0),0)/extruderLogs.length).toFixed(1)+'°C'"></td>
                                    <td class="py-3 px-2 font-bold text-blue-800"
                                        x-text="(extruderLogs.reduce((s,l)=>s+parseFloat(l.z3||0),0)/extruderLogs.length).toFixed(1)+'°C'"></td>
                                    <td class="py-3 px-2 font-bold text-blue-800"
                                        x-text="(extruderLogs.reduce((s,l)=>s+parseFloat(l.z4||0),0)/extruderLogs.length).toFixed(1)+'°C'"></td>
                                    <td class="py-3 px-2 font-bold text-blue-800">160-178°C</td>
                                    <td class="py-3 px-4"><span class="bg-green-100 text-green-700 px-2 py-1 rounded font-bold text-[10px]">STABIL</span></td>
                                    <td class="py-3 px-4 font-bold text-blue-800 text-[10px] uppercase">Parameter Proses Dalam Batas Aman QC</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>


            <!-- BAGGING TAB -->
            <div x-show="lrTab === 'bagging'" style="display: none;" class="space-y-6">
                <!-- Top Stats -->
                <div class="grid grid-cols-5 gap-4">
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Sak Selesai</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-green-500">600</span>
                            <span class="text-sm font-bold text-slate-400">/ 600</span>
                        </div>
                        <p class="text-[10px] font-bold text-green-500 mt-1">100% Selesai</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Mesin Bagging</p>
                        <p class="text-base font-bold text-slate-800">Bagging Line 01</p>
                        <p class="text-xs text-slate-400 mt-1">Semi-Auto · 25 Kg/Sak</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Total Berat</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-slate-800">15.000</span>
                            <span class="text-sm font-medium text-slate-500">Kg</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">600 sak &times; 25 Kg</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Rata-Rata Durasi</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-slate-800">2.1</span>
                            <span class="text-sm font-medium text-slate-500">mnt/sak</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Target &le; 2.5 mnt</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Ketepatan Waktu</p>
                        <p class="text-2xl font-black text-green-500">On Time</p>
                        <p class="text-xs text-slate-400 mt-1">Delay: 0 menit</p>
                    </div>
                </div>

                <!-- QC Goods Check -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">QC Goods Check</p>
                            <p class="text-sm font-bold text-slate-800">Hasil Pemeriksaan Kualitas Produk Akhir</p>
                        </div>
                        <span class="px-3 py-1 bg-green-50 border border-green-200 text-green-600 rounded-lg text-xs font-bold flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> PASSED
                        </span>
                    </div>
                    <div class="grid grid-cols-4 gap-4">
                        <div class="bg-green-50/50 border border-green-100 rounded-lg p-3 text-center">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Warna</p>
                            <p class="font-bold text-green-700">Sesuai Std</p>
                        </div>
                        <div class="bg-green-50/50 border border-green-100 rounded-lg p-3 text-center">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Ukuran Granule</p>
                            <p class="font-bold text-green-700">3.2 mm (OK)</p>
                        </div>
                        <div class="bg-green-50/50 border border-green-100 rounded-lg p-3 text-center">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Moisture</p>
                            <p class="font-bold text-green-700">0.08% (&le; 0.1%)</p>
                        </div>
                        <div class="bg-green-50/50 border border-green-100 rounded-lg p-3 text-center">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Kontaminasi</p>
                            <p class="font-bold text-green-700">Tidak Ada</p>
                        </div>
                    </div>
                </div>

                <!-- Chart & Table Container -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <!-- Chart: Produktivitas Bagging -->
                    <div class="p-5 border-b border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Produktivitas Bagging</p>
                        <p class="text-sm font-bold text-slate-800 mb-4">Sak Selesai per Jam</p>
                        <div class="relative h-40 bg-slate-50 rounded-lg border border-slate-100 overflow-hidden px-4">
                            <svg class="w-full h-full absolute inset-0" preserveAspectRatio="none" viewBox="0 0 100 100">
                                <line x1="0" y1="20" x2="100" y2="20" stroke="#e2e8f0" stroke-width="0.5"/>
                                <line x1="0" y1="40" x2="100" y2="40" stroke="#e2e8f0" stroke-width="0.5"/>
                                <line x1="0" y1="60" x2="100" y2="60" stroke="#e2e8f0" stroke-width="0.5"/>
                                <line x1="0" y1="80" x2="100" y2="80" stroke="#e2e8f0" stroke-width="0.5"/>
                                
                                <path d="M5,40 L50,45 L95,50" fill="none" stroke="#34d399" stroke-width="2"/>
                                <path d="M5,40 L50,45 L95,50 L95,100 L5,100 Z" fill="#34d399" fill-opacity="0.1"/>
                                
                                <circle cx="5" cy="40" r="3" fill="#10b981"/>
                                <circle cx="50" cy="45" r="3" fill="#10b981"/>
                                <circle cx="95" cy="50" r="3" fill="#10b981"/>
                            </svg>
                            
                            <div class="absolute left-2 top-2 bottom-2 flex flex-col justify-between text-[8px] text-slate-400">
                                <span>300</span><span>250</span><span>200</span><span>150</span><span>100</span><span>50</span><span>0</span>
                            </div>
                            
                            <div class="absolute bottom-2 left-6 text-[9px] text-slate-500 font-bold">Shift 1</div>
                            <div class="absolute bottom-2 left-1/2 -translate-x-1/2 text-[9px] text-slate-500 font-bold">Shift 2</div>
                            <div class="absolute bottom-2 right-6 text-[9px] text-slate-500 font-bold">Shift 3</div>
                        </div>
                    </div>

                    <!-- Rincian Output Table -->
                    <div>
                    <div class="flex justify-between items-center px-5 py-4 border-b border-slate-100">
                        <p class="text-sm font-bold text-slate-800">Rincian Output per Shift</p>
                        <span class="text-[10px] text-slate-400">Ringkasan 3 shift terakhir</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-center">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-500 uppercase tracking-widest font-bold">
                                    <th class="py-3 px-4 text-left">Shift</th>
                                    <th class="py-3 px-4 text-left">Operator</th>
                                    <th class="py-3 px-4">Sak Selesai</th>
                                    <th class="py-3 px-4">Berat (Kg)</th>
                                    <th class="py-3 px-4">Avg/Sak</th>
                                    <th class="py-3 px-4">Reject</th>
                                    <th class="py-3 px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700 text-left">Shift 1</td>
                                    <td class="py-3 px-4 text-slate-600 text-left">Budi, Mia</td>
                                    <td class="py-3 px-4 font-bold text-slate-800">210 sak</td>
                                    <td class="py-3 px-4 text-slate-600">5.250 Kg</td>
                                    <td class="py-3 px-4 text-slate-600">2.0 mnt</td>
                                    <td class="py-3 px-4 text-slate-600">0</td>
                                    <td class="py-3 px-4"><span class="font-bold text-green-500 text-xs">On Time</span></td>
                                </tr>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700 text-left">Shift 2</td>
                                    <td class="py-3 px-4 text-slate-600 text-left">Ayu, Bagas</td>
                                    <td class="py-3 px-4 font-bold text-slate-800">200 sak</td>
                                    <td class="py-3 px-4 text-slate-600">5.000 Kg</td>
                                    <td class="py-3 px-4 text-slate-600">2.2 mnt</td>
                                    <td class="py-3 px-4 text-slate-600">1</td>
                                    <td class="py-3 px-4"><span class="font-bold text-green-500 text-xs">On Time</span></td>
                                </tr>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700 text-left">Shift 3</td>
                                    <td class="py-3 px-4 text-slate-600 text-left">Fikri, Wahyu</td>
                                    <td class="py-3 px-4 font-bold text-slate-800">190 sak</td>
                                    <td class="py-3 px-4 text-slate-600">4.750 Kg</td>
                                    <td class="py-3 px-4 text-slate-600">2.4 mnt</td>
                                    <td class="py-3 px-4 text-slate-600">0</td>
                                    <td class="py-3 px-4"><span class="font-bold text-green-500 text-xs">On Time</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>

            <!-- SISA MATERIAL TAB -->
            <div x-show="lrTab === 'sisa'" style="display: none;" class="space-y-6">
                
                <!-- Top Stats -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white border border-amber-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-amber-500 uppercase tracking-widest mb-2">Total Sisa Material</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-amber-600">5.5</span>
                            <span class="text-sm font-bold text-amber-600">Kg</span>
                        </div>
                        <p class="text-[10px] font-bold text-amber-500 mt-1">Efisiensi Pemakaian BOM: 99.6%</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Status Penampungan Sisa</p>
                        <p class="text-base font-bold text-slate-800">Bin Sisa Gudang 2</p>
                        <p class="text-xs text-green-600 font-bold mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Labeling &amp; Timbang Ulang OK
                        </p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Petugas Verifikasi</p>
                        <p class="text-sm font-bold text-slate-800">Hana (QC) &amp; Suryanto (Gudang)</p>
                        <p class="text-[10px] text-slate-400 mt-1 italic">Terverifikasi 27 Aug 2026</p>
                    </div>
                </div>

                <!-- Rincian Sisa Table -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="flex justify-between items-start px-5 py-4 border-b border-slate-100">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Rincian Sisa Material Pasca-Batch Produksi</p>
                            <p class="text-xs text-slate-400">Selisih antara target kebutuhan BOM dan aktual pemakaian pada mesin</p>
                        </div>
                        <span class="px-2 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded mt-1 border border-amber-200">Selisih Toleransi < 0.5%</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-center">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-500 uppercase tracking-widest font-bold">
                                    <th class="py-3 px-4 text-left">Nama Material</th>
                                    <th class="py-3 px-4 text-right">Target Kebutuhan</th>
                                    <th class="py-3 px-4 text-right">Aktual Terpakai</th>
                                    <th class="py-3 px-4 text-right">Sisa Material (Kg)</th>
                                    <th class="py-3 px-4 text-left">Alasan / Keterangan Sisa</th>
                                    <th class="py-3 px-4 text-left">Lokasi Penampungan</th>
                                    <th class="py-3 px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700 text-left">Resin PVC S-65</td>
                                    <td class="py-3 px-4 text-slate-500 text-right">1.250,0 Kg</td>
                                    <td class="py-3 px-4 text-slate-600 text-right">1.246,0 Kg</td>
                                    <td class="py-3 px-4 font-bold text-amber-600 text-right">4,0 Kg</td>
                                    <td class="py-3 px-4 text-slate-500 text-left">Sisa penakaran di hopper mixer</td>
                                    <td class="py-3 px-4 text-slate-600 text-left">Bin Sisa A-01</td>
                                    <td class="py-3 px-4"><span class="px-2 py-1 bg-green-50 text-green-600 border border-green-200 text-[10px] font-bold rounded">Lengkap</span></td>
                                </tr>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700 text-left">Stabilizer Ca-Zn</td>
                                    <td class="py-3 px-4 text-slate-500 text-right">50,0 Kg</td>
                                    <td class="py-3 px-4 text-slate-600 text-right">49,0 Kg</td>
                                    <td class="py-3 px-4 font-bold text-amber-600 text-right">1,0 Kg</td>
                                    <td class="py-3 px-4 text-slate-500 text-left">Sisa kemasan segel terbuka</td>
                                    <td class="py-3 px-4 text-slate-600 text-left">Loker Aditif Khusus</td>
                                    <td class="py-3 px-4"><span class="px-2 py-1 bg-green-50 text-green-600 border border-green-200 text-[10px] font-bold rounded">Disimpan</span></td>
                                </tr>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700 text-left">Pigment White</td>
                                    <td class="py-3 px-4 text-slate-500 text-right">25,0 Kg</td>
                                    <td class="py-3 px-4 text-slate-600 text-right">24,5 Kg</td>
                                    <td class="py-3 px-4 font-bold text-amber-600 text-right">0,5 Kg</td>
                                    <td class="py-3 px-4 text-slate-500 text-left">Sisa micro-dosing feeder</td>
                                    <td class="py-3 px-4 text-slate-600 text-left">Rak Pigment Retur</td>
                                    <td class="py-3 px-4"><span class="px-2 py-1 bg-green-50 text-green-600 border border-green-200 text-[10px] font-bold rounded">Disimpan</span></td>
                                </tr>
                                <tr class="bg-amber-50/30 border-t-2 border-amber-100">
                                    <td class="py-3 px-4 font-black text-slate-800 text-left uppercase text-xs">Total Sisa Material</td>
                                    <td class="py-3 px-4 font-bold text-slate-800 text-right text-xs">1.325,0 Kg</td>
                                    <td class="py-3 px-4 font-bold text-slate-800 text-right text-xs">1.319,5 Kg</td>
                                    <td class="py-3 px-4 font-black text-amber-700 text-right text-xs">5,5 Kg</td>
                                    <td class="py-3 px-4 text-slate-500 text-left text-[10px] italic">Di-repack &amp; dilabeli untuk SPK berikutnya</td>
                                    <td class="py-3 px-4 text-center">-</td>
                                    <td class="py-3 px-4 text-center">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PENYERAHAN BARANG TAB -->
            <div x-show="lrTab === 'transfer_fg'" style="display: none;" class="space-y-6">
                
                <!-- Top Stats -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white border border-blue-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mb-2">Total Diserahkan Ke Gudang</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-blue-700">15.000</span>
                            <span class="text-sm font-bold text-blue-600">Kg</span>
                        </div>
                        <p class="text-[10px] font-bold text-blue-500 mt-1">600 Sak / Bag (25 Kg / Sak)</p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">No. Moving / Transfer Slip</p>
                        <p class="text-base font-bold text-slate-800">TRF-FG-2026-0829</p>
                        <p class="text-xs text-green-600 font-bold mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Status: Accepted by Warehouse
                        </p>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Lokasi Penyimpanan FG</p>
                        <p class="text-sm font-bold text-slate-800">Gudang Barang Jadi (Zone A-04)</p>
                        <p class="text-[10px] text-slate-400 mt-1 italic">Siap muat kontainer</p>
                    </div>
                </div>

                <!-- Log Riwayat Table -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="flex justify-between items-center px-5 py-4 border-b border-slate-100">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Log Riwayat Penyerahan Barang (Transfer Slip Barang Jadi)</p>
                            <p class="text-xs text-slate-400">Daftar bertahap serah terima hasil Bagging dari Lini Produksi ke Gudang Barang Jadi</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-center">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-500 uppercase tracking-widest font-bold">
                                    <th class="py-3 px-4 text-left">No. Transfer Slip</th>
                                    <th class="py-3 px-4">Tanggal &amp; Jam Transfer</th>
                                    <th class="py-3 px-4">Jumlah Sak</th>
                                    <th class="py-3 px-4">Total Berat (Kg)</th>
                                    <th class="py-3 px-4 text-left">Petugas Serah (Produksi)</th>
                                    <th class="py-3 px-4 text-left">Petugas Terima (Gudang)</th>
                                    <th class="py-3 px-4">Status Acceptance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700 text-left">TRF-FG-2026-0829-01</td>
                                    <td class="py-3 px-4 text-slate-500">28 Aug 2026, 14:15</td>
                                    <td class="py-3 px-4 font-bold text-slate-800">210 Sak</td>
                                    <td class="py-3 px-4 text-slate-600">5.250 Kg</td>
                                    <td class="py-3 px-4 text-slate-600 text-left">Putu (Shift 1)</td>
                                    <td class="py-3 px-4 text-slate-600 text-left">Suryanto</td>
                                    <td class="py-3 px-4"><span class="font-bold text-green-500 text-xs">Diterima Lengkap</span></td>
                                </tr>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700 text-left">TRF-FG-2026-0829-02</td>
                                    <td class="py-3 px-4 text-slate-500">28 Aug 2026, 22:30</td>
                                    <td class="py-3 px-4 font-bold text-slate-800">200 Sak</td>
                                    <td class="py-3 px-4 text-slate-600">5.000 Kg</td>
                                    <td class="py-3 px-4 text-slate-600 text-left">Putri (Shift 2)</td>
                                    <td class="py-3 px-4 text-slate-600 text-left">Bambang</td>
                                    <td class="py-3 px-4"><span class="font-bold text-green-500 text-xs">Diterima Lengkap</span></td>
                                </tr>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-bold text-slate-700 text-left">TRF-FG-2026-0829-03</td>
                                    <td class="py-3 px-4 text-slate-500">29 Aug 2026, 06:45</td>
                                    <td class="py-3 px-4 font-bold text-slate-800">190 Sak</td>
                                    <td class="py-3 px-4 text-slate-600">4.750 Kg</td>
                                    <td class="py-3 px-4 text-slate-600 text-left">Fikri (Shift 3)</td>
                                    <td class="py-3 px-4 text-slate-600 text-left">Suryanto</td>
                                    <td class="py-3 px-4"><span class="font-bold text-green-500 text-xs">Diterima Lengkap</span></td>
                                </tr>
                                <tr class="bg-slate-50/50 border-t-2 border-slate-200">
                                    <td colspan="2" class="py-3 px-4 font-black text-slate-800 text-left uppercase text-[10px] tracking-widest">Total Keseluruhan Transfer</td>
                                    <td class="py-3 px-4 font-black text-slate-800 text-sm">600 Sak</td>
                                    <td class="py-3 px-4 font-black text-slate-800 text-sm">15.000 Kg</td>
                                    <td colspan="3" class="py-3 px-4 text-slate-500 text-left text-[10px] italic">Verifikasi 100% Sesuai Target SPK</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        
        <!-- Footer Info -->
        <div class="bg-white border-t border-slate-200 px-6 py-3 flex justify-between items-center shrink-0">
            <p class="text-[10px] text-slate-400 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                Data diperbarui otomatis dari sistem mesin & input operator.
            </p>
        </div>

    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('liveReport', () => ({
        lrTab: 'extruder',
        tabs: [
            { id: 'cleaning', name: 'Cleaning', done: false },
            { id: 'penimbangan', name: 'Penimbangan', done: false },
            { id: 'mixing', name: 'Mixing', done: false },
            { id: 'extruder', name: 'Extruder', done: false },
            { id: 'bagging', name: 'Bagging', done: false },
            { id: 'sisa', name: 'Sisa Material', done: false },
            { id: 'transfer_fg', name: 'Penyerahan Barang', done: false },
        ],
        extruderForm: {
            jam: '13:00',
            operator: '',
            target: '160-178 °C',
            hasil: 'Sesuai (Normal)',
            z1: '', z2: '', z3: '', z4: '', rpm: '', ampere: '', kesimpulan: '-'
        },
        extruderLogs: [
            { jam: '06:00', operator: 'Budi', z1: 160, z2: 170, z3: 175, z4: 175, target: '160-178 °C', hasil: 'Sesuai', kesimpulan: '-' },
            { jam: '07:00', operator: 'Mia', z1: 161, z2: 171, z3: 176, z4: 176, target: '160-178 °C', hasil: 'Sesuai', kesimpulan: '-' },
            { jam: '08:00', operator: 'Ayu', z1: 160, z2: 172, z3: 178, z4: 178, target: '160-178 °C', hasil: 'Sesuai', kesimpulan: '-' },
            { jam: '09:00', operator: 'Bagas', z1: 162, z2: 170, z3: 177, z4: 179, target: '160-178 °C', hasil: 'Sesuai', kesimpulan: '-' },
            { jam: '10:00', operator: 'Rudi', z1: 160, z2: 173, z3: 180, z4: 180, target: '160-178 °C', hasil: 'Sesuai', kesimpulan: '-' },
            { jam: '11:00', operator: 'Putu', z1: 161, z2: 172, z3: 179, z4: 178, target: '160-178 °C', hasil: 'Sesuai', kesimpulan: '-' },
            { jam: '12:00', operator: 'Citra', z1: 160, z2: 171, z3: 178, z4: 178, target: '160-178 °C', hasil: 'Sesuai', kesimpulan: '-' }
        ],
        get zone1Path() { return this.generatePath('z1'); },
        get zone2Path() { return this.generatePath('z2'); },
        get zone3Path() { return this.generatePath('z3'); },
        get zone4Path() { return this.generatePath('z4'); },
        generatePath(key) {
            if(this.extruderLogs.length === 0) return '';
            if(this.extruderLogs.length === 1) {
                const y = (200 - (parseFloat(this.extruderLogs[0][key]) || 150)) * 2;
                return `M0,${y} L100,${y}`;
            }
            return this.extruderLogs.map((log, i) => {
                const x = (i / (this.extruderLogs.length - 1)) * 100;
                const val = parseFloat(log[key]) || 150;
                const y = (200 - val) * 2; // mapping 150->100, 200->0
                return `${i === 0 ? 'M' : 'L'}${x.toFixed(1)},${y.toFixed(1)}`;
            }).join(' ');
        },
        getXPos(i) {
            if(this.extruderLogs.length <= 1) return 0;
            return (i / (this.extruderLogs.length - 1)) * 100;
        },
        getYPos(log, key) {
            const val = parseFloat(log[key]) || 150;
            return (200 - val) * 2;
        },
        addExtruderData() {
            if(!this.extruderForm.operator || !this.extruderForm.z1) {
                alert("Mohon lengkapi data operator dan setidaknya Suhu Zone 1");
                return;
            }
            this.extruderLogs.push({
                jam: this.extruderForm.jam,
                operator: this.extruderForm.operator,
                z1: this.extruderForm.z1,
                z2: this.extruderForm.z2 || this.extruderForm.z1,
                z3: this.extruderForm.z3 || this.extruderForm.z1,
                z4: this.extruderForm.z4 || this.extruderForm.z1,
                target: this.extruderForm.target,
                hasil: this.extruderForm.hasil,
                kesimpulan: this.extruderForm.kesimpulan || '-'
            });
            // Increment hour
            let [h, m] = this.extruderForm.jam.split(':').map(Number);
            h = (h + 1) % 24;
            this.extruderForm.jam = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
            // Clear inputs
            this.extruderForm.z1 = ''; this.extruderForm.z2 = ''; this.extruderForm.z3 = ''; this.extruderForm.z4 = '';
            this.extruderForm.rpm = ''; this.extruderForm.ampere = '';
        },
        initLr() {
            // Set tab status based on real data
        }
    }));
});
// Move modal to body to escape z-index stacking context
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('liveReportModal');
    if (modal) {
        document.body.appendChild(modal);
    }
});
</script>
