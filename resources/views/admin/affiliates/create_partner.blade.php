@extends('admin.layouts.master')
@section('page_title', trans('global.create') .' '. trans('cruds.partner.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.affiliates.index") }}">{{ trans('cruds.affiliate.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.create_partner_name') }}</li>
@endsection

@section('content')
    <div class="x_panel">
        <div class="x_title">
            <a href="{{ route("admin.affiliates.create_affiliate") }}" class="btn btn-primary float-sm-right">
                {{ trans('global.create_affiliate_id') }}
            </a>
            <div class="clearfix"></div>
        </div>

        <div class="x_content">
            {{-- Create Partner Name --}}
            <form method="POST" action="{{ route("admin.affiliates.create_partner") }}" enctype="multipart/form-data" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                @csrf
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align required" for="name">{{ trans('global.partner_name') }}</label>
                    <div class="col-md-6 col-sm-6 ">
                        <input type="text" id="name" maxlength="35" required="required" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" value="{{ old('name', '') }}" required>

                        @if($errors->has('name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="ln_solid"></div>
                <div class="item form-group">
                    <div class="col-md-6 col-sm-6 offset-md-3">
                        <input type="hidden" id="recid" name="recid" value="">
                        <button type="submit" class="btn btn-success"  name="submit" value="Create" id="btnSubmit">{{ trans('global.create') }}</button>
                        <button type="submit" class="btn btn-info"  name="submit" value="Search" id="btnSearch">{{ trans('global.search') }}</button>
                        <button type="reset" class="btn btn-secondary" id="btnReset">{{ trans('global.reset') }}</button>
                        <a href="{{route('admin.affiliates.index')}}" class="btn btn-link" type="button">{{ trans('global.cancel') }}</a>
                    </div>
                </div>
            </form>

            {{-- List of Partner Name --}}
            <table class="table table-striped table-bordered top_affiliates">
                <thead>
                    <tr class="text-center">
                        <th>#No</th>
                        <th>{{ trans('global.partner_name') }}</th>
                        <th>{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($partArr as $partner)
                        <tr data-entry-id="{{ $partner['id'] }}" class="text-center">
                            <td>{{ $partner['count'] ?? '' }}</td>
                            <td>{{ $partner['name'] ?? '' }}</td>
                            <td>
                                <a href="#" onClick="selList({{ $partner['id'] ?? '' }}, '{{ $partner['name'] ?? '' }}')" class="edit btn btn-info btn-sm">{{ trans('global.edit') }}</a>
                                @if( $partner['isInMasterList'] == true)
                                 {{-- <a href="#" onClick="delRecord({{ $partner['id'] ?? '' }})" class="edit btn btn-danger btn-sm">Delete</a> --}}
                                 <form action="{{ route('admin.affiliates.deleteapartner', $partner['id']) }}" style="display:inline;" method="GET" onsubmit="return confirm('Are you sure want to delete?');">
                                    @csrf
                                    @method('GET')
                                    <input type="submit" class="btn btn-danger btn-sm" value="{{ trans('global.delete') }}">
                                </form>

                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {!! $partnerlist->links() !!}
        </div>
    </div>
@endsection

@push('script')
<script>
    function selList(recid, affName) {
        console.log(recid+':'+affName);
        $('#btnSubmit').text("Update");
        $('#recid').val(recid);
        $('#name').val(affName);
    }

    $('#btnReset').on('click',function(){
        $('#name').val(null).trigger('change');
        $('#recid').val(null).trigger('change');
        $('#btnSubmit').text("Create").trigger('change');
    });

    // Delete record
    function delRecord(delid) {
        if (delid > 0) {
            var con = confirm("Are you sure you want to delete this record?");
            if(con) {
                window.location="admin/affiliates/deleteapartner/"+delid;
            }
        }
    }
</script>
@endpush
