<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}
