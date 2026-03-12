@extends('admin.layouts.app')

@section('title', 'Data Users')

@section('content')
<div class="container mx-auto px-4 py-6">

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

    {{-- Title --}}
    <h1 class="text-2xl font-bold mb-4">Daftar Users</h1>

    {{-- Add User Button --}}
    <button
        type="button"
        class="openModal bg-blue-600 text-white px-4 py-2 rounded"
        data-title="Add Activities"
        data-manual="{{ route('admin.datauser.store') }}"
        data-excel="{{ route('admin.import.datauser') }}"
        data-group=""
        >
        Add User
    </button>

    <!-- GLOBAL MODAL -->
    <div id="globalModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 items-center justify-center">

        <div class="bg-white w-full max-w-6xl rounded shadow-lg">

            <!-- HEADER -->
            <div class="flex justify-between items-center px-4 py-3 border-b">
                <h2 id="modalTitle" class="text-lg font-semibold">Modal Title</h2>
                <button data-close class="text-gray-600 hover:text-black">&times;</button>
            </div>

            <!-- TABS -->
            <div class="flex border-b">
                <button class="tab-btn flex-1 py-2 border-b-2 border-blue-600 text-blue-600"
                        data-tab="manual">
                    Manual
                </button>
                <button class="tab-btn flex-1 py-2"
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

                        <input type="hidden" id="groupId" name="">

                        <div id="rows">
                            <div class="row flex gap-2 mb-2">
                                <div class="w-full">
                                    <input type="text" name="username[]" placeholder="Username"
                                        class="validate-username border p-2 w-full">
                                </div>
                                <div class="w-full">
                                    <input type="text" name="email[]" placeholder="Email"
                                        class="validate-email border p-2 w-full">
                                </div>
                                <div class="w-full">
                                    <input type="text" name="password[]" placeholder="Password"
                                        class="validate-password border p-2 w-full">
                                </div>
                                <div class="w-full">
                                    <select name="activity[]" required class="validate-required border rounded px-4 py-2 w-full">
                                        <option value="">Choose Activity</option>
                                        @foreach($activities as $activity)
                                        <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-full">
                                    <select name="role[]" required class="validate-required border rounded px-4 py-2 w-full">
                                        <option value="">Pilih Role</option>
                                        @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-center">
                                    <button type="button"
                                        class="remove w-8 h-8 flex items-center justify-center
                                            rounded-full bg-red-100 text-red-600
                                            hover:bg-red-600 hover:text-white
                                            transition duration-200">
                                        ✕
                                    </button>
                                </div>
                            </div>
                            <div class="row flex gap-2 mb-2">
                            </div>
                        </div>

                        <button type="button" id="addRow"
                                class="text-blue-600 mb-3">
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

                        <input type="file" name="file" class="border p-2 w-full mb-3">

                        <div class="text-right">
                            <a href="{{ route('admin.export.usertemplate') }}" class="bg-blue-600 text-white px-4 py-2 me-5 rounded" >
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


    {{-- Search --}}
    <div class="text-end">
        <input type="text" id="searchInput" placeholder="Search Users...." class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
    </div>

    {{-- User Table --}}
    <div class="overflow-x-auto pt-2">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="py-2 px-4 border-b">ID</th>
                    <th class="py-2 px-4 border-b">Username</th>
                    <th class="py-2 px-4 border-b">Email</th>
                    <th class="py-2 px-4 border-b">Role</th>
                    <th class="py-2 px-4 border-b">Activity</th>
                    <th class="py-2 px-4 border-b">Created At</th>
                    <th class="py-2 px-4 border-b"></th>
                </tr>
            </thead>
            <tbody id="activityTable">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 border-b">{{ $user->id }}</td>
                        <td class="py-2 px-4 border-b">{{ $user->username }}</td>
                        <td class="py-2 px-4 border-b">{{ $user->profile->email ?? '-' }}</td>
                        <td class="py-2 px-4 border-b">{{ $user->role->name ?? '-' }}</td>
                        <td class="py-2 px-4 border-b">{{ $user->profile->activity->name ?? '-' }}</td>
                        <td class="py-2 px-4 border-b">{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="py-2 px-4 border-b">
                            <div class="flex items-center justify-center gap-2">

                                <a href="{{ route('admin.datauser.show', $user->id) }}"
                                class="inline-flex items-center rounded-md border border-blue-500 shadow-sm px-3 py-1 bg-blue-400 text-black hover:bg-blue-500 text-sm font-medium">Detail</a>

                                <!-- EDIT -->
                                <div class="relative inline-block text-left">
                                    <button onclick="toggleDropdown(this)"
                                        class="inline-flex items-center rounded-md border border-yellow-500 shadow-sm px-3 py-1 bg-yellow-400 text-black hover:bg-yellow-500 text-sm font-medium">
                                        Edit
                                        <svg class="ml-2 h-4 w-4 transition-transform duration-200"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                                clip-rule="evenodd"/>
                                        </svg>
                                    </button>

                                    <div class="dropdownMenu hidden absolute right-0 mt-2 w-44
                                        rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5
                                        transform opacity-0 scale-95 transition-all duration-200 ease-out
                                        z-50">

                                        <div class="py-1">
                                            <a href="{{ route('admin.datauser.edit', $user->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Edit Profile
                                            </a>
                                            <a href="{{ route('admin.datauser.editpassword', $user->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Edit Password
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- DELETE -->
                                <div class="relative inline-block text-left">
                                    <button onclick="toggleDropdown(this)"
                                        class="inline-flex items-center rounded-md border border-red-500 shadow-sm px-3 py-1 bg-red-400 text-black hover:bg-red-500 text-sm font-medium">
                                        Action
                                        <svg class="ml-2 h-4 w-4 transition-transform duration-200"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                                clip-rule="evenodd"/>
                                        </svg>
                                    </button>

                                    <div class="dropdownMenu hidden absolute right-0 mt-2 w-52
                                        rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5
                                        transform opacity-0 scale-95 transition-all duration-200 ease-out
                                        z-50">

                                        <div class="py-1">

                                            <!-- RESET QUESIONER -->
                                            <form action="#"
                                                method="POST"
                                                onsubmit="return confirm('Reset semua jawaban user ini?')">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full text-left px-4 py-2 text-sm text-yellow-700 hover:bg-yellow-100">
                                                    Reset Jawaban Quesioner
                                                </button>
                                            </form>

                                            <!-- DELETE USER -->
                                            <form action="{{ route('admin.datauser.destroy', $user->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure want to delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-100">
                                                    Hapus User
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- userprofile -->
    <div class="overflow-x-auto pt-2">
      <table class="min-w-full bg-white border border-gray-200">
        <thead>
          <tr class="bg-gray-100 text-center">
            <th class="py-2 px-4 border-b w-[50px] text-center">
              <input type="checkbox" id="selectAll" class="cursor-pointer">
            </th>
            <th class="py-2 px-4 border-b w-[50px] text-center">No</th>
            <th class="py-2 px-4 border-b w-[50px]">ID</th>
            <th class="py-2 px-4 border-b w-[200px]">User ID</th>
            <th class="py-2 px-4 border-b w-[400px]">Fullname</th>
            <th class="py-2 px-4 border-b w-[400px]">No Handphone</th>
            <th class="py-2 px-4 border-b w-[400px]">Activiti ID</th>
            <th class="py-2 px-4 border-b w-[400px]">Group ID</th>
            <th class="py-2 px-4 border-b w-[400px]">Unit ID</th>
            <th class="py-2 px-4 border-b w-[200px]">Update At</th>
            <th class="py-2 px-4 border-b w-[200px]">Status Pengisian</th>
          </tr>
        </thead>
        <tbody id="activityTable">
          @foreach($userprofiles as $userprofile)
          <tr class="cursor-pointer hover:bg-gray-100 transition" >
            <td class="py-2 px-4 border-b text-center">
              <input type="checkbox" name="selected[]" value="{{ $activity->id }}" class="rowCheckbox cursor-pointer">
            </td>
            <td class="py-2 px-4 border-b text-center">{{ $loop->iteration }}</td>
            <td class="py-2 px-4 border-b">{{$userprofile->id}}</td>
            <td class="py-2 px-4 border-b">{{$userprofile->user_id}}</td>
            <td class="py-2 px-4 border-b">{{$userprofile->fullname ?? '-'}}</td>
            <td class="py-2 px-4 border-b ">{{$userprofile->no_handphone ?? '-'}}</td>
            <td class="py-2 px-4 border-b ">{{$userprofile->activity->name ?? '-'}}</td>
            <td class="py-2 px-4 border-b ">{{$userprofile->group->name ?? '-'}}</td>
            <td class="py-2 px-4 border-b ">{{$userprofile->unit->name ?? '-'}}</td>
            <td class="py-2 px-4 border-b">{{$userprofile->updated_at}}</td>
            <td class="py-2 px-4 border-b text-center">
              <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" onsubmit="return confirm('Are you sure want to delete this?')">
                <a href="{{ route('admin.groups', $activity->id) }}"
                class="text-blue-600 hover:text-blue-800">Detail</a> |
                <a href="{{ route('activities.edit', $activity->id) }}"
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

</div>

<script>
function toggleDropdown(button) {

    document.querySelectorAll('.dropdownMenu').forEach(menu => {
        menu.classList.add('hidden', 'opacity-0', 'scale-95');
    });

    const dropdown = button.nextElementSibling;

    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        setTimeout(() => {
            dropdown.classList.remove('opacity-0', 'scale-95');
        }, 10);
    } else {
        dropdown.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            dropdown.classList.add('hidden');
        }, 200);
    }
}

// close when click outside
window.addEventListener('click', function(e) {
    if (!e.target.closest('.relative')) {
        document.querySelectorAll('.dropdownMenu').forEach(menu => {
            menu.classList.add('hidden', 'opacity-0', 'scale-95');
        });
    }
});
</script>

@endsection

