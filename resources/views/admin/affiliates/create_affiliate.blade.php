@extends('admin.layouts.master')
@section('page_title', trans('global.create') .' '. trans('cruds.partner.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.affiliates.index") }}">{{ trans('cruds.affiliate.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.create_affiliate_id') }}</li>
@endsection

@section('content')
    <div class="x_panel">
        <div class="x_title">
            <a href="{{ route("admin.affiliates.create_partner") }}" class="btn btn-primary float-sm-right">
                {{ trans('global.create_partner_name') }}
            </a>
            <div class="clearfix"></div>
        </div>

        <div class="x_content">
            {{-- Create Affiliate ID --}}
            <form method="POST" action="{{ route("admin.affiliates.create_affiliate") }}" enctype="multipart/form-data" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                @csrf
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align required" for="name">{{ trans('global.affiliate_id') }}
                    </label>
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
                        <button type="submit" class="btn btn-success" name="submit" id="btnSubmit">{{ trans('global.create') }}</button>
                        <button type="submit" class="btn btn-info" name="submit" value="Search" id="btnSearch">{{ trans('global.search') }}</button>
                        <button type="reset" class="btn btn-secondary" id="btnReset">{{ trans('global.reset') }}</button>
                        <a href="{{route('admin.affiliates.index')}}" class="btn btn-link" type="button">{{ trans('global.cancel') }}</a>
                    </div>
                </div>
            </form>

            {{-- List of Affiliate ID --}}
            <table class="table table-striped table-bordered top_affiliates">
                <thead>
                    <tr class="text-center">
                        <th>#No</th>
                        <th>{{ trans('global.affiliate_id') }}</th>
                        <th>{{ trans('global.image') }}</th>
                        <th>{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($affArr as $affiliate)
                        <tr data-entry-id="{{ $affiliate['id'] }}" class="text-center">
                            <td>{{ $affiliate['count'] ?? '' }}</td>
                            <td>{{ $affiliate['name'] ?? '' }}</td>
                            <td>
                                <div class="row">
                                    <div class="col-6 col-sm-4">
                                        <img id="original_{{$affiliate['id']}}" src="{{ $affiliate['image'] ? asset('/storage/aff_images/'. $affiliate['image']) : asset('img/default-user.jpg') }}" class="img-fluid rounded-circle w-25">
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <form method="POST" name="imageUploadForm_{{$affiliate['id']}}" action="{{ route('admin.affiliates.saveimage', $affiliate['id']) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('POST')
                                            <input type="file" name="photo_name" id="photo_name" required="" class="form-control-file">&nbsp;
                                            <button type="submit" class="btn btn-secondary d-flex justify-content-center btn-sm">{{ trans('global.upload') }}</button>
                                            <input type="hidden" name="aff_id" value="{{$affiliate['id']}}">
                                        </form>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <a href="#"  onClick="selList({{ $affiliate['id'] ?? '' }}, '{{ $affiliate['name'] ?? '' }}')" class="edit btn btn-info btn-sm">{{ trans('global.edit') }}</a>
                                @if( $affiliate['isInMasterList'] == true)
                                    {{-- <a href="#" onClick="delRecord({{ $affiliate['id'] ?? '' }})" class="edit btn btn-danger btn-sm">Delete</a> --}}

                                    <form action="{{ route('admin.affiliates.deleteaaffiliate', $affiliate['id']) }}" style="display:inline;" method="GET" onsubmit="return confirm('Are you sure want to delete?');">
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

            {!! $affiliatelist->links() !!}
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

        if(delid>0) {
            var con = confirm("Are you sure you want to delete this record?");
            if (con) {
                window.location="admin/affiliates/deleteaaffiliate/"+delid;
            }
        }
    }

    function submitform(ids){
        console.log(ids);
        frmName = "imageUploadForm_"+ids;
        $('form[name="'+frmName+'"]').submit();
    }

    // For Image Upload
    $(document).ready(function (e) {
        $('#imageUploadForm').on('submit',(function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                type:'POST',
                url: "{{ url('admin/affiliates/save-image')}}",
                data:formData,
                cache:false,
                contentType: false,
                processData: false,
                success:function(data){
                    $('#original').attr('src', 'public/images/'+ data.photo_name);
                    $('#thumbImg').attr('src', 'storage/aff_images/'+ data.photo_name);
                },
                error: function(data){
                    console.log(data);
                }
            });
        }));
    });
</script>
@endpush
