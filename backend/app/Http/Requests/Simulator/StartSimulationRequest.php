<?php

namespace App\Http\Requests\Simulator;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Validator;

class StartSimulationRequest extends ApiFormRequest
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
            'service_ids' => ['prohibited'],
            'service_id' => ['prohibited'],
            'filters' => ['prohibited'],
            'criteria' => ['prohibited'],
            'subset' => ['prohibited'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! empty($this->all())) {
                foreach (array_keys($this->all()) as $field) {
                    $validator->errors()->add((string) $field, 'Esta solicitud no admite parametros.');
                }
            }
        });
    }
}
