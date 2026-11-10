<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Listar todos los contactos del usuario autenticado
     */
    public function index(Request $request)
    {
        $contacts = $request->user()->contacts()->get();

        return ContactResource::collection($contacts);
    }

    /**
     * Crear un nuevo contacto vinculado al usuario autenticado
     */
    public function store(StoreContactRequest $request)
    {
        $contact = $request->user()->contacts()->create($request->validated());

        return (new ContactResource($contact))
            ->additional(['message' => 'Contacto creado exitosamente'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Obtener el detalle de un contacto propio
     * Retorna HTTP 404 Not Found si el contacto no existe o pertenece a otro usuario
     */
    public function show(Request $request, string|int $id)
    {
        $contact = $request->user()->contacts()->findOrFail($id);

        return new ContactResource($contact);
    }

    /**
     * Actualizar la información de un contacto propio
     */
    public function update(UpdateContactRequest $request, string|int $id)
    {
        $contact = $request->user()->contacts()->findOrFail($id);

        $contact->update($request->validated());

        return new ContactResource($contact);
    }

    /**
     * Borrado lógico (Soft Delete) del contacto
     * Asigna la fecha actual al campo deleted_at sin eliminar la fila de la BD
     */
    public function destroy(Request $request, string|int $id)
    {
        $contact = $request->user()->contacts()->findOrFail($id);
        
        $contact->delete();

        return response()->json([
            'message' => 'Contacto eliminado correctamente'
        ], 200);
    }
}