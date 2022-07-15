@extends('admin.layouts.master')
@section('page_title', trans('cruds.affiliate.title'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ trans('cruds.affiliate.title') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if(auth()->user()->can('affiliate-block-id'))
            <a href="{{ route('admin.affiliates.block_affiliate') }}" class="btn btn-outline-warning float-sm-right">
                {{ trans('global.block_affiliate_id') }}
            </a>
            @endif

            @if(auth()->user()->can('affiliate-create'))
            <a href="{{ route("admin.affiliates.create_partner") }}" class="btn btn-primary float-sm-right">
                {{ trans('global.add_edit_partner_name') }}
            </a>

            <a href="{{ route('admin.affiliates.create_affiliate') }}" class="btn btn-primary float-sm-right">
                {{ trans('global.add_edit_affiliate_id') }}
            </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>{{ trans('global.clickbank_id_master_list') }}</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="table-responsive">
                        {{-- Search Affiliate Partner --}}
                        <form method="POST" action="{{ route("admin.affiliates.index") }}" enctype="multipart/form-data" id="frmFilter" data-parsley-validate class="form-horizontal form-label-left">
                            @csrf
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr class="text-center table-secondary">
                                        <td style="width:30%;"><h2>Search Affiliate ID or Partner Name</h2></td>
                                        <td>
                                            <select class="form-control" name="fAffiliate" id="fAffiliate">
                                            <option value="">{{ trans('global.select_affiliate_id') }}</option>
                                                @foreach($affArry as $id => $affName)
                                                    <option value="{{ $id }}">{{ $affName }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="fPartner" id="fPartner">
                                            <option value="">{{ trans('global.select_partner_name') }}</option>
                                                @foreach($partArry as $id => $partName)
                                                    <option value="{{ $id }}">{{ $partName }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-info" id="btnSearch">{{ trans('global.search') }}</button>
                                        </td>
                                    </tr>
                                </thead>
                            </table>
                        </form>

                        {{-- Assign Affiliate Partner --}}
                        <form method="POST" action="{{ url("admin/affiliates") }}" enctype="multipart/form-data" id="frmMap" data-parsley-validate class="form-horizontal form-label-left">
                        @csrf
                            <table class="table table-striped table-bordered top_affiliates">
                                <thead>
                                    <tr class="text-center table-info">
                                        <td style="width:30%;"><h2>Map Affiliate ID - Partner Name</h2></td>
                                        <td>
                                            <select class="form-control" name="affiliate" id="affiliate">
                                            <option value="">{{ trans('global.select_affiliate_id') }}</option>
                                                @foreach($affArry as $id => $affName)
                                                    <option value="{{ $id }}">{{ $affName }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="partner" id="partner">
                                            <option value="">{{ trans('global.select_partner_name') }}</option>
                                                @foreach($partArry as $id => $partName)
                                                    <option value="{{ $id }}">{{ $partName }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="hidden" id="recid" name="recid" value="">
                                            <button type="submit" class="btn btn-success" id="btnSubmit">{{ trans('global.assign') }}</button>
                                            <button type="reset" class="btn" id="btnReset" style="display:none;">{{ trans('global.cancel') }}</button>
                                        </td>
                                    </tr>
                                </thead>
                            </table>
                        </form>

                        {{-- List of Assigned Affiliate Partner --}}
                        <table class="table table-striped table-bordered top_affiliates">
                            <thead>
                                <tr class="text-center">
                                    <th>#No</th>
                                    <th>{{ trans('global.affiliate_id') }}</th>
                                    <th>{{ trans('global.partner_name') }}</th>
                                    <th>{{ trans('global.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($affPartLists as $aff_partner)
                                    <tr data-entry-id="{{ $aff_partner['id'] }}" class="text-center">
                                        <td>{{ $aff_partner['count'] ?? '' }}</td>
                                        <td>{{ $aff_partner['affiliate_id'] ?? '' }}</td>
                                        <td>{{ $aff_partner['partner_id'] ?? '' }}</td>
                                        <td>
                                            @if(auth()->user()->can('affiliate-edit'))
                                            <a href="#" onClick="selList({{ $aff_partner['id'] ?? '' }},{{ $aff_partner['aff_id'] ?? '' }},{{ $aff_partner['part_id'] ?? '' }})" class="edit btn btn-info btn-sm">{{ trans('global.edit') }}</a>
                                            @endif

                                            @if(auth()->user()->can('affiliate-delete'))
                                            <a href="#" onClick="delRecord({{ $aff_partner['id'] ?? '' }})" class="edit btn btn-danger btn-sm">{{ trans('global.delete') }}</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {!! $cbmasterlist->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<link href="{{ asset('vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('vendors/select2/dist/js/select2.full.min.js') }}"></script>

<script type="text/javascript">
    // Apply select2 class
    $("#affiliate").select2();
    $("#partner").select2();
    $("#fAffiliate").select2();
    $("#fPartner").select2();

    // Edit the record
    function selList(recid, affid, partid) {
        $('#btnSubmit').text("Update");
        $('#btnReset').show();
        $('#btnFilter').hide();
    if (affid > 0) {
        $('#affiliate').val(affid);
        $('#affiliate').trigger('change');
    }
    if (partid > 0) {
        $('#partner').val(partid);
        $('#partner').trigger('change');
    }
    $('#recid').val(recid);
    }

    // Reset mapping form
    $('#btnReset').on('click',function(){
        $('#affiliate').val(null).trigger('change');
        $('#partner').val(null).trigger('change');
        $('#recid').val(null);
        $('#btnSubmit').text("Assign");
        $('#btnReset').hide();
        $('#btnFilter').show();
    });

    $('#btnFilter').on('click',function(){
        $('#frmFilter').show();
    });

    $('#btnCancel').on('click',function(){
        window.location="cb-master";
        $('#frmFilter').hide();
    });

    // Delete record
    function delRecord(delid) {
        if (delid > 0) {
            var con = confirm("Are you sure you want to delete this record?");
            if(con) {
                window.location="admin/affiliates/delete/"+delid;
            }
        }
    }
</script>
@endpush
