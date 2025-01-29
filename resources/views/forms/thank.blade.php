@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-6 rounded-lg shadow-lg text-center">
        <h1 class="text-2xl font-bold mb-4">Thank You!</h1>
        <p class="text-gray-700 mb-6">Your form has been successfully submitted.</p>
        <a href="{{ url('/') }}" class="inline-block px-6 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">Go to Homepage</a>
    </div>
</div>
@endsection
