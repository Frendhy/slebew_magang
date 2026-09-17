@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto mt-6 fade-in">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-1">{{ $spk?->spk_number ?? '' }}</p>
            <h1 class="text-3xl font-extrabold text-slate-900">Pre-Cleaning (Persiapan Awal)</h1>
            <p class="text-slate-500 mt-1">Lakukan pembersihan mesin sebelum proses produksi dimulai. Catat semua bahan pendukung yang dipakai.</p>
        </div>
        <a href="/" class="flex items-center gap-2 text-slate-500 hover:text-brand-900 text-sm font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Dashboard
        </a>
    </div>

    @if($spk)
    <form id="cleaning-form" action="/production/cleaning/complete" method="GET">
        <input type="hidden" name="spk_id" value="{{ $spk->id }}">
        <div class="space-y-6">
            {{-- Checklist --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <div class="flex justify-between items-center mb-5">
                    <h2 class="text-lg font-bold text-slate-900">Daftar Periksa Kebersihan</h2>
                    <span class="bg-slate-50 text-slate-600 border border-slate-200 py-1 px-3 rounded-full text-sm font-bold" id="checklist-count">0 / 4 Selesai</span>
                </div>
                <div class="divide-y divide-slate-700/50">
                    @foreach([
                        ['Bersihkan sisa material', 'Pastikan mesin benar-benar kosong dari sisa material.'],
                        ['Bilas menggunakan bahan pendukung', 'Jalankan siklus bilas sesuai prosedur SOP.'],
                        ['Kuras dan keringkan', 'Buka katup pembuangan dan gunakan udara pengering.'],
                        ['Inspeksi Visual', 'Periksa jika ada residu pada bilah, roller, atau dies mesin.'],
                    ] as $item)
                    <label class="flex items-center p-5 cursor-pointer hover:bg-slate-100/50 transition-colors group" onclick="updateCount()">
                        <input type="checkbox" class="checklist-item h-7 w-7 text-brand-500 border-slate-300 bg-slate-50 rounded">
                        <div class="ml-4">
                            <span class="block text-base font-bold text-slate-900">{{ $item[0] }}</span>
                            <span class="block text-slate-500 text-sm mt-0.5">{{ $item[1] }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Cleaning Materials --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <div class="flex justify-between items-center mb-5">
                    <h2 class="text-lg font-bold text-slate-900">Bahan Pendukung Cleaning</h2>
                    <button type="button" onclick="addCleaningRow()" class="text-sm font-bold text-brand-400 hover:text-brand-300 flex items-center gap-1 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah Bahan
                    </button>
                </div>
                <div id="cleaning-rows" class="space-y-3">
                    <div class="cleaning-row grid grid-cols-12 gap-3 items-center">
                        <div class="col-span-7">
                            <select name="cleaning_materials[]" class="block w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 py-3 px-4 focus:ring-brand-500">
                                <option value="" disabled selected>Pilih bahan...</option>
                                @foreach(['Kain Majun','Thinner','Plastik','Cable Ties','Grade B PP','Grade B PE','Resin PP','Resin PE','Stearic Acid','Parawax','Whimol'] as $mat)
                                <option>{{ $mat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-4">
                            <div class="flex">
                                <input type="number" name="cleaning_qty[]" class="block w-full rounded-l-xl border-slate-300 bg-slate-50 text-slate-900 py-3 px-4 focus:ring-brand-500" placeholder="Jumlah">
                                <span class="bg-slate-100 border border-l-0 border-slate-300 text-slate-500 px-3 rounded-r-xl flex items-center text-sm">pcs/kg</span>
                            </div>
                        </div>
                        <div class="col-span-1 flex justify-center">
                            <button type="button" onclick="this.closest('.cleaning-row').remove()" class="text-slate-500 hover:text-red-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Residual Materials --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <div class="flex justify-between items-center mb-5">
                    <h2 class="text-lg font-bold text-slate-900">Material Sisa (Kembali ke Gudang)</h2>
                    <button type="button" onclick="addResidualRow()" class="text-sm font-bold text-brand-400 hover:text-brand-300 flex items-center gap-1 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah Material Sisa
                    </button>
                </div>
                <div id="residual-rows" class="space-y-3">
                    <div class="residual-row grid grid-cols-12 gap-3 items-center">
                        <div class="col-span-7">
                            <input type="text" name="residual_names[]" class="block w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 py-3 px-4" placeholder="Nama material sisa">
                        </div>
                        <div class="col-span-4">
                            <div class="flex">
                                <input type="number" step="0.01" name="residual_kg[]" class="block w-full rounded-l-xl border-slate-300 bg-slate-50 text-slate-900 py-3 px-4" placeholder="Berat sisa">
                                <span class="bg-slate-100 border border-l-0 border-slate-300 text-slate-500 px-3 rounded-r-xl flex items-center text-sm">kg</span>
                            </div>
                        </div>
                        <div class="col-span-1 flex justify-center">
                            <button type="button" onclick="this.closest('.residual-row').remove()" class="text-slate-500 hover:text-red-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Catatan Akhir</label>
                <textarea name="notes" rows="3" class="block w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 py-3 px-4 placeholder-slate-500" placeholder="Tambahkan catatan observasi jika ada..."></textarea>
            </div>
        </div>

        <div class="flex justify-between items-center mt-8 pt-6 border-t border-slate-200">
            <div class="flex items-center" style="gap:0.75rem;">
                <a href="/production/spk-detail?spk_id={{ $spk->id }}" class="text-slate-500 hover:text-brand-900 font-medium py-3 px-6 transition-colors">Kembali</a>
                @include('production.partials.emergency_button', ['step' => 'cleaning'])
            </div>
            <button type="submit" class="px-8 py-4 rounded-xl bg-brand-500 hover:bg-brand-400 text-white font-bold text-lg shadow-[0_0_20px_rgba(14,165,233,0.3)] transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Selesaikan & Lanjut Transfer
            </button>
        </div>
    </form>
    @endif
</div>

<script>
function updateCount() {
    const total = document.querySelectorAll('.checklist-item').length;
    const checked = document.querySelectorAll('.checklist-item:checked').length;
    document.getElementById('checklist-count').textContent = `${checked} / ${total} Selesai`;
}
function addCleaningRow() {
    const opts = ['Kain Majun','Thinner','Plastik','Cable Ties','Grade B PP','Grade B PE','Resin PP','Resin PE','Stearic Acid','Parawax','Whimol'].map(m=>`<option>${m}</option>`).join('');
    document.getElementById('cleaning-rows').insertAdjacentHTML('beforeend', `
        <div class="cleaning-row grid grid-cols-12 gap-3 items-center">
            <div class="col-span-7"><select name="cleaning_materials[]" class="block w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 py-3 px-4"><option value="" disabled selected>Pilih bahan...</option>${opts}</select></div>
            <div class="col-span-4"><div class="flex"><input type="number" name="cleaning_qty[]" class="block w-full rounded-l-xl border-slate-300 bg-slate-50 text-slate-900 py-3 px-4" placeholder="Jumlah"><span class="bg-slate-100 border border-l-0 border-slate-300 text-slate-500 px-3 rounded-r-xl flex items-center text-sm">pcs/kg</span></div></div>
            <div class="col-span-1 flex justify-center"><button type="button" onclick="this.closest('.cleaning-row').remove()" class="text-slate-500 hover:text-red-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
        </div>`);
}
function addResidualRow() {
    document.getElementById('residual-rows').insertAdjacentHTML('beforeend', `
        <div class="residual-row grid grid-cols-12 gap-3 items-center">
            <div class="col-span-7"><input type="text" name="residual_names[]" class="block w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 py-3 px-4" placeholder="Nama material sisa"></div>
            <div class="col-span-4"><div class="flex"><input type="number" step="0.01" name="residual_kg[]" class="block w-full rounded-l-xl border-slate-300 bg-slate-50 text-slate-900 py-3 px-4" placeholder="Berat sisa"><span class="bg-slate-100 border border-l-0 border-slate-300 text-slate-500 px-3 rounded-r-xl flex items-center text-sm">kg</span></div></div>
            <div class="col-span-1 flex justify-center"><button type="button" onclick="this.closest('.residual-row').remove()" class="text-slate-500 hover:text-red-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
        </div>`);
}
</script>
@endsection
