@extends('components.main-layout')
@section('title', 'Medicines Master')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-primary fw-bold"><i class="bi bi-capsule me-2"></i> Medicines Master</h4>
        <button class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
            <i class="bi bi-plus-lg"></i> Add Medicine
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Code</th>
                            <th>Medicine Name</th>
                            <th>Generic / Packing</th>
                            <th class="text-end">Price</th>
                            <th>Stock Alert</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($medicines as $med)
                        <tr>
                            <td><span class="badge bg-dark">{{ $med->medicine_code }}</span></td>
                            <td><strong>{{ $med->medicine_name }}</strong></td>
                            <td class="small text-muted">{{ $med->generic_name }} • {{ $med->packing }}</td>
                            <td class="text-end fw-bold text-success">Tsh{{ number_format($med->price, 2) }}</td>
                            <td>
                                @if($med->current_stock <= $med->minimum_stock)
                                    <span class="badge bg-danger">Low Stock</span>
                                @else
                                    <span class="badge bg-success">{{ $med->current_stock ?? 0 }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{ $med->is_active ? 'checked' : '' }} disabled>
                                </div>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info rounded-pill" data-bs-toggle="modal" data-bs-target="#editMed{{ $med->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editMed{{ $med->id }}">
                            <div class="modal-dialog modal-xl">
                                <form action="{{ route('medicines.update', $med) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-content border-0 rounded-4">
                                        <div class="modal-header bg-info text-white">
                                            <h5>Edit Medicine</h5>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Medicine Name</label>
                                                    <input type="text" name="medicine_name" value="{{ $med->medicine_name }}" class="form-control rounded-3" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Generic Name</label>
                                                    <input type="text" name="generic_name" value="{{ $med->generic_name }}" class="form-control rounded-3">
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Packing</label>
                                                    <input type="text" name="packing" value="{{ $med->packing }}" class="form-control rounded-3" placeholder="10x10, 1ml vial">
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Price</label>
                                                    <input type="number" step="0.01" name="price" value="{{ $med->price }}" class="form-control rounded-3" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Purchase Price</label>
                                                    <input type="number" step="0.01" name="purchase_price" value="{{ $med->purchase_price }}" class="form-control rounded-3">
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $med->is_active ? 'checked' : '' }}>
                                                        <label>Active</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success rounded-pill">Update Medicine</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">No medicines added</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card-footer bg-transparent border-top-0 py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="text-muted small">
            Showing {{ $medicines->firstItem() ?? 0 }} to {{ $medicines->lastItem() ?? 0 }}
            of {{ $medicines->total() }} entries
        </div>

        {{ $medicines->links() }}
    </div>
</div>
</div>

    <!-- Add Modal -->
    <div class="modal fade" id="addMedicineModal">
        <div class="modal-dialog modal-xl">
            <form action="{{ route('medicines.store') }}" method="POST">
                @csrf
                <div class="modal-content border-0 rounded-4">
                    <div class="modal-header bg-primary text-white">
                        <h5>Add New Medicine</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6"><input type="text" name="medicine_name" class="form-control rounded-3" placeholder="Medicine Name" required></div>
                            <div class="col-md-6"><input type="text" name="generic_name" class="form-control rounded-3" placeholder="Generic Name"></div>
                            <div class="col-md-4"><input type="text" name="packing" class="form-control rounded-3" placeholder="Packing"></div>
                            <div class="col-md-4"><input type="number" step="0.01" name="price" class="form-control rounded-3" placeholder="Selling Price" required></div>
                            <div class="col-md-4"><input type="number" step="0.01" name="purchase_price" class="form-control rounded-3" placeholder="Purchase Price"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success rounded-pill">Save Medicine</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection