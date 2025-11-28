@extends('components.main-layout')
@section('title', 'Hospital Settings')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-primary fw-bold mb-1"><i class="bi bi-gear-wide-connected me-2"></i> Hospital Settings</h4>
            <p class="text-muted small mb-0">Configure your hospital name, fees, validity, and more</p>
        </div>
        <button type="button" class="btn btn-success rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addSettingModal">
            <i class="bi bi-plus-circle"></i> Add New Setting
        </button>
    </div>

    <form action="{{ route('admin.settings') }}" method="POST" id="settingsForm">
        @csrf
        <div class="row g-4">
            <!-- General Settings -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-primary text-white rounded-top-4">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-hospital"></i> General Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Hospital Name <span class="text-danger">*</span></label>
                                <input type="text" name="hospital_name" value="{{ setting('hospital_name', 'Mana Medical Dispensary Hospital') }}" 
                                       class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tagline</label>
                                <input type="text" name="hospital_tagline" value="{{ setting('hospital_tagline') }}" 
                                       class="form-control rounded-3" placeholder="e.g. Healing with Care">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Address</label>
                                <textarea name="hospital_address" rows="2" class="form-control rounded-3">{{ setting('hospital_address') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Phone</label>
                                <input type="text" name="hospital_phone" value="{{ setting('hospital_phone') }}" class="form-control rounded-3">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="hospital_email" value="{{ setting('hospital_email') }}" class="form-control rounded-3">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Website</label>
                                <input type="text" name="hospital_website" value="{{ setting('hospital_website') }}" class="form-control rounded-3">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OPD & Registration -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-success text-white rounded-top-4">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-file-medical"></i> OPD & Registration Fees</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Registration Fee</label>
                                <div class="input-group">
                                    <span class="input-group-text">Tsh</span>
                                    <input type="number" name="registration_fee" value="{{ setting('registration_fee', 200) }}" 
                                           class="form-control rounded-end-3" min="0" step="10">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Card Validity (Months)</label>
                                <input type="number" name="card_validity_months" value="{{ setting('card_validity_months', 12) }}" 
                                       class="form-control rounded-3" min="1" max="60">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Reactivation Fee</label>
                                <div class="input-group">
                                    <span class="input-group-text">Tsh</span>
                                    <input type="number" name="reactivation_fee" value="{{ setting('reactivation_fee', 150) }}" 
                                           class="form-control rounded-end-3" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Billing & Finance -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-warning text-dark rounded-top-4">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-currency-rupee"></i> Billing Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">GST %</label>
                                <input type="number" name="gst_percentage" value="{{ setting('gst_percentage', 0) }}" 
                                       class="form-control rounded-3" min="0" max="100" step="0.01">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Discount Limit (%)</label>
                                <input type="number" name="max_discount_percent" value="{{ setting('max_discount_percent', 20) }}" 
                                       class="form-control rounded-3" min="0" max="100">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Round Off Bills</label>
                                <select name="bill_round_off" class="form-select rounded-3">
                                    <option value="1" {{ setting('bill_round_off', 1) ? 'selected' : '' }}>Yes (to nearest rupee)</option>
                                    <option value="0" {{ !setting('bill_round_off', 1) ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Settings Table (Add/Edit Any) -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-secondary text-white rounded-top-4">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-list-check"></i> Custom Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Key</th>
                                        <th>Value</th>
                                        <th>Type</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="customSettings">
                                    @foreach(\App\Models\Setting::whereNotIn('key', [
                                        'hospital_name','hospital_tagline','hospital_address','hospital_phone',
                                        'hospital_email','hospital_website','registration_fee','card_validity_months',
                                        'reactivation_fee','gst_percentage','max_discount_percent','bill_round_off'
                                    ])->orderBy('sort_order')->get() as $s)
                                    <tr>
                                        <td><code>{{ $s->key }}</code></td>
                                        <td>
                                            <input type="text" name="{{ $s->key }}" value="{{ $s->value }}" 
                                                   class="form-control form-control-sm rounded-3">
                                        </td>
                                        <td><small class="text-muted">{{ $s->type ?? 'string' }}</small></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" 
                                                    onclick="deleteSetting('{{ $s->key }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="col-lg-8 mt-4">
                <div class="text-end">
                    <button type="submit" class="btn btn-success rounded-pill px-5 shadow-lg">
                        <i class="bi bi-check2-all"></i> Save All Settings
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Add New Setting Modal -->
<div class="modal fade" id="addSettingModal">
    <div class="modal-dialog modal-lg">
        <form action="{{ url('admin/settings/custom') }}" method="POST">
            @csrf
            <div class="modal-content border-0 rounded-4">
                <div class="modal-header bg-primary text-white">
                    <h5><i class="bi bi-plus-circle"></i> Add Custom Setting</h5>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Key (unique)</label>
                            <input type="text" name="key" class="form-control rounded-3" placeholder="e.g. opd_start_time" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Value</label>
                            <input type="text" name="value" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Type</label>
                            <select name="type" class="form-select rounded-3">
                                <option value="string">String</option>
                                <option value="number">Number</option>
                                <option value="boolean">Yes/No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success rounded-pill">Add Setting</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function deleteSetting(key) {
    if(confirm('Delete setting: ' + key + '?')) {
        fetch('/admin/settings/delete/' + key, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => location.reload());
    }
}
</script>
@endsection