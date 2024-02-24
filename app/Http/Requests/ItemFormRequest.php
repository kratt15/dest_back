<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ItemFormRequest extends FormRequest
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
            'name' => ['required', 'string', Rule::unique('items')->ignore($this->route()->parameter('item'))],
            'reference' => ['string', Rule::unique('items')->ignore($this->route()->parameter('item'))],
            // 'expiration_date' => ['required', 'date'],
            'cost' => ['required', 'integer'],
            'price' => ['required', 'integer'],
            'description' => ['string'],
            'category_title' => ['required', 'string'],
            'provider_name' => ['required', 'string'],
            'brand_title' => ['required', 'string'],
            'store_name' => ['required', 'string'],
            'quantity' => ['required', 'integer'],
            'security_quantity' => ['required', 'integer'],
        ];
    }

    // Personnalisation des messages
    public function messages(): array
    {
        return [

            'name.required' => "Veuillez remplir ce champ.",

            'name.unique' => "Le nom de l'article doit être unique. Ce nom est déjà utilisé.",


            // 'reference.required' => "Veuillez remplir ce champ.",
            
            'reference.unique' => "La référence doit être unique. Cette référence est déjà utilisée.",

            // 'expiration_date.required' => "Veuillez remplir ce champ.",

            'cost.required' => "Veuillez remplir ce champ.",

            'price.required' => "Veuillez remplir ce champ.",

            'quantity.required' => "Veuillez remplir ce champ.",

            // 'description.required' => "Veuillez remplir ce champ.",

            'category_title.required' => "Veuillez remplir ce champ.",

            'provider_name.required' => "Veuillez remplir ce champ.",

            'brand_name.required' => "Veuillez remplir ce champ.",
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
