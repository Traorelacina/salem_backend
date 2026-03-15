<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminServiceController extends Controller
{
    /**
     * GET /api/admin/services
     * Liste tous les services
     */
    public function index()
    {
        $services = Service::orderBy('order')->get()->map(function ($service) {
            return array_merge($service->toArray(), [
                'image_url' => $service->image_url,
            ]);
        });

        return response()->json(['success' => true, 'data' => $services]);
    }

    /**
     * POST /api/admin/services
     * Créer un nouveau service
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'             => 'required|string|max:150',
            'slug'              => 'nullable|string|unique:services,slug',
            'icon'              => 'nullable|string|max:100',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'short_description' => 'required|string',
            'content'           => 'required|string',
            'order'             => 'nullable|integer|min:0',
            'is_active'         => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $data['slug']      = $data['slug'] ?? Str::slug($data['title']);
        $data['order']     = $data['order'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;

        $service = Service::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Service créé avec succès.',
            'data'    => array_merge($service->toArray(), ['image_url' => $service->image_url]),
        ], 201);
    }

    /**
     * GET /api/admin/services/{service}
     * Détail d'un service
     */
    public function show(Service $service)
    {
        return response()->json([
            'success' => true,
            'data'    => array_merge($service->toArray(), ['image_url' => $service->image_url]),
        ]);
    }

    /**
     * PUT /api/admin/services/{service}
     * Mettre à jour un service
     */
    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'title'             => 'nullable|string|max:150',
            'slug'              => 'nullable|string|unique:services,slug,' . $service->id,
            'icon'              => 'nullable|string|max:100',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'short_description' => 'nullable|string',
            'content'           => 'nullable|string',
            'order'             => 'nullable|integer|min:0',
            'is_active'         => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Service mis à jour.',
            'data'    => array_merge($service->fresh()->toArray(), ['image_url' => $service->image_url]),
        ]);
    }

    /**
     * DELETE /api/admin/services/{service}
     * Supprimer un service
     */
    public function destroy(Service $service)
    {
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
        $service->delete();

        return response()->json(['success' => true, 'message' => 'Service supprimé.']);
    }

    /**
     * POST /api/admin/services/reorder
     * Réordonner les services
     * Body: { "items": [{"id": 1, "order": 0}, {"id": 2, "order": 1}] }
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items'         => 'required|array',
            'items.*.id'    => 'required|integer|exists:services,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            Service::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true, 'message' => 'Ordre mis à jour.']);
    }
}