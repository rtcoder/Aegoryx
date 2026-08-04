<?php

namespace App\Http\Controllers;

use App\Models\System\Identity;
use App\Models\Tenant\User;
use App\Support\Theme\ThemePreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

final class ThemePreferenceController extends Controller
{
    public function admin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => ['required', 'string', Rule::in(ThemePreference::values())],
        ]);

        /** @var Identity $user */
        $user = $request->user('admin');
        $theme = ThemePreference::from($validated['theme']);

        $user->forceFill(['theme' => $theme])->save();

        return response()->json(['theme' => $theme->value]);
    }

    public function tenant(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => ['required', 'string', Rule::in(ThemePreference::values())],
        ]);

        /** @var User $user */
        $user = $request->user('web');
        $theme = ThemePreference::from($validated['theme']);

        $user->forceFill([
            'theme' => $theme,
            'updated_by' => $user->id,
        ])->save();

        return response()->json(['theme' => $theme->value]);
    }
}
