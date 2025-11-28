<div class="modal fade" id="showUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header bg-info text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-circle me-2"></i> Staff Details
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="avatar-lg mx-auto mb-4 bg-soft-info rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-fill text-info" style="font-size: 3rem;"></i>
                </div>
                <h4 id="show-name" class="fw-bold"></h4>
                <p class="text-muted mb-3">
                    <i class="bi bi-person-badge"></i> <span id="show-code"></span>
                </p>

                <div class="row g-4 text-start">
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                            <i class="bi bi-envelope text-primary fs-4"></i>
                            <div>
                                <small class="text-muted">Email</small>
                                <p id="show-email" class="mb-0 fw-medium"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                            <i class="bi bi-telephone text-success fs-4"></i>
                            <div>
                                <small class="text-muted">Phone</small>
                                <p id="show-phone" class="mb-0 fw-medium"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                            <i class="bi bi-building text-warning fs-4"></i>
                            <div>
                                <small class="text-muted">Department</small>
                                <p id="show-department" class="mb-0 fw-medium"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                            <i class="bi bi-shield-check text-danger fs-4"></i>
                            <div>
                                <small class="text-muted">Role</small>
                                <p id="show-role" class="mb-0 fw-medium"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                            <i class="bi bi-activity text-success fs-4"></i>
                            <div>
                                <small class="text-muted">Status</small>
                                <p id="show-status" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>