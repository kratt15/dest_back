<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class TransferItemFormRequest extends FormRequest
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
            'item_name' => ['required', 'string'],
            'destination_store_name' => ['required', 'string'],
            'quantity' => ['required', 'integer'],
        ];
    }

    // Personnalisation des messages
    public function messages(): array
    {
        return [
            'item_name.required' => "Veuillez remplir ce champ.",
            'destination_store_name.required' => "Veuillez remplir ce champ.",
            'quantity.required' => "Veuillez remplir ce champ.",
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
