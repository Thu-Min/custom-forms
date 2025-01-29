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
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">There were some problems with your input.</span>
                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
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
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">There were some problems with your input.</span>
                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
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
                    <input type="text" name="name" value="{{ $form->name }}" required class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label>Description</label>
                    <input type="text" name="description" value="{{ $form->description }}" required class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-500 @enderror">
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div id="inputs-container" class="space-y-6 relative">
                @foreach ($form->inputs as $input)
                    <div class="bg-white shadow-md border border-gray-400 rounded-lg p-6 space-y-6" id="input-{{ $input->id }}">
                        <div class="flex items-center justify-between space-x-2">
                            <div class="w-3/4">
                                <label class="block text-sm font-medium text-gray-700">Question</label>
                                <input type="text" name="inputs[{{ $input->id }}][label]" value="{{ $input->label }}" required class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('inputs.'.$input->id.'.label') border-red-500 @enderror">
                                @error('inputs.'.$input->id.'.label')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="w-1/4">
                                <label class="block text-sm font-medium text-gray-700">Input Type</label>
                                <select name="inputs[{{ $input->id }}][type]" required class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('inputs.'.$input->id.'.type') border-red-500 @enderror" onchange="handleTypeChange(this, {{ $input->id }})">
                                    <option value="text" {{ $input->type == 'text' ? 'selected' : '' }}>Short Answer</option>
                                    <option value="date" {{ $input->type == 'date' ? 'selected' : '' }}>Date</option>
                                    <option value="number" {{ $input->type == 'number' ? 'selected' : '' }}>Number</option>
                                    <option value="select" {{ $input->type == 'select' ? 'selected' : '' }}>Select</option>
                                    <option value="checkbox" {{ $input->type == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                </select>
                                @error('inputs.'.$input->id.'.type')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        @if ($input->type == 'select' || $input->type == 'checkbox')
                            <div class="w-full mt-4" id="extra-fields-{{ $input->id }}">
                                <label class="block text-sm font-medium text-gray-700">Options</label>

                                <input type="hidden" name="inputs[{{ $input->id }}][deleted_options]" id="deleted-options-{{ $input->id }}" value="[]">

                                @foreach (json_decode($input->options, true) as $option)
                                    <div class="flex items-center space-x-2 mb-2">
                                        <input type="text" name="inputs[{{ $input->id }}][options][]" value="{{ $option }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <button type="button" onclick="removeOption(this, {{ $input->id }}, '{{ $option }}')" class="mt-1 inline-flex items-center px-2 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Remove</button>
                                    </div>
                                @endforeach

                                <button type="button" onclick="addOption({{ $input->id }})" class="mt-1 inline-flex items-center px-2 py-2 text-center border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Add Option</button>
                            </div>
                        @endif

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
                            <span class="block text-gray-700">{{ is_array($response->response) ? json_encode($response->response) : $response->response }}</span>
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
                        <select name="inputs[new][${inputIndex}][type]" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" onchange="handleTypeChange(this, ${inputIndex})">
                            <option value="text">Short Answer</option>
                            <option value="textarea">Long Answer</option>
                            <option value="date">Date</option>
                            <option value="number">Number</option>
                            <option value="select">Select</option>
                            <option value="checkbox">Checkbox</option>
                        </select>
                    </div>
                </div>

                <div id="extra-fields-${inputIndex}" class="space-y-2"></div>

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

    function handleTypeChange(selectElement, index) {
        const extraFieldsContainer = document.getElementById(`extra-fields-${index}`);
        extraFieldsContainer.innerHTML = '';

        if (selectElement.value === 'select') {
            const optionsField = `
                <div id="options-container-${index}">
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Options ${index}</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" name="inputs[${index}][options][]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <button type="button" onclick="removeOption(this)" class="mt-1 inline-flex items-center px-2 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Remove</button>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="addOption(${index})" class="mt-1 inline-flex items-center px-2 py-2 text-center border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Add Option</button>
            `;
            extraFieldsContainer.insertAdjacentHTML('beforeend', optionsField);
        } else if (selectElement.value === 'checkbox') {
            const checkboxField = `
                <div id="checkbox-container-${index}">
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Options ${index}</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" name="inputs[${index}][options][]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <button type="button" onclick="removeInput(this)" class="mt-1 inline-flex items-center px-2 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Remove</button>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="addCheckbox(${index})" class="mt-1 inline-flex items-center px-2 py-2 text-center border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Add Option</button>
            `;
            extraFieldsContainer.insertAdjacentHTML('beforeend', checkboxField);
        }
    }

    function addOption(index) {
        const optionsContainer = document.getElementById(`options-container-${index}`);
        const newOption = `
            <div class="mb-2">
                <label class="block text-sm font-medium text-gray-700">Option ${optionsContainer.children.length + 1}</label>
                <div class="flex items-center space-x-2">
                    <input type="text" name="inputs[${index}][options][]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <button type="button" onclick="removeOption(this)" class="mt-1 inline-flex items-center px-2 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Remove</button>
                </div>
            </div>
        `;
        optionsContainer.insertAdjacentHTML('beforeend', newOption);
    }

    function removeOption(button, inputId, optionValue) {
        const optionDiv = button.parentElement;
        optionDiv.remove();

        let deletedOptionsInput = document.getElementById(`deleted-options-${inputId}`);
        let deletedOptions = JSON.parse(deletedOptionsInput.value);

        if (!deletedOptions.includes(optionValue)) {
            deletedOptions.push(optionValue);
        }

        deletedOptionsInput.value = JSON.stringify(deletedOptions);
    }

    function addCheckbox(index) {
        const checkboxContainer = document.getElementById(`checkbox-container-${index}`);
        const newCheckbox = `
            <div class="mb-2">
                <label class="block text-sm font-medium text-gray-700">Option ${checkboxContainer.children.length + 1}</label>
                <div class="flex items-center space-x-2">
                    <input type="text" name="inputs[${index}][options][]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <button type="button" onclick="removeCheckbox(this)" class="mt-1 inline-flex items-center px-2 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Remove</button>
                </div>
            </div>
        `;
        checkboxContainer.insertAdjacentHTML('beforeend', newCheckbox);
    }

    function removeCheckbox(button) {
        button.parentElement.remove();
    }
</script>
