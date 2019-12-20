<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StorePrescriptionRequest;
use App\Http\Requests\UpdatePrescriptionRequest;
use App\Http\Resources\Admin\PrescriptionResource;
use App\Prescription;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrescriptionApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('prescription_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PrescriptionResource(Prescription::all());
    }

    public function store(StorePrescriptionRequest $request)
    {
        $prescription = Prescription::create($request->all());

        if ($request->input('doctors_prescription', false)) {
            $prescription->addMedia(storage_path('tmp/uploads/' . $request->input('doctors_prescription')))->toMediaCollection('doctors_prescription');
        }

        return (new PrescriptionResource($prescription))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Prescription $prescription)
    {
        abort_if(Gate::denies('prescription_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PrescriptionResource($prescription);
    }

    public function update(UpdatePrescriptionRequest $request, Prescription $prescription)
    {
        $prescription->update($request->all());

        if ($request->input('doctors_prescription', false)) {
            if (!$prescription->doctors_prescription || $request->input('doctors_prescription') !== $prescription->doctors_prescription->file_name) {
                $prescription->addMedia(storage_path('tmp/uploads/' . $request->input('doctors_prescription')))->toMediaCollection('doctors_prescription');
            }
        } elseif ($prescription->doctors_prescription) {
            $prescription->doctors_prescription->delete();
        }

        return (new PrescriptionResource($prescription))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Prescription $prescription)
    {
        abort_if(Gate::denies('prescription_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $prescription->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
