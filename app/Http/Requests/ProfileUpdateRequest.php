<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'                           => ['required', 'string', 'max:255'],
            'email'                          => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'first_name'                     => ['nullable', 'string', 'max:255'],
            'last_name'                      => ['nullable', 'string', 'max:255'],
            'phone_number'                   => ['nullable', 'string', 'max:20'],
            'address'                        => ['nullable', 'string'],
            'gender'                         => ['nullable', 'in:male,female'],
            'birth_date'                     => ['nullable', 'date'],
            'emergency_contact_name'         => ['nullable', 'string', 'max:255'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone'        => ['nullable', 'string', 'max:20'],
            'drivers_license_number'         => ['nullable', 'string', 'max:50'],
            'drivers_license_expiry'         => ['nullable', 'date'],
            'valid_id.*'                     => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'drivers_license_file.*'         => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ];
    }
}
