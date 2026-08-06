@php($content = $form->description->content ?? $form->description->description ?? $form->description->text ?? null)
<article class="prose max-w-none rounded-xl border border-gray-200 bg-white p-6 text-gray-700">
    @if ($content)
        {!! $content !!}
    @else
        <p>Konten deskripsi belum tersedia.</p>
    @endif
</article>
