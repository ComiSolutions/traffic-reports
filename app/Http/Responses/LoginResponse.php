<?php

namespace App\Http\Responses;

use App\Enums\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $user = $request->user();

        if ($request->wantsJson()) {
            return new JsonResponse(['two_factor' => false], 200);
        }

        if ($user?->role === UserRole::Admin) {
            return redirect()->to('/admin/dashboard');
        }

        $team = $user?->currentTeam ?? $user?->personalTeam();

        if ($team) {
            URL::defaults(['current_team' => $team->slug]);
        }

        return redirect()->to('/dashboard');
    }
}
