@props([
    'icon' => 'o-bell',
    'size' => 'md',
    'color' => 'neutral',
    'badge' => null,
    'tooltip' => null,
    'onClick' => null,
    'pressed' => null,
])

@php
    $normalizedIcon = str_starts_with($icon, 'heroicon-')
        ? str_replace('heroicon-', '', $icon)
        : (preg_match('/^[os]-/', $icon) ? $icon : 'o-'.$icon);

    $sizeClasses = [
        'sm' => 'h-8 w-8',
        'md' => 'h-10 w-10',
        'lg' => 'h-12 w-12',
    ];

    $iconClasses = [
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
    ];

    $colorClasses = [
        'primary' => 'text-emerald-500',
        'neutral' => 'text-slate-700',
        'danger' => 'text-red-500',
    ];

    $buttonSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $buttonIconSize = $iconClasses[$size] ?? $iconClasses['md'];
    $buttonColor = $colorClasses[$color] ?? $colorClasses['neutral'];

    $hasBadge = filled($badge);
    $isDotBadge = $badge === true || $badge === 'dot';

    $badgeText = $hasBadge && ! $isDotBadge ? (string) $badge : null;
    $latestNotification = $tooltip ?: ($hasBadge ? 'Sonalika Tractors in Addis Ababa' : null);

    $ariaLabel = $badgeText
        ? $badgeText.' unread notifications'.($latestNotification ? ' about '.$latestNotification : '')
        : ($latestNotification ?: 'Icon button');

    $badgeTooltipId = 'icon-button-tooltip-'.md5($normalizedIcon.'|'.$size.'|'.$color.'|'.($badgeText ?? 'dot').'|'.($latestNotification ?? ''));
@endphp

<button
    type="button"
    {{ $attributes->merge([
        'class' => trim(implode(' ', [
            'group relative inline-flex items-center justify-center rounded-full bg-white transition-all duration-200 ease-out',
            'hover:bg-slate-100 hover:shadow-md',
            'focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2',
            'active:scale-[0.98]',
            'disabled:cursor-not-allowed disabled:opacity-50',
            $buttonSize,
        ])),
    ]) }}
    @if(! empty($onClick)) x-on:click="{{ $onClick }}" @endif
    @if(! is_null($pressed)) aria-pressed="{{ $pressed ? 'true' : 'false' }}" @endif
    aria-label="{{ $ariaLabel }}"
    @if($latestNotification) aria-describedby="{{ $badgeTooltipId }}" @endif
    x-data="{ tooltipOpen: false }"
    x-on:mouseenter="tooltipOpen = true"
    x-on:mouseleave="tooltipOpen = false"
    x-on:focus="tooltipOpen = true"
    x-on:blur="tooltipOpen = false"
>
    <span class="sr-only" aria-live="polite">{{ $ariaLabel }}</span>

    <x-dynamic-component :component="'heroicon-' . $normalizedIcon" class="{{ $buttonIconSize }} {{ $buttonColor }}" />

    @if($hasBadge)
        <span
            class="absolute -right-0.5 -top-0.5 inline-flex min-h-4 min-w-4 items-center justify-center rounded-full border border-white bg-emerald-500 px-1 text-[10px] font-semibold leading-none text-white shadow-sm"
            aria-hidden="true"
        >
            @if($isDotBadge)
                <span class="h-2 w-2 rounded-full bg-white"></span>
            @else
                {{ $badgeText }}
            @endif
        </span>
    @endif

    @if($latestNotification)
        <span
            id="{{ $badgeTooltipId }}"
            x-cloak
            x-show="tooltipOpen"
            x-transition.opacity.duration.150ms
            role="tooltip"
            class="absolute -top-11 left-1/2 z-20 -translate-x-1/2 whitespace-nowrap rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white shadow-lg"
        >
            {{ $latestNotification }}
        </span>
    @endif
</button>