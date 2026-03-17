<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\PortfolioImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminPortfolioController extends Controller
{
    // ══════════════════════════════════════════════════════
    //  CATÉGORIES
    // ══════════════════════════════════════════════════════

    /**
     * GET /api/admin/portfolio-categories
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
     * POST /api/admin/portfolio-categories
     */
    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:portfolio_categories,name',
            'order' => 'nullable|integer|min:0',
        ]);

        $id = DB::table('portfolio_categories')->insertGetId([
            'name'       => $data['name'],
            'slug'       => Str::slug($data['name']),
            'order'      => $data['order'] ?? 0,
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data'    => DB::table('portfolio_categories')->find($id),
        ], 201);
    }

    /**
     * DELETE /api/admin/portfolio-categories/{id}
     */
    public function destroyCategory(int $id)
    {
        DB::table('portfolio_categories')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════
    //  PORTFOLIO
    // ══════════════════════════════════════════════════════

    /**
     * GET /api/admin/portfolio
     */
    public function index()
    {
        $portfolios = Portfolio::with('images')->orderBy('order')->get()->map(function ($portfolio) {
            return array_merge($portfolio->toArray(), [
                'cover_url'       => $portfolio->cover_url,
                'client_logo_url' => $portfolio->client_logo_url,
                'images'          => $portfolio->images->map(fn($img) => [
                    'id'      => $img->id,
                    'url'     => $img->url,
                    'caption' => $img->caption,
                    'order'   => $img->order,
                ]),
            ]);
        });

        return response()->json(['success' => true, 'data' => $portfolios]);
    }

    /**
     * POST /api/admin/portfolio
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'             => 'required|string|max:200',
            'slug'              => 'nullable|string|unique:portfolios,slug',
            'client'            => 'required|string|max:150',
            'client_logo'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'category'          => 'required|string|max:100',
            'cover_image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'short_description' => 'required|string',
            'content'           => 'required|string',
            'external_link'     => 'nullable|url',
            'android_link'      => 'nullable|url',
            'ios_link'          => 'nullable|url',
            'is_confidential'   => 'nullable|boolean',
            'is_active'         => 'nullable|boolean',
            'is_featured'       => 'nullable|boolean',
            'order'             => 'nullable|integer|min:0',
            'gallery.*'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($request->hasFile('client_logo')) {
            $data['client_logo'] = $request->file('client_logo')->store('portfolio/logos', 'public');
        }
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('portfolio/covers', 'public');
        }

        $data['slug']            = $data['slug'] ?? Str::slug($data['title']);
        $data['order']           = $data['order'] ?? 0;
        $data['is_active']       = $data['is_active'] ?? true;
        $data['is_featured']     = $data['is_featured'] ?? false;
        $data['is_confidential'] = $data['is_confidential'] ?? false;

        $portfolio = Portfolio::create($data);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $index => $file) {
                $path = $file->store('portfolio/gallery', 'public');
                PortfolioImage::create([
                    'portfolio_id' => $portfolio->id,
                    'path'         => $path,
                    'order'        => $index,
                ]);
            }
        }

        $portfolio->load('images');

        return response()->json([
            'success' => true,
            'message' => 'Réalisation créée avec succès.',
            'data'    => array_merge($portfolio->toArray(), [
                'cover_url'       => $portfolio->cover_url,
                'client_logo_url' => $portfolio->client_logo_url,
                'images'          => $portfolio->images->map(fn($img) => [
                    'id' => $img->id, 'url' => $img->url, 'caption' => $img->caption,
                ]),
            ]),
        ], 201);
    }

    /**
     * GET /api/admin/portfolio/{portfolio}
     */
    public function show(Portfolio $portfolio)
    {
        $portfolio->load('images');

        return response()->json([
            'success' => true,
            'data'    => array_merge($portfolio->toArray(), [
                'cover_url'       => $portfolio->cover_url,
                'client_logo_url' => $portfolio->client_logo_url,
                'images'          => $portfolio->images->map(fn($img) => [
                    'id' => $img->id, 'url' => $img->url, 'caption' => $img->caption, 'order' => $img->order,
                ]),
            ]),
        ]);
    }

    /**
     * PUT /api/admin/portfolio/{portfolio}
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        $data = $request->validate([
            'title'             => 'nullable|string|max:200',
            'slug'              => 'nullable|string|unique:portfolios,slug,' . $portfolio->id,
            'client'            => 'nullable|string|max:150',
            'client_logo'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'category'          => 'nullable|string|max:100',
            'cover_image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'short_description' => 'nullable|string',
            'content'           => 'nullable|string',
            'external_link'     => 'nullable|url',
            'android_link'      => 'nullable|url',
            'ios_link'          => 'nullable|url',
            'is_confidential'   => 'nullable|boolean',
            'is_active'         => 'nullable|boolean',
            'is_featured'       => 'nullable|boolean',
            'order'             => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('client_logo')) {
            if ($portfolio->client_logo) Storage::disk('public')->delete($portfolio->client_logo);
            $data['client_logo'] = $request->file('client_logo')->store('portfolio/logos', 'public');
        }
        if ($request->hasFile('cover_image')) {
            if ($portfolio->cover_image) Storage::disk('public')->delete($portfolio->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('portfolio/covers', 'public');
        }

        $portfolio->update($data);
        $portfolio->refresh()->load('images');

        return response()->json([
            'success' => true,
            'message' => 'Réalisation mise à jour.',
            'data'    => array_merge($portfolio->toArray(), [
                'cover_url'       => $portfolio->cover_url,
                'client_logo_url' => $portfolio->client_logo_url,
                'images'          => $portfolio->images->map(fn($img) => [
                    'id' => $img->id, 'url' => $img->url, 'caption' => $img->caption,
                ]),
            ]),
        ]);
    }

    /**
     * DELETE /api/admin/portfolio/{portfolio}
     */
    public function destroy(Portfolio $portfolio)
    {
        foreach ($portfolio->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        if ($portfolio->cover_image) Storage::disk('public')->delete($portfolio->cover_image);
        if ($portfolio->client_logo) Storage::disk('public')->delete($portfolio->client_logo);

        $portfolio->delete();

        return response()->json(['success' => true, 'message' => 'Réalisation supprimée.']);
    }

    /**
     * POST /api/admin/portfolio/{portfolio}/images
     */
    public function addImage(Request $request, Portfolio $portfolio)
    {
        $request->validate([
            'image'   => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'caption' => 'nullable|string|max:200',
        ]);

        $path  = $request->file('image')->store('portfolio/gallery', 'public');
        $order = $portfolio->images()->max('order') + 1;

        $image = PortfolioImage::create([
            'portfolio_id' => $portfolio->id,
            'path'         => $path,
            'caption'      => $request->caption,
            'order'        => $order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Image ajoutée.',
            'data'    => ['id' => $image->id, 'url' => $image->url, 'caption' => $image->caption],
        ], 201);
    }

    /**
     * DELETE /api/admin/portfolio/images/{image}
     */
    public function deleteImage(PortfolioImage $image)
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Image supprimée.']);
    }
}
