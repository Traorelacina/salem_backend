<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * POST /api/v1/contact
     * Enregistre un message de contact envoyé depuis le formulaire du site
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'company' => 'nullable|string|max:100',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|min:10|max:3000',
        ], [
            'name.required'    => 'Le nom est obligatoire.',
            'email.required'   => 'L\'adresse email est obligatoire.',
            'email.email'      => 'L\'adresse email n\'est pas valide.',
            'subject.required' => 'Le sujet est obligatoire.',
            'message.required' => 'Le message est obligatoire.',
            'message.min'      => 'Le message doit contenir au moins 10 caractères.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $contact = Contact::create($validator->validated());

        // TODO: Notifier l'admin par email
        // Mail::to(config('mail.admin_address'))->send(new NewContactMail($contact));

        return response()->json([
            'success' => true,
            'message' => 'Votre message a bien été envoyé. Nous vous répondrons dans les meilleurs délais.',
            'id'      => $contact->id,
        ], 201);
    }
}