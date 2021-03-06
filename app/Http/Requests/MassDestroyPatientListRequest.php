<?php

namespace App\Http\Requests;

use App\PatientList;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyPatientListRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('patient_list_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:patient_lists,id',
        ];
    }
}
