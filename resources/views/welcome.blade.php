@extends('layouts.auth', ['title' => 'سامانه مدرن امتحان آنلاین'])

@section('content')
    <section class="hero">
        <div class="panel">
            <div class="eyebrow">نسخه جدید و نقش‌محور</div>
            <h1>ثبت‌نام و ورود حرفه‌ای برای استاد و شاگرد با داشبورد اختصاصی</h1>
            <p class="lead">
                این سامانه برای مدیریت مدرن امتحان آنلاین طراحی شده است؛ استادها می‌توانند آزمون‌ها را مدیریت کنند
                و شاگردان بعد از ورود، فضای مخصوص خودشان را برای شرکت در امتحان و دیدن نتیجه داشته باشند.
            </p>

            <div class="inline-actions" style="margin-top: 22px;">
                <a class="button primary" href="{{ route('register') }}">شروع ثبت‌نام</a>
                <a class="button secondary" href="{{ route('login') }}">ورود به حساب</a>
            </div>

            <div class="metrics">
                <div class="metric">
                    <strong>۲ نقش</strong>
                    <span class="muted">استاد و شاگرد</span>
                </div>
                <div class="metric">
                    <strong>ریسپانسیو</strong>
                    <span class="muted">نمایش عالی در موبایل و دسکتاپ</span>
                </div>
                <div class="metric">
                    <strong>سریع</strong>
                    <span class="muted">ورود و هدایت خودکار به داشبورد</span>
                </div>
            </div>
        </div>

        <div class="panel hero-visual">
            <div class="eyebrow">تجربه کاربری حرفه‌ای</div>
            <h2>چه چیزی آماده شده است؟</h2>
            <div class="list">
                <div class="list-item">
                    <div>
                        <h3>ثبت‌نام نقش‌محور</h3>
                        <p class="muted">کاربر هنگام ساخت حساب، استاد یا شاگرد بودن خود را انتخاب می‌کند.</p>
                    </div>
                    <span class="badge">Smart</span>
                </div>
                <div class="list-item">
                    <div>
                        <h3>ورود امن و ساده</h3>
                        <p class="muted">ورود با ایمیل و رمز عبور، همراه با یادآوری نشست و خروج استاندارد.</p>
                    </div>
                    <span class="badge ok">Secure</span>
                </div>
                <div class="list-item">
                    <div>
                        <h3>داشبورد جدا برای هر نقش</h3>
                        <p class="muted">بعد از ورود، کاربر مستقیماً به داشبورد مناسب خودش هدایت می‌شود.</p>
                    </div>
                    <span class="badge">Role-based</span>
                </div>
            </div>
        </div>
    </section>
@endsection
