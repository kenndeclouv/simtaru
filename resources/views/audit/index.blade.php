@extends('layouts.app')
@section('title', 'Audit Trail')

@section('page-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.19/i18n/Indonesian.json"
                }
            });
        });
    </script>
@endsection

@section('page-style')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-6">
            <h5 class="card-header border-bottom">Audit Trail</h5>
            <div class="card-body">
                <div class="table-responsive table-responsive ">
                    <table class="table table-bordered table-responsive-sm table-responsive-md table-responsive-xl w-100"
                        id="dataTable">
                        <thead>
                            <tr>
                                <th>Aksi</th>
                                <th>Oleh</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($activities as $activity)
                                <tr>
                                    <td>{{ $activity->description }}</td>
                                    <td>{{ $activity->causer ? $activity->causer->name : 'Sistem' }}</td>
                                    <td>{{ $activity->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
