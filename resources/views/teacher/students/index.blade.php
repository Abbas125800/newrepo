@extends('layouts.auth', ['title' => 'مدیریت شاگردان'])

@section('content')
    <section class="panel" style="margin-bottom: 20px;">
        <div class="dashboard-header">
            <div>
                <div class="eyebrow">مدیریت شاگردان</div>
                <h2>تایید و کنترل حساب شاگردان</h2>
                <p class="muted">شاگردان تا قبل از تایید استاد اجازه ورود به سیستم را ندارند. از اینجا می‌توانید آن‌ها را تایید، لغو تایید یا حذف کنید.</p>
            </div>
            <div class="inline-actions">
                <a class="button secondary" href="{{ route('teacher.dashboard') }}">بازگشت به داشبورد</a>
            </div>
        </div>
    </section>

    <section class="grid two">
        <div class="card">
            <h3>شاگردان در انتظار تایید</h3>
            <div class="list">
                @forelse ($pendingStudents as $student)
                    <div class="list-item">
                        <div>
                            <strong>{{ $student->name }}</strong>
                            <div class="muted">{{ $student->email }}</div>
                        </div>
                        <div class="inline-actions">
                            <form method="POST" action="{{ route('teacher.students.approve', $student) }}">
                                @csrf
                                @method('PATCH')
                                <button class="button primary" type="submit">تایید</button>
                            </form>
                            <form method="POST" action="{{ route('teacher.students.destroy', $student) }}" onsubmit="return confirm('آیا از حذف این حساب مطمئن هستید؟');">
                                @csrf
                                @method('DELETE')
                                <button class="button secondary" type="submit">حذف</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty">فعلاً شاگرد در انتظار تایید وجود ندارد.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <h3>شاگردان تاییدشده</h3>
            <div class="list">
                @forelse ($approvedStudents as $student)
                    <div class="list-item">
                        <div>
                            <strong>{{ $student->name }}</strong>
                            <div class="muted">
                                {{ $student->email }}
                                @if ($student->approved_at)
                                    | تایید در {{ $student->approved_at->timezone(config('app.timezone'))->format('Y/m/d H:i') }}
                                @endif
                                @if ($student->approver)
                                    | توسط {{ $student->approver->name }}
                                @endif
                            </div>
                        </div>
                        <div class="inline-actions">
                            <form method="POST" action="{{ route('teacher.students.revoke', $student) }}">
                                @csrf
                                @method('PATCH')
                                <button class="button secondary" type="submit">لغو تایید</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty">هنوز هیچ شاگرد تاییدشده‌ای وجود ندارد.</div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
