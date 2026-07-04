@extends('layouts.app')

@section('title', 'Xodimni tahrirlash')

@section('content')
<div class="max-w-lg bg-white rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('employees._form', ['employee' => $employee])
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg w-full">Yangilash</button>
    </form>
</div>
@endsection
