@extends('layouts.app')

@section('content')
<div class="w-full mt-5 max-w-5xl h-auto mx-auto p-6 bg-white shadow-md rounded-lg">
    <h1 class="text-lg mb-4">Create New Form</h1>

    <div class="grid grid-cols-3 gap-4">
        <a href="{{ route('forms.create') }}" class="w-40">
            <div class="flex flex-col space-y-2">
                <div class="p-5 w-40 h-40 bg-gray-200 rounded-md"></div>
                <span class="text-center">Blank Form</span>
            </div>
        </a>
    </div>
</div>
<div class="w-full mt-5 max-w-5xl mx-auto p-6 bg-white shadow-md rounded-lg">
    <h1 class="text-lg mb-4">Recent Forms</h1>

    <div class="grid grid-cols-4 gap-4">
        @foreach ($forms as $form)
            <div class="flex flex-col w-40">
                <a href="{{ route('forms.show', $form->id) }}">
                    <div class="w-52 h-40 bg-gray-200 border border-black rounded-t-md"></div>
                </a>
                <div class="w-52 bg-white border border-black rounded-b-md flex justify-between items-center py-2 px-4">
                    <div class="flex flex-col space-y-1">
                        <span class="text-md">{{ $form->name }}</span>
                        <span class="text-sm text-gray-400">Opened {{ $form->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="relative">
                        <button id="dropdown-{{ $form->id }}" onclick="toggleDropdown({{ $form->id }})" class="focus:outline-none">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                        <div id="dropdown-menu-{{ $form->id }}" class="hidden absolute left-1 bottom-0 mt-2 w-48 bg-white border border-gray-300 rounded-md shadow-lg">
                            <a href="#" onclick="copyLink('{{ route('forms.show', $form->id) }}')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Copy Link</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $form->id }}').submit();">Delete</a>
                            <form id="delete-form-{{ $form->id }}" action="{{ route('forms.destroy', $form->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

<script>
    function toggleDropdown(id) {
        var dropdown = document.getElementById('dropdown-menu-' + id);
        if (dropdown.style.display === 'none' || dropdown.style.display === '') {
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    }

    function copyLink(link) {
        navigator.clipboard.writeText(link).then(function() {
            alert('Link copied to clipboard');
        }, function() {
            alert('Failed to copy link to clipboard');
        });
    }
</script>
