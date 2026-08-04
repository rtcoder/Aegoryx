<?php

namespace App\Http\Controllers;

use App\Models\System\Identity;
use App\Models\Tenant\User;
use App\Support\Theme\ThemePreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class ThemePreferenceController extends Controller
{
    public function admin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'theme' => ['required', 'string', Rule::in(ThemePreference::values())],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first('theme'),
                'errors' => ['theme' => $validator->errors()->get('theme')],
            ], 422);
        }

        $validated = $validator->validated();

        /** @var Identity $user */
        $user = $request->user('admin');
        $theme = ThemePreference::from($validated['theme']);

        $user->forceFill(['theme' => $theme])->save();

        return response()->json(['theme' => $theme->value]);
    }

    public function tenant(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'theme' => ['required', 'string', Rule::in(ThemePreference::values())],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first('theme'),
                'errors' => ['theme' => $validator->errors()->get('theme')],
            ], 422);
        }

        $validated = $validator->validated();

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
