<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminClientController extends Controller
{
    /**
     * GET /api/admin/clients
     * Liste tous les clients du bandeau défilant
     */
    public function index()
    {
        $clients = Client::orderBy('order')->get()->map(function ($client) {
            return array_merge($client->toArray(), ['logo_url' => $client->logo_url]);
        });

        return response()->json(['success' => true, 'data' => $clients]);
    }

    /**
     * POST /api/admin/clients
     * Ajouter un client au bandeau
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:150',
            'logo'      => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'website'   => 'nullable|url',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['logo']      = $request->file('logo')->store('clients', 'public');
        $data['order']     = $data['order'] ?? (Client::max('order') + 1);
        $data['is_active'] = $data['is_active'] ?? true;

        $client = Client::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Client ajouté.',
            'data'    => array_merge($client->toArray(), ['logo_url' => $client->logo_url]),
        ], 201);
    }

    /**
     * GET /api/admin/clients/{client}
     * Détail d'un client
     */
    public function show(Client $client)
    {
        return response()->json([
            'success' => true,
            'data'    => array_merge($client->toArray(), ['logo_url' => $client->logo_url]),
        ]);
    }

    /**
     * PUT /api/admin/clients/{client}
     * Mettre à jour un client
     */
    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name'      => 'nullable|string|max:150',
            'logo'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'website'   => 'nullable|url',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('logo')) {
            Storage::disk('public')->delete($client->logo);
            $data['logo'] = $request->file('logo')->store('clients', 'public');
        }

        $client->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Client mis à jour.',
            'data'    => array_merge($client->fresh()->toArray(), ['logo_url' => $client->logo_url]),
        ]);
    }

    /**
     * DELETE /api/admin/clients/{client}
     * Supprimer un client
     */
    public function destroy(Client $client)
    {
        Storage::disk('public')->delete($client->logo);
        $client->delete();

        return response()->json(['success' => true, 'message' => 'Client supprimé.']);
    }

    /**
     * POST /api/admin/clients/reorder
     * Réordonner le bandeau de clients
     * Body: { "items": [{"id": 1, "order": 0}, {"id": 3, "order": 1}] }
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items'         => 'required|array',
            'items.*.id'    => 'required|integer|exists:clients,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            Client::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true, 'message' => 'Ordre du bandeau mis à jour.']);
    }
}