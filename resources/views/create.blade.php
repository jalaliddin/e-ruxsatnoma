@extends('layouts.app')

@section('title', 'Ruxsatnoma yaratish')

@section('content')
<div class="flex justify-center items-center min-h-[70vh]">
    <form method="POST" action="{{ route('permission.store') }}" class="space-y-4 w-full max-w-lg bg-white p-6 rounded shadow">
        @csrf

        <div>
            <label class="block text-gray-700" for="head">Ruxsat beruvchi:</label>
            <select class="w-full border px-3 py-2 rounded" name="cars" id="head">
            <option value="id">G'.B Jumaniyazov</option>
            <option value="id2">S.K Bazarbayev</option>
            <option value="id3">S.B Xudaybergenov</option>
            </select>
        </div>

        <div>
            <label class="block text-gray-700">Kimdan (avto):</label>
            <input type="text" value="{{ Auth::user()->name }}" disabled class="w-full border px-3 py-2 rounded bg-gray-200">
        </div>

        <div>
            <label class="block text-gray-700">Kimga (xodim):</label>
            <input type="text" name="employee_name" required class="w-full border px-3 py-2 rounded">
        </div>

        <div>
            <label class="block text-gray-700">Qayerga:</label>
            <input type="text" name="destination" required class="w-full border px-3 py-2 rounded">
        </div>

        <div>
            <label class="block text-gray-700">Qachondan:</label>
            <input type="datetime-local" name="from_time" required class="w-full border px-3 py-2 rounded">
        </div>

        <div>
            <label class="block text-gray-700">Qachongacha:</label>
            <input type="datetime-local" name="to_time" required class="w-full border px-3 py-2 rounded">
        </div>

        <button type="submit"
                class="bg-blue-700 hover:bg-blue-800 text-white font-semibold px-6 py-2 rounded w-full">
            Saqlash
        </button>

        @if (isset($code))
            <div class="mt-4 p-4 bg-green-100 border border-green-400 rounded text-center">
                ✅ Ruxsatnoma kodi: <strong>{{ $code }}</strong>
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    </form>
</div>
@endsection
