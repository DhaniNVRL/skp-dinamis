@foreach ($questions as $question)
    <div class="mb-4">
        <label for="answer-{{ $question->id }}" class="block font-semibold mb-1">
            {{ $question->no }}. {{ $question->name }}
        </label>

        @if ($question->id_questiontypes == 1)
            <input type="text" name="answers[{{ $question->id }}]" class="border p-2 w-full">

        @elseif ($question->id_questiontypes == 2)
            <textarea name="answers[{{ $question->id }}]" class="border p-2 w-full"></textarea>

        @elseif ($question->id_questiontypes == 3)
            <label>
                <input type="radio" name="answers[{{ $question->id }}]" value="Laki-laki"> Laki-laki
            </label><br>
            <label>
                <input type="radio" name="answers[{{ $question->id }}]" value="Perempuan"> Perempuan
            </label>

        @elseif ($question->id_questiontypes == 8)
            <input type="email" name="answers[{{ $question->id }}]" class="border p-2 w-full">

        @else
            <p>Type belum dibuat.</p>
        @endif
    </div>
@endforeach