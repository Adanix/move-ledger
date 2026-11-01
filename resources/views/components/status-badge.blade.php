@props(['status'])

@php
    $label = is_object($status) && method_exists($status, 'label') ? $status->label() : (string) $status;
    $class = is_object($status) && method_exists($status, 'badgeClass') ? $status->badgeClass() : 'bg-slate-100 text-slate-700 border-slate-300';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {$class}"]) }}>
    {{ $label }}
</span>
