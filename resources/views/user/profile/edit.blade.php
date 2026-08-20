@extends('layouts.app-role-modern')

@section('title', 'Edit Profil Responden')

@section('content')
<div
    id="respondentProfileEditPage"
    data-units-url="{{ url('/user/profile/groups') }}"
    class="mx-auto max-w-3xl space-y-6"
>
    {{-- HEADER --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-center gap-4">
            <a
                href="{{ route('profile.show') }}"
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50"
            >
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Edit Profil Responden
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Perbarui bidang kerja dan unit Anda.
                </p>
            </div>
        </div>
    </div>

    {{-- VALIDATION ERROR --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-5">
            <div class="font-semibold text-red-700">
                Data belum dapat diproses:
            </div>

            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('profile.update') }}"
        method="POST"
        class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
    >
        @csrf
        @method('PUT')

        <div class="space-y-6 p-6">
            {{-- ACTIVITY --}}
            <div>
                <label
                    class="mb-2 block text-sm font-semibold text-gray-700"
                >
                    Aktivitas
                </label>

                @if ($canSelectActivity)
                    <select
                        id="activity_id"
                        name="activity_id"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-3 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                    >
                        <option value="">Pilih activity</option>
                        @foreach ($activities as $activity)
                            <option
                                value="{{ $activity->id }}"
                                data-group-label="{{ $activity->completeProfile?->group_question ?? 'Bidang Kerja / Group' }}"
                                data-unit-label="{{ $activity->completeProfile?->unit_question ?? 'Unit / Jabatan' }}"
                                @selected((string) old('activity_id', $profile->activity_id) === (string) $activity->id)
                            >
                                {{ $activity->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('activity_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @else
                <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <i class="fa-solid fa-lock"></i>
                    </div>

                    <div class="min-w-0">
                        <div class="font-semibold text-emerald-800">
                            {{ $profile->activity?->name ?? '-' }}
                        </div>

                        <div class="mt-0.5 text-xs text-emerald-700">
                            Activity telah ditentukan untuk akun Anda dan tidak dapat diubah.
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- GROUP --}}
            <div>
                <label
                    id="groupLabel"
                    for="group_id"
                    class="mb-2 block text-sm font-semibold text-gray-700"
                >
                    {{ $completeProfile?->group_question
                        ?? 'Bidang Kerja / Group' }}
                </label>

                <select
                    id="group_id"
                    name="group_id"
                    required
                    @disabled(blank(old('activity_id', $profile->activity_id)))
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-3 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                >
                    <option value="">
                        Pilih bidang kerja
                    </option>

                    @foreach ($groups as $group)
                        <option
                            value="{{ $group->id }}"
                            data-activity-id="{{ $group->activity_id }}"
                            @selected(
                                (string) old(
                                    'group_id',
                                    $profile->group_id
                                ) === (string) $group->id
                            )
                        >
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>

                @error('group_id')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- UNIT --}}
            <div>
                <label
                    id="unitLabel"
                    for="unit_id"
                    class="mb-2 block text-sm font-semibold text-gray-700"
                >
                    {{ $completeProfile?->unit_question
                        ?? 'Unit / Jabatan' }}
                </label>

                <select
                    id="unit_id"
                    name="unit_id"
                    required
                    @disabled(blank(old('group_id', $profile->group_id)))
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-3 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed disabled:bg-gray-100"
                >
                    <option value="">
                        Pilih unit atau jabatan
                    </option>

                    @foreach ($units as $unit)
                        <option
                            value="{{ $unit->id }}"
                            @selected(
                                (string) old(
                                    'unit_id',
                                    $profile->unit_id
                                ) === (string) $unit->id
                            )
                        >
                            {{ $unit->name }}
                        </option>
                    @endforeach
                </select>

                <p
                    id="unitLoadingMessage"
                    class="mt-2 hidden text-sm text-blue-500"
                >
                    <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                    Memuat data unit...
                </p>

                <p
                    id="unitErrorMessage"
                    class="mt-2 hidden text-sm text-red-600"
                ></p>

                @error('unit_id')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-700">
                <i class="fa-solid fa-circle-info mr-2"></i>
                @if ($canSelectActivity)
                    Pilih Activity, kemudian Group dan Unit. Jika pilihan profil diubah, jawaban dan progres sebelumnya akan dihapus agar data tidak tercampur.
                @else
                    Pilih Group yang tersedia pada Activity akun Anda, kemudian pilih Unit. Jika Group atau Unit diubah, jawaban dan progres sebelumnya akan dihapus agar data tetap konsisten.
                @endif
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
            <a
                href="{{ route('profile.show') }}"
                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100"
            >
                Batal
            </a>

            <button
                id="saveProfileButton"
                type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Profil
            </button>
        </div>
    </form>
</div>
@endsection

