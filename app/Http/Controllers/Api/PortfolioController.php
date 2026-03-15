<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    /**
     * GET /api/v1/portfolio
     * Retourne toutes les réalisations actives (filtre par catégorie optionnel)
     */
    public function index(Request $request)
    {
        $query = Portfolio::active()->with('images');

        // Filtre par catégorie : ?category=web|mobile|logiciel
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filtre featured uniquement : ?featured=1
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        $portfolios = $query->get()->map(function ($portfolio) {
            return [
                'id'                => $portfolio->id,
                'title'             => $portfolio->title,
                'slug'              => $portfolio->slug,
                'client'            => $portfolio->client,
                'client_logo_url'   => $portfolio->client_logo_url,
                'category'          => $portfolio->category,
                'cover_url'         => $portfolio->cover_url,
                'short_description' => $portfolio->short_description,
                'is_featured'       => $portfolio->is_featured,
                'is_confidential'   => $portfolio->is_confidential,
                'external_link'     => $portfolio->external_link,
                'android_link'      => $portfolio->android_link,
                'ios_link'          => $portfolio->ios_link,
                'images'            => $portfolio->images->map(fn($img) => [
                    'id'      => $img->id,
                    'url'     => $img->url,
                    'caption' => $img->caption,
                ]),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $portfolios,
        ]);
    }

    /**
     * GET /api/v1/portfolio/{slug}
     * Retourne le détail d'une réalisation avec sa galerie
     */
    public function show(string $slug)
    {
        $portfolio = Portfolio::with('images')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                => $portfolio->id,
                'title'             => $portfolio->title,
                'slug'              => $portfolio->slug,
                'client'            => $portfolio->client,
                'client_logo_url'   => $portfolio->client_logo_url,
                'category'          => $portfolio->category,
                'cover_url'         => $portfolio->cover_url,
                'short_description' => $portfolio->short_description,
                'content'           => $portfolio->content,
                'is_featured'       => $portfolio->is_featured,
                'is_confidential'   => $portfolio->is_confidential,
                'external_link'     => $portfolio->external_link,
                'android_link'      => $portfolio->android_link,
                'ios_link'          => $portfolio->ios_link,
                'images'            => $portfolio->images->map(fn($img) => [
                    'id'      => $img->id,
                    'url'     => $img->url,
                    'caption' => $img->caption,
                    'order'   => $img->order,
                ]),
            ],
        ]);
    }
}