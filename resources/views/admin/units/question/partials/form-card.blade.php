<div class="space-y-6">
    @forelse ($forms->sortBy('no_urut') as $form)
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- HEADER --}}
            <div class="flex items-start justify-between gap-4 px-6 py-5 border-b border-gray-200">
                <div class="min-w-0">
                    <div class="flex items-center gap-3">
                        {{-- Nomor urut --}}
                        <span
                            class="flex items-center justify-center
                                   min-w-8 h-8 px-2
                                   bg-indigo-100 text-indigo-700
                                   text-sm font-semibold rounded-lg">
                            {{ $form->no_urut }}
                        </span>
                        {{-- Nama form --}}
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ $form->name }}
                        </h3>
                    </div>
                    {{-- Form Type --}}
                    @if ($form->formtype)

                        <div class="mt-3">

                            <span
                                class="inline-flex items-center
                                       px-3 py-1
                                       text-xs font-medium
                                       bg-indigo-50 text-indigo-700
                                       rounded-full">

                                {{ $form->formtype->name }}

                                @if ($form->formtype->description)
                                    <span class="mx-1">
                                        —
                                    </span>

                                    {{ $form->formtype->description }}
                                @endif

                            </span>

                        </div>

                    @endif

                </div>


                {{-- Action --}}
                @include(
                    'admin.units.question.partials.form-action',
                    ['form' => $form]
                )

            </div>


            {{-- BODY --}}
            <div class="p-6 bg-gray-50">

                @switch($form->formtype_id)

                    @case(1)

                        @include(
                            'admin.units.question.partials.forms.general-questionnaire.index',
                            [
                                'form' => $form,
                                'questions' => $form->questions
                            ]
                        )

                    @break


                    @case(2)

                        @include(
                            'admin.units.question.partials.forms.customer-assessment-1-5.index',
                            [
                                'form' => $form,
                                'questions' => $form->questions
                            ]
                        )

                        @break


                    @case(3)

                        @include(
                            'admin.units.question.partials.forms.customer-assessment-1-7.index',
                            [
                                'form' => $form,
                                'questions' => $form->questions
                            ]
                        )

                        @break


                    @case(4)

                        @include(
                            'admin.units.question.partials.forms.engagement-assessment-1-5.index',
                            [
                                'form' => $form,
                                'questions' => $form->questions
                            ]
                        )

                        @break


                    @case(5)

                        @include(
                            'admin.units.question.partials.forms.engagement-assessment-1-7.index',
                            [
                                'form' => $form,
                                'questions' => $form->questions
                            ]
                        )

                        @break


                    @case(6)

                        @include(
                            'admin.units.question.partials.forms.ranking-1-3.index',
                            [
                                'form' => $form,
                                'questions' => $form->questions
                            ]
                        )

                        @break


                    @case(7)

                        @include(
                            'admin.units.question.partials.forms.ranking-1-5.index',
                            [
                                'form' => $form,
                                'questions' => $form->questions
                            ]
                        )

                        @break


                    @case(8)

                        @include(
                            'admin.units.question.partials.forms.strength-complaint-suggestion.index',
                            [
                                'form' => $form,
                                'questions' => $form->questions
                            ]
                        )

                        @break


                    @case(9)

                        @include(
                            'admin.units.question.partials.forms.complaint-suggestion.index',
                            [
                                'form' => $form,
                                'questions' => $form->questions
                            ]
                        )

                        @break


                    @case(10)

                        @include(
                            'admin.units.question.partials.forms.suggestion.index',
                            [
                                'form' => $form,
                                'questions' => $form->questions
                            ]
                        )

                        @break


                    @case(11)

                        @include(
                            'admin.units.question.partials.forms.competitor-1-5.index',
                            [
                                'form' => $form,
                                'questions' => $form->questions,
                                'competitors' => $competitors,
                            ]
                        )

                        @break


                    @case(12)

                        @include(
                                'admin.units.question.partials.forms.toolbar',
                                ['form' => $form]
                            )

                            @include(
                                'admin.units.question.partials.forms.description',
                                ['form' => $form]
                            )

                        @break


                    @case(13)

                        @include(
                            'admin.units.question.partials.forms.competitor-1-7.index',
                            [
                                'form' => $form,
                                'questions' => $form->questions,
                                'competitors' => $competitors,
                            ]
                        )

                        @break


                    @default

                        <div
                            class="p-4 text-sm text-red-600
                                   bg-red-50 border border-red-200 rounded-lg">

                            Form type tidak ditemukan.

                        </div>

                @endswitch

            </div>

        </div>

    @empty

        <div
            class="bg-white border border-gray-200
                   rounded-xl py-16 text-center">

            <div class="text-gray-300 text-4xl mb-3">
                <i class="fa-regular fa-file-lines"></i>
            </div>

            <h3 class="text-gray-700 font-medium">
                Belum ada form
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Klik "Tambah Form" untuk membuat form pertama.
            </p>

        </div>

    @endforelse

</div>
