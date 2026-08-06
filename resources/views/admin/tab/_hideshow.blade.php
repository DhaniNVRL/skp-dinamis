<div class="w-full px-6 py-6">

    <div class="max-w-[1600px] mx-auto space-y-6">

        @foreach ($forms as $form)
                       
            <div class="bg-white rounded-xl shadow border-l-4 border-indigo-500 p-6">

                <!-- HEADER -->
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-2xl font-semibold text-gray-800">{{ $form->name }}</h1>
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
                            @include('admin.hideshow.kuesioner_umum', [
                                'questions' => $form->questions
                            ])
                            @break

                        @case(2)
                            @include('admin.hideshow.penilaian_pelanggan_1-5', [
                                'questions' => $form->questions
                            ])
                            @break

                        @case(3)
                            @include('admin.hideshow.penilaian_pelanggan_1-7', [
                                'questions' => $form->questions
                            ])
                            @break

                        @case(4)
                            @include('admin.hideshow.penilaian_keterikatan_1-5', [
                                'questions' => $form->questions
                            ])
                            @break

                        @case(5)
                            @include('admin.hideshow.penilaian_keterikatan_1-7', [
                                'questions' => $form->questions
                            ])
                            @break

                        @case(8)
                           @include('admin.hideshow.keunggulan_keluhan_saran', [
                               'questions' => $form->questions
                           ])
                           @break
                            @break

                         @case(6)
                            @include('admin.hideshow.rangking_1-3', [
                                'questions' => $form->questions
                            ])
                            @break

                         @case(7)
                            @include('admin.hideshow.rangking_1-5', [
                                'questions' => $form->questions
                            ])
                            @break

                         @case(9)
                            @include('admin.hideshow.keluhan_saran', [
                                'questions' => $form->questions
                            ])
                            @break
                            
                         @case(10)
                            @include('admin.hideshow.saran', [
                                'questions' => $form->questions
                            ])
                            @break

                         @case(11)
                            @include('admin.hideshow.kompetitor', [
                                'questions' => $form->questions
                            ])
                            @break

                        @case(12)
                            @include('admin.hideshow.description', [
                                'questions' => $form->questions
                            ])
                            @break

                        @default
                            <p class="text-red-500">Form tidak ditemukan</p>

                    @endswitch

                    @include('admin.components.option-template')

                </div>
            </div>
        @endforeach
    </div>
</div>
