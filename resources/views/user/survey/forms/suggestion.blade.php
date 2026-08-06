@include(
    'user.survey.forms.partials.feedback-assessment',
    [
        'feedbackFields' => [
            [
                'key' => 'suggestion',
                'label' => 'Saran',
                'placeholder' => 'Tuliskan saran...',
                'wrapperClass' =>
                    'border-blue-200 bg-blue-50',
                'labelClass' =>
                    'text-blue-700',
                'focusClass' =>
                    'focus:border-blue-500 focus:ring-blue-100',
                'icon' =>
                    'fa-solid fa-lightbulb',
            ],
        ],
    ]
)