@extends('admin.layouts.app')

@section('title', 'Hide and Show')

@section('content')

    <h2>Unit di nilai</h2>
    <form action="{{ route('subunit.question.save') }}" method="POST">
        @csrf

        <input type="hidden"
            name="subunit_id"
            value="{{ $subunit->id }}">

        @foreach($questions as $question)

            <div>
                <label>
                    <input
                        type="checkbox"
                        name="questions[]"
                        value="{{ $question->id }}"
                    >

                    {{ $question->name }}
                </label>
            </div>

        @endforeach

        <button type="submit">
            Simpan
        </button>

    </form>
@endsection
