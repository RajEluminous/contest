@extends('admin.layouts.master')
@section('page_title', trans('global.create') .' '. trans('cruds.prize.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.prizes.index") }}">{{ trans('cruds.prize.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.create') }}</li>
@endsection

@section('content')
    <form id="prize" method="POST" action="{{ route("admin.prizes.store") }}" enctype="multipart/form-data" class="form-horizontal form-label-left mt-3">
        @csrf
        <div class="x_title">
            <h2>{{ trans('global.general_information') }}</h2>
            <div class="clearfix"></div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.prize.fields.name') }}</label>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" placeholder="{{ trans('cruds.prize.fields.name') }}" value="{{ old('name', '') }}" required>

                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.contest.title_singular') }}</label>
            <div class="col-md-6 col-sm-6">
                <select class="form-control" name="contest_id" id="contest_id" required>
                    <option value="">{{ trans('global.please_select') }}</option>
                    @foreach ($contests as $key => $contest)
                        <option value="{{$contest->id}}">{{$contest->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align">{{ trans('cruds.prize.fields.aff_tools_link') }}</label>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control {{ $errors->has('aff_tools_link') ? 'is-invalid' : '' }}" name="aff_tools_link" placeholder="{{ trans('cruds.prize.fields.aff_tools_link') }}" value="{{ old('name', '') }}" required>

                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('aff_tools_link') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.contest.fields.contest_type') }}</label>
            <div class="col-md-6 col-sm-6">
                <select class="form-control" name="contest_type" id="contest_type" required>
                    <option value="">{{ trans('global.please_select') }}</option>
                    <option value="SALE">Sale</option>
                    <option value="LEAD">Lead</option>
                    <option value="BOTH">Both</option>
                </select>
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('global.column') }}</label>
            <div class="col-md-6 col-sm-6">
                <select class="form-control" name="column" id="column" required>
                    <option value="">{{ trans('global.please_select') }}</option>
                    <option value="1">1 Column</option>
                    <option value="2">2 Column</option>
                </select>
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.prize.fields.column_label_1') }}</label>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control {{ $errors->has('column_label_1') ? 'is-invalid' : '' }}" name="column_label_1" id="column_label_1" placeholder="{{ trans('cruds.prize.fields.column_label_1') }}" value="{{ old('column_label_1', '') }}" required>

                @if($errors->has('column_label_1'))
                    <div class="invalid-feedback">
                        {{ $errors->first('column_label_1') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align">{{ trans('cruds.prize.fields.column_label_2') }}</label>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control {{ $errors->has('column_label_2') ? 'is-invalid' : '' }}" name="column_label_2" id="column_label_2" placeholder="{{ trans('cruds.prize.fields.column_label_2') }}" value="{{ old('column_label_2', '') }}">

                @if($errors->has('column_label_2'))
                    <div class="invalid-feedback">
                        {{ $errors->first('column_label_2') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="ln_solid"></div>

        <div class="item form-group">
            <div class="col-md-6 col-sm-6 offset-md-3">
                <button type="submit" class="btn btn-success">{{ trans('global.create') }}</button>
                <a href="{{ route("admin.prizes.index") }}" class="btn btn-link">{{ trans('global.cancel') }}</a>
            </div>
        </div>
    </form>
@endsection

@push('script')
<script src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}" type="text/javascript"></script>
{!! JsValidator::formRequest('App\Http\Requests\PrizeStoreRequest', '#prize'); !!}

<script>
    jQuery(document).ready(function($){
        $('#column').val("1").trigger("change");
    });

    $("#column").on("change",function(){
    if(this.value==1){
        $('#column_label_2').val("");
        $('#column_label_2').prop("disabled", true);
    } else {
        $('#column_label_2').prop("disabled", false);
    }
});
</script>
@endpush
