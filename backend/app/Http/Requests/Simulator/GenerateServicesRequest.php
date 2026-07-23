<?php

namespace App\Http\Requests\Simulator;

use App\Http\Requests\ApiFormRequest;

class GenerateServicesRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'count' => ['required', 'integer', 'min:1'],
            'start_time' => ['prohibited'],
            'end_time' => ['prohibited'],
        ];
    }
}
