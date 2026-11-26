<?php

declare(strict_types=1);

namespace App\Modules\Identity\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * S2-02A: UI foundation for the Member dashboard.
 * This controller deliberately does NOT fabricate metrics for Tracker/Matching.
 * Authentication, email verification and membership are enforced by the route.
 */
final class MemberDashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        // Fail closed if someone accidentally removes the auth middleware.
        abort_if($user === null, 403);

        return Inertia::render('Mentora/Member/Dashboard', [
            'member' => [
                'name' => (string) $user->name,
            ],
        ]);
    }
}
