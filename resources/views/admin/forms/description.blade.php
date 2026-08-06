@if($form->description)
    <div class="relative bg-white rounded-xl shadow border border-gray-200 p-6">

        <!-- tombol delete (kiri atas) -->
        <form action="{{ route('description.destroy', $form->description->id) }}"
              method="POST"
              onsubmit="return confirm('Hapus description ini?')"
              class="absolute top-3 right-3">
            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="flex items-center justify-center w-8 h-8 rounded-full
                       bg-red-100 text-red-600 hover:bg-red-200 transition">
                <i class="fa fa-trash"></i>
            </button>
        </form>

        <!-- content -->
        @if($form->description)
            {!! $form->description->content !!}
        @else
            <div class="text-gray-400 italic">
                Belum ada description.
            </div>
        @endif

    </div>
@endif

<!-- Button -->
<div class="mt-6">
    <button
        type="button"
        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition"
        @click="
            const template = document.getElementById('description');

            if (!template) {
                alert('Template description tidak ditemukan!');
                return;
            }

            $dispatch('open-modal-tab', {
                title: 'Add Description',
                manual: '{{ route('description.store') }}',
                group: '{{ $groups->id }}',
                form: '{{ $form->id }}',
                content: template.innerHTML
            });
        "
    >
        <i class="fa fa-plus"></i>
        Add Description
    </button>
</div>