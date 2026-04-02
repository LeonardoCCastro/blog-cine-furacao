<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->whereNull('parent_id')],
            'subcategory_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('parent_id', $this->input('category_id'))),
            ],
            'new_subcategory_name' => ['nullable', 'string', 'max:120'],
            'image' => ['nullable', 'image', 'max:2048'],
            'published' => ['nullable', 'boolean'],
        ];
    }
}
