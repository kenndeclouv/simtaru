@extends('layouts.app')

@section('title', 'Tambahkan Role')

@section('page-script')

@endsection

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumb :items="[['text' => 'Roles', 'url' => route('roles.index')], ['text' => 'Tambah Role']]" />
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Buat Role Baru</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Role</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Permissions</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Fitur</th>
                                    @foreach ($actions as $action)
                                        <th class="text-center text-capitalize">{{ $action }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permissionsByGroup as $feature => $actionsInGroup)
                                    <tr>
                                        <td class="text-capitalize">{{ $feature }}</td>
                                        @foreach ($actions as $action)
                                            <td class="text-center">
                                                @if (in_array($action, $actionsInGroup))
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                                            value="{{ $action . ' ' . $feature }}">
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Simpan Role</button>
        </form>
    </div>
@endsection
