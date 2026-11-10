<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Http\Resources\ContactResource;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * (2) Listar todos los contactos del usuario autenticado
     */
    public function index(Request $request)
    {
        $contacts = $request->user()->contacts()->get();
        return ContactResource::collection($contacts);
    }

    /**
     * (1) Crear un nuevo contacto vinculado al usuario autenticado
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $contact = $request->user()->contacts()->create($validated);

        return (new ContactResource($contact))
            ->additional(['message' => 'Contacto creado exitosamente'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Obtener el detalle de un contacto propio (retorna 404 si no existe o es de otro usuario)
     */
    public function show(Request $request, $id)
    {
        $contact = $request->user()->contacts()->findOrFail($id);
        return new ContactResource($contact);
    }

    /**
     * (3) Actualizar la información del contacto
     */
    public function update(Request $request, $id)
    {
        $contact = $request->user()->contacts()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $contact->update($validated);

        return new ContactResource($contact);
    }

    /**
     * (4) Borrado Lógico (Soft Delete) del contacto
     */
    public function destroy(Request $request, $id)
    {
        $contact = $request->user()->contacts()->findOrFail($id);
        
        // Aplica Soft Delete (registra la fecha en deleted_at sin borrar de la BD)
        $contact->delete();

        return response()->json([
            'message' => 'Contacto eliminado correctamente'
        ], 200);
    }
}