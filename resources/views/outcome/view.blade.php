<div class="modal fade" id="viewOrgModal" tabindex="-1" role="dialog" aria-labelledby="viewOrgModalLabel" aria-hidden="true" hidden.bs.modal>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="viewOrgForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="viewOrgModalLabel">View Organization Outcome</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <input type="hidden" name="id" id="view_role_id">
                            <div class="form-group mb-3">
                                <label for="view_order" class="required">Order</label>
                                <input type="number" class="form-control capitalize" name="order[]" id="view_order" aria-describedby="" disabled>
                            </div>
                            <div class="form-group mb-3">
                                <label for="view_name" class="required capitalize">Organization Outcome</label>
                                <input type="text" class="form-control" id="view_name" name="organizational_outcome" disabled>
                                <div class="invalid-feedback" id="organizational_outcomeError"></div>
                            </div>

                            <div class="form-group  mb-3">
                                <label for="view_category" class="required">Category</label>
                                <select class="form-control capitalize" name="category" id="view_category" disabled>
                                    <option value="Core">Core</option>
                                    <option value="Non Core">Non Core</option>
                                </select>
                                <div class="invalid-feedback" id="view_category"></div>
                            </div>

                            <div class="form-group">
                                <label for="view_status" class="required">Status</label>
                                <select class="form-select capitalize" id="view_status" name="status" disabled>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="invalid-feedback" id="statusError"></div>
                        </div>


                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    {{-- <button type="submit" class="btn btn-primary">Save changes</button> --}}
                </div>
            </form>
        </div>
    </div>
</div>
