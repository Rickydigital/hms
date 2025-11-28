@extends('components.main-layout')
@section('title', 'Wards Management')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h4 class="text-primary fw-bold"><i class="bi bi-building me-2"></i> Wards & Beds</h4>
        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addWardModal">Add Ward</button>
    </div>

    <div class="row g-4">
        @forelse($wards as $ward)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">{{ $ward->ward_name }}</h5>
                    <p class="text-muted small">{{ $ward->ward_code }}</p>
                    <h3 class="text-success fw-bold">Tsh{{ number_format($ward->price_per_day) }}/day</h3>
                    <div class="mt-3">
                        <span class="badge bg-info fs-6">Total Beds: {{ $ward->total_beds }}</span>
                        <span class="badge bg-{{ $ward->available_beds > 0 ? 'success' : 'danger' }} fs-6 ms-2">
                            Available: {{ $ward->available_beds }}
                        </span>
                    </div>
                    <button class="btn btn-sm btn-outline-info rounded-pill mt-3" data-bs-toggle="modal" data-bs-target="#editWard{{ $ward->id }}">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editWard{{ $ward->id }}">
                <div class="modal-dialog">
                    <form action="{{ route('wards.update', $ward) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-content rounded-4 border-0">
                            <div class="modal-header bg-info text-white"><h5>Edit Ward</h5></div>
                            <div class="modal-body">
                                <input type="text" name="ward_name" value="{{ $ward->ward_name }}" class="form-control mb-3" required>
                                <input type="number" name="price_per_day" value="{{ $ward->price_per_day }}" class="form-control mb-3" required>
                                <input type="number" name="total_beds" value="{{ $ward->total_beds }}" class="form-control mb-3" required>
                                <textarea name="facilities" rows="3" class="form-control">{{ $ward->facilities }}</textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success rounded-pill">Update Ward</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-building display-4"></i><br>No wards added yet
        </div>
        @endforelse
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addWardModal">
        <div class="modal-dialog">
            <form action="{{ route('wards.store') }}" method="POST">
                @csrf
                <div class="modal-content rounded-4 border-0">
                    <div class="modal-header bg-primary text-white"><h5>Add New Ward</h5></div>
                    <div class="modal-body">
                        <input type="text" name="ward_name" class="form-control mb-3" placeholder="Ward Name" required>
                        <input type="number" name="price_per_day" class="form-control mb-3" placeholder="Price per Day" required>
                        <input type="number" name="total_beds" class="form-control mb-3" placeholder="Total Beds" required>
                        <textarea name="facilities" rows="3" class="form-control" placeholder="AC, TV, etc."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success rounded-pill">Save Ward</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection