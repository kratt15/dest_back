<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class StoreFormRequest extends FormRequest
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
            'name' => ['required', 'string', Rule::unique('stores')->ignore($this->route()->parameter('store'))],
            'location' => ['required', 'string'],
            // 'manager_name' => ['required', 'string'],
        ];
    }

    // Personnalisation des messages
    public function messages(): array
    {
        return [
            'name.required' => "Veuillez remplir ce champ.",
            'name.unique' => "Le nom du magasin doit être unique. Ce nom est déjà utilisé.",

            'location.required' => "Veuillez remplir ce champ.",
        ];
    }

    // Envoyer les erreurs de validation sous format json
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_BAD_REQUEST)
        );
    }
}
