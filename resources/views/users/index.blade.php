@extends('layouts.app')
@section('title', 'Manajemen User')

@section('page-script')
    <script>
        $('.select2').select2();

        $('.datatable').DataTable({
            scrollY: "300px",
            scrollX: true,
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
                        firstLast: false
                    }
                }
            },
        });
    </script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumb :items="[['text' => 'Users']]" />

        <!-- Scrollable -->
        <div class="card">
            <div class="card-body d-block d-lg-flex border-bottom">
                <h5 class="text-start">Manajemen User</h5>
                @can('create users')
                    <a href="{{ route('users.create') }}" class="btn btn-primary ms-0 ms-lg-auto">Tambahkan User</a>
                @endcan
            </div>
            <div class="card-datatable text-nowrap">
                <table class="datatable table table-bordered table-striped table-responsive">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            {{-- <th>Foto</th> --}}
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if ($user->roles->count())
                                        <ul class="mb-0">
                                            @foreach ($user->roles as $role)
                                                <li>{{ $role->name }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                {{-- <td>
                                    <img src="{{ $user->photo }}" alt="Foto" width="40" height="40" class="rounded-circle">
                                </td> --}}
                                <td>
                                    @can('edit users')
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning"
                                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i></a>
                                    @endcan
                                    @can('delete users')
                                        <x-delete :route="route('users.destroy', $user->id)" :title="'Hapus User'" />
                                    @endcan
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
