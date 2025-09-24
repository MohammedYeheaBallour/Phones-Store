@extends('layouts.app')

@section('title','سلة المشتريات')

@section('content')
<div class="container" style="padding:2rem">
  <h2 style="margin-bottom:1rem">سلة المشتريات</h2>
  @if(session('success'))
    <div style="margin-bottom:1rem;color:#2e7d32">{{ session('success') }}</div>
  @endif

  <table style="width:100%;border-collapse:collapse">
    <thead>
      <tr>
        <th style="text-align:right;border-bottom:1px solid #ddd;padding:.5rem">المنتج</th>
        <th style="text-align:right;border-bottom:1px solid #ddd;padding:.5rem">السعر</th>
        <th style="text-align:right;border-bottom:1px solid #ddd;padding:.5rem">الكمية</th>
        <th style="text-align:right;border-bottom:1px solid #ddd;padding:.5rem">الإجمالي</th>
        <th style="text-align:right;border-bottom:1px solid #ddd;padding:.5rem">إجراء</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $item)
        <tr>
          <td style="padding:.5rem">{{ $item->product?->name }}</td>
          <td style="padding:.5rem">{{ number_format($item->product?->price ?? 0, 2) }} $</td>
          <td style="padding:.5rem">
            <form method="POST" action="{{ route('cart.update', $item) }}">
              @csrf
              @method('PATCH')
              <input type="number" name="quantity" min="1" value="{{ $item->quantity }}" style="width:80px" />
              <button class="btn-cta">تحديث</button>
            </form>
          </td>
          <td style="padding:.5rem">{{ number_format(($item->product?->price ?? 0) * $item->quantity, 2) }} $</td>
          <td style="padding:.5rem">
            <form method="POST" action="{{ route('cart.remove', $item) }}">
              @csrf
              @method('DELETE')
              <button class="btn-cta" style="background:#c62828">حذف</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" style="padding:1rem">سلتك فارغة</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div style="margin-top:1rem;font-weight:bold">المجموع: {{ number_format($total, 2) }} $</div>
</div>
@endsection


