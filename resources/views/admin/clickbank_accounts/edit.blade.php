@extends('admin.layouts.master')
@section('page_title', trans('global.edit') .' '. trans('cruds.cb_account.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.clickbank_accounts.index") }}">{{ trans('cruds.cb_account.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.edit') }}</li>
@endsection

@section('content')
    <form id="cbAccount" method="POST" action="{{ route("admin.clickbank_accounts.update", [$clickbank_account->id]) }}" enctype="multipart/form-data" class="form-horizontal form-label-left mt-3">
        @csrf
        @method('PATCH')
        <div class="x_title">
            <h2>{{ trans('global.general_information') }}</h2>
            <div class="clearfix"></div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required" for="first-name">{{ trans('cruds.cb_account.fields.name') }}</label>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" value="{{ old('name', $clickbank_account->name) }}" required>

                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
            </div>
        </div>
        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">{{ trans('cruds.cb_account.fields.product') }}</label>
            <div class="col-md-6 col-sm-6">
                <button type="submit" class="btn btn-success" name="btnFetch" value="Fetch">{{ trans('global.fetch') }}</button>

            </div>
        </div>

        <div class="clearfix"></div>
        <div class="ln_solid"></div>

        <div class="item form-group">
            <div class="col-md-6 col-sm-6 offset-md-3">
                <button type="submit" class="btn btn-success">{{ trans('global.update') }}</button>
                <a href="{{ route("admin.clickbank_accounts.index") }}" class="btn btn-link">{{ trans('global.cancel') }}</a>
            </div>
        </div>
    </form>

    <table class="table table-striped table-bordered mt-3">
        <tr>
            <th>#</th>
            <th width="400px">{{ trans('cruds.cb_account.fields.product_id') }}</th>
            <th>{{ trans('cruds.cb_account.fields.product_name') }}</th>
        </tr>

        @if(count($clickbank_products) > 0)
            @php
                $i = 0;
            @endphp
            @foreach ($clickbank_products as $key => $cbprod)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>
                        {{ $cbprod->product_id }}
                    </td>
                    <td>
                         {{ $cbprod->name }}
                    </td>
                </tr>
            @endforeach
        @else
            <tr><td colspan="3" align="center">{{ trans('global.no_record_found') }}</td></tr>
        @endif
    </table>

@endsection

@push('script')
<script src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}" type="text/javascript"></script>
{{-- {!! JsValidator::formRequest('App\Http\Requests\ClickbankAccountUpdateRequest', '#cbAccount'); !!} --}}
@endpush
