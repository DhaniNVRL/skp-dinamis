@extends('admin.layouts.app')

@section('title', 'Groups')

@section('content')

<div class="container mx-auto px-4 py-6">
    @if(session('success') || session('successdelete'))

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
            setTimeout(() => {
                document.querySelectorAll('.alert-box').forEach(box => {
                    box.style.opacity = 0;
                    setTimeout(() => box.remove(), 300);
                });
            }, 5000);

            document.querySelectorAll('.close-alert').forEach(btn => {
                btn.addEventListener('click', () => {
                    const alertBox = btn.parentElement;
                    alertBox.style.opacity = 0;
                    setTimeout(() => alertBox.remove(), 300);
                });
            });
        </script>

    @endif

    <ul>
      <li>
        {{ $activity->name }}
      </li>
    </ul>
    <a href="{{ route('admin.activity')}}">
        <p class="w-max text-start px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded mb-3">
            Back
        </p>
    </a>
    <h1 class="text-2xl font-bold mb-4">Groups List</h1>

  <button
      type="button"
      class="openModal bg-green-600 text-white px-4 py-2 rounded"
      data-title="Add Group"
      data-manual="{{ route('groups.storegroup') }}"
      data-excel="{{ route('groups.import') }}"
      data-group="{{ $activity->id }}"
  >
      Add Group
  </button>


    
  <!-- GLOBAL MODAL -->
  <div id="globalModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 items-center justify-center">
      <div class="bg-white w-full max-w-xl rounded shadow-lg">

          <!-- HEADER -->
          <div class="flex justify-between items-center px-4 py-3 border-b">
              <h2 id="modalTitle" class="text-lg font-semibold"></h2>
              <button data-close class="text-gray-600 hover:text-black text-xl">&times;</button>
          </div>

          <!-- TABS -->
          <div class="flex border-b">
              <button
                  class="tab-btn flex-1 py-2 border-b-2 border-blue-600 text-blue-600"
                  data-tab="manual">
                  Manual
              </button>
              <button
                  class="tab-btn flex-1 py-2"
                  data-tab="excel">
                  Excel
              </button>
          </div>

          <!-- CONTENT -->
          <div class="p-4">

              <!-- MANUAL -->
              <div data-content="manual">
                  <form id="manualForm" method="POST">
                      @csrf
                      
                      <!-- IMPORTANT: ALIAS -->
                      <input type="hidden" id="groupId" name="id_activities">

                      <div id="rows">
                          <div class="row flex gap-2 mb-2">
                              <input type="text" name="name[]" placeholder="Name Group" class="border p-2 w-full" required>
                              <button type="button" class="remove text-red-600 font-bold">
                                  X
                              </button>
                          </div>
                      </div>

                      <button type="button" id="addRow" class="text-blue-600 mb-3">
                          + Add Row
                      </button>

                      <div class="text-right">
                          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                              Save
                          </button>
                      </div>
                  </form>
              </div>

              <!-- EXCEL -->
              <div data-content="excel" class="hidden">
                  <form id="excelForm" method="POST" enctype="multipart/form-data">
                      @csrf

                      <input type="file" name="file" class="border p-2 w-full mb-3" required>
                      <input type="hidden" name="id_activities" value="{{ $activity->id }}">

                      <div class="text-right">
                          <a href="{{ route('groups.export') }}" class="bg-blue-600 text-white px-4 py-2 me-5 rounded" >
                            Download Excel Template
                          </a>
                          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                              Import
                          </button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>



  <form id="deleteForm" action="{{ route('groups.bulkDelete') }}" method="POST">
    @csrf
    @method('DELETE')
    <div class="text-end">
        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded mb-3">
            Delete Selected
        </button>
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
            <th class="py-2 px-4 border-b w-[50px]">ID</th>
            <th class="py-2 px-4 border-b w-[200px]">Group Name</th>
            <!-- <th class="py-2 px-4 border-b w-[200px]">Update At</th> -->
            <th class="py-2 px-4 border-b w-[200px]">Action</th>
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
                <td class="border py-2 px-4 text-start">{{ $group->name }}</td>
                <td class="border py-2 px-4">
                   <form action="{{ route('groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Are you sure want to delete this?')">
                        <a href="{{ route('admin.units', $group->id)}}" class="text-blue-600 hover:text-blue-800">Detail</a> |
                        <a href="{{ route('groups.edit', $group->id) }}"
                        class="text-blue-600 hover:text-blue-800">Edit</a> |
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
  </form>

<!-- Script -->
<script>
 document.getElementById('selectAll').addEventListener('change', function() {
    const checked = this.checked;
    document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = checked);
  });

document.getElementById('deleteForm').addEventListener('submit', function(e) {
  const selected = document.querySelectorAll('.rowCheckbox:checked');
  if (selected.length === 0) {
    e.preventDefault();
    alert('Select at least one activity before deleting.');
  } else if (!confirm('Are you sure you want to delete the selected activity?')) {
    e.preventDefault();
  }
});

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
