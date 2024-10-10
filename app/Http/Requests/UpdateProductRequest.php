<?php

namespace App\Http\Requests;

use App\Enums\ProductType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'type' => ['required', Rule::enum(ProductType::class)],
        ];
    }
}
