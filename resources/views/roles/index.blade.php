@extends('layouts.app')
@section('title', 'Manajemen Role')

@section('page-script')
    <script>
        $('.select2').select2();

        $('.datatable').DataTable({
            scrollY: "300px",
            scrollX: !0,
            layout: {
                topStart: {
                    rowClass: "row mx-3 my-0 justify-content-between",
                    features: [{
                        pageLength: {
                            menu: [7, 10, 25, 50, 100],
                            text: "Show_MENU_entries"
                        }
                    }]
                },
                topEnd: {
                    search: {
                        placeholder: ""
                    }
                },
                bottomStart: {
                    rowClass: "row mx-3 justify-content-between",
                    features: ["info"]
                },
                bottomEnd: {
                    paging: {
                        firstLast: !1
                    }
                }
            },
        });
    </script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumb :items="[['text' => 'Roles']]" />

        <!-- Scrollable -->
        <div class="card">
            <div class="card-body d-block d-lg-flex border-bottom">
                <h5 class="text-start">Manajemen Role</h5>
                <a href="{{ route('roles.create') }}" class="btn btn-primary ms-0 ms-lg-auto">Tambahkan Role</a>
            </div>
            <div class="card-datatable text-nowrap">
                <table class="datatable table table-bordered table-striped table-responsive">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Role</th>
                            <th>Permission</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role->name }}</td>
                                <td>
                                    @if ($role->permissions->count())
                                        <ul class="mb-0">
                                            @foreach ($role->permissions as $permission)
                                                <li>{{ $permission->name }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    {{-- <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus role ini?')">Hapus</button>
                                    </form> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ Scrollable -->
    </div>
@endsection
