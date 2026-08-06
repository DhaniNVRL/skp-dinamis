@extends('admin.layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    
    <h2 class="text-xl font-bold mb-4">Edit Question</h2>

    <form action="{{ route('question.update', $question->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>No</label>
            <input type="text" name="no" value="{{ $question->no }}" class="validate-required border p-2 w-full">
        </div>

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" value="{{ $question->name }}" class="validate-required border p-2 w-full">
        </div>

        <div class="mb-3">
            <label>Type</label>
            <select name="formtype" class="border p-2 w-full">
                @foreach($questionypes as $questionype)
                    <option value="{{ $questionype->id }}"
                        {{ $question->id_questiontypes == $questionype->id ? 'selected' : '' }}>
                        {{ $questionype->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('admin.units', $group->id) }}?tab=questions" class="bg-gray-500 text-white px-4 py-2 rounded">
                Kembali
            </a>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>
@endsection