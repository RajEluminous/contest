@extends('admin.layouts.master')
@section('page_title', trans('cruds.role.title'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ trans('cruds.role.title') }}</li>
@endsection

@section('add_new_button')
    <a href="{{ route("admin.roles.create") }}" class="btn btn-primary float-right">
        {{ trans('global.create') }} {{ trans('cruds.role.title_singular') }}
    </a>
@endsection

@section('content')
    <div class="table-responsive">
        <table id="datatable-roles" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th class="d-none"></th>
                    <th>{{ trans('cruds.role.fields.name') }}</th>
                    <th>{{ trans('cruds.role.fields.permission') }}</th>
                    <th width="20%">{{ trans('global.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @if(count($roles) > 0)
                    @foreach ($roles as $key => $role)
                    <tr data-entry-id="{{ $role->id }}">
                            <td class="d-none">{{ $role->id ?? '' }}</td>
                            <td>{{ $role->name }}</td>

                            <td>
                                @if(!empty($role->permissions))
                                    @foreach($role->permissions as $v)
                                        <label class="label label-success">{{ $v->name }}</label><br/>
                                    @endforeach
                                @endif
                            </td>

                            <td>
                                <a class="btn btn-info btn-sm" href="{{ route('admin.roles.edit',$role->id) }}">{{ trans('global.edit') }}</a>

                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.are_you_sure_delete', ['name' => $role->name]) }}');" class="d-inline">
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
          $('#datatable-roles').DataTable({
            "lengthChange": false,
            "pageLength": 20,
            "order": [[ 0, "desc" ]],
            "columnDefs": [ {
                "targets": 3,
                "orderable": false
            }]
          });
        });
      </script>
@endpush
