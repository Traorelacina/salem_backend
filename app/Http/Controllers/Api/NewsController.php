<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * GET /api/v1/news
     * Retourne les articles publiés avec pagination
     * Paramètres: ?category=xxx&per_page=9&page=1
     */
    public function index(Request $request)
    {
        $query = News::published();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $perPage = $request->get('per_page', 9);
        $news    = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $news->getCollection()->map(function ($article) {
                return [
                    'id'           => $article->id,
                    'title'        => $article->title,
                    'slug'         => $article->slug,
                    'cover_url'    => $article->cover_url,
                    'category'     => $article->category,
                    'excerpt'      => $article->excerpt,
                    'author'       => $article->author,
                    'published_at' => $article->formatted_date,
                    'views'        => $article->views,
                ];
            }),
            'meta'    => [
                'current_page' => $news->currentPage(),
                'last_page'    => $news->lastPage(),
                'per_page'     => $news->perPage(),
                'total'        => $news->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/news/{slug}
     * Retourne le détail d'un article et incrémente les vues
     */
    public function show(string $slug)
    {
        $article = News::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $article->incrementViews();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'           => $article->id,
                'title'        => $article->title,
                'slug'         => $article->slug,
                'cover_url'    => $article->cover_url,
                'category'     => $article->category,
                'excerpt'      => $article->excerpt,
                'content'      => $article->content,
                'author'       => $article->author,
                'published_at' => $article->formatted_date,
                'views'        => $article->views,
            ],
        ]);
    }
}