<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CustomerFormRequest extends FormRequest
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
            'name_customer' => ['required', 'regex:/^[\pL\s\-]+$/u','string',Rule::unique('customers')->ignore($this->route()->parameter('customer'))],
            'phone_customer' => [
                'string',
                'regex:/^[\+0-9\s]+$/',
                'min:8',
                'max:15',
                Rule::unique('customers')->ignore($this->route()->parameter('customer')),

            ],
            'address_customer' => ['string','max:75'],
            'email_customer' => ['string', 'email', 'max:100', Rule::unique('customers')->ignore($this->route()->parameter('customer'))],

        ];
    }

    public function messages(): array
    {
        return [
            'name_customer.required' => "Veuillez remplir ce champ.",

            'name_customer.regex' => "Le nom du client ne doit pas contenir de caractères speciaux.",

            'name_customer.unique' => "Le nom de client doit être unique. Ce nom est déjà utilisé.",

            'phone_customer.unique' => "Le numéro de téléphone doit être unique. Ce numéro est déjà utilisé.",

            'phone_customer.min' => "Le numéro de numéro doit avoir au moins 8 caractères.",

            'phone_customer.max' => "Le numéro de numéro doit avoir moins de 20 caractères.",

            'adress_customer.max' => "L'adresse doit avoir moins de 200 caractères.",

            'email_customer.unique' => "L'email doit être unique. Cet email est déjà utilisé.",

            'email_customer.email' => "L'email doit être valide.",

            'email_customer max' => "L'email doit avoir moins de 100 caractères.",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_BAD_REQUEST)
        );
    }
}
