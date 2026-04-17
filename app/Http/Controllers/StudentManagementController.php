<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentManagementController extends Controller
{
    public function index(): View
    {
        $pendingStudents = User::query()
            ->where('role', 'student')
            ->whereNull('approved_at')
            ->latest()
            ->get();

        $approvedStudents = User::query()
            ->with('approver')
            ->where('role', 'student')
            ->whereNotNull('approved_at')
            ->latest('approved_at')
            ->get();

        return view('teacher.students.index', [
            'pendingStudents' => $pendingStudents,
            'approvedStudents' => $approvedStudents,
        ]);
    }

    public function approve(User $student): RedirectResponse
    {
        abort_unless($student->role === 'student', 404);

        $student->update([
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('status', 'شاگرد با موفقیت تایید شد و حالا می‌تواند وارد سیستم شود.');
    }

    public function revoke(User $student): RedirectResponse
    {
        abort_unless($student->role === 'student', 404);

        $student->update([
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return back()->with('status', 'تایید شاگرد لغو شد و تا تایید دوباره نمی‌تواند وارد سیستم شود.');
    }

    public function destroy(User $student): RedirectResponse
    {
        abort_unless($student->role === 'student', 404);

        $student->delete();

        return back()->with('status', 'حساب شاگرد حذف شد.');
    }
}
