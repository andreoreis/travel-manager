<?php

namespace App\Http\Requests;

use App\Enums\TravelRequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTravelRequest extends FormRequest
{
    // Só permite que a requisição continue se houver um usuário autenticado.
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'requester_name' => ['required','string','max:255'],
            'destination'    => ['required','string','max:255'],
            'start_date'     => ['required','date','before_or_equal:end_date'],
            'end_date'       => ['nullable','date','after_or_equal:start_date'],
            'status'         => ['nullable', Rule::in(TravelRequestStatus::values())],
        ];
    }
}
