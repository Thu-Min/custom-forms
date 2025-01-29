@extends('layouts.app')

@section('content')
<div class="w-full mt-5 max-w-5xl mx-auto">
    <form action="{{ route('forms.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="bg-gray-200 p-5 rounded-lg flex justify-between items-center sticky z-50 top-0">
            <h1 class="text-2xl font-bold">Create New Form</h1>

            <div class="flex space-x-2">
                <button type="button" onclick="addInput()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Add Input</button>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Create Form</button>
            </div>
        </div>

        <div class="bg-white shadow-md border border-gray-400 rounded-lg p-6 space-y-4 relative">
            <div>
                <label class="block text-sm font-medium text-gray-700">Form Name</label>
                <input type="text" name="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label>Description</label>
                <input type="text" name="description" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
        </div>

        <div id="inputs-container" class="space-y-6 relative">
        </div>
    </form>
</div>
@endsection

<script>
    let inputIndex = 1;

    function addInput() {
        const container = document.getElementById('inputs-container');
        const newInput = `
            <div class="bg-white shadow-md border border-gray-400 rounded-lg p-6 space-y-6">
                <div class="flex items-center justify-between space-x-2">
                    <div class="w-3/4">
                        <label class="block text-sm font-medium text-gray-700">Question</label>
                        <input type="text" name="inputs[${inputIndex}][label]" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="w-1/4">
                        <label class="block text-sm font-medium text-gray-700">Input Type</label>
                        <select name="inputs[${inputIndex}][type]" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" onchange="handleTypeChange(this, ${inputIndex})">
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
                    <button type="button" onclick="removeInput(this)">
                        <i class="fa-solid fa-trash mt-1"></i>
                    </button>
                    <div class="border border-r-black h-8"></div>
                    <div class="flex items-center space-x-1">
                        <span class="">Required</span>
                        <input type="checkbox" name="inputs[${inputIndex}][required]" class="mt-1">
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newInput);
        inputIndex++;
    }

    function removeInput(button) {
        button.parentElement.parentElement.remove();
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

    function removeOption(button) {
        button.parentElement.parentElement.remove();
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
