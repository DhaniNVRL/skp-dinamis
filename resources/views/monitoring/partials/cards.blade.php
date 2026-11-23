<section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
    @php
        $activeStatus = $filters['status'] ?? null;
        $cards = [
            ['status' => null, 'label' => 'Total Responden', 'value' => $totalRespondents, 'colors' => 'border-blue-100 bg-blue-50 text-blue-700', 'value_colors' => 'text-blue-950', 'ring' => 'ring-blue-300'],
            ['status' => 'completed', 'label' => 'Sudah Mengisi', 'value' => $completedCount, 'colors' => 'border-emerald-100 bg-emerald-50 text-emerald-700', 'value_colors' => 'text-emerald-950', 'ring' => 'ring-emerald-300'],
            ['status' => 'in_progress', 'label' => 'Sedang Mengisi', 'value' => $inProgressCount, 'colors' => 'border-amber-100 bg-amber-50 text-amber-700', 'value_colors' => 'text-amber-950', 'ring' => 'ring-amber-300'],
            ['status' => 'not_started', 'label' => 'Belum Mengisi', 'value' => $notStartedCount, 'colors' => 'border-red-100 bg-red-50 text-red-700', 'value_colors' => 'text-red-950', 'ring' => 'ring-red-300'],
        ];
    @endphp

    @foreach ($cards as $card)
        <a href="{{ request()->fullUrlWithQuery(['status' => $card['status'], 'page' => null]) }}"
           aria-label="Filter {{ $card['label'] }}"
           @if ($activeStatus === $card['status']) aria-current="true" @endif
           class="rounded-2xl border p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {{ $card['colors'] }} {{ $activeStatus === $card['status'] ? $card['ring'].' ring-2' : '' }}">
            <p class="text-sm font-semibold">{{ $card['label'] }}</p>
            <p class="mt-2 text-3xl font-bold {{ $card['value_colors'] }}">{{ number_format($card['value']) }}</p>
            <p class="mt-2 text-xs">Klik untuk memfilter daftar responden</p>
        </a>
    @endforeach

    <section class="rounded-2xl border border-violet-100 bg-violet-50 p-5 text-violet-700 shadow-sm">
        <p class="text-sm font-semibold">Persentase Penyelesaian</p>
        <p class="mt-2 text-3xl font-bold text-violet-950">{{ number_format($completionPercentage, 2, ',', '.') }}%</p>
        <p class="mt-2 text-xs">Responden sudah mengisi ÷ total responden</p>
    </section>
</section>
