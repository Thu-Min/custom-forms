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

@if (auth()->id() !== $form->user_id)
    <div class="mt-5 max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg">
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
@else
    <div id="questions-section" class="mt-5 max-w-5xl mx-auto p-6">
        <form action="{{ route('forms.update', $form->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="bg-gray-200 p-5 rounded-lg flex justify-between items-center sticky z-50 top-0">
                <h2 class="text-2xl font-bold">Edit Form</h2>

                <div class="flex space-x-2">
                    <button type="button" onclick="addInput()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Add Input</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Update Form</button>
                </div>
            </div>

            <div class="bg-white shadow-md border border-gray-400 rounded-lg p-6 space-y-4 relative">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Form Name</label>
                    <input type="text" name="name" value="{{ $form->name }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label>Description</label>
                    <input type="text" name="description" value="{{ $form->description }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>

            <div id="inputs-container" class="space-y-6 relative">
                @foreach ($form->inputs as $input)
                    <div class="bg-white shadow-md border border-gray-400 rounded-lg p-6 space-y-6" id="input-{{ $input->id }}">
                        <div class="flex items-center justify-between space-x-2">
                            <div class="w-3/4">
                                <label class="block text-sm font-medium text-gray-700">Question</label>
                                <input type="text" name="inputs[{{ $input->id }}][label]" value="{{ $input->label }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div class="w-1/4">
                                <label class="block text-sm font-medium text-gray-700">Input Type</label>
                                <select name="inputs[{{ $input->id }}][type]" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="text" {{ $input->type == 'text' ? 'selected' : '' }}>Short Answer</option>
                                    <option value="textarea" {{ $input->type == 'textarea' ? 'selected' : '' }}>Long Answer</option>
                                    <option value="date" {{ $input->type == 'date' ? 'selected' : '' }}>Date</option>
                                    <option value="number" {{ $input->type == 'number' ? 'selected' : '' }}>Number</option>
                                    <option value="select" {{ $input->type == 'select' ? 'selected' : '' }}>Select</option>
                                    <option value="checkbox" {{ $input->type == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-5">
                            <button type="button" onclick="removeInput({{ $input->id }})" class="text-red-500">
                                <i class="fa-solid fa-trash mt-1"></i> Remove
                            </button>
                            <input type="hidden" name="deleted_inputs" id="deleted-inputs">
                        </div>
                    </div>
                @endforeach
            </div>
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
@endif
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

    let inputIndex = {{ $form->inputs->count() + 1 }};

    function addInput() {
        const container = document.getElementById('inputs-container');
        const newInput = `
            <div class="bg-white shadow-md border border-gray-400 rounded-lg p-6 space-y-6" id="input-${inputIndex}">
                <div class="flex items-center justify-between space-x-2">
                    <div class="w-3/4">
                        <label class="block text-sm font-medium text-gray-700">Question</label>
                        <input type="text" name="inputs[new][${inputIndex}][label]" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="w-1/4">
                        <label class="block text-sm font-medium text-gray-700">Input Type</label>
                        <select name="inputs[new][${inputIndex}][type]" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="text">Short Answer</option>
                            <option value="textarea">Long Answer</option>
                            <option value="date">Date</option>
                            <option value="number">Number</option>
                            <option value="select">Select</option>
                            <option value="checkbox">Checkbox</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-5">
                    <button type="button" onclick="removeInput(${inputIndex})" class="text-red-500">
                        <i class="fa-solid fa-trash mt-1"></i> Remove
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newInput);
        inputIndex++;
    }

    let deletedInputs = [];

    function removeInput(id) {
        const inputElement = document.getElementById(`input-${id}`);
        if (inputElement) {
            inputElement.remove();

            if (!isNaN(id)) {
                deletedInputs.push(id);
            }

            document.getElementById('deleted-inputs').value = deletedInputs.join(',');
        }
    }
</script>
