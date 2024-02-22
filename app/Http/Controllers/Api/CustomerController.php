<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerFormRequest;

class CustomerController extends Controller
{

    // Liste des clients
    public function list(): JsonResponse
    {
        $customers = Customer::orderByDesc('created_at')->get();
        return response()->json($customers);
    }
    // Ajout de client
    public function store(CustomerFormRequest $request): JsonResponse
    {
        $customer = Customer::create($request->all());

        return response()->json(['message' => 'Client créé avec succès !', 'customer' => $customer], 201);
    }

    // Mise à jour de client
    public function update(CustomerFormRequest $request, Customer $customer)
    {
        $customer->update($request->all());

        return response()->json(['message' => 'Client modifié avec succès !', 'customer' => $customer], 200);
    }

    // Suppression de client
    public function delete(Customer $customer)
    {
        if (!$customer) {
            return response()->json(['error' => 'Client non trouvé !'], 404);
        }

        // Supprimer logiquement le client
        $customer->delete();

        // Retourner un message de succès
        return response()->json(['message' => 'Client supprimé avec succès !'], 200);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $customers = Customer::search($query)->get();

        return response()->json($customers);
    }
}
