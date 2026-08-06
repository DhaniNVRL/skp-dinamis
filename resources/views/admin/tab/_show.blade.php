<div class="w-full px-6 py-6">
    <div class="max-w-[1600px] mx-auto space-y-6">

        @foreach ($forms->sortBy('no_urut') as $form)

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- HEADER --}}
                <div class="flex justify-between items-start gap-6 p-6 border-b bg-gray-50">

                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold text-gray-800">
                            {{ $form->name }}
                        </h1>

                        <div class="mt-2">
                            <span class="inline-flex items-center gap-2 text-sm px-3 py-1
                                         bg-indigo-100 text-indigo-700 rounded-full">
                                <span class="font-medium">{{ $form->formtype->name }}</span>
                                <span class="text-gray-400">•</span>
                                <span>{{ $form->formtype->description }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- CONTENT --}}
                <div class="p-6 space-y-6">

                    {{-- QUESTIONS WRAPPER --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 md:p-6">

                        @switch($form->id_formtype)

                            @case(1)
                                @include('admin.show.kuesioner_umum', ['questions' => $form->questions])
                                @break

                            @case(2)
                                @include('admin.show.penilaian_pelanggan_1-5', ['questions' => $form->questions])
                                @break

                            @case(3)
                                @include('admin.show.penilaian_pelanggan_1-7', ['questions' => $form->questions])
                                @break

                            @case(4)
                                @include('admin.show.penilaian_keterikatan_1-5', ['questions' => $form->questions])
                                @break

                            @case(5)
                                @include('admin.show.penilaian_keterikatan_1-7', ['questions' => $form->questions])
                                @break

                            @case(8)
                                @include('admin.show.keunggulan_keluhan_saran', ['questions' => $form->questions])
                                @break

                            @case(6)
                                @include('admin.show.rangking_1-3', ['questions' => $form->questions])
                                @break

                            @case(7)
                                @include('admin.show.rangking_1-5', ['questions' => $form->questions])
                                @break

                            @case(9)
                                @include('admin.show.keluhan_saran', ['questions' => $form->questions])
                                @break

                            @case(10)
                                @include('admin.show.saran', ['questions' => $form->questions])
                                @break

                            @case(11)
                                @include('admin.show.kompetitor', ['questions' => $form->questions])
                                @break

                            @case(12)
                                @include('admin.show.description', ['questions' => $form->questions])
                                @break

                            @default
                                <p class="text-red-500 font-medium">
                                    Form tidak ditemukan
                                </p>

                        @endswitch

                        
                        @include('admin.components.option-template')

                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>