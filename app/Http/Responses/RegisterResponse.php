<?php

namespace App\Http\Responses;

use App\Enums\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): Response
    {
        $user = $request->user();

        if ($request->wantsJson()) {
            return new JsonResponse(['two_factor' => false], 201);
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
