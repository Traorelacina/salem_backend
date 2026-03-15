<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Solution;

class SolutionController extends Controller
{
    /**
     * GET /api/v1/solutions
     * Retourne toutes les solutions actives triées par ordre
     */
    public function index()
    {
        $solutions = Solution::active()->get()->map(function ($solution) {
            return [
                'id'                => $solution->id,
                'title'             => $solution->title,
                'slug'              => $solution->slug,
                'logo_url'          => $solution->logo_url,
                'cover_url'         => $solution->cover_url,
                'short_description' => $solution->short_description,
                'category'          => $solution->category,
                'external_link'     => $solution->external_link,
                'is_featured'       => $solution->is_featured,
                'order'             => $solution->order,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $solutions,
        ]);
    }

    /**
     * GET /api/v1/solutions/{slug}
     * Retourne le détail d'une solution
     */
    public function show(string $slug)
    {
        $solution = Solution::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                => $solution->id,
                'title'             => $solution->title,
                'slug'              => $solution->slug,
                'logo_url'          => $solution->logo_url,
                'cover_url'         => $solution->cover_url,
                'short_description' => $solution->short_description,
                'content'           => $solution->content,
                'category'          => $solution->category,
                'external_link'     => $solution->external_link,
                'is_featured'       => $solution->is_featured,
            ],
        ]);
    }
}