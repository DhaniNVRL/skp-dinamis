@extends('admin.layouts.app')

@section('title', 'Detail')

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

    <!-- Profile Card -->
    <a href="{{ route('admin.datauser') }}">
        <p class="w-max px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded mb-4">
            Back
        </p>
    </a>
    <form action="{{ route('admin.datauser.resetaccount', $user->id) }}" method="POST" class="inline">
        @csrf

        <div class="bg-white shadow-lg rounded-xl p-6 w-full mb-6">
            <div class="flex justify-between items-center border-b pb-3 mb-6">
                <p class="text-2xl font-bold text-gray-800">
                    Profile Information
                </p>
                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">
                    Reset Account
                </button>
            </div>


            <table class="w-full">
                <tbody class="divide-y divide-gray-200">

                    <tr class="hover:bg-gray-50">
                        <td class="py-3 font-semibold text-gray-600 w-1/3">Username</td>
                        <td class="py-3 text-gray-800">{{ $user->username ?? '-'}}</td>
                    </tr>

                    <tr class="hover:bg-gray-50">
                        <td class="py-3 font-semibold text-gray-600 w-1/3">Full Name</td>
                        <td class="py-3 text-gray-800">{{ optional($user->profile)->fullname ?? '-'}}</td>
                    </tr>

                    <tr class="hover:bg-gray-50">
                        <td class="py-3 font-semibold text-gray-600">Email</td>
                        <td class="py-3 text-gray-800">{{ optional($user->profile)->email ?? '-'}}</td>
                    </tr>

                    <tr class="hover:bg-gray-50">
                        <td class="py-3 font-semibold text-gray-600">Phone</td>
                        <td class="py-3 text-gray-800">{{ optional($user->profile)->no_handphone     ?? '-'}}</td>
                    </tr>

                    <tr class="hover:bg-gray-50">
                        <td class="py-3 font-semibold text-gray-600">Activity</td>
                        <td class="py-3 text-gray-800">{{ optional($user->profile->activity)->name ?? '-' }}</td>
                    </tr>

                    <tr class="hover:bg-gray-50">
                        <td class="py-3 font-semibold text-gray-600">Group</td>
                        <td class="py-3 text-gray-800">{{ optional($user->profile->group)->name ?? '-' }}</td>
                    </tr>

                    <tr class="hover:bg-gray-50">
                        <td class="py-3 font-semibold text-gray-600">Unit</td>
                        <td class="py-3 text-gray-800">{{ optional($user->profile->unit)->name ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>

    <!-- Question Card -->
    <div class="bg-white shadow-lg rounded-xl p-6 w-full">

        <div class="flex justify-between items-center border-b pb-3 mb-6">
            <p class="text-2xl font-bold text-gray-800">
                Questions Answer
            </p>
            <a href="#" class="text-red-600 hover:text-red-800 font-semibold">
                Reset Answer
            </a>
        </div>

        <p class="text-gray-600">
            Belum ada data jawaban.
        </p>

    </div>

</div>
@endsection
