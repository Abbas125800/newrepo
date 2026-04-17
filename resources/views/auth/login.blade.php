@extends('layouts.auth', ['title' => 'ورود به حساب'])

@section('content')
    <section class="card auth-card">
        <div class="eyebrow">ورود کاربران</div>
        <h2>خوش آمدید</h2>
        <p class="muted">برای ورود به سامانه، ایمیل و رمز عبور خود را وارد کنید.</p>

        @if ($errors->any())
            <div class="alert error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div class="input-row">
                <label class="field-label" for="email">ایمیل</label>
                <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="input-row">
                <label class="field-label" for="password">رمز عبور</label>
                <input class="input" id="password" type="password" name="password" required>
            </div>

            <div class="input-row">
                <label style="display:flex; align-items:center; gap:10px; color:var(--muted);">
                    <input type="checkbox" name="remember" value="1">
                    <span>مرا به خاطر بسپار</span>
                </label>
            </div>

            <button class="button primary" type="submit" style="width:100%;">ورود به حساب</button>
        </form>

        <p class="muted" style="margin-top: 18px;">
            حساب ندارید؟
            <a href="{{ route('register') }}" style="color: var(--brand-deep); font-weight: 700;">ثبت‌نام کنید</a>
        </p>
    </section>
@endsection
