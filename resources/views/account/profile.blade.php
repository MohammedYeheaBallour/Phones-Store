@extends('layouts.app')

@section('title', 'حسابي - Phones Store')

@section('content')

<!-- Simple navbar for account page -->
<nav>
  <div class="logo" aria-label="شعار المتجر" onclick="window.location.href='/'" style="cursor: pointer;">
    <i class="fas fa-store"></i>
    <span>متجر إلكتروني</span>
  </div>
  <div class="search-bar">
    <a href="/" class="btn-cta" style="padding: 0.6rem 1rem; text-decoration: none; white-space: nowrap;">
      <i class="fas fa-arrow-right"></i>
      العودة للرئيسية
    </a>
  </div>
  <div class="icons" aria-label="أيقونات المتجر">
    <button class="icon-btn" id="darkModeToggle" title="الوضع الليلي">
      <i class="fas fa-moon"></i>
    </button>
    <button class="icon-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;" title="الحساب - أنت هنا">
      <i class="fas fa-user"></i>
    </button>
  </div>
</nav>

<div class="container" style="margin-top:6rem; min-height:60vh;">
  <div class="page-header" style="text-align: center; margin-bottom: 2rem;" data-aos="fade-down">
    <h1 style="font-size:2rem;">حسابي</h1>
    <p style="color:var(--text-light);">هنا بيانات حسابك الشخصية</p>
  </div>

  <div class="account-card" style="max-width:900px;margin:0 auto;padding:2rem;background:var(--white);border-radius:12px;box-shadow:var(--shadow-md);">
    @if(auth()->check())
      <h3 style="margin-top:0;">معلومات المستخدم</h3>
      <ul style="list-style:none;padding:0;margin:0;line-height:2;font-size:1rem;color:var(--text-color);">
        <li><strong>الاسم:</strong> {{ auth()->user()->name }}</li>
        <li><strong>البريد الإلكتروني:</strong> {{ auth()->user()->email }}</li>
        <li><strong>تاريخ الانضمام:</strong> {{ auth()->user()->created_at ? auth()->user()->created_at->format('Y-m-d') : '-' }}</li>
      </ul>

      <div style="margin-top:1.5rem; display:flex; gap:1rem;">
        <a href="/" class="btn-cta" style="background:linear-gradient(135deg,#2563eb,#1e40af);">تصفح المنتجات</a>
        <form method="POST" action="/logout">
          @csrf
          <button type="submit" class="btn-cta" style="background:linear-gradient(135deg,#ef4444,#dc2626);">تسجيل الخروج</button>
        </form>
      </div>
    @else
      <h3>مرحبًا بك</h3>
      <p>يرجى تسجيل الدخول أو إنشاء حساب لعرض معلوماتك.</p>
      <div style="margin-top:1rem; display:flex; gap:1rem;">
        <a href="/admin/login" class="btn-cta">تسجيل الدخول</a>
        <a href="/" class="btn-cta" style="background:linear-gradient(135deg,#10b981,#059669);">تصفح كزائر</a>
      </div>
    @endif
  </div>
</div>

@endsection
