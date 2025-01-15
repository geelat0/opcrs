<div class="modal fade" id="viewRoleModal" tabindex="-1" role="dialog" aria-labelledby="viewRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewRoleModalLabel">View role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="viewRoleForm">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <input type="hidden" class="form-control capitalize" name="edit" id="view_role_id" aria-describedby="" disabled>

                            <div class="form-group">
                                <label for="name" class="required">Name</label>
                                <input type="text" class="form-control capitalize" name="name" id="view_name" aria-describedby="" disabled>
                                <div class="invalid-feedback" id="nameError"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="status" class="required">Status</label>
                                <select id="view_status" class="form-select" name="status" disabled>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback" id="role_idError"></div>
                            </div>
                        </div>
                    </div>
                
                    
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
