<?php

declare(strict_types=1);

namespace App\Modules\Identity\Presentation\Http\Controllers;

use App\Modules\Identity\Application\Queries\GetAccountSummary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AccountSummaryController
{
    public function __invoke(Request $request, GetAccountSummary $getSummary): JsonResponse
    {
        $user = $request->user();
        abort_if($user === null, 401);

        $summary = $getSummary->execute($user->getAuthIdentifier());
        abort_if($summary === null, 404);

        return response()->json([
            'data' => [
                'id' => $summary->id,
                'name' => $summary->name,
                'email' => $summary->email,
            ],
        ]);
    }
}
