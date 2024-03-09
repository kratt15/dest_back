<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ProviderFormRequest extends FormRequest
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
            'name_provider' => [
                'required',
                'string',
                'regex:/^[\pL\s\-]+$/u',
                 'max:70',
                Rule::unique('providers')->ignore($this->route()->parameter('provider')),

                ],
            'name_resp' => ['required', 'string', 'max:70'],
            'address_provider' => ['required', 'string', 'max:70'],
            'phone_provider' => [
                'required',
                'string',
                'regex:/^\+?[0-9]+$/',
                'max:15',
                'min:8',
                Rule::unique('providers')->ignore($this->route()->parameter('provider')),
            ],
            'email_provider' => ['string', 'email', 'max:45', Rule::unique('providers')->ignore($this->route()->parameter('provider'))],
        ];
    }

    public function messages(): array
    {
        return [
            'name_provider.required' => "Veuillez remplir ce champ.",
            'name_provider.max' => "Le nom du fournisseur ne doit pas dépasser 70 caractères.",
            'name_provider.unique' => "Le nom du fournisseur doit être unique. Ce nom est déjà utilisé.",
            'name_provider.regex' => "Le nom du fournisseur ne doit pas contenir de caractères speciaux.",

            'name_resp.required' => "Veuillez remplir ce champ.",
            'name_resp.max' => "Le nom du responsable ne doit pas dépasser 70 caractères.",


            'address_provider.required' => "Veuillez remplir ce champ.",
            'address_provider.max' => "L'adresse du fournisseur ne doit pas dépasser 70caractères.",

            'phone_provider.required' => "Veuillez remplir ce champ.",
            'phone_provider.unique' => "Le numéro de téléphone doit être unique. Ce numéro est déjà utilisé.",

            'email_provider.email' => "Veuillez saisir une adresse e-mail valide.",
            'email_provider.unique' => "L'e-mail doit être unique. Cet e-mail est déjà utilisé.",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_BAD_REQUEST)
        );
    }
}
