@extends('layouts.app')

@section('title', 'Foydalanuvchini tahrirlash')

@section('content')
<div class="max-w-lg bg-white rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('admin.users._form', ['user' => $user])
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg w-full">Yangilash</button>
    </form>
</div>
@endsection
