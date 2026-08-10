@php
    $globalAlerts = [];

    if ($errors->any() && ! View::hasSection('suppressGlobalValidationErrors')) {
        $globalAlerts[] = [
            'type' => 'error',
            'title' => 'Validasi gagal ('.$errors->count().' kesalahan)',
            'messages' => $errors->all(),
        ];
    }

    foreach ([
        'error' => ['error', 'Proses gagal'],
        'warning' => ['warning', 'Perhatian'],
        'success' => ['success', 'Berhasil'],
        'successdelete' => ['error', 'Data berhasil dihapus'],
        'status' => ['success', 'Berhasil'],
        'finish' => ['success', 'Berhasil'],
    ] as $key => [$type, $title]) {
        if (session()->has($key) && filled(session($key))) {
            $globalAlerts[] = [
                'type' => $type,
                'title' => $title,
                'messages' => [(string) session($key)],
            ];
        }
    }

    $globalAlertStyles = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'error' => 'border-red-200 bg-red-50 text-red-800',
    ];

    $globalAlertIcons = [
        'success' => 'fa-circle-check text-emerald-600',
        'warning' => 'fa-triangle-exclamation text-amber-600',
        'error' => 'fa-circle-exclamation text-red-600',
    ];
@endphp

@if ($globalAlerts !== [])
    <div
        id="globalAlertContainer"
        class="mb-6 flex w-full flex-col gap-3"
        role="region"
        aria-label="Notifikasi aplikasi"
        aria-live="polite"
    >
        @foreach ($globalAlerts as $alert)
            <div
                data-global-alert
                data-alert-type="{{ $alert['type'] }}"
                class="-translate-y-2 rounded-lg border p-4 opacity-0 shadow-sm transition duration-200 {{ $globalAlertStyles[$alert['type']] }}"
                role="{{ $alert['type'] === 'error' ? 'alert' : 'status' }}"
            >
                <div class="flex items-start gap-3">
                    <i class="fa-solid {{ $globalAlertIcons[$alert['type']] }} mt-0.5 text-lg" aria-hidden="true"></i>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold">{{ $alert['title'] }}</p>
                        @if (count($alert['messages']) === 1)
                            <p class="mt-1 break-words text-sm">{{ $alert['messages'][0] }}</p>
                        @else
                            <ul class="mt-2 max-h-48 list-disc space-y-1 overflow-y-auto pl-5 text-sm">
                                @foreach ($alert['messages'] as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <button
                        type="button"
                        data-global-alert-close
                        class="rounded p-1 opacity-70 transition hover:bg-black/5 hover:opacity-100"
                        aria-label="Tutup notifikasi"
                    >
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif
