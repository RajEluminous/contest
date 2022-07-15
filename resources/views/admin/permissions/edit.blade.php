@extends('admin.layouts.master')
@section('page_title', trans('global.edit') .' '. trans('cruds.permission.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.permissions.index") }}">{{ trans('cruds.permission.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.edit') }}</li>
@endsection

@section('content')
    <form id="permission" method="POST" action="{{ route("admin.permissions.update", [$permission->id]) }}" enctype="multipart/form-data" class="form-horizontal form-label-left mt-3">
        @csrf
        @method('PATCH')
        <div class="x_title">
            <h2>{{ trans('global.general_information') }}</h2>
            <div class="clearfix"></div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required" for="first-name">{{ trans('cruds.permission.fields.name') }}</label>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" value="{{ old('name', $permission->name) }}" required>

                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="ln_solid"></div>

        <div class="item form-group">
            <div class="col-md-6 col-sm-6 offset-md-3">
                <button type="submit" class="btn btn-success">{{ trans('global.update') }}</button>
                <a href="{{ route("admin.permissions.index") }}" class="btn btn-link">{{ trans('global.cancel') }}</a>
            </div>
        </div>
    </form>
@endsection
