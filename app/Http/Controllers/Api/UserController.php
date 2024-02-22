<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\registerRequest;
use App\Http\Requests\UserCreateRequest;


class UserController extends Controller
{
    //
    // public function list()
    // {

    //     $users = User::where('id', '!=', auth()->user()->id)->get();
    //     return response()->json($users);
    // }
    public function list()
{
    $users = User::with('stores_manage')->where('id', '!=', auth()->user()->id)->orderByDesc('created_at')->get();

    return response()->json($users);
}


    public function store(UserCreateRequest $request)
    {

         $password = $request->password ?? 'password';

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($password),
    ]);


        return response()->json(
       [
        "message" => "Utilisateur enregistré avec succès",
        "user" => $user
       ], 201);
    }


    public function delete( User $user)
    {
        if (!$user) {
            return response()->json(['error' => "Utilisateur non trouvée"], 404);
        }
        $user->delete();
        return response()->json(["success" => "Utilisateur supprimé"], 200);
    }



    public function update(Request $request, User $user)
{
    if (!$user) {
        return response()->json(['error' => "Utilisateur non trouvé"], 404);
    }

    // $magasins = $request->input('magasins');

    $user->update([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'statut' => $request->input('statut'),
        'role' => $request->input('role'),
    ]);


    if($user->stores_manage()->count() == 0){


    $magWith = [];
    $magWithout = [];


        foreach ($request->input('magasins') as $magasin) {
            $utilisateur = User::where('role', 'gerant')->whereHas('stores_manage', function ($query) use ($magasin) {
                $query->where('store_id', $magasin);
            });

            if ($utilisateur->exists()) {
                $magWith []= $magasin;
            } else {
                $magWithout []= $magasin;
            }
        }


    if ($user->role == 'gerant') {
        $user->stores_manage()->detach();
        $user->stores_manage()->attach($magWithout, ['calendar_id' => 1]);
        return response()->json([
            "success" => "Utilisateur mis à jour avec succès",
            "magasins ayant déjà un gérant" => $magWith,
        ], 200);
    } else {
        $user->stores_manage()->sync($request->input('magasins'));
        return response()->json([
            "success" => "Utilisateur mis à jour avec succès",

        ], 200);
    }




    }else{

        // $listeMagasinsUser = $user->stores_manage()->pluck('store_id')->toArray();

        // $listeMagasins = $request->input('magasins');


    $magWith = [];
    $magWithout = [];


        foreach ($request->input('magasins') as $magasin) {
            $userId = $user->id;
            $utilisateur = User::where('role', 'gerant')->whereHas('stores_manage', function ($query) use ($magasin, $userId) {
                $query->where('user_id', '!=', $userId)->where('store_id', $magasin);
            });


            if ($utilisateur->exists()) {
                $magWith []= $magasin;
            } else {
                $magWithout []= $magasin;
            }
        }


    if ($user->role == 'gerant') {
        $user->stores_manage()->detach();
        $user->stores_manage()->attach($magWithout, ['calendar_id' => 1]);

        return response()->json([
            "success" => "Utilisateur mis à jour avec succès",
            "magasins ayant déjà un gérant" => $magWith,
        ], 200);


    } else {

        $user->stores_manage()->detach();
        $user->stores_manage()->attach($request->input('magasins'));
        return response()->json([
            "success" => "Utilisateur mis à jour avec succès",

        ], 200);

    }


    }



}
    // public function test(){









    //     return response()->json([

    //     ]
    //     , 200);}

    public function search( Request $request){

        $query = $request->input('query');

        $users = User::search($query)->get();

        return response()->json($users);


     }

}

