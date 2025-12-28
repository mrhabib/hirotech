<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'eventId' => [
                'required',
                'integer',
                Rule::exists('events', 'id'),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->route('eventId')) {
            $this->merge([
                'eventId' => $this->route('eventId'),
            ]);
        }
    }
}

