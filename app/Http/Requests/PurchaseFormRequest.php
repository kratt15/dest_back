<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class PurchaseFormRequest extends FormRequest
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
            'customer_name' => ['required', 'string'],
            'store_name' => ['required', 'string'],
            'items_names' => ['required', 'array'],
            'quantities' => ['required', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => "Veuillez remplir ce champ.",

            'store_name.required' => "Veuillez remplir ce champ.",

            'items_names.required' => "Veuillez remplir ce champ.",

            'quantities.required' => "Veuillez remplir ce champ.",
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
