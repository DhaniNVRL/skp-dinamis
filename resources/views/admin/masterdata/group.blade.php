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
            <th class="py-2 px-4 border-b w-[50px] text-center">
              <input type="checkbox" id="selectAll" class="cursor-pointer">
            </th>
            <th class="py-2 px-4 border-b w-[50px] text-center">No</th>
            <th class="py-2 px-4 border-b w-[50px]">Group ID</th>
            <th class="py-2 px-4 border-b w-[50px]">Activity ID</th>
            <th class="py-2 px-4 border-b w-[200px]">Group Name</th>
            <th class="py-2 px-4 border-b w-[200px]">Update At</th>
          </tr>
        </thead>
        <tbody id="activityTable">
         @foreach($groups as $index => $group)
            <tr class="text-center">
                <td class="border py-2 px-4">
                    <input type="checkbox" name="selected[]" class="rowCheckbox" value="{{ $group->id }}">
                </td>
                <td class="border py-2 px-4">{{ $index + 1 }}</td>
                <td class="border py-2 px-4">{{ $group->id }}</td>
                <td class="border py-2 px-4">{{ $group->id_activities }}</td>
                <td class="border py-2 px-4 text-start">{{ $group->name }}</td>
                <td class="border py-2 px-4">{{ $group->updated_at }}              
                </td>
            </tr>
            @endforeach
        </tbody>
      </table>
    </div>
  </form>

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
