@extends('admin.layouts.master')
@section('page_title', trans('global.create') .' '. trans('cruds.role.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.roles.index") }}">{{ trans('cruds.role.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.create') }}</li>
@endsection

@section('content')
    <form id="role" method="POST" action="{{ route("admin.roles.store") }}" enctype="multipart/form-data" class="form-horizontal form-label-left mt-3">
        @csrf
        <div class="x_title">
            <h2>{{ trans('global.general_information') }}</h2>
            <div class="clearfix"></div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.role.fields.name') }}</label>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" placeholder="{{ trans('cruds.role.fields.name') }}" value="{{ old('name', '') }}" required>

                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.role.fields.permission') }}</label>
            <div class="col-md-6 col-sm-6">
                @foreach($permission as $value)
                    <label>
                        <input type="checkbox" name="permission[]" value="{{ $value->id }}">
                        <span>{{ $value->name }}</span>
                    </label><br/>
                @endforeach
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="ln_solid"></div>

        <div class="item form-group">
            <div class="col-md-6 col-sm-6 offset-md-3">
                <button type="submit" class="btn btn-success">{{ trans('global.create') }}</button>
                <a href="{{ route("admin.roles.index") }}" class="btn btn-link">{{ trans('global.cancel') }}</a>
            </div>
        </div>
    </form>
@endsection

@push('script')

@endpush
