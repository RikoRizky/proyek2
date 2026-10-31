<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Custom Indonesian error messages.
     */
    public function messages(): array
    {
        return [
            'profile_photo.image'    => 'File foto profil harus berupa gambar.',
            'profile_photo.mimes'    => 'Format foto profil tidak didukung. Gunakan JPEG, PNG, JPG, atau GIF.',
            'profile_photo.max'      => 'Foto profil tidak boleh melebihi 2 MB. Silakan pilih gambar yang lebih kecil.',
        ];
    }
}

