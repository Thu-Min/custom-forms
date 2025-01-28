@extends('layouts.app')

@section('content')
<div class="mt-5 max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ $form->name }}</h1>
        <p>{{ $form->description }}</p>
    </div>
    <form action="{{ route('forms.submit', $form->id) }}" method="POST" class="space-y-4">
        @csrf
        @foreach ($form->inputs as $input)
            <x-dynamic-input :input="$input" />
        @endforeach
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Submit</button>
    </form>
</div>
@endsection
