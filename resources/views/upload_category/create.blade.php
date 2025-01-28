<div class="modal fade" id="createCatModal" tabindex="-1" role="dialog" aria-labelledby="createCatModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCatModalLabel">Create New Upload Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createCatForm">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="name" class="required">Name</label>
                                <input type="text" class="form-control capitalize" name="category_name" id="category_name" aria-describedby="">
                                <div class="invalid-feedback" id="category_nameError"></div>
                            </div>
                            
                        </div>
                    </div>
                
                    <div class="d-flex justify-content-end mt-2">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
