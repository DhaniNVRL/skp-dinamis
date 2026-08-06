<div class="space-y-6">

    @include(
        'admin.units.question.partials.forms.description',
        [
            'form' => $form,
        ]
    )

    @include(
        'admin.units.question.partials.forms.question-list',
        [
            'form' => $form,
        ]
    )

    @include(
        'admin.units.question.partials.forms.toolbar',
        [
            'form' => $form,
        ]
    )

</div>