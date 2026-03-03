@extends('admin.layouts.app')

@section('title', 'Groups')

@section('content')

<div class="container mx-auto px-4 py-6">
    <div class="text-end">
        <input type="text" id="searchInput" placeholder="Search Activities...." class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
    </div>

    <div class="overflow-x-auto pt-2">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-gray-100 text-center">
                    <th class="py-2 px-4 border-b w-[50px]">
                        <input type="checkbox" id="selectAll" class="cursor-pointer">
                    </th>
                    <th class="py-2 px-4 border-b w-[50px]">No</th>
                    <th class="py-2 px-4 border-b w-[50px]">Unit ID</th>
                    <th class="py-2 px-4 border-b w-[50px]">Group ID</th>
                    <th class="py-2 px-4 border-b w-[200px]">Unit Name</th>
                    <th class="py-2 px-4 border-b w-[200px]">Action</th>
                </tr>
            </thead>
            <tbody id="activityTable">
                @foreach($units as $index => $unit)
                <tr class="text-center">
                    <td class="border py-2 px-4">
                        <input type="checkbox" name="selected[]" class="rowCheckbox" value="{{ $unit->id }}">
                    </td>
                    <td class="border py-2 px-4">{{ $index + 1 }}</td>
                    <td class="border py-2 px-4">{{ $unit->id }}</td>
                    <td class="border py-2 px-4">{{ $unit->id_groups }}</td>
                    <td class="border py-2 px-4 text-start">{{ $unit->name }}</td>
                    <td class="border py-2 px-4">
                        <form action="{{ route('units.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('Are you sure want to delete this?')">
                            <a href="{{ route('admin.units', $unit->id)}}" class="text-blue-600 hover:text-blue-800">Detail</a> |
                            <a href="{{ route('units.edit', $unit->id) }}" class="text-blue-600 hover:text-blue-800">Edit</a> |
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

<!-- Script -->
<script>
  document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#activityTable tr');

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(searchValue) ? '' : 'none';
    });
  });
</script>
@endsection
