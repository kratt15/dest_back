<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CategoryFormRequest extends FormRequest
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
            'title' => ['required', 'regex:/^[\pL\s\-]+$/u', 'string', Rule::unique('categories')->ignore($this->route()->parameter('category'))],
        ];
    }

    // Configuration des messages d'erreur
    public function messages(): array
    {
        return [
            'title.required' => "Veuillez remplir ce champ.",
            'title.unique' => "Le titre doit être unique. Ce titre est déjà utilisé.",
            'title.regex' => "Le titre ne doit pas contenir de caractères speciaux ni de chiffre .",
        ];
    }

    // Envoyer les messages d'erreur sous format json
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_BAD_REQUEST)
        );
    }
}
