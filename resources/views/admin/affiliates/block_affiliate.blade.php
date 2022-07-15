@extends('admin.layouts.master')
@section('page_title', trans('global.create') .' '. trans('cruds.partner.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.affiliates.index") }}">{{ trans('cruds.affiliate.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.block_affiliate_id') }}</li>
@endsection

@section('content')
    <div class="x_panel">
        <div class="x_content">
            {{-- Add Affiliate ID Block List --}}
            <form method="POST" id="frmBlockAffiliate" action="{{ route("admin.affiliates.block_affiliate") }}" enctype="multipart/form-data" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                @csrf
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('global.affiliate_id') }}</label>
                    <div class="col-md-6 col-sm-6 ">
                        <select class="form-control" name="fAffiliate" id="fAffiliate">
                            <option value="">{{ trans('global.select_affiliate_id') }}</option>
                            @foreach($affArry as $id => $affName)
                                <option value="{{ $id }}">{{ $affName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="clearfix"></div>
            </form>

            {{-- Blocked Affiliate ID List --}}
            <table class="table table-striped table-bordered top_affiliates">
                <thead>
                    <tr class="text-center">
                        <th>#No</th>
                        <th>{{ trans('global.affiliate_id') }}</th>
                        <th>{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($affArr as $affiliate)
                        <tr data-entry-id="{{ $affiliate['id'] }}" class="text-center">
                            <td>{{ $affiliate['count'] ?? '' }}</td>
                            <td>{{ $affiliate['name'] ?? '' }}</td>
                            <td>
                                <form action="{{ route('admin.affiliates.unblock_affiliate', $affiliate['id']) }}" style="display:inline;" method="GET" onsubmit="return confirm('Are you sure want to unblock this Affiliate ID?');">
                                    @csrf
                                    @method('GET')
                                    <input type="submit" class="btn btn-danger btn-sm" value="{{ trans('global.unblock') }}">
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {!! $affiliatelist->links() !!}
        </div>
    </div>
@endsection

@push('style')
<link href="{{ asset('vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('vendors/select2/dist/js/select2.full.min.js') }}"></script>
<script>
    $("#fAffiliate").select2();

    $('#fAffiliate').change(function(){
        $('#frmBlockAffiliate').submit();
    });
</script>
@endpush
