@extends('admin.layouts.master')
@section('page_title', trans('cruds.contest.title'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ trans('cruds.contest.title') }}</li>
@endsection

@section('add_new_button')
    @if(auth()->user()->can('contest-create'))
    <a href="{{ route("admin.contests.create") }}" class="btn btn-primary float-right">
        {{ trans('global.create') }} {{ trans('cruds.contest.title_singular') }}
    </a>
    @endif
@endsection

@section('content')
    <div class="table-responsive"><div class="flash-message"></div>
        <table id="datatable-contests" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th class="d-none" rowspan="2"</th>
                    <th rowspan="2">{{ trans('cruds.contest.fields.name') }}</th>
                    <th rowspan="2">{{ trans('cruds.contest.fields.contest_type') }}</th>
                    <th rowspan="2">{{ trans('cruds.contest.fields.start_end_datetime') }}</th>
                    <th colspan="5" class="text-center">{{ trans('cruds.contest.fields.display') }}</th>
                    <th rowspan="2">{{ trans('cruds.contest.fields.status') }}</th>
                    <th rowspan="2">{{ trans('global.actions') }}</th>
                </tr>
                <tr class="text-center">
                    <th>{{ trans('cruds.contest.fields.contest') }}</th>
                    <th>{{ trans('cruds.contest.fields.sales') }}</th>
                    <th>{{ trans('cruds.contest.fields.leads') }}</th>
                    <th>{{ trans('cruds.affiliate.fields.affiliate_image') }}</th>
                    <th>{{ trans('cruds.prize.title_plural') }}</th>
                </tr>
            </thead>

            <tbody>
                @if(count($contests) > 0)
                    @foreach ($contests as $key => $contest)
                    @php
                       //echo $startDate =  new DateTime('2000-01-01');
                       $startdate = new DateTime($contest->start_date );
                       $startdate->format('Y-m-d g:i A');
                    @endphp
                    <tr data-entry-id="{{ $contest->id }}">
                            <td class="d-none">
                                {{ $contest->id ?? '' }}
                            </td>
                            <td>{{ $contest->name }}</td>
                            <td>
                                @php $contestType = App\Http\Controllers\Admin\ContestController::getContestType($contest->contest_type); @endphp
                                {{ $contestType }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($contest->start_date)->format('Y-m-d g:i A') }}  <strong>to</strong>  {{ \Carbon\Carbon::parse($contest->end_date)->format('Y-m-d g:i A') }} </td>
                            <td>
                                <label>
                                    <input onclick="changeDisplayStatus('display_contest',this,{{ $contest->id }});" type="checkbox" class="js-switch switchery-small"  @if($contest->display_contest==1) checked  @endif />
                                </label>
                            </td>
                            <td>
                                @php
                                    $display_sales_view = trans('global.hide');
                                    $display_sales_view_class = 'btn-secondary';
                                    $display_sales_view_val = 1;
                                    if($contest->display_sales_view==1){
                                        $display_sales_view = trans('global.show');
                                        $display_sales_view_class = 'btn-success';
                                        $display_sales_view_val = 0;
                                    }
                                @endphp
                                {{-- <a class="btn {{$display_sales_view_class}} btn-sm" href="{{ route('admin.contests.updateviewstatus',['contest_id' => $contest->id, 'type' => 'sales', 'val' => $display_sales_view_val]) }}">{{ $display_sales_view }}</a> --}}
                                <label>
                                    <input onclick="changeDisplayStatus('display_sales_view',this,{{ $contest->id }});" type="checkbox" class="js-switch switchery-small"  @if($contest->display_sales_view==1) checked  @endif />
                                </label>
                            </td>
                            <td>
                                @php
                                    $display_leads_view = trans('global.hide');
                                    $display_leads_view_class = 'btn-secondary';
                                    $display_leads_view_val = 1;
                                    if($contest->display_leads_view==1){
                                        $display_leads_view = trans('global.show');
                                        $display_leads_view_class = 'btn-success';
                                        $display_leads_view_val = 0;
                                    }
                                @endphp
                                {{-- <a class="btn {{$display_leads_view_class}} btn-sm" href="{{ route('admin.contests.updateviewstatus',['contest_id' => $contest->id, 'type' => 'leads', 'val' => $display_leads_view_val]) }}">{{ $display_leads_view }}</a> --}}
                                <label>
                                    <input onclick="changeDisplayStatus('display_leads_view',this,{{ $contest->id }});" type="checkbox" class="js-switch switchery-small"  @if($contest->display_leads_view==1) checked  @endif />
                                </label>
                            </td>
                            <td class="text-center">
                                <label>
                                    <input onclick="changeDisplayStatus('display_affiliate_image',this,{{ $contest->id }});" type="checkbox" class="js-switch switchery-small"  @if($contest->display_affiliate_image==1) checked  @endif />
                                </label>
                            </td>
                            <td class="text-center">
                                <label>
                                    <input onclick="changeDisplayStatus('display_prizes',this,{{ $contest->id }});" type="checkbox" class="js-switch switchery-small"  @if($contest->display_prizes==1) checked  @endif />
                                </label>
                            </td>
                            <td>
                                @php
                                    if($contest->status=='RUNNING') {
                                        $bdgClass = 'success';
                                    } else if($contest->status=='ENDED') {
                                        $bdgClass = 'danger';
                                    } else {
                                        $bdgClass = 'warning';
                                    }
                                @endphp
                                <span class="badge badge-{{$bdgClass}}">{{ $contest->status }}</span>
                            </td>

                            <td>
                                @if(auth()->user()->can('contest-edit'))
                                <a class="btn btn-info btn-sm" href="{{ route('admin.contests.edit',$contest->id) }}">{{ trans('global.edit') }}</a>
                                @endif

                                @if(auth()->user()->can('contest-delete'))
                                <form action="{{ route('admin.contests.destroy', $contest->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.are_you_sure_delete', ['name' => $contest->name]) }}');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="submit" class="btn btn-danger btn-sm" value="{{ trans('global.delete') }}">
                                </form>
                                @endif

                                @if(auth()->user()->can('contest-view-results'))
                                <a class="btn btn-secondary btn-sm" href="{{ route('admin.contests.view',$contest->id) }}">{{ trans('global.view_results') }}</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="9" class="text-center">{{ trans('global.no_record_found') }}</td></tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection

@push('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css') }}">
@endpush

@push('script')
    <!-- DataTables -->
    <script src="{{ asset('vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js') }}"></script>
    <script src="{{ asset('vendors/switchery/dist/switchery.min.js') }}"></script>

    <script>
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $(function () {
          $('#datatable-contests').DataTable({
            "lengthChange": false,
            "pageLength": 20,
            "order": [[ 0, "desc" ]],
            "columnDefs": [ {
                "targets": [4, 5, 6, 7, 9],
                "orderable": false
            }]
          });
        });

        function changeDisplayStatus(display,e,cid){
            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
            });

            $.ajax({
                url: '{{ route("admin.contests.updatedisplaystatus") }}',
                type:'post',
                data:{"_token": "{{ csrf_token() }}",'update_field':display ,'contest_id':cid,'checked_status':e.checked},
                success:function(data){
                    console.log(data.message)
                    $('div.flash-message').html('');
                    $('div.flash-message').html('<div class="alert alert-success" role="alert"><i class="fa fa-check-circle"></i><span> '+data.message+'.</span></div>');
                    //alert('your change successfully saved');
                    //window.location.reload();
                }
            })
        }
      </script>
@endpush
