@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-6">{{ $form->name }}</h1>
    <form action="" method="POST" class="space-y-4">
        @csrf
        @foreach ($form->inputs as $input)
            <div class="flex flex-col">
                <label class="mb-2 font-medium">{{ $input->label }}</label>
                @if ($input->type === 'text')
                    <input type="text" name="responses[{{ $input->id }}]" class="p-2 border border-gray-300 rounded" required>
                @elseif ($input->type === 'date')
                    <input type="date" name="responses[{{ $input->id }}]" class="p-2 border border-gray-300 rounded" required>
                @elseif ($input->type === 'number')
                    <input type="number" name="responses[{{ $input->id }}]" class="p-2 border border-gray-300 rounded" required>
                @endif
            </div>
        @endforeach
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Submit</button>
    </form>
</div>
@endsection
