@extends('layouts.app')

@section('title','لوحة التحكم - المنتجات')

@section('content')
<div class="container" style="padding:2rem">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
    <h2>المنتجات</h2>
    <div>
      <a href="{{ route('admin.products.create') }}" class="btn-cta">منتج جديد</a>
      <form method="POST" action="{{ route('admin.logout') }}" style="display:inline">
        @csrf
        <button class="btn-cta" style="background:#c62828">تسجيل الخروج</button>
      </form>
    </div>
  </div>

  @if(session('success'))
    <div style="margin-bottom:1rem;color:#2e7d32">{{ session('success') }}</div>
  @endif

  <table style="width:100%;border-collapse:collapse">
    <thead>
      <tr>
        <th style="text-align:right;border-bottom:1px solid #ddd;padding:.5rem">الصورة</th>
        <th style="text-align:right;border-bottom:1px solid #ddd;padding:.5rem">الاسم</th>
        <th style="text-align:right;border-bottom:1px solid #ddd;padding:.5rem">السعر</th>
        <th style="text-align:right;border-bottom:1px solid #ddd;padding:.5rem">إجراءات</th>
      </tr>
    </thead>
    <tbody>
      @forelse($products as $product)
        <tr>
          <td style="padding:.5rem">
            @if($product->image)
              <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" style="width:60px;height:60px;object-fit:cover;border-radius:8px">
            @endif
          </td>
          <td style="padding:.5rem">{{ $product->name }}</td>
          <td style="padding:.5rem">{{ number_format($product->price, 2) }} $</td>
          <td style="padding:.5rem">
            <a href="{{ route('admin.products.edit', $product) }}" class="btn-cta">تعديل</a>
            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline">
              @csrf
              @method('DELETE')
              <button class="btn-cta" style="background:#c62828">حذف</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="4" style="padding:1rem">لا توجد منتجات</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div style="margin-top:1rem bg-black p-4 rounded text-center ">
    {{ $products->links() }}
  </div>
</div>
@endsection


