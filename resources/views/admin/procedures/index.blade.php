@extends('components.main-layout')
@section('title', 'Procedures Master')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-primary fw-bold"><i class="bi bi-activity me-2"></i> Procedures Master</h4>
        <button class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addProcedureModal">
            <i class="bi bi-plus-lg"></i> Add New Procedure
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Code</th>
                            <th>Procedure Name</th>
                            <th>Description</th>
                            <th class="text-end">Price</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($procedures as $procedure)
                        <tr>
                            <td><span class="badge bg-dark">{{ $procedure->procedure_code }}</span></td>
                            <td><strong>{{ $procedure->procedure_name }}</strong></td>
                            <td>{{ $procedure->description ? Str::limit($procedure->description, 60) : '-' }}</td>
                            <td class="text-end fw-bold text-success">Tsh{{ number_format($procedure->price, 2) }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{ $procedure->is_active ? 'checked' : '' }} disabled>
                                </div>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info rounded-pill" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editProcedure{{ $procedure->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editProcedure{{ $procedure->id }}">
                            <div class="modal-dialog modal-lg">
                                <form action="{{ route('admin.procedures.update', $procedure) }}" method="POST">
                                    @csrf @method('POST')
                                    <div class="modal-content border-0 rounded-4">
                                        <div class="modal-header bg-info text-white">
                                            <h5>Edit Procedure</h5>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <label class="form-label fw-bold">Procedure Name</label>
                                                    <input type="text" name="procedure_name" value="{{ $procedure->procedure_name }}" class="form-control rounded-3" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Price</label>
                                                    <input type="number" step="0.01" name="price" value="{{ $procedure->price }}" class="form-control rounded-3" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Description</label>
                                                    <textarea name="description" rows="3" class="form-control rounded-3">{{ $procedure->description }}</textarea>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $procedure->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label">Active / Visible</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                                <i class="bi bi-check2-all"></i> Update Procedure
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-activity display-4"></i><br>
                                No procedures added yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addProcedureModal">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.procedures.store') }}" method="POST">
                @csrf
                <div class="modal-content border-0 rounded-4">
                    <div class="modal-header bg-primary text-white">
                        <h5><i class="bi bi-plus-circle"></i> Add New Procedure</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <input type="text" name="procedure_name" class="form-control rounded-3" placeholder="Procedure Name" required>
                            </div>
                            <div class="col-md-4">
                                <input type="number" step="0.01" name="price" class="form-control rounded-3" placeholder="Price" required>
                            </div>
                            <div class="col-12">
                                <textarea name="description" rows="3" class="form-control rounded-3" placeholder="Description (optional)"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success rounded-pill">Save Procedure</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection