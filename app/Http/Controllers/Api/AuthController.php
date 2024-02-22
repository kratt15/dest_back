<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\loginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\registerRequest;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }


    public function register(registerRequest $request)
    {


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role'=>'admin'
        ]);

        $user->assignRole('admin');

        // $token = Auth::login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Utilisateur créer avec succès',
            'user' => $user,
            'role' => $user->role


        ]);
    }

    public function login(loginRequest $request)
    {


        $credentials = $request->only('email', 'password');

        $tokenCred = Auth::attempt($credentials);

        if (!$tokenCred) {
            return response()->json([
                'status' => $request->status,
                'message' => $request->message(),
            ]);
        }


        $user = Auth::user();

        $token = $user->createToken('authToken')->plainTextToken;

        //  $cookie = cookie('jwt', $token, 60 * 24);

        $cookie = cookie('jwt', $token, 60 * 24);

        if ($user->statut === 'inactif') {
            Auth::logout();
            return response()->json([
                'status' => 'error',
                'message' => 'Votre compte est désactivé. Veuillez contacter l\'administrateur.',
            ], 401);
        }


        //mofifier le role

        if($user->hasRole('user')){
            $role = Role::where('name', $user->role )->first();
            $user->syncRoles([$role->id]);
        }


        if($user->hasRole('admin')){
            $role = Role::where('name', $user->role )->first();
            $user->syncRoles([$role->id]);
        }

        if($user->hasRole('gerant')){
            $role = Role::where('name', $user->role )->first();
            $user->syncRoles([$role->id]);
        }



        //assigner le role

        if($user->role === 'user' && !$user->hasRole('user') && !$user->hasRole('admin') && !$user->hasRole('gerant')){
            $user->assignRole('user');
        }


        if($user->role === 'admin' && !$user->hasRole('admin') && !$user->hasRole('user') && !$user->hasRole('gerant')){
            $user->assignRole('admin');
        }

        if($user->role === 'gerant' && !$user->hasRole('gerant') && !$user->hasRole('admin') && !$user->hasRole('user')){
            $user->assignRole('gerant');
        }




        $magasins[] = $user->stores_manage;


        return response()->json([
            'status' => 'success',
            'message' => 'Utilisateur connecté avec succès',
            'magasins' => $magasins,
            'token' => $token,
            'user' => $user
        ])->withCookie($cookie);
    }


}
