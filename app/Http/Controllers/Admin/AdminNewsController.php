<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminNewsController extends Controller
{
    /**
     * GET /api/admin/news
     * Liste tous les articles avec pagination
     */
    public function index(Request $request)
    {
        $query = News::latest();

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $news = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $news->getCollection()->map(function ($article) {
                return array_merge($article->toArray(), ['cover_url' => $article->cover_url]);
            }),
            'meta'    => [
                'current_page' => $news->currentPage(),
                'last_page'    => $news->lastPage(),
                'total'        => $news->total(),
            ],
        ]);
    }

    /**
     * POST /api/admin/news
     * Créer un nouvel article
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:250',
            'slug'         => 'nullable|string|unique:news,slug',
            'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'category'     => 'nullable|string|max:100',
            'excerpt'      => 'required|string',
            'content'      => 'required|string',
            'author'       => 'nullable|string|max:100',
            'is_published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('news', 'public');
        }

        $data['slug']   = $data['slug'] ?? Str::slug($data['title']);
        $data['author'] = $data['author'] ?? 'Salem Technology';

        // Si publié, définir la date de publication
        if (!empty($data['is_published']) && $data['is_published']) {
            $data['published_at'] = $data['published_at'] ?? now();
        }

        $article = News::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Article créé avec succès.',
            'data'    => array_merge($article->toArray(), ['cover_url' => $article->cover_url]),
        ], 201);
    }

    /**
     * GET /api/admin/news/{news}
     * Détail d'un article
     */
    public function show(News $news)
    {
        return response()->json([
            'success' => true,
            'data'    => array_merge($news->toArray(), ['cover_url' => $news->cover_url]),
        ]);
    }

    /**
     * PUT /api/admin/news/{news}
     * Mettre à jour un article
     */
    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'title'        => 'nullable|string|max:250',
            'slug'         => 'nullable|string|unique:news,slug,' . $news->id,
            'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'category'     => 'nullable|string|max:100',
            'excerpt'      => 'nullable|string',
            'content'      => 'nullable|string',
            'author'       => 'nullable|string|max:100',
            'is_published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($news->cover_image) Storage::disk('public')->delete($news->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('news', 'public');
        }

        // Si on publie pour la première fois
        if (isset($data['is_published']) && $data['is_published'] && !$news->published_at) {
            $data['published_at'] = $data['published_at'] ?? now();
        }

        $news->update($data);
        $news->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Article mis à jour.',
            'data'    => array_merge($news->toArray(), ['cover_url' => $news->cover_url]),
        ]);
    }

    /**
     * DELETE /api/admin/news/{news}
     * Supprimer un article
     */
    public function destroy(News $news)
    {
        if ($news->cover_image) Storage::disk('public')->delete($news->cover_image);
        $news->delete();

        return response()->json(['success' => true, 'message' => 'Article supprimé.']);
    }
}