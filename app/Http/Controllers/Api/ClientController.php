<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;

class ClientController extends Controller
{
    /**
     * GET /api/v1/clients
     * Retourne les clients actifs pour le bandeau défilant
     */
    public function index()
    {
        $clients = Client::active()->get()->map(function ($client) {
            return [
                'id'       => $client->id,
                'name'     => $client->name,
                'logo_url' => $client->logo_url,
                'website'  => $client->website,
                'order'    => $client->order,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $clients,
        ]);
    }
}