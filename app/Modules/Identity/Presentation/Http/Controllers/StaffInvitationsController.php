<?php

declare(strict_types=1);

namespace App\Modules\Identity\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Identity\Application\Services\StaffInvitationService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The route requires auth + verified + role:superadmin + roles.assign.
 * The service repeats the authorization before any DB mutation.
 */
final class StaffInvitationsController extends Controller
{
    public function index(Request $request): Response
    {
        $page = User::query()
            ->select(['id', 'name', 'email', 'email_verified_at', 'created_at'])
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $ids = $page->getCollection()->pluck('id')->all();
        $roles = DB::table('user_roles')
            ->whereIn('user_id', $ids)
            ->orderBy('role_code')
            ->get(['user_id', 'role_code'])
            ->groupBy('user_id');

        $staffInvitationIds = DB::table('staff_invitation_events')
            ->where('event', 'created')
            ->whereIn('target_user_id', $ids)
            ->pluck('target_user_id')
            ->all();

        $staffInvitationSet = array_fill_keys($staffInvitationIds, true);

        $page->through(static function (User $user) use ($roles, $staffInvitationSet): array {
            $assignedRoles = $roles->get($user->getKey(), collect())
                ->pluck('role_code')->values()->all();

            return [
                'id' => $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $assignedRoles,
                'email_verified' => $user->hasVerifiedEmail(),
                'is_staff_invited' => isset($staffInvitationSet[$user->getKey()]),
                'created_at' => $user->created_at?->format('d-m-Y H:i'),
            ];
        });

        return Inertia::render('Mentora/SuperAdmin/Users', [
            'administrator' => ['name' => (string) $request->user()->name],
            'users' => $page,
            'notice' => $request->session()->get('staff_notice'),
            'warning' => $request->session()->get('staff_warning'),
            'mailerIsLocal' => in_array(config('mail.default'), ['log', 'array'], true),
        ]);
    }

    public function store(Request $request, StaffInvitationService $invitations): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:254'],
            'role_code' => ['required', 'string', Rule::in(StaffInvitationService::ALLOWED_ROLES)],
        ]);

        try {
            $result = $invitations->invite(
                $request->user(),
                $data['name'],
                $data['email'],
                $data['role_code'],
            );
        } catch (DomainException $exception) {
            return back()->withErrors(['email' => $exception->getMessage()])->withInput();
        }

        if (! $result['delivered']) {
            return redirect()->route('superadmin.users.index')->with(
                'staff_warning',
                'Akun staf telah dibuat, tetapi tautan aktivasi belum berhasil dikirim. Gunakan Kirim Ulang pada akun tersebut.'
            );
        }

        return redirect()->route('superadmin.users.index')->with(
            'staff_notice',
            'Akun staf dibuat. Tautan pembuatan password telah diproses oleh mailer MENTORA.'
        );
    }

    public function resend(Request $request, User $user, StaffInvitationService $invitations): RedirectResponse
    {
        try {
            $sent = $invitations->resend($request->user(), $user);
        } catch (DomainException $exception) {
            return redirect()->route('superadmin.users.index')->with('staff_warning', $exception->getMessage());
        }

        return redirect()->route('superadmin.users.index')->with(
            $sent ? 'staff_notice' : 'staff_warning',
            $sent
                ? 'Tautan pembuatan password berhasil diproses ulang.'
                : 'Pengiriman ulang gagal. Periksa konfigurasi mailer dan coba lagi.'
        );
    }
}
