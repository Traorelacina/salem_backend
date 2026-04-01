<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Social;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSocialController extends Controller
{
    // ── PUBLIC ───────────────────────────────────────────────
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

    // ── ADMIN ─────────────────────────────────────────────────
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
        $request->validate([
            'name'      => 'required|string|max:60',
            'icon'      => 'required|file|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'url'       => 'required|url|max:500',
            'color'     => 'nullable|string|max:20',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $iconPath = $request->file('icon')->store('socials', 'public');

        $social = Social::create([
            'name'      => $request->name,
            'icon_path' => $iconPath,
            'url'       => $request->url,
            'color'     => $request->color,
            'order'     => $request->order ?? 0,
            'is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN) ?? true,
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
        $request->validate([
            'name'      => 'sometimes|required|string|max:60',
            'icon'      => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'url'       => 'sometimes|required|url|max:500',
            'color'     => 'nullable|string|max:20',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'url', 'color', 'order']);
        $data['is_active'] = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);

        // Si une nouvelle icône est uploadée, remplacer l'ancienne
        if ($request->hasFile('icon')) {
            if ($social->icon_path) {
                Storage::disk('public')->delete($social->icon_path);
            }
            $data['icon_path'] = $request->file('icon')->store('socials', 'public');
        }

        $social->update($data);

        return response()->json([
            'success' => true,
            'data'    => $social->fresh(),
            'message' => 'Réseau social mis à jour.',
        ]);
    }

    // DELETE /api/admin/socials/{social}
    public function destroy(Social $social): JsonResponse
    {
        // Supprimer le fichier image associé
        if ($social->icon_path) {
            Storage::disk('public')->delete($social->icon_path);
        }

        $social->delete();

        return response()->json([
            'success' => true,
            'message' => 'Réseau social supprimé.',
        ]);
    }
}