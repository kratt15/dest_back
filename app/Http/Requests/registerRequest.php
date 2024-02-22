<?php

namespace App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class registerRequest extends FormRequest
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
            'password' => 'required|min:8',
        ];
    }
      /**
        * A description of the entire PHP function.
        *
        * @param Validator $validator Description of the $validator parameter
        * @throws HttpResponseException Description of the exception that can be thrown
        * @return void
        */
        protected function failedValidation(Validator $validator)
        {
            throw new HttpResponseException(
                response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_BAD_REQUEST)
            );
        }
        public function messages(){
            return [

                'name.required' => "Veuillez saisir un nom.",
                'name.unique' => "Le nom de l'utilisateur doit être unique. Ce nom est déjà utilisé.",
                'email.required' => "Veuillez saisir un email.",
                'email.unique' => "L'email doit être unique. Cet email est déjà utilisé.",
                'email.email' => "Veuillez entrer un email valide.",
                'password.required' => "Veuillez saisir un mot de passe .",
                'password.min' => "Le mot de passe doit contenir au moins 8 caractères."
            ];

        }

}
