@props(['value', 'decimals' => 1])
@php
    $level = $value === null ? 'none' : (int) min(6, max(1, floor((float) $value)));
@endphp
<span {{ $attributes->merge(['class' => "grade g{$level}"]) }}>{{ $value === null ? '—' : number_format((float) $value, $decimals, ',', '.') }}</span>
