@extends('admin.layouts.app')

@section('title', 'Activities')

@section('content')

<div class="container mx-auto px-4 py-6 ">

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
          // Auto-hide setelah 5 detik
          setTimeout(() => {
              document.querySelectorAll('.alert-box').forEach(box => {
                  box.style.opacity = 0;
                  setTimeout(() => box.remove(), 300);
              });
          }, 5000);

          // Close manual
          document.querySelectorAll('.close-alert').forEach(btn => {
              btn.addEventListener('click', () => {
                  const alertBox = btn.parentElement;
                  alertBox.style.opacity = 0;
                  setTimeout(() => alertBox.remove(), 300);
              });
          });
      </script>
    @endif

    <h1 class="text-2xl font-bold mb-4">Roles List</h1>

    <button
      type="button"
      class="openModal bg-blue-600 text-white px-4 py-2 rounded"
      data-title="Add Roles"
      data-manual="{{ route('roles.storeroles') }}"
      data-excel="{{ route('roles.import') }}"
      data-group=""
      >
        Add Roles
    </button>

    <!-- GLOBAL MODAL -->
  <div id="globalModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 items-center justify-center">
    <div class="bg-white w-full max-w-xl rounded shadow-lg">
      <!-- HEADER -->
      <div class="flex justify-between items-center px-4 py-3 border-b">
        <h2 id="modalTitle" class="text-lg font-semibold">Modal Title</h2>
        <button data-close class="text-gray-600 hover:text-black">&times;</button>
      </div>

      <!-- TABS -->
      <div class="flex border-b">
        <button class="tab-btn flex-1 py-2 border-b-2 border-blue-600 text-blue-600" data-tab="manual">
          Manual
        </button>
        <button class="tab-btn flex-1 py-2" data-tab="excel">
          Excel
        </button>
      </div>

      <!-- CONTENT -->
      <div class="p-4">
        <!-- MANUAL -->
        <div data-content="manual">
          <form id="manualForm" method="POST">
            @csrf

            <input type="hidden" id="groupId" name="group_id">

            <div id="rows">
              <div class="row flex gap-2 mb-2">
                <input type="text" name="name[]" placeholder="Name Roles" class="border p-2 w-full">
                <button type="button" class="remove text-red-600">X</button>
              </div>
                <div class="row flex gap-2 mb-2">
              </div>
            </div>

            <button type="button" id="addRow" class="text-blue-600 mb-3">
              + Add Row
            </button>

            <div class="text-right">
              <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Save
              </button>
            </div>
          </form>
        </div>

        <!-- EXCEL -->
        <div data-content="excel" class="hidden">
          <form id="excelForm" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="file" name="file" class="border p-2 w-full mb-3">

            <div class="text-right">
              <a href="{{ route('roles.export') }}" class="bg-blue-600 text-white px-4 py-2 me-5 rounded" >
                Download Excel Template
              </a>
              
              <button class="bg-green-600 text-white px-4 py-2 rounded">
                Import
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <form id="deleteForm" action="{{ route('roles.bulkDelete') }}" method="POST">
    @csrf
    @method('DELETE')
    <div class="text-end">
      <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded mb-3">
        Delete Selected
      </button>
      <input
        type="text"
        id="searchInput"
        placeholder="Search Roles...."
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
            <th class="py-2 px-4 border-b w-[50px]">Role ID</th>
            <th class="py-2 px-4 border-b w-[200px]">Role Name</th>
            <th class="py-2 px-4 border-b w-[200px]">Update At</th>
            <th class="py-2 px-4 border-b w-[200px]">Action</th>
          </tr>
        </thead>
        <tbody id="activityTable">
          @foreach($roles as $role)
          <tr class="cursor-pointer hover:bg-gray-100 transition" >
            <td class="py-2 px-4 border-b text-center">
              <input type="checkbox" name="selected[]" value="{{ $role->id }}" class="rowCheckbox cursor-pointer">
            </td>
            <td class="py-2 px-4 border-b text-center">{{ $loop->iteration }}</td>
            <td class="py-2 px-4 border-b">{{$role->id}}</td>
            <td class="py-2 px-4 border-b">{{$role->name}}</td>
            <td class="py-2 px-4 border-b">{{$role->updated_at}}</td>
            <td class="py-2 px-4 border-b text-center">
              <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure want to delete this?')">
                <a href="{{ route('roles.edit', $role->id) }}"
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

<script>
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const closeModalBtn2 = document.getElementById('closeModalBtn2');
    const multiModal = document.getElementById('multiModal');
    const addRowBtn = document.getElementById('addRowBtn');
    const multiInputs = document.getElementById('multiInputs');
    const multiForm = document.getElementById('multiForm');
    const excelFileInput = document.getElementById('excelFile');

    // Show modal
    openModalBtn.addEventListener('click', () => {
      multiModal.classList.remove('hidden');
      multiModal.classList.add('flex');
    });

    // Close modal
    [closeModalBtn, closeModalBtn2].forEach(btn => {
      btn.addEventListener('click', () => {
        multiModal.classList.add('hidden');
        multiModal.classList.remove('flex');
      });
    });

    // Tambah baris baru
    addRowBtn.addEventListener('click', () => {
      const newRow = document.querySelector('.user-row').cloneNode(true);
      newRow.querySelectorAll('input').forEach(input => input.value = '');
      multiInputs.appendChild(newRow);
      attachDeleteEvent();
    });

    // Fungsi untuk hapus baris
    function attachDeleteEvent() {
      const deleteButtons = document.querySelectorAll('.deleteRowBtn');
      deleteButtons.forEach(button => {
        button.onclick = function () {
          const rows = document.querySelectorAll('.user-row');
          if (rows.length > 1) {
            this.closest('.user-row').remove();
          } else {
            alert("Minimal 1 baris input harus ada.");
          }
        };
      });
    }

    // Jalankan pertama kali
    attachDeleteEvent();

    // Submit form (simulasi)
    multiForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      console.log("Data input manual:");
      for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
      }
      alert("Data berhasil disimpan (simulasi)");
    });

    // Upload file Excel
    excelFileInput.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        alert(`File "${file.name}" dipilih. (Proses belum diimplementasikan)`);
        // Tambahkan SheetJS atau upload ke server di sini jika dibutuhkan
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
