@php($progress = $totalForms > 0 ? min(100, ($currentPosition / $totalForms) * 100) : 0)
<div class="px-6 py-4">
    <div class="mb-2 flex justify-between text-xs text-gray-600">
        <span>Progress survei</span>
        <span>{{ $currentPosition }} dari {{ $totalForms }}</span>
    </div>
    <div class="h-2 overflow-hidden rounded-full bg-gray-200">
        <div class="h-full rounded-full bg-indigo-600 transition-all" style="width: {{ $progress }}%"></div>
    </div>
</div>
