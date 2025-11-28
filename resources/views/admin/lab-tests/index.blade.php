@extends('components.main-layout')
@section('title', 'Lab Tests Master')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-primary fw-bold"><i class="bi bi-vial me-2"></i> Lab Tests Master</h4>
        <button class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addTestModal">
            <i class="bi bi-plus-lg"></i> Add New Test
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Code</th>
                            <th>Test Name</th>
                            <th>Description</th>
                            <th class="text-end">Price</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tests as $test)
                        <tr>
                            <td><span class="badge bg-dark">{{ $test->test_code }}</span></td>
                            <td><strong>{{ $test->test_name }}</strong></td>
                            <td>{{ $test->description ? Str::limit($test->description, 60) : '-' }}</td>
                            <td class="text-end fw-bold text-success">Tsh{{ number_format($test->price, 2) }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{ $test->is_active ? 'checked' : '' }} disabled>
                                </div>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info rounded-pill" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editTest{{ $test->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editTest{{ $test->id }}">
                            <div class="modal-dialog modal-lg">
                                <form action="{{ route('lab-tests.update', $test) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-content border-0 rounded-4">
                                        <div class="modal-header bg-info text-white">
                                            <h5>Edit Lab Test</h5>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <label class="form-label fw-bold">Test Name</label>
                                                    <input type="text" name="test_name" value="{{ $test->test_name }}" class="form-control rounded-3" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Price</label>
                                                    <input type="number" step="0.01" name="price" value="{{ $test->price }}" class="form-control rounded-3" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Description</label>
                                                    <textarea name="description" rows="3" class="form-control rounded-3">{{ $test->description }}</textarea>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $test->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label">Active / Visible</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                                <i class="bi bi-check2-all"></i> Update Test
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-vial display-4"></i><br>
                                No lab tests added yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addTestModal">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('lab-tests.store') }}" method="POST">
                @csrf
               Same modal as edit, but empty
                <div class="modal-content border-0 rounded-4">
                    <div class="modal-header bg-primary text-white">
                        <h5><i class="bi bi-plus-circle"></i> Add New Lab Test</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <input type="text" name="test_name" class="form-control rounded-3" placeholder="Test Name" required>
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
                        <button type="submit" class="btn btn-success rounded-pill">Save Test</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection