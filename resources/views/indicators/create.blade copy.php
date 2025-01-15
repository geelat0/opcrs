@extends('components.app')

@section('content')

<div class="container mt-5">

    <form id="NewIndicatorForm">
        @csrf
        <div class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"> <a href="/indicator" class="text-primary"><i class='bx bx-left-arrow-circle'></i></a>
                            Create</h4>
                        <div class="row">
                            <div class="form-group">
                                <label for="org_id" class="required">Organizational Outcome</label>
                                <select id="org_id" class="form-select capitalize" name="org_id">
                                </select>
                                <div class="invalid-feedback" id="org_idError"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div id="cards-containers">
                <div class="col-lg-12 grid-margin stretch-card card-template cards-container">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group mb-3">
                                    <label for="measures_0" class="required">Measure</label>
                                    <textarea type="text" class="form-control" name="measures[]" id="measures_0" aria-describedby=""></textarea>
                                    <div class="invalid-feedback" id="measuresError_0"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="division_id_0" class="required">Division</label>
                                    <select id="division_id_0" class="division-select form-select" name="division_id[0][]" multiple="multiple">
                                    </select>
                                    <div class="invalid-feedback" id="division_idError_0"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="">Target Type:</label>
                                        <div class="form-check form-check-inline percent">
                                        <input class="form-check-input" type="radio" name="targetType_0" id="Percentage_0" value="percentage">
                                    <label class="form-check-label" for="Percentage_0">Percentage</label>
                                    </div>
                                    <div class="form-check form-check-inline number">
                                        <input class="form-check-input" type="radio" name="targetType_0" id="Number_0" value="number">
                                        <label class="form-check-label" for="Number_0">Number</label>
                                    </div>
                                    <div class="form-check form-check-inline actual">
                                        <input class="form-check-input" type="radio" name="targetType_0" id="Actual_0" value="actual">
                                        <label class="form-check-label" for="Actual_0">Actual</label>
                                    </div>
                                </div>
                            </div>

                            {{-- CONTAINER FOR TARGET AND BUDGET --}}

                            <div class="row row-cols-4 mb-3" id="targetFields_0">
                                <div class="col mb-3" >
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group ">
                                        <label for="target_0" class="required">Target</label>
                                        <input type="text" class="form-control capitalize target-input" name="target[]" id="target_0" aria-describedby="" readonly>
                                        {{-- <input type="text" class="form-control capitalize target-input d-none" name="target[]" id="targetDivision_0" aria-describedby="" disabled> --}}
                                        <div class="invalid-feedback" id="targetError_0"></div>
                                    </div>

                                </div>
                                <div class="col">
                                    <div class="form-group" class="required">
                                        <label for="alloted_budget_0">Alloted Budget</label>
                                        <input type="number" step="any"  class="form-control capitalize alloted-budget" name="alloted_budget[]" id="alloted_budget_0" aria-describedby="" placeholder="0" min="0">
                                        <div class="invalid-feedback" id="alloted_budgetError_0"></div>
                                    </div>
                                </div>
                            </div>




                            <div class="row mt-3">

                                <p class="d-inline-flex gap-1">
                                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample_0" aria-expanded="false" aria-controls="collapseExample">
                                      Add Quarterly Target
                                    </button>
                                  </p>
                                  <div class="collapse" id="collapseExample_0">
                                    <div class="card card-body">

                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="quarter1_target_0">Quarter 1</label>
                                                    <input type="text" step="any"  class="form-control capitalize alloted-budget" name="Q1_target[]" id="quarter1_target_0" aria-describedby="" placeholder="0" readonly>
                                                    <div class="invalid-feedback" id="Q1_targetError_0"></div>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="quarter2_target_0">Quarter 2</label>
                                                    <input type="text" step="any"  class="form-control capitalize alloted-budget" name="Q2_target[]" id="quarter2_target_0" aria-describedby="" placeholder="0" readonly>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="quarter3_target_0">Quarter 3</label>
                                                    <input type="text" step="any"  class="form-control capitalize alloted-budget" name="Q3_target[]" id="quarter3_target_0" aria-describedby="" placeholder="0" readonly>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="quarter4_target_0">Quarter 4</label>
                                                    <input type="text" step="any"  class="form-control capitalize alloted-budget" name="Q4_target[]" id="quarter4_target_0" aria-describedby="" placeholder="0" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row mt-3">
            <div class="col">
                <div class="d-flex justify-content-start">
                    <button type="button" class="btn btn-primary btn-add-card" id="addOutcomeBtn"><i class="mdi mdi-plus-circle-outline"></i>Add Entries</button>
                </div>
            </div>
            <div class="col">
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('components.specific_page_scripts')

<script>
$(document).ready(function() {

    //---------------------------------------------------START NEW CARD---------------------------------------------------//

    let outcomeIndex = 1;

    function addOutcomeCard() {
        const newOutcomeHtml = `
        <div class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card card-template cards-container" id="cards-container_${outcomeIndex}">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group mb-3">
                                <label for="measures_${outcomeIndex}" class="required">Measure</label>
                                <textarea type="text" class="form-control capitalize" name="measures[]" id="measures_${outcomeIndex}" aria-describedby=""></textarea>
                                <div class="invalid-feedback" id="measuresError_${outcomeIndex}"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="division_id_${outcomeIndex}" class="required">Division</label>
                                <select id="division_id_${outcomeIndex}" class="division-select form-select" name="division_id[${outcomeIndex}][]" multiple="multiple">
                                </select>
                                <div class="invalid-feedback" id="division_idError_${outcomeIndex}"></div>
                            </div>
                             <div class="form-group mb-3">
                                 <label for="">Target Type:</label>
                                <div class="form-check form-check-inline percent">
                                    <input class="form-check-input target-type" type="radio" name="targetType_${outcomeIndex}" id="Percentage_${outcomeIndex}" value="percentage">
                                    <label class="form-check-label" for="Percentage_${outcomeIndex}">Percentage</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input target-type" type="radio" name="targetType_${outcomeIndex}" id="Number_${outcomeIndex}" value="number">
                                    <label class="form-check-label" for="Number_${outcomeIndex}">Number</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input target-type" type="radio" name="targetType_${outcomeIndex}" id="Actual_${outcomeIndex}" value="actual">
                                    <label class="form-check-label" for="Actual_${outcomeIndex}">Actual</label>
                                </div>
                            </div>


                            <div class="row row-cols-4 mb-3" id="targetFields_${outcomeIndex}">
                                <div class="col mb-3" >
                                </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col">
                                <div class="form-group">
                                     <label for="target_${outcomeIndex}" class="required">Target</label>
                                    <input type="text" class="form-control capitalize" name="target[]" id="target_${outcomeIndex}" aria-describedby="" disabled>
                                    <div class="invalid-feedback" id="targetError_${outcomeIndex}"></div>
                                </div>

                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="alloted_budget_${outcomeIndex}">Alloted Budget</label>
                                    <input type="number" step="any"  class="form-control capitalize" name="alloted_budget[]" id="alloted_budget_${outcomeIndex}" aria-describedby="" placeholder="0" min="0">
                                    <div class="invalid-feedback" id="alloted_budgetError_${outcomeIndex}"></div>
                                </div>
                            </div>
                        </div>

                         <div class="row mt-3">

                                <p class="d-inline-flex gap-1">
                                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample_${outcomeIndex}" aria-expanded="false" aria-controls="collapseExample">
                                      Add Quarterly Target
                                    </button>
                                  </p>
                                  <div class="collapse" id="collapseExample_${outcomeIndex}">
                                    <div class="card card-body">

                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="quarter1_target_${outcomeIndex}">Quarter 1</label>
                                                    <input type="text" step="any"  class="form-control capitalize alloted-budget" name="Q1_target[]" id="quarter1_target_${outcomeIndex}" aria-describedby="" placeholder="0" readonly>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="quarter2_target_${outcomeIndex}">Quarter 2</label>
                                                    <input type="text" step="any"  class="form-control capitalize alloted-budget" name="Q2_target[]" id="quarter2_target_${outcomeIndex}" aria-describedby="" placeholder="0" readonly>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="quarter3_target_${outcomeIndex}">Quarter 3</label>
                                                    <input type="text" step="any"  class="form-control capitalize alloted-budget" name="Q3_target[]" id="quarter3_target_${outcomeIndex}" aria-describedby="" placeholder="0" readonly>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="quarter4_target_${outcomeIndex}">Quarter 4</label>
                                                    <input type="text" step="any"  class="form-control capitalize alloted-budget" name="Q4_target[]" id="quarter4_target_${outcomeIndex}" aria-describedby="" placeholder="0" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                            </div>

                            <button type="button" class="btn btn-danger btn-sm mt-2 removeOutcomeBtn" data-index="${outcomeIndex}">Remove</button>

                    </div>
                </div>
            </div>
        </div>
        `;
        $('#cards-containers').append(newOutcomeHtml);

        initializeDivisionSelect();
        outcomeIndex++;
    }

    $('#addOutcomeBtn').click(function () {
        addOutcomeCard();
    });

    $(document).on('click', '.removeOutcomeBtn', function () {
        const index = $(this).data('index');
        $(`#cards-container_${index}`).remove();
    });

    //---------------------------------------------------END NEW CARD---------------------------------------------------//



    //---------------------------------------------------START GET OUTCOME---------------------------------------------------//

    $('#org_id').select2({
        placeholder: 'Select an Option',
        allowClear: true,
        ajax: {
            url: '{{ route('org.getOrg') }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term // search term
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.organizational_outcome
                        };
                    })
                };
            },
            cache: true
        }
    });
    //---------------------------------------------------END GET OUTCOME---------------------------------------------------//


    //---------------------------------------------------START GET DIVISION---------------------------------------------------//

    function initializeDivisionSelect() {
        $('.division-select').select2({
            placeholder: 'Select an Option',
            allowClear: true,
            ajax: {
                url: '{{ route('indicator.getDivision') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // search term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.id,
                                text: item.division_name
                            };
                        })
                    };
                },
                cache: true
            }
        });
    }
    initializeDivisionSelect();

    //---------------------------------------------------END GET DIVISION---------------------------------------------------//


    //---------------------------------------------------START JS FOR TARGET TYPE---------------------------------------------------//


    $(document).on('change', 'input[name^="targetType"]', function() {
        const index = $(this).closest('.card').find('input[name^="targetType"]').attr('id').split('_').pop();
        const selectedType = $(this).val();
        const targetInput = $(`#target_${index}`);
        let currentValue = targetInput.val();
        // const currentValue = targetInput.val().replace('%', '')

        if (selectedType == 'percentage') {
            targetInput
            .attr('type', 'text') // Set type to text to allow appending "%"
            .attr('min', '0')
            .attr('max', '100')
            .attr('placeholder', '%')
            .removeAttr('readonly')
            .val(currentValue.includes('Actual') ? '' : `${currentValue.replace('%', '')}%`)
            .off('input.percentage')
            .on('input.percentage', function() {
                // Remove non-numeric characters except '%'
                let value = $(this).val().replace(/[^\d%]/g, '');

                if (value.indexOf('%') !== -1) {
                    value = value.substring(0, value.indexOf('%') + 1); // Keep only one "%"
                }
                // Ensure the value is within the range
                if ($.isNumeric(value) && value >= 0 && value <= 100) {
                    $(this).val(`${value}%`);
                } else {
                    $(this).val(value);
                }
            });
        } else if (selectedType == 'number') {
            targetInput
            .attr('type', 'number')
            .removeAttr('min')
            .removeAttr('max')
            .removeAttr('placeholder')
            .removeAttr('readonly')
            .val(currentValue.replace('%', '').replace('Actual', ''))
            .off('input.percentage');
        } else if (selectedType == 'actual') {
            targetInput
            .attr('type', 'text')
            .attr('readonly', 'readonly')
            .removeAttr('placeholder')
            .off('input.percentage')
            .val('Actual');
            $(`#target_${index}`).val('Actual');
        }


    });

    $(document).on('change', 'input[name^="targetType"]', function() {
        const index = $(this).closest('.card').find('input[name^="targetType"]').attr('id').split('_').pop();
        const selectedType = $(this).val();

        // Loop through all the quarter inputs for the selected target type
        const quarterInputs = [
            $(`#quarter1_target_${index}`),
            $(`#quarter2_target_${index}`),
            $(`#quarter3_target_${index}`),
            $(`#quarter4_target_${index}`)
        ];

        quarterInputs.forEach(function(quarterInput) {
            let currentValue = quarterInput.val();

            if (selectedType == 'percentage') {
                quarterInput
                    .attr('type', 'text') // Set type to text to allow appending "%"
                    .attr('min', '0')
                    .attr('max', '100')
                    .attr('placeholder', '%')
                    .removeAttr('readonly')
                    .val(currentValue.includes('Actual') ? '' : `${currentValue.replace('%', '')}%`)
                    .off('input.percentage')
                    .on('input.percentage', function() {
                        // Remove non-numeric characters except '%'
                        let value = $(this).val().replace(/[^\d%]/g, '');

                        if (value.indexOf('%') !== -1) {
                            value = value.substring(0, value.indexOf('%') + 1); // Keep only one "%"
                        }
                        // Ensure the value is within the range
                        if ($.isNumeric(value) && value >= 0 && value <= 100) {
                            $(this).val(`${value}%`);
                        } else {
                            $(this).val(value);
                        }
                    });
            } else if (selectedType == 'number') {
                quarterInput
                    .attr('type', 'number')
                    .removeAttr('min')
                    .removeAttr('max')
                    .removeAttr('placeholder')
                    .removeAttr('readonly')
                    .val(currentValue.replace('%', '').replace('Actual', ''))
                    .off('input.percentage');
            } else if (selectedType == 'actual') {
                quarterInput
                        .attr('type', 'text')
                        .attr('readonly', 'readonly')
                        .removeAttr('placeholder')
                        .off('input.percentage')
                        .val('Actual');
            }
        });
    });

    //---------------------------------------------------END JS FOR TARGET TYPE---------------------------------------------------//

    //---------------------------------------------------START JS FOR DIVISION'S INPUTS---------------------------------------------------//


    function updateTargetFields(index, selectedDivisions) {
        const targetContainer = $(`#targetFields_${index}`);

        // Store existing input values
        let existingValues = {};
        $(`#targetFields_${index} .target-input`).each(function() {
            const targetId = $(this).attr('id');
            existingValues[targetId] = $(this).val(); // Store the current target value
        });

        $(`#targetFields_${index} .alloted-budget`).each(function() {
            const budgetId = $(this).attr('id');
            existingValues[budgetId] = $(this).val(); // Store the current budget value
        });

        targetContainer.empty(); // Clear the container

        if (selectedDivisions.length > 0) {
            selectedDivisions.forEach((divisionId) => {
                const divisionName = $(`#division_id_${index} option[value="${divisionId}"]`).text();
                const cleanedDivisionName = divisionName.replace(/\s*PO$/, '');

                if (divisionName.includes("PO")) {
                    const targetHtml = `
                    <div class= "col mb-3">
                        <div class="form-group">
                            <label for="target_${divisionId}_${index}" class="required">${divisionName} Target</label>
                            <input type="text" class="form-control capitalize target-input" name="${cleanedDivisionName}_target[]" id="target_${divisionId}_${index}" aria-describedby="" readonly>
                            <div class="invalid-feedback" id="targetError_${divisionId}_${index}"></div>
                        </div>
                    </div>
                    <div class= "col mb-3">
                        <div class="form-group">
                            <label for="budget_${divisionId}_${index}" class="required">${divisionName} Budget</label>
                            <input type="number" step="any"  class="form-control capitalize alloted-budget" name="${cleanedDivisionName}_budget[]" id="budget_${divisionId}_${index}" aria-describedby="">
                            <div class="invalid-feedback" id="budgetError_${divisionId}_${index}"></div>
                        </div>
                    </div>
                    `;
                    targetContainer.append(targetHtml);

                    // Enable the target input and attach the input event to calculate total
                    const targetInput = $(`#target_${divisionId}_${index}`);
                    const selectedType = $(`input[name="targetType_${index}"]:checked`).val(); // Get the selected target type
                    applyTargetType(targetInput, selectedType); // Apply the existing target type to the new division

                    targetInput.on('input', function() {
                        let total = 0;
                        let selectedType = $(`input[name="targetType_${index}"]:checked`).val();

                        $(`#targetFields_${index} .target-input`).each(function() {
                            let value = parseFloat($(this).val());
                            if (!isNaN(value)) {
                                total += value;
                            }
                        });
                        if (selectedType === 'percentage') {
                            $(`#target_${index}`).val(`${total}%`);
                        } else {
                            $(`#target_${index}`).val(total);
                        }
                    });

                    // Attach the input event to budget fields to calculate the total budget
                    const budgetInput = $(`#budget_${divisionId}_${index}`);
                    budgetInput.on('input', function() {
                        let totalBudget = 0;
                        $(`#targetFields_${index} .alloted-budget`).each(function() {
                            const value = parseFloat($(this).val());
                            if (!isNaN(value)) {
                                totalBudget += value;
                            }
                        });
                        $(`#alloted_budget_${index}`).val(totalBudget);
                    });

                    // Restore existing values after the new inputs are appended
                    if (existingValues[`target_${divisionId}_${index}`]) {
                        targetInput.val(existingValues[`target_${divisionId}_${index}`]);
                    }
                    if (existingValues[`budget_${divisionId}_${index}`]) {
                        budgetInput.val(existingValues[`budget_${divisionId}_${index}`]);
                    }
                } else {
                    $(`.percent`).removeClass('d-none');
                }
            });
        }
    }

    // Function to apply the target type to new divisions without clearing the input value
    function applyTargetType(targetInput, selectedType) {
        let currentValue = targetInput.val().replace('%', ''); // Remove the '%' symbol if present but keep the value

        if (selectedType === 'percentage') {
            targetInput
                .attr('type', 'text')
                .attr('min', '0')
                .attr('max', '100')
                .attr('placeholder', '%')
                .removeAttr('readonly')
                .val(`${currentValue}%`) // Add the '%' symbol to the existing value
                .off('input.percentage')
                .on('input.percentage', function() {
                    let value = $(this).val().replace(/[^\d%]/g, '');
                    if (value.indexOf('%') !== -1) {
                        value = value.substring(0, value.indexOf('%') + 1); // Keep only one "%"
                    }
                    if ($.isNumeric(value) && value >= 0 && value <= 100) {
                        $(this).val(`${value}%`);
                    } else {
                        $(this).val(value);
                    }
                });

            // Remove any hidden input if present
            // targetInput.siblings('input[type="hidden"]').remove();

        } else if (selectedType === 'number') {
            targetInput
                .attr('type', 'number')
                .removeAttr('min')
                .removeAttr('max')
                .removeAttr('placeholder')
                .removeAttr('readonly')
                .val(currentValue) // Remove the '%' symbol but keep the numerical value
                .off('input.percentage');

            // Remove any hidden input if present
            // targetInput.siblings('input[type="hidden"]').remove();

        } else if (selectedType === 'actual') {
            targetInput
                .attr('type', 'text')
                .attr('readonly', 'readonly')
                .attr('placeholder', '')
                .off('input.percentage')
                .val(`Actual`);

            // Check if the hidden input already exists, if not, create it
            // if (targetInput.siblings('input[type="hidden"]').length === 0) {
            //     // Create a hidden input and set its value
            //     const hiddenInput = $('<input>')
            //         .attr('type', 'hidden')
            //         .attr('name', targetInput.attr('name')) // use the same name as the disabled input
            //         .val(`Actual`);

            //     // Append the hidden input right after the disabled input
            //     targetInput.after(hiddenInput);
            // }
        }
    }

    $(document).on('change', '.division-select', function() {
        const index = $(this).attr('id').split('_').pop();
        const selectedDivisions = $(this).val();

        const hasPO = selectedDivisions.some((divisionId) => {
            const divisionName = $(`#division_id_${index} option[value="${divisionId}"]`).text();
            return divisionName.includes("PO");
        });

        $(`.percent`).removeClass('d-none');

        updateTargetFields(index, selectedDivisions);
        if (hasPO) {
            updateTargetFields(index, selectedDivisions);
        }
    });

    $(document).on('change', 'input[name^="targetType"]', function() {
        const index = $(this).closest('.card').find('input[name^="targetType"]').attr('id').split('_').pop();
        const selectedType = $(this).val();

        $(`#targetFields_${index} .target-input`).each(function() {
            const targetInput = $(this);
            applyTargetType(targetInput, selectedType); // Apply target type to all current fields without clearing values
        });
    });


    //---------------------------------------------------END JS FOR DIVISION'S INPUTS---------------------------------------------------//

    //---------------------------------------------------START JS FOR SAVING THE DATA---------------------------------------------------//


    $('#NewIndicatorForm').on('submit', function(e) {
        e.preventDefault();
        showLoader();

        $.ajax({
            url: '{{ route('indicator.store') }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                hideLoader();
                window.location.href = '/indicator';
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                    });
                    $('#NewIndicatorForm')[0].reset(); // Reset the form
                    $('#cards-containers').html($('.cards-container:first').clone());
                }
            },
            error: function(xhr) {
                hideLoader();
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let isValid = true;
                    $('.invalid-feedback').html(''); // Clear any previous error messages
                    $('.is-invalid').removeClass('is-invalid');
                    for (let key in errors) {
                        const keyParts = key.split('.');
                        console.log(keyParts);
                        if (keyParts.length > 1) {
                            const index = keyParts[1];
                            const errorKey = keyParts[0];
                            console.log(errorKey);
                            $(`#${errorKey}_${index}`).addClass('is-invalid');
                            $(`#${errorKey}Error_${index}`).html(errors[key][0]).show();
                        } else {
                            $(`#${key}`).addClass('is-invalid');
                            $(`#${key}Error`).html(errors[key][0]).show();
                        }
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Errors!',
                        html: 'Please fill out the required fields with asterisk',
                        showConfirmButton: true,
                    });

                    $('.division-select').each(function() {
                        const index = $(this).attr('id').split('_').pop();
                        if ($(this).val().length === 0) { // Check if no value is selected
                            $(`#division_id_${index}`).addClass('is-invalid');
                            $(`#division_idError_${index}`).html('Please select at least one division.').show();
                            isValid = false;
                        } else {
                            $(`#division_idError_${index}`).html('').hide();
                        }
                    });

                    if (!isValid) {
                        hideLoader();
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Errors!',
                            html: 'Please fill out the required fields with asterisk',
                            showConfirmButton: true,
                        });
                        return;
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oh no!',
                        text: 'Something went wrong.',
                        showConfirmButton: true,
                    });
                }
            }
        });
    });

    //---------------------------------------------------END JS FOR SAVING THE DATA---------------------------------------------------//
});


</script>
@endsection
