<?php

namespace App\Http\Requests;

use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Aturan sama dengan registrasi; kode_responden sengaja tidak ada (tidak bisa diubah).
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return RegisteredUserController::aturanIdentitas($this->user());
    }
}
