@extends('layouts.app')

@section('title', 'Ruxsatnoma so\'rovi #'.$permission->id)

@section('content')
    <div class="max-w-2xl bg-white rounded-xl shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">So'rov #{{ $permission->id }}</h2>
            @include('partials.status-badge', ['status' => $permission->status])
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Xodim</dt>
                <dd class="font-medium">{{ $permission->employee->full_name ?? $permission->employee_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Kategoriya</dt>
                <dd class="font-medium">{{ $permission->category->name ?? '—' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-gray-500">Sabab</dt>
                <dd class="font-medium">{{ $permission->reason ?? $permission->destination ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Boshlanish</dt>
                <dd class="font-medium">{{ optional($permission->from_time)->format('d.m.Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Tugash</dt>
                <dd class="font-medium">{{ optional($permission->to_time)->format('d.m.Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Rahbar</dt>
                <dd class="font-medium">{{ $permission->approver->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Kod</dt>
                <dd class="font-medium">{{ $permission->code ?? '—' }}</dd>
            </div>
        </dl>

        @auth
            @if ((auth()->user()->isAdmin() || auth()->user()->isHr()) && $permission->status === 'pending')
                <form method="POST" action="{{ route('permission.assign', $permission) }}" class="flex items-end gap-2 pt-4 border-t">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm text-gray-600 mb-1">Rahbarni tanlang</label>
                        <select name="approver_id" required class="w-full border rounded-lg px-3 py-2">
                            @foreach (\App\Models\User::where('role', 'manager')->get() as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Yuborish</button>
                </form>
            @endif

            @if ((auth()->user()->isManager() || auth()->user()->isAdmin()) && $permission->status === 'awaiting_manager')
                <div class="flex gap-2 pt-4 border-t">
                    <form method="POST" action="{{ route('permission.decide', $permission) }}">
                        @csrf
                        <input type="hidden" name="decision" value="approve">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">✅ Roziman</button>
                    </form>
                    <form method="POST" action="{{ route('permission.decide', $permission) }}">
                        @csrf
                        <input type="hidden" name="decision" value="reject">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">❌ Rad etaman</button>
                    </form>
                </div>
            @endif
        @endauth
    </div>
@endsection
