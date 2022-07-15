@extends('admin.layouts.master')
@section('page_title', trans('global.edit') .' '. trans('cruds.team.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.teams.index") }}">{{ trans('cruds.team.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.edit') }}</li>
@endsection

@section('content')
    <form id="team" method="POST" action="{{ route("admin.teams.update", [$team->id]) }}" enctype="multipart/form-data" class="form-horizontal form-label-left mt-3">
        @csrf
        @method('PATCH')
        <div class="x_title">
            <h2>{{ trans('global.general_information') }}</h2>
            <div class="clearfix"></div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required" for="first-name">{{ trans('cruds.team.fields.name') }}</label>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" value="{{ old('name', $team->name) }}" required>

                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required" for="last-name">{{ trans('cruds.team.fields.affiliate') }}</label>
            <div class="col-md-6 col-sm-6">
                <select class="select2_single form-control" name="affiliate_select[]" id="affiliate_select" required multiple>
                    @foreach ($affiliate_list as $key => $affiliate)
                        <option value="{{ $key }}" @if(in_array($key, $teamAff)) selected  @endif>{{ $affiliate }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="ln_solid"></div>

        <div class="item form-group">
            <div class="col-md-6 col-sm-6 offset-md-3">
                <button type="submit" class="btn btn-success">{{ trans('global.update') }}</button>
                <a href="{{ route("admin.teams.index") }}" class="btn btn-link">{{ trans('global.cancel') }}</a>
            </div>
        </div>
    </form>
@endsection

@push('style')
<link href="{{ asset('vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}" type="text/javascript"></script>
{!! JsValidator::formRequest('App\Http\Requests\TeamStoreRequest', '#team'); !!}
<script src="{{ asset('vendors/select2/dist/js/select2.full.min.js') }}"></script>

<script>
    $("#affiliate_select").select2({
        placeholder: "{{ trans('cruds.team.select_affiliate') }}",
    });
</script>
@endpush
