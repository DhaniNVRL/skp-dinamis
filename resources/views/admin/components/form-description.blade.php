<template id="description">
    <form id="manualForm"
        method="POST"
        action="{{ route('description.store') }}">

        @csrf

        <input type="hidden" name="group_id">
        <input type="hidden" name="form_id">

        <div class="p-4">
    
            <!-- TOOLBAR -->
            <div class="flex flex-wrap gap-2 p-2 border rounded bg-gray-100">
    
                <button type="button" class="cmd px-2 font-bold" data-cmd="bold">B</button>
                <button type="button" class="cmd px-2 italic" data-cmd="italic">I</button>
                <button type="button" class="cmd px-2 underline" data-cmd="underline">U</button>
    
                <select class="cmd border px-2" data-cmd="formatBlock">
                    <option value="P">Paragraph</option>
                    <option value="H2">Heading 2</option>
                    <option value="H3">Heading 3</option>
                </select>
    
                <button type="button" class="cmd" data-cmd="insertUnorderedList">• List</button>
                <button type="button" class="cmd" data-cmd="insertOrderedList">1. List</button>
    
                <button type="button" id="insertTable">Table</button>
    
            </div>
    
            <!-- EDITOR -->
            <div id="editor"
                 contenteditable="true"
                 class="border p-4 min-h-[400px] mt-2 focus:outline-none">
    
                <h2>Judul Dokumen</h2>
                <p>Mulai mengetik di sini...</p>
    
            </div>
    
            <!-- hidden input -->
            <textarea
                id="contentInput"
                name="content"
                class="hidden"></textarea>
    
        </div>
        <div class="text-right">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
        </div>
    </form>

</template>