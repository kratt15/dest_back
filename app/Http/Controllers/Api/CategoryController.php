<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\CategoryFormRequest;

class CategoryController extends Controller
{

    // Liste des catégories
    public function list()
    {
        $categories = Category::orderByDesc('created_at')->get();

        return response()->json($categories);
    }

    // Ajout de catégorie
    public function store(CategoryFormRequest $request)
    {
        $category = Category::create($request->all());

        return response()->json(['message' => "Catégorie créée avec succès !", 'category' => $category], 201);
    }

    // Mise à jour de catégorie
    public function update(Category $category, CategoryFormRequest $request)
    {
        $category->update($request->all());

        return response()->json(['message' => "Catégorie modifiée avec succès !", 'category' => $category], 200);
    }

    // Suppression de catégorie
    public function delete(Category $category)
    {
        // Si la catégorie n'existe pas renvoyer une page not found
        if (!$category) {
            return response()->json(['error' => "Catégorie non trouvée !"], 404);
        }

        // Supprimer logiquement la catégorie
        $category->delete();

        return response()->json(['message' => "Catégorie supprimée avec succès !"], 200);
    }
     public function search( Request $request){

        $query = $request->input('query');

        $categories = Category::search($query)->get();

        return response()->json($categories);


     }
}
