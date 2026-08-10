@extends('layouts.app')

@section('title', 'Monitoring Survey')

@section('content')

<div class="min-h-screen bg-gray-50">

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        @include('monitoring.partials.header')


        {{-- ========================================================= --}}
        {{-- STATUS CARDS --}}
        {{-- ========================================================= --}}

        @include('monitoring.partials.cards')


        {{-- ========================================================= --}}
        {{-- FILTER --}}
        {{-- ========================================================= --}}

        @include('monitoring.partials.filter')


        {{-- ========================================================= --}}
        {{-- TABLE RESPONDEN --}}
        {{-- ========================================================= --}}

        @include('monitoring.partials.table')

    </div>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const group = document.getElementById('monitoringGroup');
        const unit = document.getElementById('monitoringUnit');
        if (!group || !unit) return;

        const filterUnits = function () {
            const selectedGroup = group.value;
            Array.from(unit.options).forEach(function (option) {
                if (!option.value) return;
                const visible = !selectedGroup || option.dataset.groupId === selectedGroup;
                option.hidden = !visible;
                option.disabled = !visible;
            });

            const selected = unit.options[unit.selectedIndex];
            if (selected && selected.disabled) unit.value = '';
        };

        group.addEventListener('change', filterUnits);
        filterUnits();
    });
</script>
@endpush
