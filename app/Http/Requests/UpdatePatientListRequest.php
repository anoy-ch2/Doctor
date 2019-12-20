<?php

namespace App\Http\Requests;

use App\PatientList;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class UpdatePatientListRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('patient_list_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
        ];
    }
}
