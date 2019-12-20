<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPatientListRequest;
use App\Http\Requests\StorePatientListRequest;
use App\Http\Requests\UpdatePatientListRequest;
use App\PatientList;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PatientListController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('patient_list_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $patientLists = PatientList::all();

        return view('admin.patientLists.index', compact('patientLists'));
    }

    public function create()
    {
        abort_if(Gate::denies('patient_list_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.patientLists.create');
    }

    public function store(StorePatientListRequest $request)
    {
        $patientList = PatientList::create($request->all());

        foreach ($request->input('prescription', []) as $file) {
            $patientList->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('prescription');
        }

        return redirect()->route('admin.patient-lists.index');
    }

    public function edit(PatientList $patientList)
    {
        abort_if(Gate::denies('patient_list_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.patientLists.edit', compact('patientList'));
    }

    public function update(UpdatePatientListRequest $request, PatientList $patientList)
    {
        $patientList->update($request->all());

        if (count($patientList->prescription) > 0) {
            foreach ($patientList->prescription as $media) {
                if (!in_array($media->file_name, $request->input('prescription', []))) {
                    $media->delete();
                }
            }
        }

        $media = $patientList->prescription->pluck('file_name')->toArray();

        foreach ($request->input('prescription', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $patientList->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('prescription');
            }
        }

        return redirect()->route('admin.patient-lists.index');
    }

    public function show(PatientList $patientList)
    {
        abort_if(Gate::denies('patient_list_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.patientLists.show', compact('patientList'));
    }

    public function destroy(PatientList $patientList)
    {
        abort_if(Gate::denies('patient_list_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $patientList->delete();

        return back();
    }

    public function massDestroy(MassDestroyPatientListRequest $request)
    {
        PatientList::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
