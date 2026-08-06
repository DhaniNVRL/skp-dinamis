@include(
    'user.survey.forms.partials.feedback-assessment',
    [
        'feedbackFields' => [
            [
                'key' => 'strength',
                'label' => 'Keunggulan',
                'placeholder' => 'Tuliskan keunggulan...',
                'wrapperClass' =>
                    'border-emerald-200 bg-emerald-50',
                'labelClass' =>
                    'text-emerald-700',
                'focusClass' =>
                    'focus:border-emerald-500 focus:ring-emerald-100',
                'icon' =>
                    'fa-solid fa-thumbs-up',
            ],
            [
                'key' => 'complaint',
                'label' => 'Keluhan',
                'placeholder' => 'Tuliskan keluhan...',
                'wrapperClass' =>
                    'border-red-200 bg-red-50',
                'labelClass' =>
                    'text-red-700',
                'focusClass' =>
                    'focus:border-red-500 focus:ring-red-100',
                'icon' =>
                    'fa-solid fa-comment-dots',
            ],
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