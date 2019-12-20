<?php

namespace App\Http\Requests;

use App\Prescription;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class UpdatePrescriptionRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('prescription_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
        ];
    }
}
