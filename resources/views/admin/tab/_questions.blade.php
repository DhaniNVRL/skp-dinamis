<div class="w-full px-6 py-6">

    <!-- Tombol Add Form -->
    <button
        type="button"
        class="bg-green-600 text-white px-4 py-2 rounded mb-6"
        @click="
            $dispatch('open-modal-tab', {
                title: 'Add Form',
                manual: '{{ route('forms.storeforms') }}',
                group: '{{ $groups->id }}',
                content: document.getElementById('form-template').innerHTML
            })
        "
    >
        Add Form
    </button>

    <div class="max-w-[1600px] mx-auto space-y-6">

        @foreach ($forms as $form)
            <div class="bg-white rounded-xl shadow border-l-4 border-indigo-500 p-6">

                <!-- HEADER -->
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-2xl font-semibold text-gray-800">{{ $form->name }}</h1>

                    <div class="flex space-x-2">
                        <a href="{{ route('forms.edit', $form->id) }}"
                           class="px-3 py-1 bg-yellow-400 text-white rounded">
                            Edit
                        </a>

                        <form action="{{ route('forms.copy', $form->id) }}" method="POST">
                            @csrf
                            <button class="px-3 py-1 bg-blue-400 text-white rounded">
                                Copy
                            </button>
                        </form>

                        <form action="{{ route('forms.destroy', $form->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="px-3 py-1 bg-red-500 text-white rounded">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>

                <!-- FORM TYPE -->
                <div class="mb-4">
                    <span class="text-sm px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full">
                        {{ $form->formtype->name }} — {{ $form->formtype->description }}
                    </span>
                </div>

                <!-- ✅ FORM JAWABAN (1 FORM PER FORM) -->
                <form action="{{ route('answer.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_id" value="{{ $form->id }}">

                    <div class="space-y-4">
                        <div class="bg-gray-50 border rounded p-4">
                            @include('admin.forms.kuesioner_umum', [
                                'questions' => $form->questions
                            ])
                        </div>
                    </div>

                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded mt-6">
                        Submit Jawaban
                    </button>
                </form>

                <!-- ADD QUESTION -->
                <div class="mt-6">
                    <button
                        type="button"
                        class="bg-green-600 text-white px-4 py-2 rounded"
                        @click="
                            $dispatch('open-modal-tab', {
                                title: 'Add Question',
                                manual: '{{ route('question.store') }}',
                                group: '{{ $groups->id }}',
                                form: '{{ $form->id }}',
                                content: document.getElementById('question-form').innerHTML
                            })
                        "
                    >
                        Add Question
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>