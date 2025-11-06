@extends('layouts.app')

@section('title', 'تعديل الحساب - Phones Store')

@section('content')

<nav>
  <div class="logo" onclick="window.location.href='/'" style="cursor:pointer;"><i class="fas fa-store"></i><span>متجر إلكتروني</span></div>
  <div class="search-bar"></div>
  <div class="icons">
    <button class="icon-btn" id="darkModeToggle"><i class="fas fa-moon"></i></button>
  </div>
</nav>

<div class="container" style="margin-top:6rem;">
  <div style="max-width:800px;margin:0 auto;background:var(--white);padding:2rem;border-radius:12px;box-shadow:var(--shadow-md);">
    <h2>تعديل الملف الشخصي</h2>
    @if(session('error'))<div class="toast error" style="display:block;margin:0.5rem 0;">{{ session('error') }}</div>@endif
    @if(session('success'))<div class="toast success" style="display:block;margin:0.5rem 0;">{{ session('success') }}</div>@endif

    @if(auth()->check())
      <form method="POST" action="{{ route('account.update') }}">
        @csrf
        <div class="form-group">
          <label for="name">الاسم</label>
          <input type="text" id="name" name="name" value="{{ auth()->user()->name }}" required />
        </div>
        <div class="form-group">
          <label for="email">البريد الإلكتروني</label>
          <input type="email" id="email" name="email" value="{{ auth()->user()->email }}" required />
        </div>
        <button class="btn-cta" type="submit">حفظ التغييرات</button>
      </form>

      <hr style="margin:1.5rem 0;" />

      <h3>تغيير كلمة المرور</h3>
      <form method="POST" action="{{ route('account.password.update') }}">
        @csrf
        <div class="form-group">
          <label for="current_password">كلمة المرور الحالية</label>
          <input type="password" id="current_password" name="current_password" required />
        </div>
        <div class="form-group">
          <label for="password">كلمة المرور الجديدة</label>
          <input type="password" id="password" name="password" required />
        </div>
        <div class="form-group">
          <label for="password_confirmation">تأكيد كلمة المرور</label>
          <input type="password" id="password_confirmation" name="password_confirmation" required />
        </div>
        <button class="btn-cta" type="submit">تغيير كلمة المرور</button>
      </form>
    @else
      <p>يجب تسجيل الدخول للوصول لهذه الصفحة.</p>
    @endif
  </div>
</div>

@endsection
