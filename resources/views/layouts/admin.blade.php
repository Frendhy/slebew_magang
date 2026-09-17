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
            <h2 class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-500 mb-5 px-2">Menu Navigasi</h2>
            <nav class="space-y-3">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all"{{ request()->routeIs('admin.dashboard') ? 'bg-brand-500 text-white shadow-[0_0_20px_rgba(14,165,233,0.3)]' : 'text-slate-600 hover:text-brand-900 hover:bg-slate-50' }}>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span class="font-bold text-sm">Dasbor</span>
                </a>
                <a href="{{ route('admin.calendar') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all"{{ request()->routeIs('admin.calendar') ? 'bg-brand-500 text-white shadow-[0_0_20px_rgba(14,165,233,0.3)]' : 'text-slate-600 hover:text-brand-900 hover:bg-slate-50' }}>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="font-bold text-sm">Kalender</span>
                </a>
                <a href="{{ route('admin.search') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all"{{ request()->routeIs('admin.search') ? 'bg-brand-500 text-white shadow-[0_0_20px_rgba(14,165,233,0.3)]' : 'text-slate-600 hover:text-brand-900 hover:bg-slate-50' }}>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span class="font-bold text-sm">Pencarian SPK</span>
                </a>
                <a href="{{ route('admin.operators.index') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all"{{ request()->routeIs('admin.operators.*') ? 'bg-brand-500 text-white shadow-[0_0_20px_rgba(14,165,233,0.3)]' : 'text-slate-600 hover:text-brand-900 hover:bg-slate-50' }}>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="font-bold text-sm">Manajemen Pekerja</span>
                </a>
                <a href="{{ route('admin.schedules.index') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all"{{ request()->routeIs('admin.schedules.*') ? 'bg-brand-500 text-white shadow-[0_0_20px_rgba(14,165,233,0.3)]' : 'text-slate-600 hover:text-brand-900 hover:bg-slate-50' }}>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-bold text-sm">Manajemen Jadwal</span>
                </a>

                {{-- Emergency Report --}}
                <a href="{{ route('emergency.index') }}"
                   class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all font-bold text-sm {{ request()->routeIs('emergency.*') ? 'bg-red-500 text-white shadow-[0_0_20px_rgba(239,68,68,0.3)]' : 'text-red-500 hover:bg-red-50 hover:text-red-600' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="flex-1 text-left">Emergency Report</span>
                    @if($emergencyOpenCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-600 text-white">{{ $emergencyOpenCount }}</span>
                    @endif
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 w-full overflow-hidden">
        @yield('admin_content')
    </main>
</div>
@endsection
