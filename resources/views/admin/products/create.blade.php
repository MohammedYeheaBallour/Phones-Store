@extends('layouts.app')

@section('title','إضافة منتج')

@section('content')
<div class="container" style="padding:2rem;max-width:720px">
  <h2 style="margin-bottom:1rem">إضافة منتج</h2>
  <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="form-group" style="margin-bottom:1rem">
      <label>الاسم</label>
      <input type="text" name="name" value="{{ old('name') }}" required />
      @error('name')<div style="color:#b00020">{{ $message }}</div>@enderror
    </div>
    <div class="form-group" style="margin-bottom:1rem">
      <label>الوصف</label>
      <textarea name="description">{{ old('description') }}</textarea>
    </div>
    <div class="form-group" style="margin-bottom:1rem">
      <label>السعر</label>
      <input type="number" step="0.01" name="price" value="{{ old('price', 0) }}" required />
      @error('price')<div style="color:#b00020">{{ $message }}</div>@enderror
    </div>
    <div class="form-group" style="margin-bottom:1rem">
      <label>الصورة</label>
      <input type="file" name="image" accept="image/*" />
      @error('image')<div style="color:#b00020">{{ $message }}</div>@enderror
    </div>
    <div class="form-group" style="margin-bottom:1rem">
      <label>
        <input type="checkbox" name="is_flash_sale" value="1" {{ old('is_flash_sale') ? 'checked' : '' }} />
        عرض فلاش (Flash Sale)
      </label>
    </div>
    <button class="btn-cta">حفظ</button>
    <a href="{{ route('admin.products.index') }}" class="btn-cta" style="background:#9e9e9e">رجوع</a>
  </form>
</div>
@endsection


