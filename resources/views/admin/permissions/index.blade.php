@extends('admin.layouts.master')
@section('page_title', trans('cruds.permission.title'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ trans('cruds.permission.title') }}</li>
@endsection

@section('add_new_button')
    <a href="{{ route("admin.permissions.create") }}" class="btn btn-primary float-right">
        {{ trans('global.create') }} {{ trans('cruds.permission.title_singular') }}
    </a>
@endsection

@section('content')
    <div class="table-responsive">
        <table id="datatable-permissions" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th class="d-none"></th>
                    <th>{{ trans('cruds.permission.fields.name') }}</th>
                    <th>{{ trans('global.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @if(count($permissions) > 0)
                    @foreach ($permissions as $key => $permission)
                    <tr data-entry-id="{{ $permission->id }}">
                            <td class="d-none">{{ $permission->id ?? '' }}</td>
                            <td>{{ $permission->name }}</td>

                            <td>
                                <a class="btn btn-info btn-sm" href="{{ route('admin.permissions.edit',$permission->id) }}">{{ trans('global.edit') }}</a>

                                <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.are_you_sure_delete', ['name' => $permission->name]) }}');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="submit" class="btn btn-danger btn-sm" value="{{ trans('global.delete') }}">
                                </form>
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
          $('#datatable-permissions').DataTable({
            "lengthChange": false,
            "pageLength": 20,
            "order": [[ 0, "desc" ]],
            "columnDefs": [ {
                "targets": 2,
                "orderable": false
            }]
          });
        });
      </script>
@endpush
