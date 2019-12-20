<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StorePatientListRequest;
use App\Http\Requests\UpdatePatientListRequest;
use App\Http\Resources\Admin\PatientListResource;
use App\PatientList;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PatientListApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('patient_list_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PatientListResource(PatientList::all());
    }

    public function store(StorePatientListRequest $request)
    {
        $patientList = PatientList::create($request->all());

        if ($request->input('prescription', false)) {
            $patientList->addMedia(storage_path('tmp/uploads/' . $request->input('prescription')))->toMediaCollection('prescription');
        }

        return (new PatientListResource($patientList))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(PatientList $patientList)
    {
        abort_if(Gate::denies('patient_list_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PatientListResource($patientList);
    }

    public function update(UpdatePatientListRequest $request, PatientList $patientList)
    {
        $patientList->update($request->all());

        if ($request->input('prescription', false)) {
            if (!$patientList->prescription || $request->input('prescription') !== $patientList->prescription->file_name) {
                $patientList->addMedia(storage_path('tmp/uploads/' . $request->input('prescription')))->toMediaCollection('prescription');
            }
        } elseif ($patientList->prescription) {
            $patientList->prescription->delete();
        }

        return (new PatientListResource($patientList))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(PatientList $patientList)
    {
        abort_if(Gate::denies('patient_list_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $patientList->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
