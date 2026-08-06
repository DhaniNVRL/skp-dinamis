@foreach ($questions->groupBy('no_header') as $noHeader => $group)

    {{-- ================= HEADER ================= --}}
    @foreach ($group->where('id_questiontypes', 1)->sortBy('no') as $question)
        <div class="mb-6 p-4 border rounded-lg bg-white shadow-sm">
            <div class="font-semibold text-gray-800 mb-4">
                {{ $question->no_header }}{{ $question->no }}.
                {{ $question->name }}
            </div>
            @foreach ($subunits as $subunit)
                <div class="flex items-center justify-between border rounded p-3 mb-2">
                    <span class="font-medium">
                        {{ $subunit->name }}
                    </span>
                    <button
                        type="button"
                        class="toggle-question w-8 h-8 rounded-full bg-blue-100 hover:bg-blue-200"
                        data-questionId="{{ $question->id }}"
                        data-subunit="{{ $subunit->id }}"
                        data-form="{{ $form->id }}"
                    >
                    @if(in_array($subunit->id, $activeMap[$question->id] ?? []))
                        <i class="fa fa-eye text-green-600"></i>
                    @else
                        <i class="fa fa-eye-slash text-red-500"></i>
                    @endif
                    </button>
                </div>
            @endforeach
        </div>
    @endforeach


    @foreach ($group->where('id_questiontypes', 2)->sortBy('no') as $question)
        <div class="mb-6 p-4 border rounded-lg bg-white shadow-sm">
            <div class="font-semibold text-gray-800 mb-4">
                {{ $question->no_header }}{{ $question->no }}.
                {{ $question->name }}
            </div>
            @foreach ($subunits as $subunit)
                <div class="flex items-center justify-between border rounded p-3 mb-2">
                    <span class="font-medium">
                        {{ $subunit->name }}
                    </span>
                    <button
                        type="button"
                        class="toggle-question w-8 h-8 rounded-full bg-blue-100 hover:bg-blue-200"
                        data-questionId="{{ $question->id }}"
                        data-subunit="{{ $subunit->id }}"
                        data-form="{{ $form->id }}"
                    >
                    @if(in_array($subunit->id, $activeMap[$question->id] ?? []))
                        <i class="fa fa-eye text-green-600"></i>
                    @else
                        <i class="fa fa-eye-slash text-red-500"></i>
                    @endif
                    </button>
                </div>
            @endforeach
        </div>
    @endforeach


    @foreach ($group->where('id_questiontypes', 3)->sortBy('no') as $question)
        <div class="mb-6 p-4 border rounded-lg bg-white shadow-sm">
            <div class="font-semibold text-gray-800 mb-4">
                {{ $question->no_header }}{{ $question->no }}.
                {{ $question->name }}
            </div>
            @foreach ($subunits as $subunit)
                <div class="flex items-center justify-between border rounded p-3 mb-2">
                    <span class="font-medium">
                        {{ $subunit->name }}
                    </span>
                    <button
                        type="button"
                        class="toggle-question w-8 h-8 rounded-full bg-blue-100 hover:bg-blue-200"
                        data-questionId="{{ $question->id }}"
                        data-subunit="{{ $subunit->id }}"
                        data-form="{{ $form->id }}"
                    >
                    @if(in_array($subunit->id, $activeMap[$question->id] ?? []))
                        <i class="fa fa-eye text-green-600"></i>
                    @else
                        <i class="fa fa-eye-slash text-red-500"></i>
                    @endif
                    </button>
                </div>
            @endforeach
        </div>
     @endforeach


    {{-- ================= SUB INDIKATOR ================= --}}
    @foreach ($group->where('id_questiontypes', 4)->sortBy('no') as $question)
        <div class="mb-6 p-4 border rounded-lg bg-white shadow-sm">
            <div class="font-semibold text-gray-800 mb-4">
                {{ $question->no_header }}{{ $question->no }}.
                {{ $question->name }}
            </div>
            @foreach ($subunits as $subunit)
                <div class="flex items-center justify-between border rounded p-3 mb-2">
                    <span class="font-medium">
                        {{ $subunit->name }}
                    </span>
                    <button
                        type="button"
                        class="toggle-question w-8 h-8 rounded-full bg-blue-100 hover:bg-blue-200"
                        data-questionId="{{ $question->id }}"
                        data-subunit="{{ $subunit->id }}"
                        data-form="{{ $form->id }}"
                    >
                    @if(in_array($subunit->id, $activeMap[$question->id] ?? []))
                        <i class="fa fa-eye text-green-600"></i>
                    @else
                        <i class="fa fa-eye-slash text-red-500"></i>
                    @endif
                    </button>
                </div>
            @endforeach
        </div>
    @endforeach
@endforeach

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<script>
    $(document).on('click', '.toggle-question', function () {
        let btn = $(this);
        let icon = btn.find('i');
        $.ajax({
            url: "{{ route('subunit-question.toggle') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                question_id: btn.attr('data-questionId'),
                subunit_id: btn.attr('data-subunit'),
                form_id: btn.attr('data-form')
            },
            success: function (res) {
                if (res.status === 'added') {
                    icon.removeClass('fa-eye-slash text-red-500')
                        .addClass('fa-eye text-green-600');
                } else if (res.status === 'removed') {
                    icon.removeClass('fa-eye text-green-600')
                        .addClass('fa-eye-slash text-red-500');
                }
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    });
</script>
{{-- <script>
    $(document).on('click', '.toggle-question', function () {

        console.log("BUTTON DIKLIK");

        let btn = $(this);

        console.log(btn.data('question'));
        console.log(btn.data('subunit'));
        console.log(btn.data('form'));
        console.log("HIT TOGGLE AJAX");

        $.ajax({
            url: "/subunit-questions/toggle",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                question_id: questionId,
                subunit_id: subunitId,
                form_id: formId
            },
            success:function(res){
                console.log(res);
            },
            error:function(xhr){
                console.log(xhr.status);
                console.log(xhr.responseText);
            }
        });

    });
    error: function(xhr){
        console.log("ERROR STATUS:", xhr.status);
        console.log("RESPONSE:", xhr.responseText);
    }
</script> --}}