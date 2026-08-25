@props([
    'label' => 'Coming Soon',
    'feature' => null,
    'title' => 'Coming Soon',
    'message' => null,
    'icon' => 'fas fa-bullhorn',
    'modalIcon' => null,
    'tone' => 'slate',
    'type' => 'button',
    'href' => null,
])

@php
    $featureText = trim((string) ($feature ?? ''));
    $resolvedModalIcon = trim((string) ($modalIcon ?: $icon));
    $resolvedMessage = trim(
        (string) ($message ?:
        ($featureText !== ''
            ? "{$featureText} is under development and will be available soon."
            : 'This feature is under development and will be available soon.')),
    );
@endphp

@if ($href)
    <a href="{{ $href }}"
        {{ $attributes->merge([
            'role' => 'button',
            'aria-haspopup' => 'dialog',
            'aria-controls' => 'comingSoonModal',
            'data-coming-soon-trigger' => '1',
            'data-coming-soon-title' => $title,
            'data-coming-soon-feature' => $featureText,
            'data-coming-soon-message' => $resolvedMessage,
            'data-coming-soon-icon' => $resolvedModalIcon,
            'data-coming-soon-tone' => strtolower((string) $tone),
        ]) }}>
        {{ $label }}
    </a>
@else
    <button type="{{ $type }}"
        {{ $attributes->merge([
            'aria-haspopup' => 'dialog',
            'aria-controls' => 'comingSoonModal',
            'data-coming-soon-trigger' => '1',
            'data-coming-soon-title' => $title,
            'data-coming-soon-feature' => $featureText,
            'data-coming-soon-message' => $resolvedMessage,
            'data-coming-soon-icon' => $resolvedModalIcon,
            'data-coming-soon-tone' => strtolower((string) $tone),
        ]) }}>
        {{ $label }}
    </button>
@endif

@once
    <style>
        .coming-soon-trigger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .6rem;
            min-height: 48px;
            padding: .75rem 1rem;
            border: 1px solid transparent;
            border-radius: .95rem;
            font-size: .875rem;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: .01em;
            text-align: center;
            text-decoration: none;
            transition:
                transform .18s ease,
                box-shadow .18s ease,
                border-color .18s ease,
                background-color .18s ease,
                color .18s ease,
                opacity .18s ease;
            box-shadow: 0 10px 22px rgba(15, 23, 42, .08);
            cursor: pointer;
            user-select: none;
        }

        .coming-soon-trigger:hover {
            transform: translateY(-1px);
        }

        .coming-soon-trigger:focus-visible {
            outline: none;
            box-shadow:
                0 0 0 3px rgba(249, 115, 22, .18),
                0 12px 24px rgba(15, 23, 42, .08);
        }

        .coming-soon-trigger-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.75rem;
            height: 1.75rem;
            border-radius: .65rem;
            flex-shrink: 0;
            font-size: .78rem;
            background: rgba(255, 255, 255, .08);
            color: inherit;
        }

        .coming-soon-tone-slate {
            background: rgba(51, 65, 85, .82);
            border-color: rgba(71, 85, 105, .92);
            color: #e2e8f0;
        }

        .coming-soon-tone-blue {
            background: rgba(37, 99, 235, .12);
            border-color: rgba(59, 130, 246, .38);
            color: #bfdbfe;
        }

        .coming-soon-tone-indigo {
            background: rgba(79, 70, 229, .12);
            border-color: rgba(99, 102, 241, .38);
            color: #c7d2fe;
        }

        .coming-soon-tone-orange {
            background: rgba(249, 115, 22, .12);
            border-color: rgba(249, 115, 22, .38);
            color: #fed7aa;
        }

        .coming-soon-tone-emerald {
            background: rgba(16, 185, 129, .12);
            border-color: rgba(16, 185, 129, .34);
            color: #a7f3d0;
        }

        .coming-soon-tone-rose {
            background: rgba(244, 63, 94, .12);
            border-color: rgba(244, 63, 94, .34);
            color: #fecdd3;
        }

        body.light-theme .coming-soon-trigger {
            box-shadow: 0 8px 22px rgba(15, 23, 42, .06);
        }

        body.light-theme .coming-soon-tone-slate {
            background: rgba(248, 250, 252, .98);
            border-color: rgba(226, 232, 240, .95);
            color: #0f172a;
        }

        body.light-theme .coming-soon-tone-blue {
            background: rgba(59, 130, 246, .08);
            border-color: rgba(59, 130, 246, .24);
            color: #1d4ed8;
        }

        body.light-theme .coming-soon-tone-indigo {
            background: rgba(99, 102, 241, .08);
            border-color: rgba(99, 102, 241, .24);
            color: #4338ca;
        }

        body.light-theme .coming-soon-tone-orange {
            background: rgba(249, 115, 22, .09);
            border-color: rgba(249, 115, 22, .28);
            color: #c2410c;
        }

        body.light-theme .coming-soon-tone-emerald {
            background: rgba(16, 185, 129, .09);
            border-color: rgba(16, 185, 129, .26);
            color: #047857;
        }

        body.light-theme .coming-soon-tone-rose {
            background: rgba(244, 63, 94, .08);
            border-color: rgba(244, 63, 94, .24);
            color: #be123c;
        }

        .coming-soon-modal {
            z-index: 180;
        }

        .coming-soon-backdrop {
            background: rgba(2, 6, 23, .78);
            backdrop-filter: blur(16px);
        }

        body.light-theme .coming-soon-backdrop {
            background: rgba(15, 23, 42, .42);
        }

        .coming-soon-panel {
            position: relative;
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, .1);
            background: linear-gradient(180deg, rgba(15, 23, 42, .98), rgba(17, 24, 39, .96));
            color: #f8fafc;
            box-shadow: 0 30px 80px rgba(2, 6, 23, .55);
        }

        body.light-theme .coming-soon-panel {
            background: rgba(255, 255, 255, .98);
            border-color: rgba(148, 163, 184, .32);
            color: #0f172a;
            box-shadow: 0 28px 60px rgba(15, 23, 42, .16);
        }

        .coming-soon-icon-shell {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 4.5rem;
            height: 4.5rem;
            border-radius: 9999px;
            border: 1px solid rgba(249, 115, 22, .35);
            background:
                radial-gradient(circle at top, rgba(249, 115, 22, .28), rgba(249, 115, 22, .08)),
                rgba(255, 255, 255, .05);
            color: #fb923c;
            box-shadow: 0 0 0 6px rgba(249, 115, 22, .08);
        }

        body.light-theme .coming-soon-icon-shell {
            background:
                radial-gradient(circle at top, rgba(249, 115, 22, .16), rgba(249, 115, 22, .06)),
                rgba(255, 255, 255, .9);
            border-color: rgba(249, 115, 22, .24);
            box-shadow: 0 0 0 6px rgba(249, 115, 22, .06);
        }

        .coming-soon-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            border-radius: 9999px;
            border: 1px solid rgba(249, 115, 22, .22);
            background: rgba(249, 115, 22, .12);
            padding: .35rem .8rem;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: #fdba74;
        }

        body.light-theme .coming-soon-chip {
            border-color: rgba(249, 115, 22, .18);
            background: rgba(249, 115, 22, .08);
            color: #c2410c;
        }

        .coming-soon-title {
            margin-top: 1rem;
            font-size: 1.6rem;
            font-weight: 900;
            letter-spacing: -.03em;
        }

        .coming-soon-message {
            margin-top: .85rem;
            font-size: .95rem;
            line-height: 1.75;
            color: #cbd5e1;
        }

        body.light-theme .coming-soon-message {
            color: #475569;
        }

        .coming-soon-close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            width: 100%;
            min-height: 48px;
            border-radius: .95rem;
            border: 1px solid rgba(255, 255, 255, .1);
            background: rgba(255, 255, 255, .06);
            color: #e2e8f0;
            font-size: .9rem;
            font-weight: 800;
            transition:
                transform .18s ease,
                background-color .18s ease,
                border-color .18s ease,
                color .18s ease;
        }

        .coming-soon-close:hover {
            transform: translateY(-1px);
            border-color: rgba(249, 115, 22, .28);
            background: rgba(249, 115, 22, .12);
            color: #fdba74;
        }

        .coming-soon-close:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, .18);
        }

        body.light-theme .coming-soon-close {
            border-color: rgba(226, 232, 240, .95);
            background: #f8fafc;
            color: #0f172a;
        }

        body.light-theme .coming-soon-close:hover {
            background: rgba(249, 115, 22, .08);
            border-color: rgba(249, 115, 22, .22);
            color: #c2410c;
        }

        .coming-soon-close-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, .1);
            background: rgba(255, 255, 255, .05);
            color: #cbd5e1;
            transition:
                transform .18s ease,
                background-color .18s ease,
                border-color .18s ease,
                color .18s ease;
        }

        .coming-soon-close-icon:hover {
            transform: translateY(-1px);
        }

        .coming-soon-close-icon:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, .18);
        }

        body.light-theme .coming-soon-close-icon {
            border-color: rgba(226, 232, 240, .95);
            background: #f8fafc;
            color: #475569;
        }

        body.light-theme .coming-soon-close-icon:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
    </style>

    <div id="comingSoonModal" class="coming-soon-modal fixed inset-0 hidden items-center justify-center px-4 py-6"
        role="dialog" aria-modal="true" aria-hidden="true">
        <div data-coming-soon-overlay
            class="coming-soon-backdrop absolute inset-0 opacity-0 transition-opacity duration-200 ease-out">
        </div>

        <div class="relative z-10 flex w-full items-center justify-center">
            <div data-coming-soon-panel
                class="coming-soon-panel w-full max-w-[440px] translate-y-4 scale-95 opacity-0 px-5 py-5 sm:px-6 sm:py-6 transition-all duration-200 ease-out">
                <button type="button" data-coming-soon-close aria-label="Close coming soon modal"
                    class="coming-soon-close-icon absolute right-4 top-4 cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>

                <div class="flex flex-col items-center text-center">
                    @if ($resolvedModalIcon !== '')
                        <div class="coming-soon-icon-shell" data-coming-soon-icon-shell>
                            <i data-coming-soon-icon class="{{ $resolvedModalIcon }} text-2xl"></i>
                        </div>
                    @endif

                    <span data-coming-soon-chip class="coming-soon-chip mt-5 hidden"></span>
                    <h3 data-coming-soon-title class="coming-soon-title">{{ $title }}</h3>
                    <p data-coming-soon-message class="coming-soon-message">{{ $resolvedMessage }}</p>
                </div>

                <button type="button" data-coming-soon-close class="coming-soon-close mt-7 cursor-pointer">
                    Got it
                </button>
            </div>
        </div>
    </div>
@endonce
