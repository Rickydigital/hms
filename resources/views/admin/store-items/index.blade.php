@extends('components.main-layout')
@section('title', 'Store Items Master')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h4 class="text-primary fw-bold"><i class="bi bi-box-seam me-2"></i> Store Items (Gloves, Syringe, etc.)</h4>
        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addItemModal">Add Item</button>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Code</th>
                        <th>Item Name</th>
                        <th>Unit</th>
                        <th class="text-end">Price</th>
                        <th>Min Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td><span class="badge bg-dark">{{ $item->item_code }}</span></td>
                        <td><strong>{{ $item->item_name }}</strong></td>
                        <td>{{ $item->unit }}</td>
                        <td class="text-end fw-bold">Tsh{{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->minimum_stock }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info rounded-pill" data-bs-toggle="modal" data-bs-target="#editItem{{ $item->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editItem{{ $item->id }}">
                        <div class="modal-dialog">
                            <form action="{{ route('store-items.update', $item) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-content rounded-4 border-0">
                                    <div class="modal-header bg-info text-white"><h5>Edit Item</h5></div>
                                    <div class="modal-body">
                                        <input type="text" name="item_name" value="{{ $item->item_name }}" class="form-control mb-3" required>
                                        <input type="text" name="unit" value="{{ $item->unit }}" class="form-control mb-3">
                                        <input type="number" step="0.01" name="price" value="{{ $item->price }}" class="form-control mb-3" required>
                                        <input type="number" name="minimum_stock" value="{{ $item->minimum_stock }}" class="form-control" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success rounded-pill">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">No items added</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addItemModal">
        <div class="modal-dialog">
            <form action="{{ route('store-items.store') }}" method="POST">
                @csrf
                <div class="modal-content rounded-4 border-0">
                    <div class="modal-header bg-primary text-white"><h5>Add New Item</h5></div>
                    <div class="modal-body">
                        <input type="text" name="item_name" class="form-control mb-3" placeholder="Item Name" required>
                        <input type="text" name="unit" class="form-control mb-3" placeholder="Unit (e.g. Piece, Box)" value="Piece">
                        <input type="number" step="0.01" name="price" class="form-control mb-3" placeholder="Price" required>
                        <input type="number" name="minimum_stock" class="form-control" placeholder="Minimum Stock" value="50">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success rounded-pill">Save Item</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection