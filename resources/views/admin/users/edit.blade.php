@extends('admin.layouts.master')
@section('page_title', trans('global.edit') .' '. trans('cruds.user.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.users.index") }}">{{ trans('cruds.user.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.edit') }}</li>
@endsection

@section('content')
    <form id="user" method="POST" action="{{ route("admin.users.update", [$user->id]) }}" enctype="multipart/form-data" class="form-horizontal form-label-left mt-3">
        @csrf
        @method('PATCH')
        <div class="x_title">
            <h2>{{ trans('global.general_information') }}</h2>
            <div class="clearfix"></div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.user.fields.name') }}</label>
            <div class="col-md-6">
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" value="{{ old('name', $user->name) }}" required>

                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.user.fields.email') }}</label>
            <div class="col-md-6">
                <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" name="email" value="{{ old('email', $user->email) }}" required>

                @if($errors->has('email'))
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.user.fields.username') }}</label>
            <div class="col-md-6">
                <input type="text" class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}" name="username" value="{{ old('username', $user->username) }}" required>

                @if($errors->has('username'))
                    <div class="invalid-feedback">
                        {{ $errors->first('username') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.user.fields.role') }}</label>
            <div class="col-md-3">
                <select name="roles[]" class="form-control" required>
                    @foreach ($roles as $kRole => $vRole)
                        <option value="{{ $kRole }}" @if(reset($userRole) == $kRole) selected @endif>{{ $vRole }}</option>
                    @endforeach
                </select>

                @if($errors->has('role'))
                    <div class="invalid-feedback">
                        {{ $errors->first('role') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.user.fields.status') }}</label>
            <div class="col-md-3">
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" required>
                    @foreach(App\Models\User::LoadStatus(false) as $id => $status)
                        <option value="{{ $id }}" @if($id == $user->status) selected @endif>{{ $status }}</option>
                    @endforeach
                </select>

                @if($errors->has('status'))
                    <div class="invalid-feedback">
                        {{ $errors->first('status') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="ln_solid"></div>

        <div class="item form-group">
            <div class="col-md-6 offset-md-3">
                <button type="submit" class="btn btn-success">{{ trans('global.update') }}</button>
                <a href="{{ route("admin.users.index") }}" class="btn btn-link">{{ trans('global.cancel') }}</a>
            </div>
        </div>
    </form>
@endsection

@push('script')
<script src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}" type="text/javascript"></script>
{!! JsValidator::formRequest('App\Http\Requests\UserStoreRequest', '#user'); !!}
@endpush
