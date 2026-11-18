<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = $request->user()->contacts()->latest()->paginate(15);

        return ContactResource::collection($contacts);
    }

    public function store(StoreContactRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $contact = $request->user()->contacts()->create($data);

        return (new ContactResource($contact))
            ->additional(['message' => 'Contacto creado exitosamente'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string|int $id)
    {
        $contact = $this->findOwnedContact($request, $id);

        return new ContactResource($contact);
    }

    public function update(UpdateContactRequest $request, string|int $id)
    {
        $contact = $this->findOwnedContact($request, $id);
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            // Eliminar el avatar anterior si existe
            if ($contact->avatar) {
                Storage::disk('public')->delete($contact->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $contact->update($data);

        return (new ContactResource($contact))
            ->additional(['message' => 'Contacto actualizado exitosamente']);
    }

    public function destroy(Request $request, string|int $id)
    {
        $contact = $this->findOwnedContact($request, $id);

        $contact->delete();

        return response()->json([
            'message' => 'Contacto eliminado correctamente',
        ], 200);
    }

    /**
     * Busca el contacto y verifica que sea del usuario en sesión.
     * 404 si no existe (o fue eliminado), 403 si es de otro usuario.
     */
    private function findOwnedContact(Request $request, string|int $id): Contact
    {
        $contact = Contact::findOrFail($id);

        abort_if($contact->user()->isNot($request->user()), 403);

        return $contact;
    }
}
