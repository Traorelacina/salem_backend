<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class AdminContactController extends Controller
{
    /**
     * GET /api/admin/contacts
     * Liste tous les messages reçus avec pagination
     * Paramètres: ?status=new|read|replied|archived&per_page=20
     */
    public function index(Request $request)
    {
        $query = Contact::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $contacts = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $contacts->items(),
            'meta'    => [
                'current_page' => $contacts->currentPage(),
                'last_page'    => $contacts->lastPage(),
                'total'        => $contacts->total(),
            ],
        ]);
    }

    /**
     * GET /api/admin/contacts/stats
     * Statistiques des messages
     */
    public function stats()
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'total'    => Contact::count(),
                'new'      => Contact::where('status', 'new')->count(),
                'read'     => Contact::where('status', 'read')->count(),
                'replied'  => Contact::where('status', 'replied')->count(),
                'archived' => Contact::where('status', 'archived')->count(),
            ],
        ]);
    }

    /**
     * GET /api/admin/contacts/{contact}
     * Détail d'un message — marque automatiquement comme lu
     */
    public function show(Contact $contact)
    {
        $contact->markAsRead();

        return response()->json([
            'success' => true,
            'data'    => $contact->fresh(),
        ]);
    }

    /**
     * PUT /api/admin/contacts/{contact}
     * Mettre à jour le statut ou ajouter une note admin
     * Body: { "status": "replied", "admin_note": "..." }
     */
    public function update(Request $request, Contact $contact)
    {
        $data = $request->validate([
            'status'     => 'nullable|in:new,read,replied,archived',
            'admin_note' => 'nullable|string|max:2000',
        ]);

        if (isset($data['status']) && $data['status'] === 'replied' && !$contact->replied_at) {
            $data['replied_at'] = now();
        }

        $contact->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Contact mis à jour.',
            'data'    => $contact->fresh(),
        ]);
    }

    /**
     * DELETE /api/admin/contacts/{contact}
     * Supprimer un message
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json(['success' => true, 'message' => 'Message supprimé.']);
    }
}