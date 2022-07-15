@extends('admin.layouts.master')
@section('page_title', trans('global.edit') .' '. trans('cruds.contest.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.contests.index") }}">{{ trans('cruds.contest.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.edit') }}</li>
@endsection

@section('content')
    <nav class="nav-justified ">
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="pop1-tab" data-toggle="tab" href="#pop1" role="tab" aria-controls="pop1" aria-selected="true">{{ trans('global.general_information') }}</a>
            <a class="nav-item nav-link" id="pop2-tab" data-toggle="tab" href="#pop2" role="tab" aria-controls="pop2" aria-selected="false">{{ trans('global.contest_config') }}</a>
        </div>
    </nav>

    <div class="tab-content" id="nav-tabContent">
        {{-- General Information section --}}
        <div class="tab-pane fade show active" id="pop1" role="tabpanel" aria-labelledby="pop1-tab">
            <div class="x_title my-3">
                <h2>{{ trans('global.general_information') }} </h2>
                <div class="clearfix"></div>
            </div>

            <form method="POST" action="{{ route("admin.contests.update", [$contest->id]) }}" enctype="multipart/form-data" class="form-horizontal form-label-left">
                @csrf
                @method('PATCH')

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align required">{{ trans('cruds.contest.fields.name') }}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" value="{{ old('name', $contest->name) }}" required>
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align required">{{ trans('cruds.contest.fields.start_end_datetime') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="datefilter" id="datefilter" value="" class="date_range_filter date hasDatepicker form-control" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%" />
                        <input type="text" id="start_date" name="start_date" value="{{ $contest->cal_start_date }}" style="display: none"><input type="text" id="end_date" name="end_date" value="{{ $contest->cal_end_date }}" style="display: none">
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align required">{{ trans('cruds.contest.fields.contest_result_places') }}</label>
                    <div class="col-md-3">
                        <input type="number" class="form-control {{ $errors->has('contest_result_places') ? 'is-invalid' : '' }}" name="contest_result_places" value="{{ old('contest_result_places', $contest->contest_result_places) }}" required>
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align required">{{ trans('cruds.contest.fields.display_countdown_timer') }}</label>
                    <div class="col-md-6">
                        <div class="checkbox mt-2">
                            <label>
                            <input type="checkbox" value="1" name="is_display_counter_timer" @if($contest->is_display_counter_timer == 1) checked  @endif>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align required">{{ trans('cruds.contest.fields.action_after_countdown_expires') }}</label>
                    <div class="col-md-3">
                        <select class="form-control" name="action_after_countdown_expire" id="action_after_countdown_expire" required>
                            <option>{{ trans('global.please_select') }}</option>
                            <option value="redirect" @if($contest->action_after_countdown_expire=='redirect') selected  @endif>{{ trans('global.redirect_to') }}</option>
                            <option value="display_text"  @if($contest->action_after_countdown_expire=='display_text') selected  @endif>{{ trans('global.display_text') }}</option>
                            <option value="hide" @if($contest->action_after_countdown_expire=='hide') selected  @endif>{{ trans('global.hide') }}</option>
                        </select><br>
                        <input class="form-control @if($contest->action_after_countdown_expire=='hide') d-none  @endif" value="{{ $contest->action_after_countdown_expire_value }}" placeholder="{{ trans('global.display_text_to_contestant') }}" name="action_after_countdown_expire_value" id="action_after_countdown_expire_value">
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align required">{{ trans('cruds.contest.fields.status') }}</label>
                    <div class="col-md-3">
                        <select class="form-control" name="status" id="status" data-pre="{{ $contest->status }}" required>
                            <option>{{ trans('global.please_select') }}</option>
                            <option value="RUNNING" @if($contest->status=='RUNNING') selected  @endif>{{ trans('global.running') }}</option>
                            <option value="PAUSE" @if($contest->status=='PAUSE') selected  @endif>{{ trans('global.pause') }}</option>
                            <option value="ENDED" @if($contest->status=='ENDED') selected  @endif>{{ trans('global.ended') }}</option>
                            <option value="CLOSED" @if($contest->status=='CLOSED') selected  @endif>{{ trans('global.closed') }}</option>
                        </select>
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="ln_solid"></div>

                <div class="item form-group">
                    <div class="col-md-6 col-sm-6 offset-md-3">
                        <button type="submit" class="btn btn-success">{{ trans('global.update') }}</button>
                        <a href="{{ route("admin.contests.index") }}" class="btn btn-link">{{ trans('global.cancel') }}</a>
                    </div>
                </div>
            </form>
        </div>
        {{-- End: General Information section --}}

        {{-- Contest Configuration section --}}
        <div class="tab-pane fade" id="pop2" role="tabpanel" aria-labelledby="pop2-tab">
            {{-- Sales Contest Type --}}
            <div class="x_title my-3">
                <h2>{{ trans('global.sales_contest_type') }}</h2>
                <div class="clearfix"></div>
            </div>
            <form method="POST" class="form-horizontal form-label-left" id="frm_cb_contest_type" action="{{ route("admin.contests.savecbcontest", [$contest->id]) }}">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="item form-group col-md-4">
                        <label class="col-form-label col-md-3 label-align required">{{ trans('cruds.contest.fields.contest_type') }}</label>
                        <div class="col-md-6">
                            <select class="form-control" name="contest_type" id="contest_type" required>
                                <option>{{ trans('global.please_select') }}</option>
                                <option value="top_sales_count" @if($contest->contest_type=='top_sales_count') selected  @endif>{{ trans('global.top_sales_count') }}</option>
                                <option value="top_revenue" @if($contest->contest_type=='top_revenue') selected  @endif>{{ trans('global.top_revenue') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="item form-group col-md-4">
                        <div class="@if($contest->contest_type=='top_revenue') d-none  @endif" id="top_revenue">
                            <label class="col-form-label col-md-6 label-align">{{ trans('global.display_revenue_on_ldrbrd') }}</label>
                            <div class="col-md-2">
                                <div class="checkbox mt-2">
                                    <label>
                                        <input type="checkbox" value="1" name="is_display_revenue" @if($contest->is_display_revenue==1) checked  @endif>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="@if($contest->contest_type=='top_sales_count') d-none  @endif" id="top_sales_count">
                            <label class="col-form-label col-md-6 label-align">{{ trans('global.display_top_sales_on_ldrbrd') }}</label>
                            <div class="col-md-2">
                                <div class="checkbox mt-2">
                                    <label>
                                        <input type="checkbox" value="1" name="is_display_total_sale" @if($contest->is_display_total_sale==1) checked  @endif>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item form-group col-md-2">
                        <label class="col-form-label col-md-6 label-align">{{ trans('global.display_result_on_ldrbrd') }}</label>
                        <div class="col-md-2">
                            <div class="checkbox mt-2">
                                <label>
                                    <input type="checkbox" value="1" name="display_sales_result" @if($contest->display_sales_result==1) checked  @endif>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="item form-group col-md-2">
                        <div class="col-md-6 col-sm-6">
                            <button type="submit" class="btn btn-success float-right">{{ trans('global.update') }}</button>
                        </div>
                    </div>
                </div>
            </form>
            {{-- End: Sales Contest Type --}}

            {{-- Clickbank Account Configuration --}}
            <div class="x_title my-3">
                <h2><i class="fa fa-dollar text-warning"></i> {{ trans('global.sales_contest_config') }} </h2>
                <div class="clearfix"></div>
            </div>

            <form method="POST" class="form-horizontal form-label-left" id="frmcbprod" action="{{ route("admin.contests.savecbprods", [$contest->id]) }}">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="item form-group col-md-5">
                        <label class="col-form-label col-md-3 label-align required">{{ trans('global.clickbank_account') }}</label>
                        <div class="col-md-6">
                            <select class="form-control" name="clickbank_account" id="clickbank_account" required>
                                <option>{{ trans('global.please_select') }}</option>
                                @foreach ($cbaccounts as $key => $cbaccount)
                                    <option value="{{$cbaccount->id}}">{{$cbaccount->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="item form-group col-md-4">
                        <label class="col-form-label col-md-3 label-align required">{{ trans('global.product_id') }}</label>
                        <div class="col-md-6">

                         <select class="form-control" name="clickbank_product_ids[]" id="clickbank_product_ids" required>

                        </select>
                            {{-- <input type="hidden" id="restTagsValue" value=""> --}}
                        </div>
                    </div>

                    <div class="item form-group col-md-2">
                        <label class="col-form-label col-md-6 label-align">{{ trans('global.include_rebill') }}</label>
                        <div class="col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="include_rebill" name="include_rebill">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="cb_product_id" name="cb_product_id" value="">
                    <input type="hidden" id="flagContestType" name="flagContestType" value="SALES">
                </div>

                <button type="submit" id="btnSubmit" name="btnSubmit" value="btnAdd" class="btn btn-primary float-right">{{ trans('global.add_account_to_sales') }}</button>
                <button type="submit" id="btnUpdate" name="btnUpdate" value="btnUpdate" class="btn btn-success d-none float-right">{{ trans('global.update_account') }}</button>
            </form>

            <table class="table table-striped table-bordered mt-4">
                <tr>
                    <th>#</th>
                    <th>{{ trans('global.clickbank_account') }}</th>
                    <th>{{ trans('global.product_id') }}</th>
                    <th>{{ trans('global.include_rebill') }}</th>
                    <th width="280px">{{ trans('global.actions') }}</th>
                </tr>

                @if(count($contestcbprods) > 0)
                    @foreach ($contestcbprods as $key => $cbprod)
                        @if($cbprod->flag_contest_type=='SALES')
                            <tr>
                                <td>{{ ++$i }}</td>

                                <td>
                                    @php
                                        $cbaccount_name = App\Http\Controllers\Admin\ContestController::getClickbankAccountName($cbprod->clickbank_account);
                                    @endphp
                                    {{ $cbaccount_name }}
                                </td>

                                <td>
                                    @php $cbprd = explode(',' ,$cbprod->clickbank_product_ids); @endphp
                                    @if (count($cbprd)>0)
                                        @foreach ($cbprd as $cbpro)
                                        <span class="badge badge-secondary"> {{$cbpro}} </span>
                                        @endforeach
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if ($cbprod->include_rebill==1)
                                        <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                    @else
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                    @endif
                                </td>

                                <td>

                                    <form action="{{ route('admin.contests.deletecbprods', $cbprod->id) }}" style="display:inline;" method="POST" onsubmit="return confirm('Are you sure want to delete?');">
                                        @csrf
                                        @method('POST')
                                        <input type="submit" class="btn btn-danger btn-sm" value="{{ trans('global.delete') }}">
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    <tr><td colspan="5" align="center">{{ trans('global.no_record_found') }}</td></tr>
                @endif
            </table>
            {{-- End: Clickbank Account Configuration --}}

            {{-- Lead Contest Type --}}
            <div class="x_title mt-5">
                <h2><i class="fa fa-gift text-danger"></i> {{ trans('global.leads_contest_config') }}</h2>
                <div class="clearfix"></div>
            </div>

            <form method="POST" class="form-horizontal form-label-left" id="frmcbprod" action="{{ route("admin.contests.savecbprods", [$contest->id]) }}">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="item form-group col-md-5">
                        <label class="col-form-label col-md-3 label-align required">{{ trans('global.clickbank_account') }}</label>
                        <div class="col-md-6">
                            <select class="form-control" name="clickbank_account" id="clickbank_account" required>
                                <option>{{ trans('global.please_select') }}</option>
                                @foreach ($cbaccounts as $key => $cbaccount)
                                    <option value="{{$cbaccount->id}}">{{$cbaccount->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" id="flagContestType" name="flagContestType" value="LEAD">
                    </div>
                    <div class="item form-group col-md-3">
                        <button type="submit" id="btnSubmit" name="btnSubmit" value="btnAdd" class="btn btn-primary float-right">{{ trans('global.add_account_to_leads') }}</button>
                    </div>

                    <div class="item form-group col-md-2">
                        <label class="col-form-label col-md-6 label-align">{{ trans('global.display_result_on_ldrbrd') }}</label>
                        <div class="col-md-2">
                            <div class="checkbox mt-2">
                                <label>
                                    <input type="checkbox" value="1" name="display_leads_result" @if($contest->display_leads_result==1) checked  @endif>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="item form-group col-md-2">
                        <button type="submit" id="btnUpdateContestLeadDisplay" name="btnUpdateContestLeadDisplay" value="btnUpd" class="btn btn-success float-right">{{ trans('global.update') }}</button>
                    </div>
                </div>
            </form>

            <table class="table table-striped table-bordered mt-3">
                <tr>
                    <th>#</th>
                    <th>{{ trans('global.clickbank_account') }}</th>

                    <th width="280px">{{ trans('global.actions') }}</th>
                </tr>

                @if(count($contestcbprods) > 0)
                    @foreach ($contestcbprods as $key => $cbprod)
                        @if($cbprod->flag_contest_type=='LEAD')
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>
                                @php
                                    $cbaccount_name = App\Http\Controllers\Admin\ContestController::getClickbankAccountName($cbprod->clickbank_account);
                                @endphp
                                {{ $cbaccount_name }}
                            </td>

                            <td>
                                <form action="{{ route('admin.contests.deletecbprods', $cbprod->id) }}" style="display:inline;" method="POST" onsubmit="return confirm('Are you sure want to delete?');">
                                    @csrf
                                    @method('POST')
                                    <input type="submit" class="btn btn-danger btn-sm" value="{{ trans('global.delete') }}">
                                </form>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @else
                    <tr><td colspan="3" align="center">{{ trans('global.no_record_found') }}</td></tr>
                @endif
            </table>
            {{-- End: Lead Contest Type --}}
        </div>
        {{-- End: Contest Configuration section --}}
    </div>
@endsection

@push('style')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>.select2-results__message {display: none !important;}</style>
<link href="{{ asset('vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('vendors/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
{{-- <script src="{{ asset('vendors/jquery.tagsinput/src/jquery.tagsinput.js') }}"></script> --}}
<script>

    $(document).ready(function(){
        // Redirection for tabs
        var url = $(location).attr('href'),
        parts = url.split("/"),
        last_part = parts[parts.length-1];
        if(last_part == 'tab2') {
            $("#pop2-tab").trigger("click");
        }

        $('#contest_type').on('change', function() {
            if (this.value == 'top_sales_count') {
                $("#top_sales_count").addClass('d-none');
                $("#top_revenue").removeClass('d-none');
            }
            if (this.value == 'top_revenue') {
                $("#top_sales_count").removeClass('d-none');
                $("#top_revenue").addClass('d-none');
            }
            if (this.value == 'top_leads') {
                $("#top_sales_count").addClass('d-none');
                $("#top_revenue").addClass('d-none');
            }
        });

        $('#status').on('change', function(e) {
            var preval = $(this).data('pre');
            if ( this.value == 'PAUSE') {
                if(preval!='PAUSE') {
                    if(confirm("Are you sure you want to PAUSE contest?")){}
                    else {
                        $('#status option[value='+preval+']').prop('selected', 'selected').change();
                    }
                }
            }

            if ( this.value == 'ENDED') {
                if(preval!='ENDED') {
                    if(confirm("Are you sure you want to END contest?")){}
                    else{
                        $('#status option[value='+preval+']').prop('selected', 'selected').change();
                    }
                }
            }

            if ( this.value == 'CLOSED') {
                if(preval!='CLOSED') {
                    if(confirm("Are you sure you want to CLOSE contest?")){}
                    else{
                        $('#status option[value='+preval+']').prop('selected', 'selected').change();
                    }
                }
            }

            if ( this.value == 'RUNNING')
            {
               var contcbprods = $('#contestcbprods').val();
               if(contcbprods==0){
                    alert("Clickbank configuration must have atleast 1 record.");
                    $('#status option[value='+preval+']').prop('selected', 'selected').change();
                    $("#pop2-tab").trigger("click");
                    //return false;
               }
            }
        });

        $('#action_after_countdown_expire').on('change', function() {
            $('#action_after_countdown_expire_value').val("");
            $("#action_after_countdown_expire_value").removeClass('d-none');
            if(this.value=='redirect'){
                $('#action_after_countdown_expire_value').attr("placeholder", "e.g. http://google.com");
            }
            else if(this.value=='display_text'){
                $('#action_after_countdown_expire_value').attr("placeholder", "e.g. Thank you");
            }
            else {
                $('#action_after_countdown_expire_value').val("");
                $("#action_after_countdown_expire_value").addClass('d-none');
            }
        });

        // for auto selecting the tags/product ids
        $('#clickbank_account').on('change', function() {
           //$("#clickbank_product_ids").select2('data', { id:"elementID", text: "Hello!"});
           //var $newOption = $("<option selected='selected'></option>").val("TheID").text("The text")

           // $("#clickbank_product_ids").append($newOption);
           $('#clickbank_product_ids').select2({
                tags: true,
                maximumSelectionSize: 12,
                minimumResultsForSearch: Infinity,
                multiple: true,
                minimumInputLength: 0,
                width:'100%',
                placeholder: "Search products",
                ajax: {
                    url: '{{ route("contests.search") }}',
                    delay: 300,
                    dataType: 'json',
                    data: {
                     cb_account_id: this.value,
                    }
                },
            });



        });

    });
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    // $('#clickbank_product_ids').select2({
    //     tags: true,
    //     maximumSelectionSize: 12,
    //     minimumResultsForSearch: Infinity,
    //     multiple: true,
    //     minimumInputLength: 0,
    //     width:'100%',
    //     placeholder: "Search products",
    //     ajax: {
    //         url: '{{ route("contests.search") }}',
    //         dataType: 'json',
    //     },
    // });
    // $("#clickbank_product_ids").select2({
    //         // tags: true,
    //         // maximumSelectionSize: 12,
    //         // minimumResultsForSearch: Infinity,
    //         // multiple: true,
    //         // minimumInputLength: 0,
    //         placeholder: "Search products",
    //         width: '100%',
    //         ajax: {
    //             url: '/contest/select2-autocomplete-ajax',
    //             dataType: 'json',
    //             delay: 400,
    //             processResults: function (data) {
    //                 console.log(data)
    //             // return {
    //             //     results:  $.map(data, function (item) {
    //             //         return {
    //             //             text: item.name,
    //             //             id: item.id
    //             //         }
    //             //     })
    //             // };
    //             },
    //             cache: true
    //         }


    // });

    $("#clickbank_productids").select2({
            tags: true,
            maximumSelectionSize: 12,
            minimumResultsForSearch: Infinity,
            multiple: true,
            minimumInputLength: 0,
            placeholder: "Add product id",
    });

    function selList(cb_product_id,cbaid,cbprod,irebill) {
        console.log(cbaid);
        console.log(cbprod);
        console.log(irebill);
        $('#cb_product_id').val(cb_product_id);
        $('#btnUpdate').removeClass('d-none');
        $('#btnSubmit').addClass('d-none');
        // $('#btnSubmit').text("Update");
        // $('#btnSubmit').val('btnUpdate');
        if(cbaid>0) {
            $('#clickbank_account').val(cbaid);
            $('#clickbank_account').trigger('change');
        }

        if(cbprod.length >0){
            $('#restTagsValue').val(cbprod);

            var array = cbprod.split(",");
            $.each(array,function(i){
             $('#tags_1').addTag(array[i]);
            });
        }
        $('#include_rebill').prop('checked', false);
        if(irebill==1)
         $('#include_rebill').prop('checked', true);
    }

    $(function() {
                var start = moment().subtract(29, 'days');
                var end = moment();
                function cb(start, end) {
                    $('#datefilter span').html(start.format('YYYY-MM-DD hh:mm A') + ' - ' + end.format('YYYY-MM-DD hh:mm A'));
                }
                $('input[name="datefilter"]').daterangepicker({
                    timePicker: true,
                    startDate: "{{ $contest->cal_start_date }}",
                    endDate: "{{ $contest->cal_end_date }}",
                    autoUpdateInput: true,
                    locale: {
                        cancelLabel: 'Clear',
                        format: "YYYY-MM-DD hh:mm A",
                        separator: " to ",
                        cancelLabel: "Clear",
                        customRangeLabel: "Custom",
                    }
                },cb);
                cb(start, end);

                $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD hh:mm A') + ' to ' + picker.endDate.format('YYYY-MM-DD hh:mm A'));
                    $("#start_date").val(picker.startDate.format('YYYY-MM-DD hh:mm A'));
                    $("#end_date").val(picker.endDate.format('YYYY-MM-DD hh:mm A'));

                    console.log(picker.startDate.format('YYYY-MM-DD hh:mm A') + ' to ' + picker.endDate.format('YYYY-MM-DD hh:mm A'));

                });

                $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });

    });
</script>
@endpush
