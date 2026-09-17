@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-6">
    <!-- Breadcrumb / Progress -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
      <ol class="flex items-center space-x-4">
        <li>
          <div>
            <a href="/" class="text-slate-500 hover:text-brand-400 transition-colors">
              <svg class="flex-shrink-0 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            </a>
          </div>
        </li>
        <li>
          <div class="flex items-center">
            <svg class="flex-shrink-0 h-5 w-5 text-brand-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
            <a href="/transfer" class="ml-4 text-sm font-medium text-slate-500 hover:text-brand-900 transition-colors">1. Pemindahan Material</a>
          </div>
        </li>
        <li>
          <div class="flex items-center">
            <svg class="flex-shrink-0 h-5 w-5 text-slate-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
            <span class="ml-4 text-sm font-bold text-brand-400">2. Pengesahan</span>
          </div>
        </li>
      </ol>
    </nav>

    <div class="fade-in">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Pengesahan Digital</h1>
        <p class="text-slate-500 mb-8">Tinjau material yang dipindahkan dan tanda tangani secara digital untuk mengonfirmasi penerimaan.</p>

        <x-card class="mb-6 border-brand-500/20">
            <x-slot:header>
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-bold text-slate-900">Ringkasan Pemindahan</h2>
                    <span class="text-sm text-slate-500">{{ date('d M Y, H:i') }}</span>
                </div>
            </x-slot>

            <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 mb-8">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Material</p>
                        <p class="text-lg font-bold text-slate-900">Industrial Adhesive A (MAT-1004)</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Kuantitas Dipindah</p>
                        <p class="text-lg font-bold text-brand-400">250.00 kg</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Dari</p>
                        <p class="text-lg font-medium text-slate-900">Gudang Zona B</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Ke</p>
                        <p class="text-lg font-medium text-slate-900">Area Produksi A</p>
                    </div>
                </div>
            </div>

            <div class="border-2 border-dashed border-brand-500/50 rounded-xl p-8 text-center bg-slate-800/30 hover:bg-slate-700/50 transition-colors cursor-pointer" onclick="document.getElementById('pin-modal').classList.remove('hidden')">
                <div class="w-16 h-16 bg-brand-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Ketuk untuk Mengesahkan</h3>
                <p class="text-slate-500">Masukkan PIN Operator Anda untuk mengautentikasi catatan ini</p>
            </div>
        </x-card>
        
        <div class="flex justify-between items-center mt-8">
            <a href="/transfer" class="text-slate-500 hover:text-brand-900 font-medium py-4 px-6 flex items-center transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <a href="/production/transfer/complete?spk_id={{ request('spk_id') }}" class="block w-full max-w-xs">
                <x-button size="xl" variant="secondary" class="w-full justify-between group border border-slate-300">
                    <span>Lanjut: Penimbangan</span>
                    <svg class="w-6 h-6 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </x-button>
            </a>
        </div>
    </div>
</div>

<!-- Mock PIN Modal -->
<div id="pin-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-white bg-opacity-90 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-slate-50 rounded-2xl text-left overflow-hidden shadow-2xl border border-slate-200 transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full">
            <div class="bg-slate-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-bold text-slate-900 mb-4" id="modal-title">Masukkan PIN Operator</h3>
                        <div class="flex justify-center space-x-2 mb-6">
                            <div class="w-12 h-14 bg-white rounded-lg border-2 border-brand-500 text-2xl font-bold flex items-center justify-center text-slate-900">1</div>
                            <div class="w-12 h-14 bg-white rounded-lg border-2 border-slate-200 text-2xl font-bold flex items-center justify-center text-slate-900">4</div>
                            <div class="w-12 h-14 bg-white rounded-lg border-2 border-slate-200 text-2xl font-bold flex items-center justify-center text-slate-500">*</div>
                            <div class="w-12 h-14 bg-white rounded-lg border-2 border-slate-200 text-2xl font-bold flex items-center justify-center text-slate-500">*</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-slate-100/50 px-4 py-3 border-t border-slate-200 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="document.getElementById('pin-modal').classList.add('hidden')" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg shadow-brand-500/30 px-4 py-3 bg-brand-500 text-base font-medium text-white hover:bg-brand-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Konfirmasi Pengesahan
                </button>
                <button type="button" onclick="document.getElementById('pin-modal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-3 bg-slate-100 text-base font-medium text-slate-900 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-400 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
