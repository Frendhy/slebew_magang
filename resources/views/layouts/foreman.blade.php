@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row gap-8 pb-12">
    <!-- Sidebar -->
    <aside class="w-full md:w-64 flex-shrink-0">
@php
    $emergencyOpenCount = \App\Models\EmergencyReport::whereJsonContains('recipient_roles', auth()->user()->role)
        ->where('status', 'open')->count();
@endphp
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sticky top-28 shadow-sm">
            <p class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-500 mb-5 px-2">Menu Foreman</p>
            <nav class="space-y-2">
                <a href="{{ route('foreman.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm {{ request()->routeIs('foreman.dashboard') ? 'bg-brand-500 text-slate-900 shadow-[0_0_20px_rgba(14,165,233,0.3)]' : 'text-slate-600 hover:text-brand-900 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    Dasbor
                </a>
                <a href="{{ route('foreman.list') }}"
                   class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm {{ request()->routeIs('foreman.list') ? 'bg-brand-500 text-slate-900 shadow-[0_0_20px_rgba(14,165,233,0.3)]' : 'text-slate-600 hover:text-brand-900 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                    List SPK
                </a>
                <a href="{{ route('foreman.upcoming') }}"
                   class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm {{ request()->routeIs('foreman.upcoming') ? 'bg-brand-500 text-slate-900 shadow-[0_0_20px_rgba(14,165,233,0.3)]' : 'text-slate-600 hover:text-brand-900 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Jadwal Mendatang
                </a>
                <a href="{{ route('foreman.assign') }}"
                   class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm {{ request()->routeIs('foreman.assign') || request()->routeIs('foreman.spk.*') ? 'bg-brand-500 text-slate-900 shadow-[0_0_20px_rgba(14,165,233,0.3)]' : 'text-slate-600 hover:text-brand-900 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Penugasan Pekerja
                </a>

                {{-- Emergency Report --}}
                <a href="{{ route('emergency.index') }}"
                   class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm {{ request()->routeIs('emergency.*') ? 'bg-red-500 text-white shadow-[0_0_20px_rgba(239,68,68,0.3)]' : 'text-red-500 hover:bg-red-50 hover:text-red-600' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="flex-1">Emergency Report</span>
                    @if($emergencyOpenCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-600 text-white">{{ $emergencyOpenCount }}</span>
                    @endif
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 min-w-0">
        @yield('foreman_content')
    </main>
</div>
@endsection
