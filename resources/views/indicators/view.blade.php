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
                            View</h4>
                        <div class="row">
                            <input type="hidden" name="id" value="{{ $indicator->id }}">
                            @if(in_array(Auth::user()->role->name, ['SuperAdmin', 'Admin']))
                            <div class="form-group">
                                <label for="org_id" class="required">Organizational Outcome</label>
                                <select id="org_id" class="form-select capitalize" name="org_id" disabled>
                                    @if($indicator)
                                        <option value="{{ $indicator->org_id }}" selected>{{ $indicator->org->organizational_outcome }}</option>
                                    @endif
                                </select>
                            </div>
                            @endif
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
                                    <label for="measures" class="required">Measure</label>
                                    <textarea type="text" class="form-control capitalize" name="measures" id="measures_0" aria-describedby="" disabled>{{ $indicator->measures }}</textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="division_id" class="required">Division</label>
                                    <select id="division_id_0" class="division-select form-select" name="division_id[]" multiple="multiple" disabled >
                                        @if($indicator)
                                            @foreach( $division_ids as $division)
                                                <option value="{{ $division }}" selected>{{ App\Models\Division::find($division)->division_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="invalid-feedback" id="division_idError"></div>
                                </div>
                            </div>


                            <div class="row  mb-3">
                                <div class="col">
                                    <div class="accordion" id="accordionWithIcon">
                                        @if(in_array(Auth::user()->role->name, ['SuperAdmin', 'Admin']))
                                        <div class="card accordion-item ">
                                          <h2 class="accordion-header d-flex align-items-center">
                                            <button type="button" class="accordion-button collapsed btn-outline-danger"  data-bs-toggle="collapse" data-bs-target="#accordionWithIcon-1" aria-expanded="false">
                                              <i class='bx bxs-circle-quarter me-2'></i>
                                              Quarter One Target
                                            </button>
                                          </h2>
                                        
                                          <div id="accordionWithIcon-1" class="accordion-collapse collapse" >
                                            <div class="accordion-body">

                                                <div class="row row-cols-3 mb-3" id="Q1targetFields_0">
                                                    {{-- <div class="col mb-3" >
                                                    </div> --}}
                                                </div>

                                            
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group ">
                                                            <label for="quarter1_target_0" class="required">Q1 Target</label>
                                                            <input type="text" class="form-control capitalize target-input" name="Q1_target[]" id="quarter1_target_0" aria-describedby="" value="{{ $indicator->Q1_target }}" disabled>
                                                            {{-- <input type="text" class="form-control capitalize target-input d-none" name="target[]" id="targetDivision_0" aria-describedby="" disabled> --}}
                                                            <div class="invalid-feedback" id="Q1_targetError_0"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                          </div>
                                        </div>
                                        
                                        <div class="accordion-item card">
                                          <h2 class="accordion-header d-flex align-items-center">
                                            <button type="button" class="accordion-button collapsed btn-outline-warning" data-bs-toggle="collapse" data-bs-target="#accordionWithIcon-2" aria-expanded="false">
                                                <i class='bx bxs-circle-half me-2'></i>
                                              Quarter Two Target
                                            </button>
                                          </h2>
                                          <div id="accordionWithIcon-2" class="accordion-collapse collapse" >
                                            <div class="accordion-body">

                                                <div class="row row-cols-3 mb-3" id="Q2targetFields_0">
                                                    {{-- <div class="col mb-3" >
                                                    </div> --}}
                                                </div>

                                            
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group ">
                                                            <label for="quarter2_target_0" class="required">Q2 Target</label>
                                                            <input type="text" class="form-control capitalize target-input" name="Q2_target[]" id="quarter2_target_0" aria-describedby="" value="{{ $indicator->Q2_target }}" disabled>
                                                            {{-- <input type="text" class="form-control capitalize target-input d-none" name="target[]" id="targetDivision_0" aria-describedby="" disabled> --}}
                                                            <div class="invalid-feedback" id="Q2_targetError_0"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            
                                            </div>
                                          </div>
                                        </div>
                                        
                                        <div class="accordion-item card">
                                          <h2 class="accordion-header d-flex align-items-center">
                                            <button type="button" class="accordion-button collapsed btn-outline-success" data-bs-toggle="collapse" data-bs-target="#accordionWithIcon-3" aria-expanded="false">
                                              <i class='bx bxs-circle-three-quarter me-2' ></i>
                                              Quarter Three Target
                                            </button>
                                          </h2>
                                          <div id="accordionWithIcon-3" class="accordion-collapse collapse" >
                                            <div class="accordion-body">


                                                <div class="row row-cols-3 mb-3" id="Q3targetFields_0">
                                                    {{-- <div class="col mb-3" >
                                                    </div> --}}
                                                </div>

                                            
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group ">
                                                            <label for="quarter3_target_0" class="required">Q3 Target</label>
                                                            <input type="text" class="form-control capitalize target-input" name="Q3_target[]" id="quarter3_target_0" aria-describedby="" value="{{ $indicator->Q3_target }}" disabled>
                                                            {{-- <input type="text" class="form-control capitalize target-input d-none" name="target[]" id="targetDivision_0" aria-describedby="" disabled> --}}
                                                            <div class="invalid-feedback" id="Q3_targetError_0"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                             
                                            </div>
                                          </div>
                                        </div>

                                        <div class="accordion-item card">
                                          <h2 class="accordion-header d-flex align-items-center">
                                            <button type="button" class="accordion-button collapsed btn-outline-info" data-bs-toggle="collapse" data-bs-target="#accordionWithIcon-4" aria-expanded="false">
                                                <i class='bx bxs-circle me-2' ></i>
                                              Quarter Four Target
                                            </button>
                                          </h2>
                                          <div id="accordionWithIcon-4" class="accordion-collapse collapse" >
                                            <div class="accordion-body">
                                             
                                                <div class="row row-cols-3 mb-3" id="Q4targetFields_0">
                                                    {{-- <div class="col mb-3" >
                                                    </div> --}}
                                                </div>

                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group ">
                                                            <label for="quarter4_target_0" class="required">Q4 Target</label>
                                                            <input type="text" class="form-control capitalize target-input" name="Q4_target[]" id="quarter4_target_0" aria-describedby="" value="{{ $indicator->Q4_target }}" disabled>
                                                            {{-- <input type="text" class="form-control capitalize target-input d-none" name="target[]" id="targetDivision_0" aria-describedby="" disabled> --}}
                                                            <div class="invalid-feedback" id="Q4_targetError_0"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                          </div>
                                        </div>
                                        @endif

                                        <div class="accordion-item card">
                                          <h2 class="accordion-header d-flex align-items-center">
                                            <button type="button" class="accordion-button btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#accordionWithIcon-5" aria-expanded="true">
                                                <i class='bx bxs-circle me-2' ></i>
                                              Annual
                                            </button>
                                          </h2>
                                          <div id="accordionWithIcon-5" class="accordion-collapse collapse show" >
                                            <div class="accordion-body">

                                                <div class="row row-cols-4 mb-3" id="targetFields_0">
                                                    <div class="col mb-3" >
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group mb-3">
                                                            <label for="target" class="required">Target</label>
                                                            <input type="text" class="form-control capitalize" name="target" id="target_0" aria-describedby="" value="{{ $indicator->target }}" {{ $indicator->targetType == 'actual' ? 'disabled' : '' }} disabled >
                                                            <div class="invalid-feedback" id="targetError[]"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="alloted_budget" class="required">Alloted Budget</label>
                                                            <input type="number" class="form-control capitalize" name="alloted_budget" id="alloted_budget_0" aria-describedby="" value="{{ $indicator->alloted_budget }}" disabled>
                                                            <div class="invalid-feedback" id="alloted_budgetError"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                          </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="row mt-3">

                                <p class="d-inline-flex gap-1">
                                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample_0"  aria-expanded="true" aria-controls="collapseExample">
                                    Quarterly Target
                                    </button>
                                  </p>
                                  <div class="collapse show" id="collapseExample_0">
                                    <div class="card card-body">

                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="quarter1_target_0">Quarter 1</label>
                                                    <input type="text" step="any"  class="form-control capitalize alloted-budget" name="Q1_target" id="quarter1_target_0" aria-describedby="" value="{{ $indicator->Q1_target }}" disabled>
                                                    <div class="invalid-feedback" id="Q1_targetError_0"></div>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="quarter2_target_0">Quarter 2</label>
                                                    <input type="text" step="any"  class="form-control capitalize alloted-budget" name="Q2_target" id="quarter2_target_0" aria-describedby="" value="{{ $indicator->Q2_target }}" disabled>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="quarter3_target_0">Quarter 3</label>
                                                    <input type="text" step="any"  class="form-control capitalize alloted-budget" name="Q3_target" id="quarter3_target_0 "aria-describedby="" value="{{ $indicator->Q3_target }}" disabled>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="quarter4_target_0">Quarter 4</label>
                                                    <input type="text" step="any"  class="form-control capitalize alloted-budget" name="Q4_target" id="quarter4_target_0" aria-describedby="" value="{{ $indicator->Q4_target }}" disabled>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>

                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection

@section('components.specific_page_scripts')

<script>
$(document).ready(function() {

    const regionTargets = @json($region_targets);

    // Initialize Select2 for Organizational Outcome
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

    // Initialize Select2 for Division
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
        }).on('change', function() {
            const index = $(this).attr('id').split('_').pop();
            const selectedDivisions = $(this).val();
            updateTargetFields(index, selectedDivisions);
            updateQuarterTargetFields(index, selectedDivisions);
        });

        // Check if any divisions are already selected and update accordingly
        $('.division-select').each(function() {
            const index = $(this).attr('id').split('_').pop();
            const selectedDivisions = $(this).val();
            if (selectedDivisions.length > 0) {
                updateTargetFields(index, selectedDivisions);
                updateQuarterTargetFields(index, selectedDivisions);
            }
        });
    }
    // initializeDivisionSelect();


    // Handle target type change
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
            .removeAttr('disabled')
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
            .removeAttr('disabled')
            .val(currentValue.replace('%', '').replace('Actual', ''))
            .off('input.percentage');
        } else if (selectedType == 'actual') {
            targetInput
            .attr('type', 'text')
            .attr('disabled', 'disabled')
            .removeAttr('placeholder')
            .off('input.percentage')
            .val('Actual');
            $(`#target_${index}`).val('Actual');
        }
    });


     //---------------------------------------------------START JS FOR DIVISION'S INPUTS---------------------------------------------------//

   // Function to update target fields based on selected divisions
    function updateTargetFields(index, selectedDivisions, isInitialLoad = false) {
        const targetContainer = $(`#targetFields_${index}`);
        targetContainer.empty();

        if (selectedDivisions.length > 0) {
            selectedDivisions.forEach((divisionId) => {
                const divisionName = $(`#division_id_${index} option[value="${divisionId}"]`).text();
                const cleanedDivisionName = divisionName.replace(/\s*PO$/, '');

                if (divisionName.includes("PO")) {
                    const targetValue = @json($division_targets)[divisionId];
                    const budgetValue = @json($division_budget)[divisionId];

                    const displayValue = targetValue == 0 ? 'Actual' : targetValue;


                    const targetHtml = `
                        <div class= "col mb-3">
                        <div class="form-group">
                            <label for="target_${divisionId}_${index}" class="required">${divisionName} Target</label>
                            <input type="text" class="form-control capitalize target-input" name="${cleanedDivisionName}_target[]" id="target_${divisionId}_${index}" aria-describedby="" value="${displayValue}" disabled>
                            <div class="invalid-feedback" id="targetError_${divisionId}_${index}"></div>
                        </div>
                    </div>
                    <div class= "col mb-3">
                        <div class="form-group">
                            <label for="budget_${divisionId}_${index}" class="required">${divisionName} Budget</label>
                            <input type="number" class="form-control capitalize alloted-budget" name="${cleanedDivisionName}_budget[]" id="budget_${divisionId}_${index}" value="${budgetValue}" disabled>
                            <div class="invalid-feedback" id="budgetError_${divisionId}_${index}"></div>
                        </div>
                    </div>
                    `;
                    targetContainer.append(targetHtml);

                    // Enable the target input and attach the input event to calculate total
                    const targetInput = $(`#target_${divisionId}_${index}`);
                    if (!isInitialLoad) {

                    } else {
                        const targetValue = $(`#target_${index}`).val();
                        targetInput.val(targetValue).removeAttr('disabled');
                    }

                    targetInput.on('input', function() {
                        let total = 0;
                        let selectedType = $(`#Percentage_${index}`).val();
                        console.log(selectedType);

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
                } else {
                    $(`.percent`).removeClass('d-none');
                }
            });
        }
    }

    function updateQuarterTargetFields(index, selectedDivisions, isInitialLoad = false) {
        // Define quarterly target field containers
        const quarters = [
            `#Q1targetFields_${index}`,
            `#Q2targetFields_${index}`,
            `#Q3targetFields_${index}`,
            `#Q4targetFields_${index}`
        ];

        const selectedType = $(`input[name="targetType_${index}"]:checked`).val();

        // Iterate over selected divisions
        if (selectedDivisions.length > 0) {
            selectedDivisions.forEach((divisionId) => {
                const divisionName = $(`#division_id_${index} option[value="${divisionId}"]`).text();
                const cleanedDivisionName = divisionName.replace(/\s*PO$/, '');

                if (divisionName.includes("PO")) {
                    quarters.forEach((quarter, quarterIndex) => {
                        // Use regionTargets to get the appropriate value for each region and quarter
                        const quarterKey = `Q${quarterIndex + 1}`;  // Example: "Q1", "Q2", etc.
                        const regionName = cleanedDivisionName;  // Assume cleanedDivisionName corresponds to region
                        const targetValue = regionTargets[regionName] && regionTargets[regionName][quarterKey] ? regionTargets[regionName][quarterKey] : '';  // Default to empty if not found
                        
                        const displayValue = targetValue === 'Actual' ? 'Actual' : targetValue;
                        const targetDisabled = targetValue === 'Actual' ? 'readonly' : '';
                        const targetHtml = `
                        <div class="col mb-3">
                            <div class="form-group">
                                <label for="target_${divisionId}_${index}_Q${quarterIndex + 1}" class="required">${divisionName} Target (Q${quarterIndex + 1})</label>
                                <input type="text" class="form-control capitalize target-input" name="${cleanedDivisionName}_target_Q${quarterIndex + 1}[]" id="target_${divisionId}_${index}_Q${quarterIndex + 1}" aria-describedby="" value="${displayValue}" ${targetDisabled} disabled>
                                <div class="invalid-feedback" id="targetError_${divisionId}_${index}_Q${quarterIndex + 1}"></div>
                            </div>
                        </div>
                        `;
                        $(quarter).append(targetHtml);

                        // Enable the target input and attach the input event to calculate total
                        const targetInput = $(`#target_${divisionId}_${index}_Q${quarterIndex + 1}`);
                        
                        // Apply the selected target type to the new division fields
                        if (selectedType === 'percentage') {
                            targetInput
                                .attr('type', 'text')
                                .attr('min', '0')
                                .attr('max', '100')
                                .attr('placeholder', '%')
                                .val(`${displayValue}%`);  // Set value as percentage if not 'Actual'
                        } else if (selectedType === 'number') {
                            targetInput
                                .attr('type', 'number')
                                .val(displayValue);  // Set value as a number
                        } else if (selectedType === 'actual') {
                            targetInput
                                .attr('type', 'text')
                                .attr('readonly', 'readonly')
                                .val('Actual');  // Set as 'Actual'
                        }

                    });
                }
            });
        }
    }

    // Trigger the initialization function on page load
    initializeDivisionSelect();

    $(document).on('change', '.division-select', function() {
        const index = $(this).attr('id').split('_').pop();
        const selectedDivisions = $(this).val();

        const hasPO = selectedDivisions.some((divisionId) => {
            const divisionName = $(`#division_id_${index} option[value="${divisionId}"]`).text();
            return divisionName.includes("PO");
        });

        $(`.percent`).removeClass('d-none')

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
            // const currentValue = targetInput.val().replace('%', ''); // Remove % if present
            let currentValue = targetInput.val();

            if (selectedType === 'percentage') {
                targetInput
                    .attr('type', 'text')
                    .attr('min', '0')
                    .attr('max', '100')
                    .attr('placeholder', '%')
                    .removeAttr('disabled')
                    .val(currentValue.includes('Actual') ? '' : `${currentValue.replace('%', '')}%`)
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

            } else if (selectedType === 'number') {
                targetInput
                    .attr('type', 'number')
                    .removeAttr('min')
                    .removeAttr('max')
                    .removeAttr('placeholder')
                    .removeAttr('disabled')
                    .val(currentValue.replace('%', '').replace('Actual', ''))
                    .off('input.percentage');

                let total = 0;
                $(`#targetFields_${index} .target-input`).each(function() {
                    const value = parseFloat($(this).val().replace('%', ''));
                    if (!isNaN(value)) {
                        total += value;
                    }
                });

                $(`#target_${index}`).val(total);
            } else if (selectedType === 'actual') {
                targetInput
                    .attr('type', 'text')
                    .attr('disabled', 'disabled')
                    .removeAttr('placeholder')
                    .off('input.percentage')
                    .val('Actual');
                $(`#target_${index}`).val('Actual');
            }
        });

    });



    //---------------------------------------------------END JS FOR DIVISION'S INPUTS---------------------------------------------------//

    // Form submission
    @if(in_array(Auth::user()->role->name, ['SuperAdmin', 'Admin']))
    $('#NewIndicatorForm').on('submit', function(e) {
        e.preventDefault();
        showLoader();

        $.ajax({
            url: '{{ route('indicator.update', $indicator->id) }}', // Assuming you have a route named 'indicator.update'
            type: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                hideLoader();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                    });
                    window.location.href = '/indicator';
                }
            },
            error: function(xhr) {
                hideLoader();
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $('.invalid-feedback').html(''); // Clear any previous error messages
                    for (let key in errors) {
                        const keyParts = key.split('.');
                        if (keyParts.length > 1) {
                            const index = keyParts[1];
                            const errorKey = keyParts[0];
                            $(`#${errorKey}Error_${index}`).html(errors[key][0]).show();
                        } else {
                            $(`#${key}Error`).html(errors[key][0]).show();
                        }
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Errors!',
                        html: 'Please fill out the required fields with asterisk',
                        showConfirmButton: true,
                    });
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
    @endif

    @if(!in_array(Auth::user()->role->name, ['SuperAdmin', 'Admin']))
    $('#NewIndicatorForm').on('submit', function(e) {
            e.preventDefault();
            showLoader();

            $.ajax({
                url: '{{ route('indicator.update_nonSuperAdminV2') }}',
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
                        for (let key in errors) {
                            const keyParts = key.split('.');
                            console.log(keyParts);
                            if (keyParts.length > 1) {
                                const index = keyParts[1];
                                const errorKey = keyParts[0];
                                console.log(errorKey);
                                $(`#${errorKey}Error_${index}`).html(errors[key][0]).show();
                            } else {
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
        @endif
});
</script>
@endsection
