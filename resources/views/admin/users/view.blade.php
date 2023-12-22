@extends('admin.layouts.app')
@section('bread-title')
    Users
@endsection
@section('bread-content')
<li class="breadcrumb-item"><a href="{{ $dashboard }}">Dashboard</a></li>
<li class="breadcrumb-item active"> Users</li>
@endsection

@section('table-header')
<tr>
    <th>#</th>
    <th>Rendering engine</th>
    <th>Browser</th>
    <th>Action</th>
</tr>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            {{-- <div class="card-header">
                <h3 class="card-title">DataTable with default features</h3>
            </div> --}}
            <div class="card-body">
                <table id="tbl_user" class="table table-bordered table-striped">
                    <thead>
                        @yield('table-header')
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        @yield('table-header')
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>  

<!-- Modal Section -->
@include('admin.modals.user.modal')
<!-- End Section / -->

@endsection