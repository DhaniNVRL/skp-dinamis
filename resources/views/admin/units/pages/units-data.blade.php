<div
    id="unitPage"
    class="space-y-4"
    data-group-id="{{ $groups->id }}"
>
    @include('admin.units.units.partials.toolbar')
    @include('admin.units.units.partials.filter')
    @include('admin.units.units.partials.bulk-action')
    @include('admin.units.units.partials.table')
</div>
