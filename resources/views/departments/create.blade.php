@extends('layouts.app')

@section('title', "Yangi bo'lim")

@section('content')
<div class="max-w-lg bg-white rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('departments.store') }}" class="space-y-4">
        @csrf
        @include('departments._form', ['department' => null])
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg w-full">Saqlash</button>
    </form>
</div>
@endsection
