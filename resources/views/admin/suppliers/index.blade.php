@extends('components.main-layout')
@section('title', 'Suppliers Management')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h4 class="text-primary fw-bold"><i class="bi bi-truck me-2"></i> Suppliers</h4>
        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addSupplierModal">Add Supplier</button>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Code</th>
                        <th>Company / Name</th>
                        <th>Contact</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $s)
                    <tr>
                        <td><span class="badge bg-dark">{{ $s->supplier_code }}</span></td>
                        <td><strong>{{ $s->company_name ?: $s->name }}</strong></td>
                        <td>{{ $s->name }}</td>
                        <td>{{ $s->phone }}</td>
                        <td><span class="badge bg-{{ $s->is_active ? 'success' : 'secondary' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-info rounded-pill" data-bs-toggle="modal" data-bs-target="#editSupp{{ $s->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editSupp{{ $s->id }}">
                        <div class="modal-dialog modal-lg">
                            <form action="{{ route('suppliers.update', $s) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-content rounded-4 border-0">
                                    <div class="modal-header bg-info text-white"><h5>Edit Supplier</h5></div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6"><input type="text" name="name" value="{{ $s->name }}" class="form-control" required></div>
                                            <div class="col-md-6"><input type="text" name="company_name" value="{{ $s->company_name }}" class="form-control"></div>
                                            <div class="col-md-6"><input type="text" name="phone" value="{{ $s->phone }}" class="form-control" required></div>
                                            <div class="col-md-6"><input type="email" name="email" value="{{ $s->email }}" class="form-control"></div>
                                            <div class="col-12"><textarea name="address" rows="2" class="form-control">{{ $s->address }}</textarea></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success rounded-pill">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">No suppliers added</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addSupplierModal">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-content rounded-4 border-0">
                    <div class="modal-header bg-primary text-white"><h5>Add New Supplier</h5></div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6"><input type="text" name="name" class="form-control" placeholder="Contact Person" required></div>
                            <div class="col-md-6"><input type="text" name="company_name" class="form-control" placeholder="Company Name"></div>
                            <div class="col-md-6"><input type="text" name="phone" class="form-control" placeholder="Phone" required></div>
                            <div class="col-md-6"><input type="email" name="email" class="form-control" placeholder="Email"></div>
                            <div class="col-12"><textarea name="address" rows="2" class="form-control" placeholder="Address"></textarea></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success rounded-pill">Save Supplier</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection