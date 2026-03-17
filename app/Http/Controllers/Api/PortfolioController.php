<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PortfolioController extends Controller
{
    /**
     * GET /api/v1/portfolio-categories
     * Retourne les catégories actives pour les filtres publics
     */
    public function categories()
    {
        $categories = DB::table('portfolio_categories')
            ->where('is_active', true)
            ->orderBy('order')
            ->get(['id', 'name', 'slug']);

        return response()->json(['success' => true, 'data' => $categories]);
    }

    /**
     * GET /api/v1/portfolio
     */
    public function index(Request $request)
    {
        $query = Portfolio::active()->with('images');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

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

        return response()->json(['success' => true, 'data' => $portfolios]);
    }

    /**
     * GET /api/v1/portfolio/{slug}
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
