@extends('layouts.admin')

@section('admin_content')
<div class="fade-in">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-1">Dashboard Admin Produksi</h1>
        <p class="text-slate-500">Pantau progres dan urgensi seluruh Surat Perintah Kerja (SPK).</p>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <h3 class="text-slate-500 text-xs uppercase tracking-widest font-semibold mb-2">Total SPK Aktif</h3>
            <p class="text-3xl font-extrabold text-slate-900">{{ $spks->where('status', '!=', 'done')->count() }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <h3 class="text-slate-500 text-xs uppercase tracking-widest font-semibold mb-2">Mendatang</h3>
            <p class="text-3xl font-extrabold text-slate-600">{{ $spks->where('status', 'upcoming')->count() }}</p>
        </div>
        <div class="bg-white border border-amber-500/20 rounded-2xl p-5">
            <h3 class="text-slate-500 text-xs uppercase tracking-widest font-semibold mb-2">Sedang Berjalan</h3>
            <p class="text-3xl font-extrabold text-amber-400">{{ $spks->where('status', 'ongoing')->count() }}</p>
        </div>
        <div class="bg-white border border-green-500/20 rounded-2xl p-5">
            <h3 class="text-slate-500 text-xs uppercase tracking-widest font-semibold mb-2">Selesai (Done)</h3>
            <p class="text-3xl font-extrabold text-green-400">{{ $spks->where('status', 'done')->count() }}</p>
        </div>
    </div>

    <!-- SPK Table -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-2xl">
        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">Daftar SPK (Diurutkan Berdasarkan Urgensi)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="text-xs text-slate-500 uppercase bg-slate-100/50">
                    <tr>
                        <th class="px-6 py-4 font-semibold">No. SPK</th>
                        <th class="px-6 py-4 font-semibold">Produk</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold">Tahap Saat Ini</th>
                        <th class="px-6 py-4 font-semibold">Alokasi</th>
                        <th class="px-6 py-4 font-semibold text-right">Tenggat Waktu</th>
                        <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($spks as $spk)
                        @php
                            $isOverdue = $spk->status != 'done' && $spk->due_date && \Carbon\Carbon::parse($spk->due_date)->isPast();
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition-colors"{{ $isOverdue ? 'border-l-2 border-l-red-500' : '' }}>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $spk->spk_number }}</td>
                            <td class="px-6 py-4">{{ $spk->product_name }}</td>
                            <td class="px-6 py-4">
                                @if($spk->status == 'upcoming')
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold border border-slate-300 whitespace-nowrap">Mendatang</span>
                                @elseif($spk->status == 'ongoing')
                                    <span class="px-3 py-1 bg-amber-500/20 text-amber-400 rounded-full text-xs font-bold border border-amber-500/20 whitespace-nowrap">Sedang Berjalan</span>
                                @else
                                    <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-bold border border-green-500/20 whitespace-nowrap">Selesai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-brand-400 uppercase">{{ $spk->current_step ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $spk->man_allocation ?? 0 }} orang</td>
                            <td class="px-6 py-4 text-right">
                                <span class=""{{ $isOverdue ? 'text-red-400 font-bold' : 'text-slate-300' }}>
                                    {{ $spk->due_date ? \Carbon\Carbon::parse($spk->due_date)->format('d M Y') : '-' }}
                                    @if($isOverdue) <span class="text-[10px] block text-red-400">⚠️ OVERDUE</span> @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ url('/admin/spk/' . $spk->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-500/20 hover:bg-brand-500 text-brand-400 hover:text-white border border-brand-500/30 text-xs font-bold transition-all">
                                    Detail
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">Belum ada SPK.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
