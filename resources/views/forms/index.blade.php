@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">All Forms</h1>
    <a href="{{ route('forms.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">Create New Form</a>
    <ul class="list-disc pl-5">
        @foreach ($forms as $form)
            <li class="mb-2">
                <a href="{{ route('forms.show', $form->id) }}" class="text-blue-500 hover:underline">{{ $form->name }}</a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
