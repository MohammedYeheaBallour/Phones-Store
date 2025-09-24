@extends('layouts.app')

@section('title', 'تسجيل دخول الإدمن')

@section('content')
<div class="container" style="max-width:480px; padding:2rem">
  <h2 style="margin-bottom:1rem">تسجيل دخول الإدمن</h2>
  @if ($errors->any())
    <div style="color:#b00020; margin-bottom:1rem">{{ $errors->first() }}</div>
  @endif
  <form method="POST" action="{{ route('admin.login.post') }}">
    @csrf
    <div class="form-group" style="margin-bottom:1rem">
      <label for="email">البريد الإلكتروني</label>
      <input id="email" type="email" name="email" required class="form-control" />
    </div>
    <div class="form-group" style="margin-bottom:1rem">
      <label for="password">كلمة المرور</label>
      <input id="password" type="password" name="password" required class="form-control" />
    </div>
    <button type="submit" class="btn-cta">دخول</button>
  </form>
</div>
@endsection


