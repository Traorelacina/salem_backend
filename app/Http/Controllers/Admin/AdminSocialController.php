<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Social;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSocialController extends Controller
{
    // ── PUBLIC ──────────────────────────────────────────────
    // GET /api/v1/socials  → utilisé par le Footer React
    public function index(): JsonResponse
    {
        $socials = Social::where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $socials,
        ]);
    }

    // ── ADMIN ────────────────────────────────────────────────
    // GET /api/admin/socials
    public function adminIndex(): JsonResponse
    {
        $socials = Social::orderBy('order')->get();

        return response()->json([
            'success' => true,
            'data'    => $socials,
        ]);
    }

    // POST /api/admin/socials
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:60',
            'icon'      => 'required|string|max:40',
            'url'       => 'required|url|max:500',
            'color'     => 'nullable|string|max:20',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $social = Social::create([
            'name'      => $validated['name'],
            'icon'      => $validated['icon'],
            'url'       => $validated['url'],
            'color'     => $validated['color'] ?? null,
            'order'     => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $social,
            'message' => 'Réseau social ajouté avec succès.',
        ], 201);
    }

    // PUT /api/admin/socials/{social}
    public function update(Request $request, Social $social): JsonResponse
    {
        $validated = $request->validate([
            'name'      => 'sometimes|required|string|max:60',
            'icon'      => 'sometimes|required|string|max:40',
            'url'       => 'sometimes|required|url|max:500',
            'color'     => 'nullable|string|max:20',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $social->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $social->fresh(),
            'message' => 'Réseau social mis à jour.',
        ]);
    }

    // DELETE /api/admin/socials/{social}
    public function destroy(Social $social): JsonResponse
    {
        $social->delete();

        return response()->json([
            'success' => true,
            'message' => 'Réseau social supprimé.',
        ]);
    }
}