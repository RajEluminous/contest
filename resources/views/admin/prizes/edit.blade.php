@extends('admin.layouts.master')
@section('page_title', trans('global.edit') .' '. trans('cruds.prize.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.prizes.index") }}">{{ trans('cruds.prize.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.edit') }}</li>
@endsection

@section('content')
    <form id="prize" method="POST" action="{{ route("admin.prizes.update", [$prize->id]) }}" enctype="multipart/form-data" class="form-horizontal form-label-left mt-3">
        @csrf
        @method('PATCH')
        <div class="x_title">
            <h2>{{ trans('global.general_information') }}</h2>
            <div class="clearfix"></div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required" for="first-name">{{ trans('cruds.cb_account.fields.name') }}</label>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" value="{{ old('name', $prize->name) }}" required>

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
                        <option value="{{$contest->id}}" @if($contest->id==$prize->contest_id) selected  @endif>{{$contest->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align">{{ trans('cruds.prize.fields.aff_tools_link') }}</label>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control {{ $errors->has('aff_tools_link') ? 'is-invalid' : '' }}" name="aff_tools_link" value="{{ old('aff_tools_link', $prize->aff_tools_link) }}" placeholder="{{ trans('cruds.prize.fields.aff_tools_link') }}" value="{{ old('name', '') }}" required>

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
                    <option value="SALE" @if($prize->contest_type=='SALE') selected  @endif>Sale</option>
                    <option value="LEAD" @if($prize->contest_type=='LEAD') selected  @endif>Lead</option>
                    <option value="BOTH" @if($prize->contest_type=='BOTH') selected  @endif>Both</option>
                </select>
                @if($errors->has('contest_type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('contest_type') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('global.column') }}</label>
            <div class="col-md-6 col-sm-6">
                <select class="form-control" name="column" id="column" required>
                    <option value="">{{ trans('global.please_select') }}</option>
                    <option value="1" @if($prize->column=='1') selected  @endif>1 Column</option>
                    <option value="2" @if($prize->column=='2') selected  @endif>2 Column</option>
                </select>
                @if($errors->has('column'))
                    <div class="invalid-feedback">
                        {{ $errors->first('column') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.prize.fields.column_label_1') }}</label>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control {{ $errors->has('column_label_1') ? 'is-invalid' : '' }}" name="column_label_1" id="column_label_1" value="{{ old('column_label_1', $prize->column_label_1) }}" placeholder="{{ trans('cruds.prize.fields.column_label_1') }}" value="{{ old('column_label_1', '') }}" required>

                @if($errors->has('column_label_1'))
                    <div class="invalid-feedback">
                        {{ $errors->first('column_label_1') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="item form-group">
            <label class="col-form-label col-md-3 col-sm-3 label-align" id="lbl_column_label_2">{{ trans('cruds.prize.fields.column_label_2') }}</label>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control {{ $errors->has('column_label_2') ? 'is-invalid' : '' }}" name="column_label_2" id="column_label_2" value="{{ old('column_label_2', $prize->column_label_2) }}" placeholder="{{ trans('cruds.prize.fields.column_label_2') }}" value="{{ old('column_label_2', '') }}">

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
                <input type="hidden" id="pz_column_id" name="pz_column_id" value="{{$prize->column}}">
                <input type="hidden" id="pz_contest_type" name="pz_contest_type" value="{{$prize->contest_type}}">
                <button type="submit" class="btn btn-success">{{ trans('global.update') }}</button>
                <a href="{{ route("admin.prizes.index") }}" class="btn btn-link">{{ trans('global.cancel') }}</a>
            </div>
        </div>
    </form>

    <form onreset="myfunc()" method="POST" class="form-horizontal form-label-left mt-5" id="frm_add_prize" action="{{ route("admin.prizes.addprize", [$prize->id]) }}">
        @csrf
        @method('POST')
        <div class="x_title">
            <h2><i class="fa fa-list"></i> {{ trans('global.add_prize_list') }}</h2>
            <div class="clearfix"></div>
        </div>
        <table id='tbl' class="table table-striped table-bordered mt-3">
            <tbody>
                <tr>
                    <td>
                        <select class="select2_single form-control" name="prize_category[]" id="prize_category" required>
                            <option value="">{{ trans('global.select_prize_type') }}</option>
                            <option value="top">{{ trans('global.top') }}</option>
                            <option value="all">{{ trans('global.all') }}</option>
                        </select>
                    </td>
                    <td><input type="text" class="form-control" autofocus placeholder="{{ trans('cruds.prize.fields.short_desc') }}" name="short_desc[]" id="short_desc"></td>
                    <td><input type="text" class="form-control" placeholder="{{ trans('cruds.prize.fields.amount') }}" name="amount[]" id="amount"></td>
                </tr>
            </tbody>
        </table>

        <div class="col-md-4 alert alert-primary" role="alert">
            <strong><i class="fa fa-lightbulb-o"></i> Tips:</strong> You may reorder the list by dragging a prize list to a new position.
        </div>

        <input type="hidden" id="pz_prize_id" name="pz_prize_id" value="">
        <button type="submit" id="btnSubmit" name="btnSubmit" class="btn btn-success float-right">{{ trans('global.save_prize_list') }}</button>
        <input type="reset" class="btn btn-link float-right d-none" id="btnReset" value="{{ trans('global.cancel') }}"></button>
        <button type="submit" id="btnUpdate" name="btnUpdate" value="btnUpdate" class="btn btn-success d-none float-right">{{ trans('global.update') }}</button>
        <input type="button" class="btn btn-info float-right" id="btnAdd" value="{{ trans('global.add_new_row') }}"></button>
        <button class="btn btn-danger float-right" id="btnDeleteLastRow" onclick="removeThis()">{{ trans('global.delete_last_row') }}</button>
    </form>

    <table class="table table-striped table-bordered mt-3">
        <tr>
            <th>#</th>
            <th width="400px">{{ trans('cruds.prize.fields.prize_type') }}</th>
            <th>{{ trans('cruds.prize.fields.short_desc') }}</th>
            <th>{{ trans('cruds.prize.fields.amount') }}</th>
            <th>{{ trans('global.actions') }}</th>
        </tr>
        <tbody class="row_position">
        @if(count($rs_pd) > 0)
            @php
                $i = 0;
            @endphp
            @foreach ($rs_pd as $key => $rs_pdetail)
                <tr id="{{ $rs_pdetail->id }}">
                    <td>{{ ++$i }}</td>
                    <td>
                        {{ $rs_pdetail->prize_type }}
                    </td>
                    <td>
                        {{ $rs_pdetail->short_desc }}
                    </td>
                    <td>
                        {{ number_format($rs_pdetail->amount) }}
                   </td>
                   <td>
                    <a onClick="selList({{ $rs_pdetail->id }},'{{ $rs_pdetail->prize_type ?? '' }}','{{ $rs_pdetail->short_desc }}',{{ $rs_pdetail->amount }})" class="btn btn-info btn-sm" href="javascript:void(0);">{{ trans('global.edit') }}</a>
                    <form action="{{ route('admin.prizes.deletepricedetail', $rs_pdetail->id) }}" style="display:inline;" method="POST" onsubmit="return confirm('Are you sure want to delete?');">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="up_prize_id" value="{{ $prize->id }}">
                        <input type="submit" class="btn btn-danger btn-sm" value="{{ trans('global.delete') }}">
                    </form>
                </td>
                </tr>
            @endforeach
        @else
            <tr><td colspan="5" align="center">{{ trans('global.no_record_found') }}</td></tr>
        @endif
        </tbody>
    </table>

@endsection

@push('script')
<script src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

{!! JsValidator::formRequest('App\Http\Requests\PrizeUpdateRequest', '#prize'); !!}

<script>
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
jQuery(document).ready(function($){
        var columnid = $('#pz_column_id').val();
        $('#column').val(columnid).trigger("change");

        var pz_contest_type = $('#pz_contest_type').val();
        $("#prize_category").html("");
        if(pz_contest_type=='BOTH'){
            $("#prize_category").append(new Option('SALE', 'SALE'));
            $("#prize_category").append(new Option('LEAD', 'LEAD'));
        } else{
            $("#prize_category").append(new Option(pz_contest_type, pz_contest_type));
        }
});

$("#column").on("change",function(){
    if(this.value==1){
        $('#column_label_2').val("");
        $('#column_label_2').prop("disabled", true);
        $('#column_label_2').removeAttr('required');
        $('#lbl_column_label_2').removeClass("required");
    } else {
        $('#column_label_2').prop("disabled", false);
        $('#column_label_2').prop("required", true);
        $('#lbl_column_label_2').addClass("required");

    }
});

$("#contest_type").on("change",function(){
    //$('#prize_category').val(this.value).text(this.value).trigger("change");
    $("#prize_category").html("");
    if(this.value=='BOTH'){
        $("#prize_category").append(new Option('SALE', 'SALE'));
        $("#prize_category").append(new Option('LEAD', 'LEAD'));
    } else{
        $("#prize_category").append(new Option(this.value, this.value));
    }
});

console.log(CSRF_TOKEN);
$( ".row_position" ).sortable({
        delay: 150,
        stop: function() {
            var selectedData = new Array();
            $('.row_position>tr').each(function() {
                selectedData.push($(this).attr("id"));
            });
            updateOrder(selectedData);
        }
    });

    function updateOrder(data) {
        console.log("---------Update Order ---------");
        console.log(data);

        $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': CSRF_TOKEN
                  }
              });

        $.ajax({
            url: '{{ route("admin.prizes.ajaxupdateorder") }}',
            type:'post',
            data:{"_token": "{{ csrf_token() }}",position:data},
            success:function(){
                //alert('your change successfully saved');
                window.location.reload();
            }
        })
    }

$("#btnAdd").on("click",function(){
    var $tableBody = $('#tbl').find("tbody"),
            $trLast = $tableBody.find("tr:last"),
            $trNew = $trLast.clone();

            $trNew.find('input,select').each(function () {
                $(this).val('');
            });

        $trLast.after($trNew);
});

function myfunc(){
    $('#btnUpdate').addClass('d-none');
    $('#btnReset').addClass('d-none');
    $('#pz_prize_id').val('');
    $('#btnSubmit').removeClass('d-none');
    $('#btnDeleteLastRow').removeClass('d-none');
    $('#btnAdd').removeClass('d-none');
}

function removeThis(){
    var $tableBody = $('#tbl').find("tbody"),
        $last = $tableBody.find("tr:last");
        if($last.is(':first-child')){
            alert('There is no row to delete.')
        }else {
            $last.remove()
        }
}

function selList(pz_prize_id,prize_type,short_desc,amount) {
        // console.log(pz_prize_id);
        // console.log(prize_type);
        // console.log(short_desc);
        // console.log(amount);

        $('#pz_prize_id').val(pz_prize_id);
        $('#btnUpdate').removeClass('d-none');
        $('#btnReset').removeClass('d-none');
        $('#btnSubmit').addClass('d-none');
        $('#btnDeleteLastRow').addClass('d-none');
        $('#btnAdd').addClass('d-none');
        $('#prize_category').val(prize_type).trigger('change');
        $('#short_desc').val(short_desc);
        $('#amount').val(amount);
        // if(cbaid>0) {
        //     $('#clickbank_account').val(cbaid);
        //     $('#clickbank_account').trigger('change');
        // }

        // if(cbprod.length >0){
        //     $('#restTagsValue').val(cbprod);

        //     var array = cbprod.split(",");
        //     $.each(array,function(i){
        //      $('#tags_1').addTag(array[i]);
        //     });
        // }
        // $('#include_rebill').prop('checked', false);
        // if(irebill==1)
        //  $('#include_rebill').prop('checked', true);
    }

</script>
@endpush
