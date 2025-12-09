@extends('admin.layouts.app')

@section('title', 'Data Users')

@section('content')
  <div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Daftar Users</h1>

      <button id="openModalBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 ">
          Tambah User
      </button>

      <div id="multiModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 relative">

          <!-- Header -->
          <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-bold">Tambah User</h2>
              <button id="closeModalBtn" class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
          </div>

          <!-- Form -->
          <form id="multiForm">
            <div id="multiInputs">
              <!-- Label Baris (Hanya satu kali) -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-2">
                <div><label class="block text-sm font-medium text-gray-700">Nama</label></div>
                <div><label class="block text-sm font-medium text-gray-700">Username</label></div>
                <div><label class="block text-sm font-medium text-gray-700">Email</label></div>
                <div><label class="block text-sm font-medium text-gray-700">Role</label></div>
              </div>

              <!-- Input Rows (Bisa ditambah/dikurang) -->
              <div id="userRows">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 items-start user-row relative">

                  <!-- Nama -->
                  <div>
                    <input type="text" name="nama[]" placeholder="Nama" required
                          class="border rounded px-4 py-2 w-full">
                  </div>

                  <!-- Username -->
                  <div>
                    <input type="text" name="username[]" placeholder="Username" required
                          class="border rounded px-4 py-2 w-full">
                  </div>

                  <!-- Email -->
                  <div>
                    <input type="email" name="email[]" placeholder="Email" required
                          class="border rounded px-4 py-2 w-full">
                  </div>

                  <!-- Role + Tombol Hapus -->
                  <div>
                    <div class="flex flex-col md:flex-row gap-2">
                      <select name="role[]" required class="border rounded px-4 py-2 w-full md:flex-1">
                        <option value="">Pilih Role</option>
                        @foreach($users as $user)
                            <option value="{{ $user->role->id ?? '-' }}">{{ $user->role->name ?? '-' }}</option>
                        @endforeach
                      </select>

                      <!-- Tombol hapus baris -->
                      <button type="button"
                        class="deleteRowBtn text-red-500 hover:text-red-700 font-bold text-xl md:mt-0 mt-2">
                        &times;
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>



            <!-- Tambah baris input -->
            <button type="button" id="addRowBtn" class="bg-gray-200 text-sm px-3 py-1 rounded hover:bg-gray-300 mb-4">
            + Tambah Baris
            </button>

            <hr class="my-4">

            <!-- Upload Excel -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Import dari Excel</label>
              <input type="file" id="excelFile" accept=".xls,.xlsx" class="block w-full text-sm text-gray-700 border border-gray-300 rounded cursor-pointer">
              <p class="text-xs text-gray-500 mt-1">Hanya file .xls atau .xlsx</p>
            </div>

            <!-- Download Template -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Download Template Excel</label>
              <a href="{{ route('admin.export.usertemplate') }}"
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
                  Download Template
              </a>
            </div>


            <!-- Footer Buttons -->
            <div class="flex justify-end gap-2 mt-6">
              <button type="button" id="closeModalBtn2" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Tutup</button>
              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Data</button>
            </div>
          </form>
        </div>
      </div>

        <!-- Table Data User -->
        <div class="overflow-x-auto mt-5">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="py-2 px-4 border-b">ID</th>
                        <th class="py-2 px-4 border-b">Name</th>
                        <th class="py-2 px-4 border-b">Email</th>
                        <th class="py-2 px-4 border-b">Role</th>
                        <th class="py-2 px-4 border-b">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b">{{ $user->id }}</td>
                            <td class="py-2 px-4 border-b">{{ $user->name }}</td>
                            <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                            <td class="py-2 px-4 border-b">
                                {{ $user->role->name ?? '-' }}
                            </td>
                            <td class="py-2 px-4 border-b">{{ $user->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

<!-- Script -->
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
  </script>
@endsection
