@extends('admin.layouts.master')
@section('page_title', trans('global.create') .' '. trans('cruds.user.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.users.index") }}">{{ trans('cruds.user.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.create') }}</li>
@endsection

@section('content')
    <form id="user" method="POST" action="{{ route("admin.users.store") }}" enctype="multipart/form-data" class="form-horizontal form-label-left mt-3">
        @csrf
        <div class="x_title">
            <h2>{{ trans('global.general_information') }}</h2>
            <div class="clearfix"></div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.user.fields.name') }}</label>
            <div class="col-md-6">
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" placeholder="{{ trans('cruds.user.fields.name') }}" value="{{ old('name', '') }}" required>

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
                <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" name="email" placeholder="{{ trans('cruds.user.fields.email') }}" value="{{ old('email', '') }}" required>

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
                <input type="text" class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}" name="username" placeholder="{{ trans('cruds.user.fields.username') }}" value="{{ old('username', '') }}" required>

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
                        <option value="{{ $kRole }}">{{ $vRole }}</option>
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
                    @foreach(App\Models\User::LoadStatus(true) as $id => $status)
                        <option value="{{ $id }}" >{{ $status }}</option>
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
        <div class="x_title my-3">
            <h2>{{ trans('cruds.user.fields.password') }}</h2>
            <div class="clearfix"></div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.user.fields.password') }}</label>
            <div class="col-md-6">
                <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password" placeholder="{{ trans('cruds.user.fields.password') }}" value="{{ old('password', '') }}" required>

                @if($errors->has('password'))
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.user.fields.confirm_password') }}</label>
            <div class="col-md-6">
                <input type="password" class="form-control {{ $errors->has('confirm_password') ? 'is-invalid' : '' }}" name="confirm-password" placeholder="{{ trans('cruds.user.fields.confirm_password') }}" value="{{ old('confirm_password', '') }}" required>

                @if($errors->has('confirm_password'))
                    <div class="invalid-feedback">
                        {{ $errors->first('confirm_password') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="ln_solid"></div>

        <div class="item form-group">
            <div class="col-md-6 offset-md-3">
                <button type="submit" class="btn btn-success">{{ trans('global.create') }}</button>
                <a href="{{ route("admin.users.index") }}" class="btn btn-link">{{ trans('global.cancel') }}</a>
            </div>
        </div>
    </form>
@endsection

@push('script')
<script src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}" type="text/javascript"></script>
{!! JsValidator::formRequest('App\Http\Requests\UserStoreRequest', '#user'); !!}
@endpush
