@extends('layouts.app')

@section('content')
@if (auth()->id() === $form->user_id)
    <div class="text-center space-x-4">
        <button id="questions-btn" class="p-3 bg-gray-200">
            <span class="text-lg">Questions</span>
        </button>
        <button id="answers-btn" class="p-3 bg-gray-200">
            <span class="text-lg">Answers</span>
        </button>
    </div>
@endif
<div id="questions-section" class="mt-5 max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ $form->name }}</h1>
        <p>{{ $form->description }}</p>
    </div>
    <div>
        @auth
            @if (auth()->id() !== $form->user_id)
                <p>We will send a notification to
                <span>{{ auth()->user()->email }}</span>
                after you filled this form.</p>
            @endif
        @endauth
        @guest()
            <p>Please login to get the email notification.</p>
        @endguest
    </div>
    <form action="{{ route('forms.submit', $form->id) }}" method="POST" class="space-y-4">
        @csrf
        @foreach ($form->inputs as $input)
            <x-dynamic-input :input="$input" />
        @endforeach
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Submit</button>
    </form>
</div>
<div id="answers-section" class="mt-5 max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg hidden">
    <div class="space-y-4">
        @foreach ($form->inputs as $input)
            <div class="border-b pb-4">
                <span class="block font-semibold">{{ $input->label }}</span>
                @foreach ($form->responses as $response)
                    @if ($response->form_input_id === $input->id)
                        <span class="block text-gray-700">{{ $response->response }}</span>
                    @endif
                @endforeach
            </div>
        @endforeach
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('questions-btn').addEventListener('click', function() {
            document.getElementById('questions-section').classList.remove('hidden');
            document.getElementById('answers-section').classList.add('hidden');
        });

        document.getElementById('answers-btn').addEventListener('click', function() {
            document.getElementById('questions-section').classList.add('hidden');
            document.getElementById('answers-section').classList.remove('hidden');
        });
    });
</script>
