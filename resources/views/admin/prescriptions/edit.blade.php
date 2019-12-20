@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.prescription.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.prescriptions.update", [$prescription->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('patient_name') ? 'has-error' : '' }}">
                            <label for="patient_name">{{ trans('cruds.prescription.fields.patient_name') }}</label>
                            <input class="form-control" type="text" name="patient_name" id="patient_name" value="{{ old('patient_name', $prescription->patient_name) }}">
                            @if($errors->has('patient_name'))
                                <span class="help-block" role="alert">{{ $errors->first('patient_name') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.prescription.fields.patient_name_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label for="email">{{ trans('cruds.prescription.fields.email') }}</label>
                            <input class="form-control" type="text" name="email" id="email" value="{{ old('email', $prescription->email) }}">
                            @if($errors->has('email'))
                                <span class="help-block" role="alert">{{ $errors->first('email') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.prescription.fields.email_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('disease') ? 'has-error' : '' }}">
                            <label for="disease">{{ trans('cruds.prescription.fields.disease') }}</label>
                            <textarea class="form-control" name="disease" id="disease">{{ old('disease', $prescription->disease) }}</textarea>
                            @if($errors->has('disease'))
                                <span class="help-block" role="alert">{{ $errors->first('disease') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.prescription.fields.disease_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('doctors_prescription') ? 'has-error' : '' }}">
                            <label for="doctors_prescription">{{ trans('cruds.prescription.fields.doctors_prescription') }}</label>
                            <div class="needsclick dropzone" id="doctors_prescription-dropzone">
                            </div>
                            @if($errors->has('doctors_prescription'))
                                <span class="help-block" role="alert">{{ $errors->first('doctors_prescription') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.prescription.fields.doctors_prescription_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger" type="submit">
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>



        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var uploadedDoctorsPrescriptionMap = {}
Dropzone.options.doctorsPrescriptionDropzone = {
    url: '{{ route('admin.prescriptions.storeMedia') }}',
    maxFilesize: 10, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 10,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="doctors_prescription[]" value="' + response.name + '">')
      uploadedDoctorsPrescriptionMap[file.name] = response.name
    },
    removedfile: function (file) {
      console.log(file)
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedDoctorsPrescriptionMap[file.name]
      }
      $('form').find('input[name="doctors_prescription[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($prescription) && $prescription->doctors_prescription)
      var files =
        {!! json_encode($prescription->doctors_prescription) !!}
          for (var i in files) {
          var file = files[i]
          this.options.addedfile.call(this, file)
          this.options.thumbnail.call(this, file, file.url)
          file.previewElement.classList.add('dz-complete')
          $('form').append('<input type="hidden" name="doctors_prescription[]" value="' + file.file_name + '">')
        }
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>
@endsection