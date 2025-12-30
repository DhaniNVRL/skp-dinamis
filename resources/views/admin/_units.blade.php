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

  <button id="openModalBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded mb-3">
    Add Units
  </button>

  <!-- Modal -->
  <div id="multiModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 relative">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Add Units</h2>
        <button id="closeModalBtn" class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
      </div>

      <!-- Tabs Manual / Excel -->
      <div class="flex mb-4 border-b">
        <button id="tabManual" class="flex-1 py-2 text-center font-medium border-b-2 border-blue-600 text-blue-600">
          Manual Entry
        </button>
        <button id="tabExcel" class="flex-1 py-2 text-center font-medium border-b-2 border-transparent hover:text-blue-600">
          Import Data (Excel)
        </button>
      </div>

      <!-- === FORM INPUT MANUAL === -->
      <div id="manualFormSection">
        <form id="manualForm" action="{{ route('units.storegroup') }}" method="POST">
            @csrf

            <!-- KIRIM id_groups -->
            <input type="hidden" name="id_groups" value="{{ $groups->id }}">

            <div id="multiInputs" class="w-full">

                <!-- Label Baris -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-2">
                    <div class="md:col-span-5">
                        <label class="block text-center font-medium text-gray-700">Name</label>
                    </div>
                </div>

                <!-- Input Rows -->
                <div id="userRows" class="w-full">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4 items-start user-row relative w-full">
                        <div class="md:col-span-5 w-full">
                            <input type="text" name="name[]" placeholder="Name" required class="border rounded px-4 py-2 w-full">
                        </div>
                        <div>
                            <button type="button" class="deleteRowBtn text-red-500 hover:text-red-700 font-bold text-xl">&times;</button>
                        </div>
                    </div>
                </div>

            </div>

            <button type="button" id="addRowBtn" class="bg-gray-200 text-sm px-3 py-1 rounded hover:bg-gray-300 mb-4">
                + Add
            </button>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" id="closeModalBtn2" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Close</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
            </div>
        </form>
      </div>

      <!-- === FORM IMPORT EXCEL === -->
      <div id="excelFormSection" class="hidden">
        <form action="{{  route('units.import', $groups->id) }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Import from Excel</label>
            <input type="file" name="file" id="excelFile" accept=".xls,.xlsx" class="block w-full text-sm text-gray-700 border border-gray-300 rounded cursor-pointer">
            <p class="text-xs text-gray-500 mt-1">Upload Excel file (.xls or .xlsx)</p>
          </div>

          <div class="flex justify-between items-center">
            <a href="{{ route('units.export') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
              Download Excel Template
            </a>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded">
              Import Excel
            </button>
          </div>

          <div class="flex justify-end mt-6">
            <button type="button" id="closeModalBtn3" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <form id="deleteForm" action="{{ route('units.bulkDelete') }}" method="POST">
    @csrf
    @method('DELETE')
    <div class="text-end">
        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded mb-3">
            Delete Selected
        </button>
      <input
        type="text"
        id="searchInput"
        placeholder="Search Activities...."
        class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
      >
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
            @foreach($units as $index => $unit)
                <tr class="text-center">
                    <td class="border py-2 px-4">
                        <input type="checkbox" name="selected[]" class="rowCheckbox" value="{{ $unit->id }}">
                    </td>
                    <td class="border py-2 px-4">{{ $index + 1 }}</td>
                    <td class="border py-2 px-4">{{ $unit->id }}</td>
                    <td class="border py-2 px-4 text-start">{{ $unit->name }}</td>
                    <td class="border py-2 px-4">
                    <form action="{{ route('units.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('Are you sure want to delete this?')">
                            <a href="{{ route('admin.units', $unit->id)}}" class="text-blue-600 hover:text-blue-800">Detail</a> |
                            <a href="{{ route('units.edit', $unit->id) }}"
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
  const openModalBtn = document.getElementById('openModalBtn');
  const closeBtns = [document.getElementById('closeModalBtn'), document.getElementById('closeModalBtn2'), document.getElementById('closeModalBtn3')];
  const multiModal = document.getElementById('multiModal');
  const addRowBtn = document.getElementById('addRowBtn');

  const tabManual = document.getElementById('tabManual');
  const tabExcel = document.getElementById('tabExcel');
  const manualFormSection = document.getElementById('manualFormSection');
  const excelFormSection = document.getElementById('excelFormSection');

  // Modal
  openModalBtn.onclick = () => {
    multiModal.classList.remove('hidden');
    multiModal.classList.add('flex');
  };
  closeBtns.forEach(btn => btn.onclick = () => {
    multiModal.classList.add('hidden');
    multiModal.classList.remove('flex');
  });

  // Tabs
  tabManual.onclick = () => {
    tabManual.classList.add('border-blue-600', 'text-blue-600');
    tabExcel.classList.remove('border-blue-600', 'text-blue-600');
    manualFormSection.classList.remove('hidden');
    excelFormSection.classList.add('hidden');
  };
  tabExcel.onclick = () => {
    tabExcel.classList.add('border-blue-600', 'text-blue-600');
    tabManual.classList.remove('border-blue-600', 'text-blue-600');
    excelFormSection.classList.remove('hidden');
    manualFormSection.classList.add('hidden');
  };

  // Tambah baris input manual
  addRowBtn.onclick = () => {
    const newRow = document.querySelector('.user-row').cloneNode(true);
    newRow.querySelectorAll('input, textarea').forEach(el => el.value = '');
    newRow.querySelector('.deleteRowBtn').onclick = () => newRow.remove();
    document.getElementById('userRows').appendChild(newRow);
  };

  // Delete row pertama
  document.querySelectorAll('.deleteRowBtn').forEach(btn => {
    btn.onclick = function () {
      const rows = document.querySelectorAll('.user-row');
      if (rows.length > 1) this.closest('.user-row').remove();
      else alert("Minimum one input row must be provided.");
    };
  });

 document.getElementById('selectAll').addEventListener('change', function() {
  const checked = this.checked;
  document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = checked);
});

document.getElementById('deleteForm').addEventListener('submit', function(e) {
  const selected = document.querySelectorAll('.rowCheckbox:checked');
  if (selected.length === 0) {
    e.preventDefault();
    alert('Select at least one unit before deleting.');
  } else if (!confirm('Are you sure you want to delete the selected unit?')) {
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