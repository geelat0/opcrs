<div class="modal fade" id="editCatModal" tabindex="-1" role="dialog" aria-labelledby="editCatModalLabel" aria-hidden="true" hidden.bs.modal>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editCatForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editCatModalLabel">Update Upload Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <input type="hidden" name="id" id="edit_category_id">
                            <div class="form-group">
                                <label for="edit_category_name" class="required capitalize">Name</label>
                                <input type="text" class="form-control" id="edit_category_name" name="category_name">
                                <div class="invalid-feedback" id="category_nameError"></div>
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