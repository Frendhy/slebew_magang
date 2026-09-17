<div class="bg-slate-50/80 backdrop-blur-sm rounded-2xl shadow-xl border border-slate-200 overflow-hidden"{{ $class ?? '' }}>
    @if(isset($header))
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            {{ $header }}
        </div>
    @endif
    
    <div class="p-6">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 bg-slate-100/50 border-t border-slate-200 flex items-center justify-between">
            {{ $footer }}
        </div>
    @endif
</div>
