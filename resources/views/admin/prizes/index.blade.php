@extends('admin.layouts.master')
@section('page_title', trans('cruds.prize.title'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ trans('cruds.prize.title') }}</li>
@endsection

@section('add_new_button')
    @if(auth()->user()->can('prize-create'))
    <a href="{{ route("admin.prizes.create") }}" class="btn btn-primary float-right">
        {{ trans('global.create') }} {{ trans('cruds.prize.title_singular') }}
    </a>
    @endif
@endsection

@section('content')
    <div class="table-responsive">
        <table id="datatable-cb-accounts" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>{{ trans('cruds.cb_account.fields.name') }}</th>
                    <th>{{ trans('cruds.cb_account.title_singular') }}</th>
                    <th>{{ trans('global.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @if(count($prizes) > 0)
                    @foreach ($prizes as $key => $prize)
                    <tr data-entry-id="{{ $prize->id }}">
                        <td>{{ $prize->name }}</td>
                        <td> @php
                                $contest_name = App\Http\Controllers\Admin\PrizeController::getContestName($prize->contest_id);
                            @endphp
                            {{ $contest_name }}
                        </td>
                        <td>
                            @if(auth()->user()->can('prize-edit'))
                            <a class="btn btn-info btn-sm" href="{{ route('admin.prizes.edit',$prize->id) }}">{{ trans('global.edit') }}</a>
                            @endif

                            @if(auth()->user()->can('prize-delete'))
                            <form action="{{ route('admin.prizes.destroy', $prize->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.are_you_sure_delete', ['name' => $prize->name]) }}');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <input type="submit" class="btn btn-danger btn-sm" value="{{ trans('global.delete') }}">
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr><td colspan="3" class="text-center">{{ trans('global.no_record_found') }}</td></tr>
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

    <script>
        $(function () {
          $('#datatable-cb-accounts').DataTable({
            "lengthChange": false,
            "pageLength": 20,
            "order": [[ 0, "desc" ]],
            "columnDefs": [ {
                "targets": 1,
                "orderable": false
            }]
          });
        });
      </script>
@endpush
