@extends('admin.layouts.master')
@section('page_title', trans('cruds.team.title'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ trans('cruds.team.title') }}</li>
@endsection

@section('add_new_button')
    @if(auth()->user()->can('team-create'))
    <a href="{{ route("admin.teams.create") }}" class="btn btn-primary float-right">
        {{ trans('global.create') }} {{ trans('cruds.team.title_singular') }}
    </a>
    @endif
@endsection

@section('content')
    <div class="table-responsive">
        <table id="datatable-teams" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th class="d-none"></th>
                    <th>{{ trans('cruds.team.fields.name') }}</th>
                    <th>{{ trans('cruds.team.fields.affiliate') }}</th>
                    <th>{{ trans('global.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @if(count($teams) > 0)
                    @foreach ($teams as $key => $team)
                    <tr data-entry-id="{{ $team->id }}">
                            <td class="d-none">
                                {{ $team->id ?? '' }}
                            </td>
                            <td>{{ $team->name }}</td>
                            <td>
                                @php $affNames = App\Http\Controllers\Admin\TeamController::getTeamAffiliates($team->id) @endphp
                                @if(! empty($affNames))
                                    @foreach($affNames as $v)
                                        <span class="badge badge-secondary">{{ $v }}</span>
                                    @endforeach
                                @endif
                            </td>

                            <td>
                                @if(auth()->user()->can('team-edit'))
                                <a class="btn btn-info btn-sm" href="{{ route('admin.teams.edit',$team->id) }}">{{ trans('global.edit') }}</a>
                                @endif

                                @if(auth()->user()->can('team-delete'))
                                <form action="{{ route('admin.teams.destroy', $team->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.are_you_sure_delete', ['name' => $team->name]) }}');" class="d-inline">
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
          $('#datatable-teams').DataTable({
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
