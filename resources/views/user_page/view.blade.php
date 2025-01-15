<div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="viewUserForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="viewUserModalLabel">View</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" name="id" id="view_user_id">
                            <div class="form-group mb-3">
                                <label for="view_firtsname" class="required">First Name</label>
                                <input type="text" class="form-control" id="view_firtsname" name="first_name" disabled>
                                <div class="invalid-feedback" id="first_nameError"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="view_lastname" class="required">Last Name</label>
                                <input type="text" class="form-control capitalize" id="view_lastname" name="last_name" aria-describedby="" disabled>
                                <div class="invalid-feedback" id="last_nameError"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="view_middlesname" class="required">Middle Name</label>
                                <input type="text" class="form-control capitalize" id="view_middlesname" name="middle_name" disabled>
                                <div class="invalid-feedback" id="middle_nameError"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="view_suffix">Suffix</label>
                                <input type="text" class="form-control capitalize" id="view_suffix" name="suffix" aria-describedby="" disabled>
                            </div>

                            <div class="form-group mb-3">
                                <label for="view_role" class="required">Role</label>
                                <select class="form-select capitalize" id="view_role" name="role_id" disabled>
                                </select>
                            </div>
                            <div class="invalid-feedback" id="role_idError"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="view_email" class="required">Email</label>
                                <input type="email" class="form-control" id="view_email" name="email" disabled>
                                <div class="invalid-feedback" id="emailError"></div>
                            </div>
        
                            <div class="form-group mb-3">
                                <label for="view_contactNumber" class="required">Mobile Number</label>
                                <input type="text" class="form-control" id="view_contactNumber" name="mobile_number" disabled>
                                <div class="invalid-feedback" id="mobile_numberError"></div>
                            </div>
        
                            <div class="form-group mb-3">
                                <label for="view_position" class="required">Position</label>
                                <input type="text" class="form-control" id="view_position" name="position" disabled>
                                <div class="invalid-feedback" id="positionError"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="view_province" class="required">Province</label>
                                <select class="form-select capitalize" id="view_province" name="province" disabled>
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
                                <label for="view_division_id" class="required">Division</label>
                                <select id="view_division_id" class="division-select select2 form-select" name="division_id[]" multiple="multiple" disabled>

                                </select>
                                <div class="invalid-feedback" id="division_idError"></div>
                            </div>

                            {{-- <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" aria-describedby="" name="password" placeholder="Password">
                                <div class="invalid-feedback" id="passwordError"></div>
                            </div>
                            <div class="form-group">
                                <label for="confirmPassword">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword" aria-describedby="" name="password" placeholder="Confirm Password">
                                <div class="invalid-feedback" id="confirmPasswordError"></div>
                            </div> --}}
                        </div>
                    </div>
                       
                   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>