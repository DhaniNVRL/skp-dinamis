<template id="answer-form">
    <div class="bg-white w-full max-w-xl rounded shadow-lg">

        <!-- HEADER -->
        <div class="border-b px-4 py-2 font-semibold">
            Add Answers
        </div>

        <div class="p-4">
            <form id="answerForm" method="POST">
                @csrf

                <input type="hidden" name="question_id" id="questionId">
                <input type="hidden" name="form_id" id="formId">
                <input type="hidden" name="group_id" id="groupId">

                <div id="answerRows">
                    <div class="row flex gap-2 mb-2 items-center cursor-move">
                        <span class="drag text-gray-500">1</span>
                        <input type="text" name="answers[]" class="border p-2 w-full rounded answer-text" placeholder="Jawaban..." required>
                        <input type="number" name="values[]" class="border p-2 w-24 rounded" placeholder="Value">
                        <button type="button" class="remove text-red-600 font-bold">✕</button>
                    </div>
                </div>

                <button type="button" id="addAnswerRow" class="text-blue-600 mb-3">+ Add Answer</button>

                <div class="mb-4">
                    <span class="font-semibold">Preview:</span>
                    <div id="answerPreview" class="mt-2 border rounded p-2 bg-gray-50"></div>
                </div>

                <div class="text-right">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
                </div>
            </form>
        </div>
    </div>
</template>