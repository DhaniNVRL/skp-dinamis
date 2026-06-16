<div class="w-full px-6 py-6">

    <!-- Tombol Form -->
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
                           class="text-yellow-500 hover:text-yellow-600 transition" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>

                        <form action="{{ route('forms.copy', $form->id) }}" method="POST">
                            @csrf
                            <button class="text-blue-500 hover:text-blue-600 transition" title="Copy">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </form>

                        <form action="{{ route('forms.destroy', $form->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 hover:text-red-600 transition" title="Delete">
                                <i class="fa fa-trash"></i>
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
                <div class="bg-gray-50 border rounded p-4">

                    @switch($form->id_formtype)

                        @case(1)
                            @include('admin.forms.kuesioner_umum', [
                                'questions' => $form->questions
                            ])
                            @break

                        @case(2)
                            @include('admin.forms.penilaian_pelanggan_1-5', [
                                'questions' => $form->questions
                            ])
                            @break

                        @case(3)
                            @include('admin.forms.penilaian_pelanggan_1-7', [
                                'questions' => $form->questions
                            ])
                            @break

                        @case(4)
                            @include('admin.forms.penilaian_keterikatan_1-5', [
                                'questions' => $form->questions
                            ])
                            @break

                        @case(5)
                            @include('admin.forms.penilaian_keterikatan_1-7', [
                                'questions' => $form->questions
                            ])
                            @break

                        @case(6)
                           @include('admin.forms.keunggulan_keluhan_saran', [
                               'questions' => $form->questions
                           ])
                           @break
                            @break

                         @case(7)
                            @include('admin.forms.rangking_1-3', [
                                'questions' => $form->questions
                            ])
                            @break

                         @case(8)
                            @include('admin.forms.rangking_1-5', [
                                'questions' => $form->questions
                            ])
                            @break

                         @case(9)
                            @include('admin.forms.keluhan_saran', [
                                'questions' => $form->questions
                            ])
                            @break

                        @default
                            <p class="text-red-500">Form tidak ditemukan</p>

                    @endswitch

                    @include('admin.components.option-template')

                </div>

                <!-- FORM SUBMIT JAWABAN -->
                <form action="{{ route('answer.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_id" value="{{ $form->id }}">

                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded mt-6">
                        Submit Jawaban
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</div>
