<?php

namespace App\Http\Controllers\Api;

use App\Models\Brands;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\BrandFormRequest;

class BrandController extends Controller
{
    //
    public function list(): JsonResponse
    {
        $brands = Brands::orderByDesc('created_at')->get();

        return response()->json($brands);
    }

    public function store(BrandFormRequest $request)
    {

        $brand = Brands::create($request->all());

        return response()->json(['message' => "Marque ajoutée avec succès !", 'brand' => $brand], 201);

    }

    public function update(Brands $brand, BrandFormRequest $request)
    {
        $brand->update($request->all());

        return response()->json(['message' => "Marque modifiée avec succès !", 'brand' => $brand], 200);

    }

    public function delete(Brands $brand)
    {
        // Si la marque n'existe pas renvoyer une page not found
        if (!$brand) {
            return response()->json(['error' => "Marque non trouvée !"], 404);
        }

        // Supprimer logiquement la marque
        $brand->delete();

    }
}
