@extends('admin.layouts.master')
@section('page_title', trans('global.create') .' '. trans('cruds.cb_account.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.clickbank_accounts.index") }}">{{ trans('cruds.cb_account.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.create') }}</li>
@endsection

@section('content')
    <form id="cbAccount" method="POST" action="{{ route("admin.clickbank_accounts.store") }}" enctype="multipart/form-data" class="form-horizontal form-label-left mt-3">
        @csrf
        <div class="x_title">
            <h2>{{ trans('global.general_information') }}</h2>
            <div class="clearfix"></div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.cb_account.fields.name') }}</label>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" placeholder="{{ trans('cruds.cb_account.fields.name') }}" value="{{ old('name', '') }}" required>

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
                <button type="submit" class="btn btn-success">{{ trans('global.create') }}</button>
                <a href="{{ route("admin.clickbank_accounts.index") }}" class="btn btn-link">{{ trans('global.cancel') }}</a>
            </div>
        </div>
    </form>
@endsection

@push('script')
<script src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}" type="text/javascript"></script>
{!! JsValidator::formRequest('App\Http\Requests\ClickbankAccountStoreRequest', '#cbAccount'); !!}
@endpush
