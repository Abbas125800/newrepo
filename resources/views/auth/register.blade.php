@extends('layouts.auth', ['title' => 'ایجاد حساب'])

@section('content')
    <section class="card auth-card">
        <div class="eyebrow">ایجاد حساب جدید</div>
        <h2>شروع همکاری در سامانه</h2>
        <p class="muted">فرم زیر را تکمیل کنید تا حساب استاد یا شاگرد شما ساخته شود.</p>

        @if ($errors->any())
            <div class="alert error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <div class="input-row">
                <label class="field-label" for="name">نام کامل</label>
                <input class="input" id="name" type="text" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="input-row">
                <label class="field-label" for="email">ایمیل</label>
                <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="input-row">
                <label class="field-label" for="role">نوع حساب</label>
                <select class="select" id="role" name="role" required>
                    <option value="teacher" @selected(old('role') === 'teacher')>استاد</option>
                    <option value="student" @selected(old('role', 'student') === 'student')>شاگرد</option>
                </select>
            </div>

            <div class="grid two">
                <div class="input-row">
                    <label class="field-label" for="password">رمز عبور</label>
                    <input class="input" id="password" type="password" name="password" required>
                </div>

                <div class="input-row">
                    <label class="field-label" for="password_confirmation">تکرار رمز عبور</label>
                    <input class="input" id="password_confirmation" type="password" name="password_confirmation" required>
                </div>
            </div>

            <button class="button primary" type="submit" style="width:100%;">ایجاد حساب و ورود</button>
        </form>

        <p class="muted" style="margin-top: 18px;">
            قبلاً حساب ساخته‌اید؟
            <a href="{{ route('login') }}" style="color: var(--brand-deep); font-weight: 700;">وارد شوید</a>
        </p>
    </section>
@endsection
