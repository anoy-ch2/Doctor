<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPrescriptionRequest;
use App\Http\Requests\StorePrescriptionRequest;
use App\Http\Requests\UpdatePrescriptionRequest;
use App\Prescription;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrescriptionController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('prescription_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $prescriptions = Prescription::all();

        return view('admin.prescriptions.index', compact('prescriptions'));
    }

    public function create()
    {
        abort_if(Gate::denies('prescription_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.prescriptions.create');
    }

    public function store(StorePrescriptionRequest $request)
    {
        $prescription = Prescription::create($request->all());

        foreach ($request->input('doctors_prescription', []) as $file) {
            $prescription->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('doctors_prescription');
        }

        return redirect()->route('admin.prescriptions.index');
    }

    public function edit(Prescription $prescription)
    {
        abort_if(Gate::denies('prescription_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.prescriptions.edit', compact('prescription'));
    }

    public function update(UpdatePrescriptionRequest $request, Prescription $prescription)
    {
        $prescription->update($request->all());

        if (count($prescription->doctors_prescription) > 0) {
            foreach ($prescription->doctors_prescription as $media) {
                if (!in_array($media->file_name, $request->input('doctors_prescription', []))) {
                    $media->delete();
                }
            }
        }

        $media = $prescription->doctors_prescription->pluck('file_name')->toArray();

        foreach ($request->input('doctors_prescription', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $prescription->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('doctors_prescription');
            }
        }

        return redirect()->route('admin.prescriptions.index');
    }

    public function show(Prescription $prescription)
    {
        abort_if(Gate::denies('prescription_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.prescriptions.show', compact('prescription'));
    }

    public function destroy(Prescription $prescription)
    {
        abort_if(Gate::denies('prescription_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $prescription->delete();

        return back();
    }

    public function massDestroy(MassDestroyPrescriptionRequest $request)
    {
        Prescription::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
