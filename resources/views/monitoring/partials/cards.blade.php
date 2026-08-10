<section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @php
        $activeStatus = $filters['status'] ?? null;
        $cards = [
            ['status' => null, 'label' => 'Total Responden', 'value' => $totalRespondents, 'class' => 'bg-blue-50 text-blue-700', 'ring' => 'border-blue-500 ring-blue-100'],
            ['status' => 'completed', 'label' => 'Sudah Mengisi', 'value' => $completedCount, 'class' => 'bg-emerald-50 text-emerald-700', 'ring' => 'border-emerald-500 ring-emerald-100'],
            ['status' => 'in_progress', 'label' => 'Sedang Mengisi', 'value' => $inProgressCount, 'class' => 'bg-amber-50 text-amber-700', 'ring' => 'border-amber-500 ring-amber-100'],
            ['status' => 'not_started', 'label' => 'Belum Mengisi', 'value' => $notStartedCount, 'class' => 'bg-gray-100 text-gray-700', 'ring' => 'border-gray-500 ring-gray-200'],
        ];
    @endphp

    @foreach ($cards as $card)
        <a href="{{ request()->fullUrlWithQuery(['status' => $card['status'], 'page' => null]) }}"
           aria-label="Filter {{ $card['label'] }}"
           @if ($activeStatus === $card['status']) aria-current="true" @endif
           class="rounded-2xl border bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {{ $activeStatus === $card['status'] ? $card['ring'].' ring-2' : 'border-gray-200' }}">
            <div class="inline-flex rounded-lg px-3 py-1 text-xs font-semibold {{ $card['class'] }}">
                {{ $card['label'] }}
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($card['value']) }}</p>
        </a>
    @endforeach
</section>
