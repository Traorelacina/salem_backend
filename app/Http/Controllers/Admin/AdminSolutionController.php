<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Solution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminSolutionController extends Controller
{
    /**
     * GET /api/admin/solutions
     * Liste toutes les solutions
     */
    public function index()
    {
        $solutions = Solution::orderBy('order')->get()->map(function ($solution) {
            return array_merge($solution->toArray(), [
                'logo_url'  => $solution->logo_url,
                'cover_url' => $solution->cover_url,
            ]);
        });

        return response()->json(['success' => true, 'data' => $solutions]);
    }

    /**
     * POST /api/admin/solutions
     * Créer une nouvelle solution
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'             => 'required|string|max:150',
            'slug'              => 'nullable|string|unique:solutions,slug',
            'logo'              => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'cover_image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'short_description' => 'required|string',
            'content'           => 'required|string',
            'external_link'     => 'nullable|url',
            'category'          => 'nullable|string|max:100',
            'order'             => 'nullable|integer|min:0',
            'is_active'         => 'nullable|boolean',
            'is_featured'       => 'nullable|boolean',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('solutions/logos', 'public');
        }
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('solutions/covers', 'public');
        }

        $data['slug']        = $data['slug'] ?? Str::slug($data['title']);
        $data['order']       = $data['order'] ?? 0;
        $data['is_active']   = $data['is_active'] ?? true;
        $data['is_featured'] = $data['is_featured'] ?? false;

        $solution = Solution::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Solution créée avec succès.',
            'data'    => array_merge($solution->toArray(), [
                'logo_url'  => $solution->logo_url,
                'cover_url' => $solution->cover_url,
            ]),
        ], 201);
    }

    /**
     * GET /api/admin/solutions/{solution}
     * Détail d'une solution
     */
    public function show(Solution $solution)
    {
        return response()->json([
            'success' => true,
            'data'    => array_merge($solution->toArray(), [
                'logo_url'  => $solution->logo_url,
                'cover_url' => $solution->cover_url,
            ]),
        ]);
    }

    /**
     * PUT /api/admin/solutions/{solution}
     * Mettre à jour une solution
     */
    public function update(Request $request, Solution $solution)
    {
        $data = $request->validate([
            'title'             => 'nullable|string|max:150',
            'slug'              => 'nullable|string|unique:solutions,slug,' . $solution->id,
            'logo'              => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'cover_image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'short_description' => 'nullable|string',
            'content'           => 'nullable|string',
            'external_link'     => 'nullable|url',
            'category'          => 'nullable|string|max:100',
            'order'             => 'nullable|integer|min:0',
            'is_active'         => 'nullable|boolean',
            'is_featured'       => 'nullable|boolean',
        ]);

        if ($request->hasFile('logo')) {
            if ($solution->logo) Storage::disk('public')->delete($solution->logo);
            $data['logo'] = $request->file('logo')->store('solutions/logos', 'public');
        }
        if ($request->hasFile('cover_image')) {
            if ($solution->cover_image) Storage::disk('public')->delete($solution->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('solutions/covers', 'public');
        }

        $solution->update($data);
        $solution->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Solution mise à jour.',
            'data'    => array_merge($solution->toArray(), [
                'logo_url'  => $solution->logo_url,
                'cover_url' => $solution->cover_url,
            ]),
        ]);
    }

    /**
     * DELETE /api/admin/solutions/{solution}
     * Supprimer une solution
     */
    public function destroy(Solution $solution)
    {
        if ($solution->logo)        Storage::disk('public')->delete($solution->logo);
        if ($solution->cover_image) Storage::disk('public')->delete($solution->cover_image);

        $solution->delete();

        return response()->json(['success' => true, 'message' => 'Solution supprimée.']);
    }
}