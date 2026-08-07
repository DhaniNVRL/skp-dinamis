<div class="mt-3 border-t border-gray-100 pt-3">

    @if (!empty($option->answer_text2))

        <p class="mb-2 text-sm font-medium text-red-600">
            {{ $option->answer_text2 }}
        </p>

    @endif


    <textarea
        disabled
        rows="2"
        placeholder="{{ $option->answer_text2 ?: 'Jawaban tambahan' }}"
        class="w-full resize-none rounded-lg
               border border-gray-300 bg-gray-50
               px-3 py-2 text-sm text-gray-500"
    ></textarea>

</div>
