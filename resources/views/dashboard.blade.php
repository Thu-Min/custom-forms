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

    <div class="grid grid-cols-3 gap-4">
        @foreach ($forms as $form)
            <div class="flex flex-col w-40">
                <div class="w-52 h-40 bg-gray-200 border border-black rounded-t-md"></div>
                <div class="w-52 bg-white border border-black rounded-b-md flex justify-between items-center py-2 px-4">
                    <div class="flex flex-col space-y-1">
                        <span class="text-md">{{ $form->name }}</span>
                        <span class="text-sm text-gray-400">Opened {{ $form->created_at->format('d/m/Y') }}</span>
                    </div>
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
