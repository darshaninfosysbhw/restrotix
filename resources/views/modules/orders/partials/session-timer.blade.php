@php
    $sessionScope = $sessionOrderId ?: $selectedTableId ?: $selectedTableNumber ?: 'default';
    $sessionTimerId = 'session-timer-' . preg_replace('/[^A-Za-z0-9_-]/', '-', (string) $sessionScope);
    $sessionStartedAtValue = $sessionStartedAt ?? null;
    $sessionEndedAtValue = $sessionEndedAt ?? null;
@endphp

<div class="inline-flex min-w-[60px] items-center gap-1 rounded-xl bg-gray-700 p-2 text-center">
    <p class="text-[11px] text-gray-400">Session :</p>
    <p id="{{ $sessionTimerId }}" class="text-[10px] font-bold text-white"
        data-session-started-at="{{ $sessionStartedAtValue }}" data-session-ended-at="{{ $sessionEndedAtValue }}">
        0m
    </p>
</div>

<script>
    (() => {
        const timerEl = document.getElementById(@json($sessionTimerId));
        if (!timerEl) return;

        const startedAtRaw = String(timerEl.dataset.sessionStartedAt || '').trim();
        const endedAtRaw = String(timerEl.dataset.sessionEndedAt || '').trim();

        const parseTimestamp = (value) => {
            if (!value) return null;
            const timestamp = Date.parse(value);
            return Number.isFinite(timestamp) ? timestamp : null;
        };

        const formatDuration = (ms) => {
            const totalMinutes = Math.max(0, Math.floor(ms / 60000));
            const hours = Math.floor(totalMinutes / 60);
            const minutes = totalMinutes % 60;

            if (hours > 0) {
                return `${hours}h ${minutes}m`;
            }

            return `${totalMinutes}m`;
        };

        const startedAt = parseTimestamp(startedAtRaw);
        const endedAt = parseTimestamp(endedAtRaw);

        if (!startedAt) {
            timerEl.textContent = '0m';
            return;
        }

        const render = () => {
            const targetTime = endedAt ?? Date.now();
            timerEl.textContent = formatDuration(targetTime - startedAt);
        };

        render();

        if (!endedAt) {
            window.setInterval(render, 15000);
        }
    })();
</script>
