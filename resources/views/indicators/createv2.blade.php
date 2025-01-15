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
                        {{-- <div class="row">
                            <div class="form-group">
                                <label for="org_id" class="required">Organizational Outcome</label>
                                <select id="org_id" class="form-select capitalize" name="org_id">
                                </select>
                                <div class="invalid-feedback" id="org_idError"></div>
                            </div>
                        </div> --}}
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

                                <input type="hidden" id="measure_id" name="id">
                                <div class="form-group mb-3">
                                    <label for="measures" class="required">Measure</label>
                                    <select id="measures" class="form-select measure-select capitalize" name="measures">
                                    </select>
                                    <div class="invalid-feedback" id="measuresError"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="division_id_0" class="required">Division</label>
                                    <select id="division_id_0" class="division-select form-select" name="division_id[0][]" multiple="multiple" disabled>
                                    </select>
                                    <div class="invalid-feedback" id="division_idError_0"></div>
                                </div>
                            </div>


                            <div class="row  mb-3">
                                <div class="col">
                                    <div class="accordion" id="accordionWithIcon">
                                        

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
                                                        <div class="form-group">
                                                            <label for="target" class="required">Total Target</label>
                                                            <input type="text" class="form-control capitalize target-input" name="target" id="target" aria-describedby="" disabled>
                                                            {{-- <input type="text" class="form-control capitalize target-input d-none" name="target[]" id="targetDivision_0" aria-describedby="" disabled> --}}
                                                            <div class="invalid-feedback" id="targetError"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group" class="required">
                                                            <label for="alloted_budget">Alloted Budget</label>
                                                            <input type="number" step="any"  class="form-control capitalize" name="alloted_budget" id="alloted_budget" aria-describedby="">
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

                             {{-- CONTAINER FOR TARGET AND BUDGET --}}

                           

                            {{-- <div class="row mt-3">

                                <p class="d-inline-flex gap-1">
                                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample_0" aria-expanded="false" aria-controls="collapseExample">
                                     Quarterly Target
                                    </button>
                                  </p>
                                  <div class="collapse" id="collapseExample_0">
                                    <div class="card card-body">

                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="Q1_target_">Quarter 1</label>
                                                    <input type="text" step="any"  class="form-control capitalize" name="Q1_target" id="Q1_target" aria-describedby=""disabled>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="Q2_target">Quarter 2</label>
                                                    <input type="text" step="any"  class="form-control capitalize" name="Q2_target" id="Q2_target" aria-describedby="" disabled>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="Q3_target">Quarter 3</label>
                                                    <input type="text" step="any"  class="form-control capitalize" name="Q3_target" id="Q3_target" aria-describedby=""disabled>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="form-group" class="">
                                                    <label for="Q4_target">Quarter 4</label>
                                                    <input type="text" step="any"  class="form-control capitalize" name="Q4_target" id="Q4_target" aria-describedby=""disabled>
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

        <div class="row mt-3">
            <div class="col">
                {{-- <div class="d-flex justify-content-start">
                    <button type="button" class="btn btn-primary btn-add-card" id="addOutcomeBtn"><i class="mdi mdi-plus-circle-outline"></i>Add Entries</button>
                </div> --}}

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

        $('#measures').select2({
            placeholder: 'Select an Option',
            allowClear: true,
            ajax: {
                url: '{{ route('getIndicator') }}',
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
                                text: item.measures
                            };
                        })
                    };
                },
                cache: true
            }
        });

        $('#measures').on('select2:select', function(e) {
            e.preventDefault();
            var id = e.params.data.id;
            const user_id = @json(Auth::user()->id);
            const selectedDivisions = $(this).val();

            $.ajax({
                url: '{{ route('entries.getMeasureDetails') }}',
                method: 'GET',
                data: {
                    id: id
                },
                success: function(response) {

                    // const divisionIds = Object.keys(response.division_targets);
                    $('#division_id_0').val(divisionIds).trigger('change');
                    console.log(response.measure.Q3_target);

                    $('#target').val(response.measure.target);
                    $('#Q1_target').val(response.measure.Q1_target);
                    $('#Q2_target').val(response.measure.Q2_target);
                    $('#Q3_target').val(response.measure.Q3_target);
                    $('#Q4_target').val(response.measure.Q4_target);
                    $('#alloted_budget').val(response.measure.alloted_budget)
                    $('#months_0').val(response.measure.months);
                    $('#measure_id').val(response.measure.id);
                    $('#division_id_0').val(null).change();


                    initializeDivisionSelect();

                    const index = 0;
                    var divisionIds = response.divisions;

                    $.each(divisionIds, function(index, division) {
                        var newOption = new Option(division.division_name, division.id, true, true);
                        $('#division_id_0').append(newOption);
                    });

                    $('#division_id_0').trigger('change');
                    console.log(response.division_ids);
                    updateTargetFields(0, response.division_ids, response.division_targets, response.division_budget, response.division_name);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });

        });

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

        function updateTargetFields(index, selectedDivisions, divisionTargets, divisionBudget, divisions) {
            const targetContainer = $(`#targetFields_${index}`);
            targetContainer.empty();

            const divisionNames = new Set();
            const initialBudget = parseFloat($(`#alloted_budget`).val()) || 0;

            if (selectedDivisions.length > 0) {
                selectedDivisions.forEach((divisionId) => {
                    const divisionName = $(`#division_id_${index} option[value="${divisionId}"]`).text();
                    const cleanedDivisionName = divisionName.replace(/\s*PO$/, '');

                    if (divisionName.includes("PO")) {
                        const targetValue = divisionTargets[divisionId] || '';
                        const budgetValue = divisionBudget[divisionId] || '';
                        const nameValue = divisions[divisionId] || '';


                            const targetHtml = `
                                <div class="col mb-3 target-budget-group" data-division-id="${divisionId}">
                                    <div class="form-group">
                                        <label for="target_${divisionId}_${index}" class="required">${nameValue} Target</label>
                                        <input type="text" class="form-control capitalize target-input" name="${cleanedDivisionName}_target[]" id="target_${divisionId}_${index}" aria-describedby="" value="${targetValue}" disabled>
                                        <div class="invalid-feedback" id="targetError_${divisionId}_${index}"></div>
                                    </div>
                                </div>
                                <div class="col mb-3 target-budget-group" data-division-id="${divisionId}">
                                    <div class="form-group">
                                        <label for="budget_${divisionId}_${index}" class="required">${nameValue} Budget</label>
                                        <input type="number" step="any"  class="form-control capitalize alloted-budget" name="${cleanedDivisionName}_budget[]" id="budget_${divisionId}_${index}" value="${budgetValue}" aria-describedby="">
                                        <div class="invalid-feedback" id="budgetError_${divisionId}_${index}"></div>
                                    </div>
                                </div>
                            `;
                            targetContainer.append(targetHtml);

                            const targetInput = $(`#target_${divisionId}_${index}`);
                            targetInput.on('input', function() {
                                let total = 0;
                                $(`#targetFields_${index} .target-input`).each(function() {
                                    const value = parseFloat($(this).val().replace('%', ''));
                                    if (!isNaN(value)) {
                                        total += value;
                                    }
                                });

                                $(`#target_${index}`).val(total);
                            });
                            const budgetInput = $(`#budget_${divisionId}_${index}`);
                            budgetInput.on('input', function() {
                            let totalBudget = parseFloat($('#alloted_budget').val()) || 0;
                            let currentBudgetValue = parseFloat(budgetInput.data('initial-value')) || 0;

                            if (isNaN(totalBudget)) {
                                totalBudget = 0;
                            }

                            if (isNaN(currentBudgetValue)) {
                                currentBudgetValue = 0;
                            }

                            const newValue = parseFloat(budgetInput.val()) || 0;
                            totalBudget = totalBudget - currentBudgetValue + newValue;

                            budgetInput.data('initial-value', newValue);
                            $('#alloted_budget').val(totalBudget);
                        });
                        // Store the initial budget value for accurate calculations
                        budgetInput.data('initial-value', budgetValue);
                    } else {
                        $('.percent').removeClass('d-none');
                    }
                });
            }
        }


        $('#NewIndicatorForm').on('submit', function(e) {
            e.preventDefault();
            showLoader();

            $.ajax({
                url: '{{ route('indicator.update_nonSuperAdmin') }}',
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

                        $('.measure-select').each(function() {
                            const index = $(this).attr('id').split('_').pop();
                            if ($(this).val().length === 0) { // Check if no value is selected
                                $(`#measures`).addClass('is-invalid');
                                $(`#measuresError`).html('Please select at least one measure.').show();
                                isValid = false;
                            } else {
                                $(`#measuresError`).html('').hide();
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

    });
</script>
@endsection
