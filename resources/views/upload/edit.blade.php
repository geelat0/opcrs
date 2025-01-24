
<div class="modal fade" id="editUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel1">Upload File</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="updateUploadForm">
            @csrf
            <div class="modal-body">
                <div class="row mb-4">
                    <input type="hidden" name="id" id="edit_id" hidden>
                  <div class="col mb-6">
                      <div class="form-group">
                          <label for="file" class="required">File</label>
                          <input type="file" name="file" class="form-control" id="edit_inputGroupFile02">
                          <div class="invalid-feedback" id="fileError"></div>
                      </div>
                  </div>
                </div>
      
                <div class="row">
                  <div class="col mb-6">
                      <div class="form-group">
                          <label for="category" class="required">Category</label>
                          <select class="form-select edit-category-select" name="upload_category_id" id="edit_category">
                            <option value="">Select Category</option>
                          </select>
                          <div class="invalid-feedback" id="upload_category_idError"></div>
                      </div>
                  </div>
                </div>
                
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
        </form>
        
      </div>
    </div>
  </div>

