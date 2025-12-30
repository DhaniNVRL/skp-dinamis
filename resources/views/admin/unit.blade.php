@extends('admin.layouts.app')

@section('title', 'Units')

@section('content')

<div x-data="{ tab: new URLSearchParams(window.location.search).get('tab') || 'units' }">

    <!-- BACK -->
    <a href="{{ route('admin.groups', $groups->id_activities) }}">
        <p class="w-max px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded mb-4">
            Back
        </p>
    </a>

    <!-- TABS -->
    <div class="flex gap-2 border-b mb-4">
        <button
             @click="
              tab = 'units';
              history.replaceState(null, '', '?tab=units')
          "
          :class="tab === 'units'
              ? 'border-blue-600 text-blue-600'
              : 'border-transparent text-gray-500'"
          class="px-4 py-2 font-medium border-b-2">
          Units
        </button>

        <button
             @click="
            tab = 'questions';
            history.replaceState(null, '', '?tab=questions')
        "
        :class="tab === 'questions'
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-500'"
        class="px-4 py-2 font-medium border-b-2">
        Pertanyaan
        </button>
    </div>

    <!-- TITLE -->
    <h1 class="text-2xl font-bold mb-4">
        <span x-show="tab === 'units'">Units List</span>
        <span x-show="tab === 'questions'">Daftar Pertanyaan</span>
    </h1>

    <!-- CONTENT -->
    <template x-if="tab === 'units'">
      @include('admin._units')
    </template>

    <template x-if="tab === 'questions'">
      @include('admin._questions')
    </template>

</div>

@endsection
