@extends('admin.layouts.master')
@section('page_title', trans('cruds.user.title'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ trans('cruds.user.title') }}</li>
@endsection

@section('add_new_button')
    <a href="{{ route("admin.users.create") }}" class="btn btn-primary float-right">
        {{ trans('global.create') }} {{ trans('cruds.user.title_singular') }}
    </a>
@endsection

@section('content')
    <div class="table-responsive">
        <table id="datatable-users" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th class="d-none"></th>
                    <th>{{ trans('cruds.user.fields.name') }}</th>
                    <th>{{ trans('cruds.user.fields.email') }}</th>
                    <th>{{ trans('cruds.user.fields.role') }}</th>
                    <th>{{ trans('cruds.user.fields.status') }}</th>
                    <th>{{ trans('global.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @if(count($users) > 0)
                    @foreach ($users as $key => $user)
                    <tr data-entry-id="{{ $user->id }}">
                            <td class="d-none">{{ $user->id ?? '' }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->roles->pluck('name')[0]  }}</td>
                            <td><span class="badge {{ $user->status == "ACTIVE" ? 'badge-success' : 'badge-secondary' }}">{{ $user->status }}</span></td>

                            <td>
                                <a class="btn btn-info btn-sm" href="{{ route('admin.users.edit',$user->id) }}">{{ trans('global.edit') }}</a>

                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.are_you_sure_delete', ['name' => $user->name]) }}');" class="d-inline">
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
          $('#datatable-users').DataTable({
            "lengthChange": false,
            "pageLength": 20,
            "order": [[ 0, "desc" ]],
            "columnDefs": [ {
                "targets": 5,
                "orderable": false
            }]
          });
        });
      </script>
@endpush
