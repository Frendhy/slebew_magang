@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto pb-12 fade-in">

    {{-- Back --}}
    <a href="{{ route('emergency.index') }}" class="flex items-center gap-2 text-slate-500 hover:text-brand-900 text-sm font-semibold mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Daftar Laporan
    </a>

    {{-- Success Flash --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <p class="text-sm font-semibold text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Status Banner --}}
    @php
        $statusBg = match($report->status) {
            'open'         => 'bg-red-50 border-red-300',
            'acknowledged' => 'bg-amber-50 border-amber-300',
            'resolved'     => 'bg-green-50 border-green-300',
            default        => 'bg-slate-50 border-slate-200'
        };
        $statusLabel = match($report->status) {
            'open'         => '🔴 Belum Ditangani',
            'acknowledged' => '🟡 Sedang Ditinjau oleh R&D',
            'resolved'     => '🟢 Selesai — Proses Diizinkan Lanjut',
            default        => $report->status
        };
    @endphp
    <div class="mb-6 p-4 border rounded-xl {{ $statusBg }}">
        <p class="font-bold text-sm text-slate-700">{{ $statusLabel }}</p>
        @if($report->isResolved())
        <p class="text-xs text-slate-500 mt-1">
            Diselesaikan oleh <strong>{{ $report->resolver->name ?? 'R&D' }}</strong> 
            pada {{ $report->resolved_at->format('d M Y, H:i') }}
        </p>
        @if($report->resolve_note)
        <p class="text-sm text-slate-700 mt-2 font-medium">Catatan: {{ $report->resolve_note }}</p>
        @endif
        @endif
    </div>

    {{-- Report Card --}}
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-slate-200 bg-red-50/50">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-extrabold text-red-600">🚨 Laporan Darurat</span>
                <span class="text-xs font-bold text-slate-500">·</span>
                <span class="text-xs font-bold uppercase tracking-widest text-slate-500">{{ $report->spk->spk_number ?? '-' }}</span>
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-[11px] font-bold border border-blue-200 uppercase">{{ $report->step }}</span>
            </div>
        </div>

        <div class="p-6 space-y-5">
            {{-- Info Grid --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Dilaporkan Oleh</p>
                    <p class="font-bold text-slate-900">{{ $report->worker->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Produk SPK</p>
                    <p class="font-bold text-slate-900">{{ $report->spk->product_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Tahap Produksi</p>
                    <p class="font-bold text-slate-900 uppercase">{{ $report->step }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Waktu Laporan</p>
                    <p class="font-bold text-slate-900">{{ $report->created_at->format('d M Y, H:i:s') }}</p>
                </div>
            </div>

            {{-- Recipient Roles --}}
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Dikirimkan Ke</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($report->recipient_roles as $role)
                    <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-bold border border-slate-200 uppercase">{{ $role }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Description --}}
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Deskripsi Masalah</p>
                <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <p class="text-sm text-slate-800 leading-relaxed font-medium">{{ $report->description }}</p>
                </div>
            </div>

            {{-- Media Evidence --}}
            @if(!empty($report->media_files))
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Bukti Lampiran ({{ count($report->media_files) }} file)</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($report->media_files as $media)
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex flex-col items-center justify-center">
                        @if($media['type'] === 'video')
                        <video controls class="w-full h-auto max-h-64 rounded-lg bg-black" style="max-width:100%;">
                            <source src="{{ asset('storage/' . $media['path']) }}" type="video/mp4">
                            Browser Anda tidak mendukung pemutar video.
                        </video>
                        @else
                        <img src="{{ asset('storage/' . $media['path']) }}" alt="Bukti laporan darurat"
                             class="w-full h-auto max-h-64 object-contain rounded-lg cursor-pointer hover:opacity-90 transition-opacity" style="max-width:100%;" 
                             onclick="openImageModal('{{ asset('storage/' . $media['path']) }}')" />
                        @endif
                        <p class="text-xs text-slate-400 mt-2 truncate w-full text-center" title="{{ $media['original_name'] }}">{{ $media['original_name'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @elseif($report->media_path)
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Bukti Foto / Video</p>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                    @if($report->media_type === 'video')
                    <video controls class="w-full max-h-96 rounded-lg" style="max-width:100%;">
                        <source src="{{ $report->media_url }}" type="video/mp4">
                        Browser Anda tidak mendukung pemutar video.
                    </video>
                    @else
                    <img src="{{ $report->media_url }}" alt="Bukti foto laporan darurat"
                         class="w-full max-h-96 object-contain rounded-lg cursor-pointer hover:opacity-90 transition-opacity" style="max-width:100%;"
                         onclick="openImageModal('{{ $report->media_url }}')" />
                    @endif
                    <p class="text-xs text-slate-400 mt-2">{{ $report->media_original_name }}</p>
                </div>
            </div>
            @else
            <div class="text-sm text-slate-400 italic">Tidak ada bukti media yang dilampirkan.</div>
            @endif
        </div>
    </div>

    {{-- Resolve Form (Only for RND, only if not yet resolved) --}}
    @if(auth()->user()->role === 'rnd' && !$report->isResolved())
    <div class="bg-white border-2 border-green-500/40 rounded-2xl p-6 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 mb-1">✅ Berikan Izin Lanjut</h3>
        <p class="text-sm text-slate-500 mb-4">Setelah Anda mengklik "Selesaikan & Izinkan Lanjut", proses <strong class="uppercase text-brand-600">{{ $report->step }}</strong> untuk SPK <strong class="text-brand-600">{{ $report->spk->spk_number }}</strong> akan dibuka kembali.</p>

        <form method="POST" action="{{ route('emergency.resolve', $report->id) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Catatan Resolusi (opsional)</label>
                <textarea name="resolve_note" rows="3" placeholder="Tuliskan instruksi atau catatan untuk operator..."
                    class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"></textarea>
            </div>
            <button type="submit"
                class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-green-600 hover:bg-green-500 text-white font-bold text-sm transition-all shadow-lg shadow-green-500/30"
                onclick="return confirm('Apakah Anda yakin ingin memberikan izin lanjut untuk proses {{ $report->step }}?')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Selesaikan & Izinkan Proses Lanjut
            </button>
        </form>
    </div>
    @elseif(auth()->user()->role !== 'rnd' && !$report->isResolved())
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-center">
        <p class="text-sm font-semibold text-amber-700">⏳ Menunggu keputusan dari <strong>R&D</strong> untuk membuka kembali proses produksi.</p>
    </div>
    @endif

</div>

{{-- Image Modal --}}
<div id="imageModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/90 backdrop-blur-sm transition-opacity" onclick="closeImageModal()">
    <div class="relative w-full max-w-5xl max-h-screen p-4 flex flex-col items-center justify-center">
        <button class="absolute top-4 right-4 text-white/70 hover:text-white bg-slate-800/50 hover:bg-slate-800 p-2 rounded-full transition-colors" onclick="closeImageModal()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <img id="modalImage" src="" alt="Layar Penuh" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl" onclick="event.stopPropagation()">
    </div>
</div>

<script>
    function openImageModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.getElementById('imageModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('imageModal').classList.remove('flex');
        document.body.style.overflow = '';
        setTimeout(() => { document.getElementById('modalImage').src = ''; }, 300);
    }
</script>
@endsection
