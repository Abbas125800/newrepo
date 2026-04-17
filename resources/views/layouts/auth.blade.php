<!DOCTYPE html>
<html lang="fa" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'سامانه امتحان آنلاین' }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=vazirmatn:400,500,700,800" rel="stylesheet" />
        <style>
            :root {
                --bg: #f4efe8;
                --bg-dark: #201814;
                --panel: rgba(255, 252, 247, 0.88);
                --panel-strong: #fffaf3;
                --line: rgba(83, 52, 34, 0.12);
                --text: #2c2019;
                --muted: #796657;
                --brand: #c65a11;
                --brand-deep: #88370d;
                --teal: #0f766e;
                --amber: #f1bb59;
                --danger: #b42318;
                --shadow: 0 30px 80px rgba(67, 34, 16, 0.14);
            }

            * { box-sizing: border-box; }
            body {
                margin: 0;
                min-height: 100vh;
                font-family: 'Vazirmatn', sans-serif;
                color: var(--text);
                background:
                    radial-gradient(circle at 15% 20%, rgba(241, 187, 89, 0.5), transparent 28%),
                    radial-gradient(circle at 85% 15%, rgba(15, 118, 110, 0.14), transparent 22%),
                    radial-gradient(circle at 80% 82%, rgba(198, 90, 17, 0.14), transparent 24%),
                    linear-gradient(145deg, #fbf6ef 0%, #efe2d3 100%);
            }

            a { color: inherit; text-decoration: none; }
            button, input, select { font: inherit; }

            .shell {
                width: min(1220px, calc(100% - 28px));
                margin: 0 auto;
                padding: 24px 0 48px;
            }

            .nav {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                margin-bottom: 24px;
                padding: 12px 16px;
                border: 1px solid rgba(255, 255, 255, 0.4);
                background: rgba(255, 252, 247, 0.55);
                backdrop-filter: blur(16px);
                border-radius: 24px;
                box-shadow: 0 8px 30px rgba(72, 39, 19, 0.08);
            }

            .brand {
                display: inline-flex;
                align-items: center;
                gap: 12px;
                font-weight: 800;
            }

            .brand-mark {
                width: 44px;
                height: 44px;
                border-radius: 16px;
                display: grid;
                place-items: center;
                color: #fff;
                background: linear-gradient(135deg, var(--brand), var(--brand-deep));
                box-shadow: 0 12px 24px rgba(136, 55, 13, 0.26);
            }

            .nav-actions, .inline-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                align-items: center;
            }

            .button {
                display: inline-flex;
                justify-content: center;
                align-items: center;
                gap: 10px;
                min-width: 140px;
                padding: 14px 20px;
                border: 0;
                border-radius: 18px;
                cursor: pointer;
                font-size: 15px;
                font-weight: 700;
                transition: transform 180ms ease, box-shadow 180ms ease, opacity 180ms ease;
            }

            .button:hover { transform: translateY(-1px); }
            .button.primary {
                color: #fff;
                background: linear-gradient(135deg, var(--brand), var(--brand-deep));
                box-shadow: 0 18px 34px rgba(136, 55, 13, 0.22);
            }

            .button.secondary {
                color: var(--text);
                background: rgba(255,255,255,0.72);
                border: 1px solid var(--line);
            }

            .hero {
                display: grid;
                grid-template-columns: 1.08fr 0.92fr;
                gap: 22px;
                margin-bottom: 22px;
            }

            .panel, .card {
                position: relative;
                overflow: hidden;
                background: var(--panel);
                border: 1px solid rgba(255, 255, 255, 0.45);
                border-radius: 30px;
                box-shadow: var(--shadow);
                backdrop-filter: blur(16px);
            }

            .panel { padding: 28px; }
            .card { padding: 24px; }

            .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 18px;
                padding: 8px 12px;
                border-radius: 999px;
                background: rgba(255,255,255,0.68);
                color: var(--brand-deep);
                font-size: 13px;
                font-weight: 800;
            }

            h1, h2, h3, p { margin-top: 0; }
            h1 {
                font-size: clamp(34px, 5vw, 64px);
                line-height: 1.03;
                margin-bottom: 16px;
            }

            h2 {
                font-size: clamp(22px, 3vw, 34px);
                margin-bottom: 12px;
            }

            h3 {
                font-size: 18px;
                margin-bottom: 8px;
            }

            .lead, .muted {
                color: var(--muted);
                line-height: 1.95;
            }

            .hero-visual {
                min-height: 100%;
                background:
                    linear-gradient(145deg, rgba(255,255,255,0.86), rgba(252, 242, 227, 0.74)),
                    var(--panel);
            }

            .metrics {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 14px;
                margin-top: 22px;
            }

            .metric {
                padding: 18px;
                border-radius: 24px;
                background: rgba(255,255,255,0.7);
                border: 1px solid rgba(255,255,255,0.7);
            }

            .metric strong {
                display: block;
                margin-bottom: 8px;
                font-size: 30px;
            }

            .auth-card {
                max-width: 540px;
                margin: 0 auto;
            }

            .grid {
                display: grid;
                gap: 18px;
            }

            .grid.two {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .field-label {
                display: block;
                margin-bottom: 8px;
                font-weight: 700;
                font-size: 14px;
            }

            .input, .select {
                width: 100%;
                padding: 15px 16px;
                border-radius: 18px;
                border: 1px solid var(--line);
                background: rgba(255,255,255,0.82);
                color: var(--text);
                outline: none;
            }

            .input:focus, .select:focus {
                border-color: rgba(198, 90, 17, 0.5);
                box-shadow: 0 0 0 4px rgba(198, 90, 17, 0.09);
            }

            .input-row { margin-bottom: 16px; }

            .alert {
                margin-bottom: 18px;
                padding: 14px 16px;
                border-radius: 18px;
                font-size: 14px;
                line-height: 1.8;
            }

            .alert.error {
                color: var(--danger);
                background: rgba(180, 35, 24, 0.08);
            }

            .list, .table {
                display: grid;
                gap: 14px;
            }

            .list-item, .table-row {
                display: flex;
                justify-content: space-between;
                gap: 18px;
                align-items: center;
                padding: 16px 18px;
                border-radius: 20px;
                background: rgba(255,255,255,0.66);
                border: 1px solid rgba(255,255,255,0.65);
            }

            .badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                border-radius: 999px;
                font-size: 13px;
                font-weight: 800;
                background: rgba(198, 90, 17, 0.1);
                color: var(--brand-deep);
            }

            .badge.ok {
                background: rgba(15, 118, 110, 0.1);
                color: var(--teal);
            }

            .dashboard-header {
                display: flex;
                justify-content: space-between;
                align-items: start;
                gap: 18px;
            }

            .empty {
                padding: 18px;
                border-radius: 20px;
                background: rgba(255,255,255,0.52);
                border: 1px dashed var(--line);
                color: var(--muted);
            }

            @media (max-width: 960px) {
                .hero, .grid.two, .metrics {
                    grid-template-columns: 1fr;
                }

                .dashboard-header, .nav {
                    flex-direction: column;
                    align-items: stretch;
                }

                .nav-actions, .inline-actions {
                    justify-content: stretch;
                }

                .button { width: 100%; }
            }
        </style>
    </head>
    <body>
        <div class="shell">
            <nav class="nav">
                <a class="brand" href="{{ route('home') }}">
                    <span class="brand-mark">Q</span>
                    <span>سامانه امتحان آنلاین</span>
                </a>

                <div class="nav-actions">
            @auth
                        <a class="button secondary" href="{{ route('dashboard') }}">داشبورد من</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="button primary" type="submit">خروج</button>
                        </form>
                    @else
                        <a class="button secondary" href="{{ route('login') }}">ورود</a>
                        <a class="button primary" href="{{ route('register') }}">ایجاد حساب</a>
                    @endauth
                </div>
            </nav>

            @if (session('status'))
                <div class="alert" style="background: rgba(15, 118, 110, 0.09); color: var(--teal); margin-bottom: 18px;">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </div>
    </body>
</html>
