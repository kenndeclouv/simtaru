@extends('layouts.app')

@section('title', 'Tambahkan Role')

@section('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            $('#check-all-master').on('click', function() {
                $('table tbody .permission-check').prop('checked', $(this).prop('checked'));
            });

            $('.check-all-column').on('click', function() {
                const columnIndex = $(this).closest('th').index();
                $('table tbody tr').each(function() {
                    $(this).find('td').eq(columnIndex).find('.permission-check').prop('checked', $(
                        this).closest('th').find('.check-all-column').prop('checked'));
                });
            });

            $('.check-all-row').on('click', function() {
                $(this).closest('tr').find('.permission-check').prop('checked', $(this).prop('checked'));
            });
        });
    </script>
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
                        <input type="text" name="name" id="name" class="form-control" required
                            placeholder="Contoh: Staff Lapangan" value="{{ old('name') }}">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Permissions</h5>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="check-all-master">
                        <label class="form-check-label" for="check-all-master">
                            Pilih Semua
                        </label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th class="align-middle" style="min-width: 160px;">
                                        Fitur
                                    </th>
                                    {{-- Kolom header-nya sekarang isinya 'view', 'view any', 'create', dll --}}
                                    @foreach ($actions as $action)
                                        <th class="text-center text-capitalize align-middle" style="min-width: 120px;">
                                            <div>{{ $action }}</div>
                                            {{-- Bonus: Tombol check all per kolom --}}
                                            <div class="form-check d-flex justify-content-center mt-1">
                                                <input class="form-check-input check-all-column" type="checkbox"
                                                    title="Pilih semua '{{ $action }}'">
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                {{-- $permissionsByGroup sekarang [ 'permohonan' => [ 'view' => 'view permohonan', 'view any' => 'view any permohonan' ] ] --}}
                                @foreach ($permissionsByGroup as $feature => $permissionsInGroup)
                                    <tr>
                                        <td class="text-capitalize align-middle" style="vertical-align: middle;">
                                            <div class="d-flex align-items-center ">
                                                <div class="form-check">
                                                    <input class="form-check-input check-all-row" type="checkbox"
                                                        title="Pilih semua untuk '{{ $feature }}'">
                                                </div>
                                                <span>{{ $feature }}</span>
                                            </div>
                                        </td>

                                        {{-- Loop per kolom aksi --}}
                                        @foreach ($actions as $action)
                                            <td class="text-center align-middle">
                                                @php
                                                    $permissionName = $permissionsInGroup[$action] ?? null;
                                                @endphp

                                                @if ($permissionName)
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input permission-check" type="checkbox"
                                                            name="permissions[]" value="{{ $permissionName }}">
                                                    </div>
                                                @else
                                                    {{-- Kalo permission-nya nggak ada (e.g., 'approve' di 'template'), kasih strip --}}
                                                    <span class="text-muted">-</span>
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
