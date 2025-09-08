@extends('layouts.app')
@section('title', 'Data Key Storage')

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Reset form and errors when modal is closed
            $('#createKeyStorageModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').remove();
            });
        });
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
        <x-breadcrumb :items="[['text' => 'Key Storage']]" />

        <!-- Scrollable -->
        <div class="card">
            <div class="card-body d-block d-lg-flex border-bottom">
                <h5 class="text-start">Key Storage</h5>
                @can('create key-storage')
                    <button type="button" class="btn btn-primary ms-0 ms-lg-auto" data-bs-toggle="modal"
                        data-bs-target="#createKeyStorageModal">
                        Tambahkan Key Storage
                    </button>
                @endcan
            </div>
            <div class="card-datatable text-nowrap">
                <table class="datatable table table-stripped table-responsive">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Key</th>
                            <th>Value</th>
                            <th>Description</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($keyStorages as $keyStorage)
                            <tr>
                                <td>{{ $keyStorage->id }}</td>
                                <td>{{ $keyStorage->var_key }}</td>
                                <td>{{ $keyStorage->var_value }}</td>
                                <td>{{ $keyStorage->var_description }}</td>
                                <td>
                                    @can('edit key-storage')
                                        <a href="{{ route('key-storages.edit', $keyStorage->id) }}"
                                            class="btn btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="Edit Key Storage"><i class="fas fa-pen-to-square"></i></a>
                                    @endcan
                                    @can('delete key-storage')
                                        <x-delete :title="'Hapus Key Storage'" :route="route('key-storages.destroy', $keyStorage->id)" :size="'btn-sm'" />
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ Scrollable -->

        <!-- Create Key Storage Modal -->
        <div class="modal fade" id="createKeyStorageModal" tabindex="-1" aria-labelledby="createKeyStorageModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('key-storages.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="createKeyStorageModalLabel">Tambah Key Storage</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="var_key" class="form-label">Key <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('var_key') is-invalid @enderror"
                                    id="var_key" name="var_key" value="{{ old('var_key') }}" required>
                                @error('var_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="var_value" class="form-label">Value <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('var_value') is-invalid @enderror"
                                    id="var_value" name="var_value" value="{{ old('var_value') }}" required>
                                @error('var_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="var_description" class="form-label">Description</label>
                                <input type="text" class="form-control @error('var_description') is-invalid @enderror"
                                    id="var_description" name="var_description" value="{{ old('var_description') }}">
                                @error('var_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Create Key Storage Modal -->
    </div>
@endsection
