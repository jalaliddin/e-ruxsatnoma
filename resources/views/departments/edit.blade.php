@extends('layouts.app')

@section('title', "Bo'limni tahrirlash")

@section('content')
<div class="max-w-lg bg-white rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('departments.update', $department) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('departments._form', ['department' => $department])
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg w-full">Yangilash</button>
    </form>
</div>
@endsection
