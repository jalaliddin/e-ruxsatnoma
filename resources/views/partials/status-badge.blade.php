@php
    $statusMap = [
        'pending' => ['⏳ Kutilmoqda', 'bg-yellow-100 text-yellow-800'],
        'awaiting_manager' => ['⏳ Rahbarda', 'bg-amber-100 text-amber-800'],
        'approved' => ['✅ Tasdiqlangan', 'bg-green-100 text-green-800'],
        'rejected' => ['❌ Rad etilgan', 'bg-red-100 text-red-800'],
    ];
    [$label, $classes] = $statusMap[$status] ?? [$status, 'bg-gray-100 text-gray-800'];
@endphp
<span class="inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $classes }}">{{ $label }}</span>
