@extends('admin.layouts.app')

@section('title', 'Units & Questions')

@section('content')
<div
    x-data="{
        tab: new URLSearchParams(window.location.search).get('tab') || 'units',
        questionsInitialized: false,

        init() {
            if (this.tab === 'questions') {
                this.initQuestions();
            }
        },

        changeTab(name) {
            this.tab = name;
            history.replaceState(null, '', '?tab=' + name);

            if (name === 'questions') {
                this.initQuestions();
            }
        },

        initQuestions() {
            if (this.questionsInitialized) return;

            this.$nextTick(() => {
                if (typeof initQuestionsJS === 'function') {
                    initQuestionsJS();
                    this.questionsInitialized = true;
                }
            });
        }
    }"
    x-init="init()"
>

    <!-- BACK -->
    <a href="{{ route('admin.groups', $groups->id_activities) }}">
        <p class="w-max px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded mb-4">
            Back
        </p>
    </a>

    <!-- TABS -->
    <div class="flex gap-4 border-b mb-6">
        <button
            @click.prevent="changeTab('units')"
            class="pb-2 border-b-2 transition"
            :class="tab === 'units'
                ? 'border-blue-600 text-blue-600 font-semibold'
                : 'border-transparent text-gray-500 hover:text-gray-700'">
            Units
        </button>

        <button
            @click.prevent="changeTab('questions')"
            class="pb-2 border-b-2 transition"
            :class="tab === 'questions'
                ? 'border-blue-600 text-blue-600 font-semibold'
                : 'border-transparent text-gray-500 hover:text-gray-700'">
            Questions
        </button>
    </div>

    <!-- TITLE -->
    <h1 class="text-2xl font-bold mb-6">
        <span x-show="tab === 'units'">Units List</span>
        <span x-show="tab === 'questions'">Questions List</span>
    </h1>

    <!-- CONTENT : UNITS -->
    <div x-show="tab === 'units'" x-transition>
        <template x-if="tab === 'units'">
            @include('admin._units')
        </template>
    </div>

    <!-- CONTENT : QUESTIONS -->
    <div x-show="tab === 'questions'" x-transition x-cloak>
        <template x-if="tab === 'questions'">
            @include('admin._questions')
        </template>
    </div>
</div>
@endsection
