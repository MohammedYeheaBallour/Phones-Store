@extends('layouts.app')

@section('title','تعديل منتج')

@section('content')
<div class="container" style="padding:2rem;max-width:720px">
  <h2 style="margin-bottom:1rem">تعديل منتج</h2>
  <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="form-group" style="margin-bottom:1rem">
      <label>الاسم</label>
      <input type="text" name="name" value="{{ old('name', $product->name) }}" required />
      @error('name')<div style="color:#b00020">{{ $message }}</div>@enderror
    </div>
    <div class="form-group" style="margin-bottom:1rem">
      <label>الوصف</label>
      <textarea name="description">{{ old('description', $product->description) }}</textarea>
    </div>
    <div class="form-group" style="margin-bottom:1rem">
      <label>السعر</label>
      <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required />
      @error('price')<div style="color:#b00020">{{ $message }}</div>@enderror
    </div>
    <div class="form-group" style="margin-bottom:1rem">
      <label>الصورة</label>
      @if($product->image)
        <div style="margin-bottom:.5rem">
          <img src="{{ asset('storage/'.$product->image) }}" alt="" style="width:100px;height:100px;object-fit:cover;border-radius:8px" />
        </div>
      @endif
      <input type="file" name="image" accept="image/*" />
      @error('image')<div style="color:#b00020">{{ $message }}</div>@enderror
    </div>
    <div class="form-group" style="margin-bottom:1rem">
      <label>
        <input type="checkbox" name="is_flash_sale" value="1" {{ old('is_flash_sale', $product->is_flash_sale) ? 'checked' : '' }} />
        عرض فلاش (Flash Sale)
      </label>
    </div>
    <button class="btn-cta">تحديث</button>
    <a href="{{ route('admin.products.index') }}" class="btn-cta" style="background:#9e9e9e">رجوع</a>
  </form>
</div>
@endsection


