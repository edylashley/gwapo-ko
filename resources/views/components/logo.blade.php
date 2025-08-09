@props(['size' => 'md', 'class' => '', 'background' => true])

@php
    $sizes = [
        'sm' => 'w-10 h-10',
        'md' => 'w-14 h-14',
        'lg' => 'w-20 h-20',
        'xl' => 'w-28 h-28',
        '2xl' => 'w-36 h-36'
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="{{ $sizeClass }} {{ $class }}">
    <svg viewBox="0 0 512 512" class="w-full h-full">
        <!-- Clipboard background -->
        <rect x="120" y="80" width="280" height="360" rx="20" ry="20" fill="#FFFFFF" stroke="#1F2937" stroke-width="12"/>

        <!-- Clipboard clip -->
        <rect x="200" y="40" width="112" height="60" rx="30" ry="30" fill="#60A5FA" stroke="#1F2937" stroke-width="8"/>
        <circle cx="256" cy="60" r="8" fill="#1F2937"/>

        <!-- Document lines -->
        <line x1="160" y1="140" x2="200" y2="140" stroke="#1F2937" stroke-width="8" stroke-linecap="round"/>
        <line x1="220" y1="140" x2="340" y2="140" stroke="#1F2937" stroke-width="8" stroke-linecap="round"/>

        <line x1="160" y1="180" x2="280" y2="180" stroke="#1F2937" stroke-width="8" stroke-linecap="round"/>
        <line x1="300" y1="180" x2="340" y2="180" stroke="#1F2937" stroke-width="8" stroke-linecap="round"/>

        <line x1="160" y1="220" x2="260" y2="220" stroke="#1F2937" stroke-width="8" stroke-linecap="round"/>
        <line x1="280" y1="220" x2="340" y2="220" stroke="#1F2937" stroke-width="8" stroke-linecap="round"/>

        <line x1="160" y1="260" x2="220" y2="260" stroke="#1F2937" stroke-width="8" stroke-linecap="round"/>
        <line x1="240" y1="260" x2="280" y2="260" stroke="#1F2937" stroke-width="8" stroke-linecap="round"/>
        <line x1="300" y1="260" x2="340" y2="260" stroke="#1F2937" stroke-width="8" stroke-linecap="round"/>

        <line x1="200" y1="340" x2="340" y2="340" stroke="#1F2937" stroke-width="8" stroke-linecap="round"/>

        <line x1="200" y1="380" x2="300" y2="380" stroke="#1F2937" stroke-width="8" stroke-linecap="round"/>
        <line x1="320" y1="380" x2="340" y2="380" stroke="#1F2937" stroke-width="8" stroke-linecap="round"/>

        <circle cx="320" cy="420" r="6" fill="#1F2937"/>

        <!-- Coins stack -->
        <!-- Yellow coins -->
        <ellipse cx="120" cy="300" rx="35" ry="20" fill="#FCD34D" stroke="#1F2937" stroke-width="6"/>
        <rect x="85" y="300" width="70" height="25" fill="#FCD34D"/>
        <ellipse cx="120" cy="325" rx="35" ry="20" fill="#F59E0B" stroke="#1F2937" stroke-width="6"/>

        <!-- Green coins -->
        <ellipse cx="120" cy="350" rx="35" ry="20" fill="#86EFAC" stroke="#1F2937" stroke-width="6"/>
        <rect x="85" y="350" width="70" height="25" fill="#86EFAC"/>
        <ellipse cx="120" cy="375" rx="35" ry="20" fill="#22C55E" stroke="#1F2937" stroke-width="6"/>

        <ellipse cx="120" cy="400" rx="35" ry="20" fill="#65A30D" stroke="#1F2937" stroke-width="6"/>
        <rect x="85" y="400" width="70" height="25" fill="#65A30D"/>
        <ellipse cx="120" cy="425" rx="35" ry="20" fill="#4D7C0F" stroke="#1F2937" stroke-width="6"/>

        <!-- Side panel -->
        <rect x="420" y="120" width="40" height="280" rx="8" ry="8" fill="#60A5FA" stroke="#1F2937" stroke-width="8"/>
        <rect x="430" y="200" width="20" height="120" fill="#FCD34D"/>

        <!-- Plus symbol -->
        <circle cx="450" cy="440" r="25" fill="#EF4444" stroke="#1F2937" stroke-width="8"/>
        <line x1="440" y1="440" x2="460" y2="440" stroke="#FFFFFF" stroke-width="6" stroke-linecap="round"/>
        <line x1="450" y1="430" x2="450" y2="450" stroke="#FFFFFF" stroke-width="6" stroke-linecap="round"/>
    </svg>
</div>
