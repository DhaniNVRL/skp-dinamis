<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table id="cprofileTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100"><tr><th class="w-20 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">No</th><th class="w-24 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">ID</th><th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Pertanyaan Group</th><th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Pertanyaan Unit</th><th class="w-32 px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">Aksi</th></tr></thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($cprofiles as $cprofile)
                    <tr class="transition hover:bg-gray-50" data-search="{{ strtolower($cprofile->id . ' ' . $cprofile->group_question . ' ' . $cprofile->unit_question) }}"><td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td><td class="px-4 py-3 text-sm text-gray-500">{{ $cprofile->id }}</td><td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $cprofile->group_question }}</td><td class="px-4 py-3 text-sm text-gray-700">{{ $cprofile->unit_question }}</td><td class="px-4 py-3 text-center">@include('admin.groups.complete-profile.partials.row-action')</td></tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500"><i class="fa-solid fa-inbox mb-3 block text-3xl text-gray-300"></i>Data Complete Profile tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
