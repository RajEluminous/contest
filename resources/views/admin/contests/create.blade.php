@extends('admin.layouts.master')
@section('page_title', trans('global.create') .' '. trans('cruds.contest.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.contests.index") }}">{{ trans('cruds.contest.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.create') }}</li>
@endsection

@section('content')
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="pop1" role="tabpanel" aria-labelledby="pop1-tab">
            <div class="x_title my-3">
                <h2>{{ trans('global.general_information') }} </h2>
                <div class="clearfix"></div>
            </div>

            {{-- Configuration section --}}
            <form method="POST" action="{{ route("admin.contests.store") }}" enctype="multipart/form-data" class="form-horizontal form-label-left">
                @csrf
                <div class="field item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.contest.fields.name') }}</label>
                    <div class="col-md-6 col-sm-6">
                        <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" placeholder="{{ trans('cruds.contest.fields.name') }}" value="{{ old('name', '') }}" required>
                        @if($errors->has('name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="field item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.contest.fields.start_end_datetime') }}</label>
                    <div class="col-md-6 col-sm-6">
                        <input type="text" name="datefilter" id="datefilter" value="" class="date_range_filter date hasDatepicker form-control" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%" />
                        <input type="text" id="start_date" name="start_date" style="display: none"><input type="text" id="end_date" name="end_date" style="display: none">
                        @if($errors->has('start_date'))
                            <div class="invalid-feedback">
                                {{ $errors->first('start_date') }}
                            </div>
                        @endif
                        @if($errors->has('end_date'))
                            <div class="invalid-feedback">
                                {{ $errors->first('end_date') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="field item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.contest.fields.contest_result_places') }}</label>
                    <div class="col-md-6 col-sm-6">
                        <input type="number" class="form-control {{ $errors->has('contest_result_places') ? 'is-invalid' : '' }}" name="contest_result_places" placeholder="{{ trans('cruds.contest.fields.contest_result_places') }}" value="{{ old('contest_result_places', '') }}" required>
                        @if($errors->has('contest_result_places'))
                            <div class="invalid-feedback">
                                {{ $errors->first('contest_result_places') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="field item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.contest.fields.display_countdown_timer') }}</label>
                    <div class="col-md-6 col-sm-6 align-bottom">
                        <div class="checkbox mt-2">
                            <label>
                                <input type="checkbox" value="1" name="is_display_counter_timer">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="field item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align required">{{ trans('cruds.contest.fields.action_after_countdown_expires') }}</label>
                    <div class="col-md-3">
                        <select class="form-control" name="action_after_countdown_expire" id="action_after_countdown_expire" required>
                            <option>{{ trans('global.please_select') }}</option>
                            <option value="redirect">{{ trans('global.redirect_to') }}</option>
                            <option value="display_text" selected>{{ trans('global.display_text') }}</option>
                            <option value="hide">{{ trans('global.hide') }}</option>
                        </select><br>
                        <input class="form-control" placeholder="{{ trans('global.display_text_to_contestant') }}" data-validate-length-range="6" data-validate-words="2" name="action_after_countdown_expire_value" id="action_after_countdown_expire_value">
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="ln_solid"></div>

                <div class="item form-group">
                    <div class="col-md-6 col-sm-6 offset-md-3">
                        <button type="submit" class="btn btn-success">{{ trans('global.create') }}</button>
                        <a href="{{ route("admin.contests.index") }}" class="btn btn-link">{{ trans('global.cancel') }}</a>
                    </div>
                </div>
            </form>
            {{-- End: Configuration section --}}
        </div>
    </div>
@endsection

@push('style')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('script')
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(document).ready(function(){
        $("#top_sales_count").addClass('d-none');
        $("#top_revenue").addClass('d-none');

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

        $('#action_after_countdown_expire').on('change', function() {
            $("#action_after_countdown_expire_value").removeClass('d-none');
            if(this.value=='redirect'){
                $('#action_after_countdown_expire_value').attr("placeholder", "{{ trans('global.redirect_contestant_to') }}");
            }
            else if(this.value=='display_text'){
                $('#action_after_countdown_expire_value').attr("placeholder", "{{ trans('global.display_text_to_contestant') }}");
            }
            else {
                $('#action_after_countdown_expire_value').val("");
                $("#action_after_countdown_expire_value").addClass('d-none');
            }
        });
    });

    $(function() {
        var start = moment().subtract(29, 'days');
        var end = moment();
        function cb(start, end) {
            $('#datefilter span').html(start.format('YYYY-MM-DD hh:mm A') + ' - ' + end.format('YYYY-MM-DD hh:mm A'));
        }

        $('input[name="datefilter"]').daterangepicker({
            timePicker: true,
            //startDate: moment().startOf('hour'),
            //endDate: moment().startOf('hour').add(64, 'hour'),
            autoUpdateInput: true,
            locale: {
                cancelLabel: 'Clear',
                format: "YYYY-MM-DD hh:mm A",
                separator: " to ",
                cancelLabel: "Clear",
                customRangeLabel: "Custom",
            }
        }, cb);

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
