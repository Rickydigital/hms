<div class="modal fade" id="assignLabModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="assignLabForm" class="modal-content border-0 rounded-4">
            @csrf
            <input type="hidden" name="patient_id" id="assign-lab-patient-id">

            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-beaker me-2"></i>
                    Assign Lab - <span id="assign-lab-patient-name"></span>
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="alert alert-info rounded-3">
                    Patient ID: <strong id="assign-lab-patient-code"></strong>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Lab Tests <span class="text-danger">*</span></label>
                    <select name="lab_tests[]" id="assign-lab-tests" class="form-select" multiple="multiple" style="width:100%;">
                        @foreach(\App\Models\LabTestMaster::active()->orderBy('test_name')->get() as $test)
                            <option value="{{ $test->id }}">
                                {{ $test->test_name }} — {{ number_format($test->price) }} Tsh
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Search and select one or more lab tests</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Lab Instruction</label>
                    <textarea name="lab_instruction" rows="2" class="form-control rounded-3"
                              placeholder="Special instruction for lab..."></textarea>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-bold">Notes</label>
                    <textarea name="notes" rows="2" class="form-control rounded-3"
                              placeholder="Optional note..."></textarea>
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    Assign to Lab
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAssignLabModal(id, name, code) {
    document.getElementById('assign-lab-patient-id').value = id;
    document.getElementById('assign-lab-patient-name').textContent = name;
    document.getElementById('assign-lab-patient-code').textContent = code;

    const form = document.getElementById('assignLabForm');
    form.reset();

    const $select = $('#assign-lab-tests');
    $select.val(null).trigger('change');
}

document.addEventListener('DOMContentLoaded', function () {
    const $assignLabSelect = $('#assign-lab-tests');

    if ($assignLabSelect.length) {
        $assignLabSelect.select2({
            placeholder: 'Search and select lab tests...',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#assignLabModal')
        });
    }

    const form = document.getElementById('assignLabForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const patientId = document.getElementById('assign-lab-patient-id').value;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Assigning...';

        const formData = new FormData(form);

        fetch(`/patients/${patientId}/rch-direct-lab`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) throw data;
            return data;
        })
        .then(data => {
            alert(data.message || 'Lab assigned successfully!');
            const modalEl = document.getElementById('assignLabModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            location.reload();
        })
        .catch(error => {
            console.error('Assign lab error:', error);

            if (error.errors) {
                const firstKey = Object.keys(error.errors)[0];
                alert(error.errors[firstKey][0] || 'Validation failed.');
            } else {
                alert(error.message || 'Failed to assign lab.');
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});
</script>