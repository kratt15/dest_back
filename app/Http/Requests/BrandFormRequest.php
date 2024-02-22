<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BrandFromRequest extends FormRequest
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
                'title' => ['required', 'string', Rule::unique('brands')->ignore($this->route()->parameter('brand'))],

        ];
    }
    public function messages(): array
    {
        return [
            'title.required' => "Veuillez remplir ce champ.",
            'title.unique' => "Le titre doit être unique. Ce titre est déjà utilisé.",
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
