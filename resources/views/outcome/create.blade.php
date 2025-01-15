<div class="modal fade" id="createOrgModal" tabindex="-1" role="dialog" aria-labelledby="createOrgModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createOrgModalLabel">Create New Organization Outcome</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createOrgForm">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <div id="organizational_outcomes">
                                <div class="form-group mb-3">
                                    <label for="order_0" class="required">Order</label>
                                    <input type="number" class="form-control capitalize" name="order[]" id="order_0" aria-describedby="" placeholder="0" min="0">
                                    <div class="invalid-feedback" id="orderError_0"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="organizational_outcome_0" class="required">Organization Outcome</label>
                                    <input type="text" class="form-control capitalize" name="organizational_outcome[]" id="organizational_outcome_0" aria-describedby="">
                                    <div class="invalid-feedback" id="organizational_outcomeError_0"></div>
                                </div>
                                <div class="form-group">
                                    <label for="category_0" class="required">Category</label>
                                    <select class="form-control capitalize" name="category[]" id="category_0" >
                                        <option value="Core">Core</option>
                                        <option value="Non Core">Non Core</option>
                                    </select>
                                    <div class="invalid-feedback" id="category_0"></div>
                                </div>
                                
                            </div>
                            <button type="button" class="btn btn-primary btn-sm mt-2" id="addOutcomeBtn"><i class='bx bx-plus-circle'></i></button>
                            <div class="form-group mt-3">
                                <label for="status" class="required">Status</label>
                                <select id="status" class="form-select" name="status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback" id="statusError"></div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>

</script>
