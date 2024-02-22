<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //

            'name' => 'required|unique:users',
            'email' => 'required|unique:users|email',
            'password' => 'nullable|min:8',
        ];
    }

    public function messages():array
    {

        return [


            'name.required' => "Veuillez saisir un nom.",
            'name.unique' => "Le nom de l'utilisateur doit être unique. Ce nom est déjà utilisé.",
            'email.required' => "Veuillez saisir un email.",
            'email.unique' => "L'email doit être unique. Cet email est déjà utilisé.",
            'email.email' => "Veuillez entrer un email valide.",
            // 'password.required' => "Veuillez saisir un mot de passe .",
            'password.min' => "Le mot de passe doit contenir au moins 8 caractères."
         ];
    }

}
