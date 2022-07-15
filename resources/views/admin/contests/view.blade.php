@extends('admin.layouts.master')
@section('page_title', trans('global.view') .' '. trans('cruds.contest.title_singular'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route("admin.contests.index") }}">{{ trans('cruds.contest.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.view_results') }}</li>
@endsection

@section('content')
<div class="x_title my-3">
    <h2><i class=" text-warning"></i> {{ trans('global.view_results') }} </h2>
    <div class="clearfix"></div>
</div>
<h2>{{ trans('global.leads_contest') }}</h2>
<table class="table table-striped table-bordered mt-4" id="myTable1">
    <thead>
    <tr>
        <th>#</th>
        <th>{{ trans('cruds.cb_account.title_singular') }}</th>
        <th>{{ trans('cruds.affiliate.title_singular') }}</th>
        <th>{{ trans('cruds.contest.fields.lead_count') }}</th>
    </tr>
    </thead>
    <tbody>
    @if(count($cbleads) > 0)
    @php
        $i = 0;
    @endphp
        @foreach ($cbleads as $key => $objLead)
        @if($contest_status == 'RUNNING')
            <tr>
                <td>{{ ++$i }}</td>
                <td>
                    {{ $objLead['cb_account'] }}
                </td>
                <td>
                    {{ $objLead['affiliate_id'] }}
                </td>
                <td>
                    {{ $objLead['counts'] }}
                </td>
            </tr>
        @else
            <tr>
                <td>{{ ++$i }}</td>
                <td>
                    {{ $objLead->cb_account }}
                </td>
                <td>
                    {{ $objLead->affiliate_id }}
                </td>
                <td>
                    {{ $objLead->counts }}
                </td>
            </tr>
        @endif
        @endforeach
    @else
        <tr><td colspan="5" align="center">{{ trans('global.no_record_found') }}</td></tr>
    @endif
    </tbody>
</table>
<br />
<h2>{{ trans('global.sales_contest') }}</h2>
<table class="table table-striped table-bordered mt-4" id="myTable">
    <thead>
    <tr>
        <th>#</th>
        <th>{{ trans('cruds.cb_account.title_singular') }}</th>
        <th>{{ trans('cruds.affiliate.title_singular') }}</th>
        <th>{{ trans('cruds.contest.fields.sales_count') }}</th>
        <th>{{ trans('cruds.contest.fields.customer_amount') }}</th>
    </tr>
    </thead>
    <tbody>
    @if(count($transactions) > 0)
    @php
        $i = 0;
    @endphp
        @foreach ($transactions as $key => $objTrans)
        @if($contest_status == 'RUNNING')
        <tr>
            <td>{{ ++$i }}</td>
            <td>
                {{  strtoupper($objTrans['vendor']) }}
            </td>
            <td>
                {{  $objTrans['affiliate'] }}
            </td>
            <td>
                {{  $objTrans['sale_count'] }}
            </td>
            <td>
                {{  $objTrans['sale_amount'] }}
            </td>
        </tr>
        @else
        <tr>
            <td>{{ ++$i }}</td>
            <td>
                {{ strtoupper($objTrans->vendor) }}
            </td>
            <td>
                {{ $objTrans->affiliate }}
            </td>
            <td>
                {{ $objTrans->sales_count }}
            </td>
            <td>
                {{ $objTrans->customer_amount }}
            </td>
        </tr>
        @endif
        @endforeach
    @else
        <tr><td colspan="5" align="center">{{ trans('global.no_record_found') }}</td></tr>
    @endif
    </tbody>
</table>

@endsection

@push('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css') }}">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">
@endpush

@push('script')
    <!-- DataTables -->
    <script src="{{ asset('vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js') }}"></script>

    <script src="//cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="//cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable( {
                dom: 'Bfrtip',
                bInfo: false,
                bFilter: false,
                buttons: [
                    'pdf'
                ]
            });
            $('#myTable1').DataTable( {
                dom: 'Bfrtip',
                bFilter: false,
                buttons: [
                    'pdf'
                ]
            });
        });
    </script>
@endpush
