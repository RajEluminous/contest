@extends('admin.layouts.master')
@section('page_title', trans('cruds.cb_account.title'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ trans('cruds.cb_account.title') }}</li>
@endsection

@section('add_new_button')
    @if(auth()->user()->can('clickbank-account-create'))
    <a href="{{ route("admin.clickbank_accounts.create") }}" class="btn btn-primary float-right">
        {{ trans('global.create') }} {{ trans('cruds.cb_account.title_singular') }}
    </a>
    @endif
@endsection

@section('content')
    <div class="table-responsive">
        <table id="datatable-cb-accounts" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>{{ trans('cruds.cb_account.fields.name') }}</th>
                    <th>{{ trans('global.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @if(count($cb_accounts) > 0)
                    @foreach ($cb_accounts as $key => $cb_accounts)
                    <tr data-entry-id="{{ $cb_accounts->id }}">
                        <td>{{ $cb_accounts->name }}</td>
                        <td>
                            @if(auth()->user()->can('clickbank-account-edit'))
                            <a class="btn btn-info btn-sm" href="{{ route('admin.clickbank_accounts.edit',$cb_accounts->id) }}">{{ trans('global.edit') }}</a>
                            @endif

                            @if(auth()->user()->can('clickbank-account-delete'))
                            <form action="{{ route('admin.clickbank_accounts.destroy', $cb_accounts->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.are_you_sure_delete', ['name' => $cb_accounts->name]) }}');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <input type="submit" class="btn btn-danger btn-sm" value="{{ trans('global.delete') }}">
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr><td colspan="4" class="text-center">{{ trans('global.no_record_found') }}</td></tr>
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
