<div class="space-y-6">
    @include('admin.units.question.partials.forms.description', ['form' => $form])
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-700">
        <i class="fa-solid fa-circle-info mr-2"></i>
        Nama dan jumlah kompetitor diisi oleh masing-masing responden (minimal 1, maksimal 10).
    </div>
    @include('admin.units.question.partials.forms.question-list', ['form' => $form, 'competitors' => collect()])
    @include('admin.units.question.partials.forms.toolbar', ['form' => $form])
</div>
