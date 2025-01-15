<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true" hidden.bs.modal>
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="editUserForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" name="id" id="edit_user_id">
                            <div class="form-group mb-3">
                                <label for="edit_firtsname" class="required capitalize">First Name</label>
                                <input type="text" class="form-control" id="edit_firtsname" name="first_name">
                                <div class="invalid-feedback" id="first_nameError"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_lastname" class="required">Last Name</label>
                                <input type="text" class="form-control capitalize" id="edit_lastname" name="last_name" aria-describedby="">
                                <div class="invalid-feedback" id="last_nameError"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_middlesname" class="required">Middle Name</label>
                                <input type="text" class="form-control capitalize" id="edit_middlesname" name="middle_name">
                                <div class="invalid-feedback" id="middle_nameError"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_suffix">Suffix</label>
                                <input type="text" class="form-control capitalize" id="edit_suffix" name="suffix" aria-describedby="">
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_role" class="required">Role</label>
                                <select class="form-select capitalize" id="edit_role" name="role_id">
                                </select>
                            </div>
                            <div class="invalid-feedback" id="role_idError"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_email" class="required">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email" placeholder="@gmail.com">
                                <div class="invalid-feedback" id="emailError"></div>
                            </div>
        
                            <div class="form-group mb-3">
                                <label for="edit_contactNumber" class="required">Mobile Number</label>
                                <input type="text" class="form-control" id="edit_contactNumber" name="mobile_number" placeholder="09+">
                                <div class="invalid-feedback" id="mobile_numberError"></div>
                            </div>
        
                            <div class="form-group mb-3">
                                <label for="edit_position" class="required">Position</label>
                                <input type="text" class="form-control" id="edit_position" name="position">
                                <div class="invalid-feedback" id="positionError"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_province" class="required">Province</label>
                                <select class="form-select capitalize" id="edit_province" name="province">
                                    <option value="">Select a Province...</option>
                                    <option value="Albay">Albay</option>
                                    <option value="Camarines Norte">Camarines Norte</option>
                                    <option value="Camarines Sur">Camarines Sur</option>
                                    <option value="Catanduanes">Catanduanes</option>
                                    <option value="Masbate">Masbate</option>
                                    <option value="Sorsogon">Sorsogon</option>
                                </select>
                                <div class="invalid-feedback" id="provinceError"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_division_id" class="required">Division</label>
                                <select id="edit_division_id" class="division-select select2 form-select" name="division_id[]" multiple="multiple">

                                </select>
                                <div class="invalid-feedback" id="division_idError"></div>
                            </div>

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