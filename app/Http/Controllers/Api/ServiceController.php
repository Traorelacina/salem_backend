<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * GET /api/v1/services
     * Retourne tous les services actifs triés par ordre
     */
    public function index()
    {
        $services = Service::active()->get()->map(function ($service) {
            return [
                'id'                => $service->id,
                'title'             => $service->title,
                'slug'              => $service->slug,
                'icon'              => $service->icon,
                'image_url'         => $service->image_url,
                'short_description' => $service->short_description,
                'content'           => $service->content,
                'order'             => $service->order,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $services,
        ]);
    }

    /**
     * GET /api/v1/services/{slug}
     * Retourne le détail d'un service
     */
    public function show(string $slug)
    {
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                => $service->id,
                'title'             => $service->title,
                'slug'              => $service->slug,
                'icon'              => $service->icon,
                'image_url'         => $service->image_url,
                'short_description' => $service->short_description,
                'content'           => $service->content,
                'order'             => $service->order,
            ],
        ]);
    }
}