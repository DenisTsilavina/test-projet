<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DescriptionRequest extends FormRequest
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
            'description' => 'required|string',
            'effectif' => 'required|integer',
            'stock_id' => 'required|exists:stocks,id',
            'stock_categorie'=> 'nullable|string',
            'prix_achat' => 'nullable|numeric',
            'prix_vente' => 'nullable|numeric',
        ];
    }
}
