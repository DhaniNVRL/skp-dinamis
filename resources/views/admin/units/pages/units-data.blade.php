<div
    id="unitPage"
    class="space-y-4"
    data-group-id="{{ $groups->id }}"
>
    {{-- NOTIFICATION --}}
    @if (session('success'))
        <div
            class="rounded-lg border border-green-200
                bg-green-50 px-4 py-3 text-sm text-green-700"
        >
            {{ session('success') }}
        </div>
    @endif

    @if (session('successdelete'))
        <div
            class="rounded-lg border border-green-200
                bg-green-50 px-4 py-3 text-sm text-green-700"
        >
            {{ session('successdelete') }}
        </div>
    @endif

    @if (session('error'))
        <div
            class="rounded-lg border border-red-200
                bg-red-50 px-4 py-3 text-sm text-red-700"
        >
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div
            class="rounded-lg border border-red-200
                bg-red-50 px-4 py-3 text-sm text-red-700"
        >
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('admin.units.units.partials.toolbar')
    @include('admin.units.units.partials.filter')
    @include('admin.units.units.partials.table')
</div>