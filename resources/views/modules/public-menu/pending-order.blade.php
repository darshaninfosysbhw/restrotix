<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="no-referrer">
    <title>Order confirmation</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 text-gray-900">
    <main class="max-w-lg mx-auto p-5 py-10">
        @if ($showOrderPlaced)
            <div role="status" class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-800">
                <p class="font-bold text-lg">Order Placed Successfully!</p>
                <p class="mt-1 text-sm">Your order has reached the restaurant. Staff confirmation is pending.</p>
            </div>
        @endif
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <p class="text-sm text-gray-500">Table {{ $submission->table->table_number }}</p>
            <h1 id="confirmationTitle" class="text-2xl font-bold mt-2">{{ $submission->status === 'rejected' ? 'Order not accepted' : 'Awaiting restaurant confirmation' }}</h1>
            <p id="confirmationMessage" class="text-sm text-gray-600 mt-3" aria-live="polite">{{ $submission->status === 'rejected' ? $submission->rejection_reason : 'Your request has reached the staff. Cooking starts after they confirm it. Please do not submit it again.' }}</p>
            <ul class="divide-y divide-gray-100 my-5">
                @foreach ($submission->payload['items'] as $item)
                    <li class="py-3 text-sm break-words">
                        {{ $item['quantity'] }} × {{ $item['name'] }} {{ $item['variant_name'] ?? '' }}
                        @foreach ($item['addons'] ?? [] as $addon)
                            <span class="block text-xs text-gray-500">+ {{ $addon['quantity'] }} × {{ $addon['name'] }}</span>
                        @endforeach
                        @if (!empty($item['notes']))<span class="block text-xs text-gray-500">{{ $item['notes'] }}</span>@endif
                    </li>
                @endforeach
            </ul>
            <p class="font-semibold">Total: {{ $submission->branch?->currency?->symbol }} {{ number_format((float) $submission->total, 2) }}</p>
            <p id="connectionMessage" class="text-xs text-gray-500 mt-4"></p>
            @if (\App\Models\Order::where('table_id', $submission->table_id)->where('status', 'running')->exists())
                <a href="{{ route('public.order.status', ['qr_token' => $submission->table->qr_token]) }}" class="inline-block mt-5 text-sm text-orange-600">Track accepted items</a>
            @endif
        </div>
    </main>
    <script>
        (() => {
            let status = @json($submission->status);
            const url = @json(route('qr-submissions.status', $submission->public_token));
            async function check() {
                if (status !== 'pending') return;
                try {
                    const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: AbortSignal.timeout(10000) });
                    if (!response.ok) throw new Error('Unable to check status');
                    const data = await response.json();
                    status = data.status;
                    document.getElementById('connectionMessage').textContent = '';
                    if (data.redirect_url) { window.location.replace(data.redirect_url); return; }
                    if (status === 'rejected') {
                        document.getElementById('confirmationTitle').textContent = 'Order not accepted';
                        document.getElementById('confirmationMessage').textContent = data.reason || 'Please speak to your waiter.';
                    }
                } catch (_) {
                    document.getElementById('connectionMessage').textContent = 'Reconnecting… Your submitted request is saved.';
                }
                if (status === 'pending') setTimeout(check, 3000);
            }
            check();
        })();
    </script>
</body>
</html>
