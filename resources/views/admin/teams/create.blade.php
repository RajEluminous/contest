@extends('admin.layouts.master')
@section('page_title', trans('global.create') .' '. trans('cruds.team.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.teams.index") }}">{{ trans('cruds.team.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.create') }}</li>
@endsection

@section('content')
    <form id="team" method="POST" action="{{ route("admin.teams.store") }}" enctype="multipart/form-data" class="form-horizontal form-label-left mt-3">
        @csrf
        <div class="x_title">
            <h2>{{ trans('global.general_information') }}</h2>
            <div class="clearfix"></div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.team.fields.name') }}</label>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" placeholder="{{ trans('cruds.team.fields.name') }}" value="{{ old('name', '') }}" required>

                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.team.fields.affiliate') }}</label>
            <div class="col-md-6 col-sm-6">
                <select id="aff_select" class="select2_single form-control {{ $errors->has('affiliate_select') ? 'is-invalid' : '' }}" name="affiliate_select[]" multiple="multiple" required>
                    @foreach ($affiliate_list as $key => $affiliate)
                        <option value="{{ $key }}">{{ $affiliate }}</option>
                    @endforeach
                </select>

                @if($errors->has('affiliate_select'))
                    <div class="invalid-feedback">
                        {{ $errors->first('affiliate_select') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="ln_solid"></div>

        <div class="item form-group">
            <div class="col-md-6 col-sm-6 offset-md-3">
                <button type="submit" class="btn btn-success">{{ trans('global.create') }}</button>
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
    $('#aff_select').select2({
        placeholder: "{{ trans('cruds.team.select_affiliate') }}",
    });
</script>
@endpush
