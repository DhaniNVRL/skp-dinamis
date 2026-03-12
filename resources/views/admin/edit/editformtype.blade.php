@extends('admin.layouts.app')

@section('title', 'Edit Activity')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white shadow-md rounded px-8 pt-6 pb-8">

    {{-- Alert Messages --}}
    @if(session('success') || session('successdelete') || session('error'))
        @if(session('success'))
            <div class="alert-box flex items-center justify-between bg-green-500 bg-opacity-30 border border-green-600 text-green-900 px-4 py-3 rounded mb-4 transition duration-300">
                <span>{{ session('success') }}</span>
                <button class="close-alert text-green-800 font-bold text-lg leading-none hover:text-green-900">&times;</button>
            </div>
        @endif
        @if(session('successdelete'))
            <div class="alert-box flex items-center justify-between bg-red-500 bg-opacity-30 border border-red-600 text-red-900 px-4 py-3 rounded mb-4 transition duration-300">
                <span>{{ session('successdelete') }}</span>
                <button class="close-alert text-red-800 font-bold text-lg leading-none hover:text-red-900">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-500 text-white px-4 py-2 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
        <script>
            // Auto-hide
            setTimeout(() => {
                document.querySelectorAll('.alert-box').forEach(box => {
                    box.style.opacity = 0;
                    setTimeout(() => box.remove(), 300);
                });
            }, 5000);
            document.querySelectorAll('.close-alert').forEach(btn => {
                btn.addEventListener('click', () => {
                    const box = btn.parentElement;
                    box.style.opacity = 0;
                    setTimeout(() => box.remove(), 300);
                });
            });
        </script>
    @endif
  <h2 class="text-2xl font-semibold mb-6 text-center">Edit Form Type</h2>

  <form action="{{ route('formtype.update', $formtypes->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-4">
      <label for="name" class="block text-gray-700 font-bold mb-2">ID:</label>
      <input readonly type="text" name="id" id="id" value="{{ old('id', $formtypes->id) }}"
             class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
      @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="mb-4">
      <label for="name" class="block text-gray-700 font-bold mb-2">Name Type:</label>
      <input type="text" name="name" id="name" value="{{ old('name', $formtypes->name) }}"
             class="validate-required shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
      @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="mb-4">
      <label for="description" class="block text-gray-700 font-bold mb-2">Description:</label>
      <input type="text" name="description" id="description" value="{{ old('description', $formtypes->description) }}"
             class="validate-email shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
      @error('description')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="flex justify-between items-center">
      <a href="{{ route('admin.formtype') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
        Batal
      </a>
      <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
        Simpan Perubahan
      </button>
    </div>
  </form>
</div>
@endsection

