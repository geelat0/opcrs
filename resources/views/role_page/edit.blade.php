<div class="modal fade" id="editRoleModal" tabindex="-1" role="dialog" aria-labelledby="editRoleModalLabel" aria-hidden="true" hidden.bs.modal>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editRoleForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">Update Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <input type="hidden" name="id" id="edit_role_id">
                            <div class="form-group">
                                <label for="name" class="required capitalize">First Name</label>
                                <input type="text" class="form-control" id="edit_name" name="name">
                                <div class="invalid-feedback" id="nameError"></div>
                            </div>
                            <div class="form-group">
                                <label for="edit_status" class="required">Status</label>
                                <select class="form-select capitalize" id="edit_status" name="status">
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
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>