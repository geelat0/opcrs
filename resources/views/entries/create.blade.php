@extends('components.app')

@section('content')

<div class="container mt-5">

    <form id="NewEntriesForm">
        @csrf
        <div class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"> <a href="/accomplishment" class="text-primary"><i class='bx bx-left-arrow-circle'></i></a>
                            Add Accomplishment</h4>
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

                                <div class="form-group mb-3">
                                    <input type="hidden" id="indicator_id" name="indicator_id" value="{{ $entries->id }}">
                                    <label for="indicator" class="required">Indicator</label>
                                    <select id="indicator" class="form-select capitalize" >
                                        @if($entries)
                                            <option value="{{ $entries->indicator_id }}" selected>{{ '('. $entries->target. ')'. '  ' .$entries->measures }}</option>
                                        @endif
                                    </select>
                                    <div class="invalid-feedback" id="measuresError"></div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <div class="form-group">
                                            <label for="months">Month</label>
                                            <select id="months" class="months form-select" name="months" disabled>
                                                <option value="">Select Month</option>
                                                @for ($i = 1; $i <= 12; $i++)
                                                    <option selected>{{ date('F', mktime(0, 0, 0, $entries->months, 10)) }}</option>
                                                @endfor
                                            </select>
                                            <div class="invalid-feedback" id="monthsError"></div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <div class="form-group">
                                            <label for="file" class="required">Upload</label>
                                            <input class="form-control" name="file" type="file" id="file">
                                            <div id="fileError" class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row d-none">
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="division_id_0" class="required">Division</label>
                                        <select id="division_id_0" class="division-select form-select" name="division_id[0][]" multiple="multiple" disabled>
                                        </select>
                                        <div class="invalid-feedback" id="division_idError_0"></div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                             {{-- CONTAINER FOR TARGET AND BUDGET --}}

                             <label for="division_id_0 mb-3">Division</label>

                             <div class="row row-cols-3 mb-3" id="targetFields_0" >
                                <div class="col mb-4" >

                                </div>
                            </div>

                            <div class="row">
                                {{-- <label for="accomplishment" class="required">Accomplishment</label> --}}
                                <div class="col">
                                    <div class="mb-3">
                                        <div class="form-group">
                                            <label for="total_accomplishment" class="required">Accomplishment Total</label>
                                            <input type="number" id="total_accomplishment" class="form-control" name="total_accomplishment">
                                            <div id="total_accomplishmentError" class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <div class="form-group">
                                            <label for="accomplishment_text" class="required">Accomplishment Remark</label>
                                            <textArea type="text" id="accomplishment_text" class="form-control" name="accomplishment_text">{{ trim($entries->measures) }}</textArea>
                                            <div class="invalid-feedback" id="accomplishment_textError"></div>
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

        var selectedIndicatorId = {!! json_encode($entries->id) !!};

        // Check if there is a selected indicator ID
        if (selectedIndicatorId) {
            showLoader();
            $.ajax({
                url: '/getIndicatorById/' + selectedIndicatorId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Create the option in Select2 and select it
                    var newOption = new Option(data.text, data.id, true, true);
                    $('#indicator').append(newOption).trigger('change');
                    $('#indicator').prop('disabled', true);

                    getMeasureDetails(data.id);

                },
                error: function(xhr) {
                    console.log('Error fetching indicator:', xhr.responseText);
                }
            });

            // Function to call indicator.getMeasureDetails
            function getMeasureDetails(indicatorId) {
                const user_id = @json(Auth::user()->id);

                $.ajax({
                    url: '{{ route('entries.getMeasureDetails') }}',
                    method: 'GET',
                    data: {
                        id: indicatorId
                    },
                    success: function(response) {
                        // Populate form fields with measure details
                        $('#division_id_0').val(response.division_ids).trigger('change');
                        $('#target').val(response.measure.target);
                        $('#alloted_budget').val(response.measure.alloted_budget);
                        $('#months_0').val(response.measure.months);
                        $('#measure_id').val(response.measure.id);

                        $('#division_id_0').val(null).change(); // Clear the division select field

                        initializeDivisionSelect();

                        const index = 0;
                        const divisionIds = response.divisions;

                        // Loop through divisionIds and add them to the division select field
                        $.each(divisionIds, function(index, division) {
                            var newOption = new Option(division.division_name, division.id, true, true);
                            $('#division_id_0').append(newOption);
                        });

                        $('#division_id_0').trigger('change');
                        updateTargetFields(0, response.division_ids, response.division_targets, response.division_budget, response.division_name);
                        hideLoader();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }
        }


        function initializeDivisionSelect() {
            $('.division-select').select2({
                placeholder: 'Select an Option',
                allowClear: true,
                ajax: {
                    url: '{{ route('indicator.getDivision') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term // search term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
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
                        // const targetValue = divisionTargets[divisionId] || '';
                        const AccomValue = divisionBudget[divisionId] || '';
                        const nameValue = divisions[divisionId] || '';


                            const targetHtml = `
                                <div class="col mb-3 target-accomplisment-group" data-division-id="${divisionId}">
                                    <div class="form-group">
                                        <label for="accomplisment_${divisionId}_${index}" class="required">${nameValue} Accomplisment</label>
                                        <input type="number" step="any"  class="form-control capitalize accomplisment" name="${cleanedDivisionName}_accomplishment[]" id="accomplisment_${divisionId}_${index}" aria-describedby="">
                                        <div class="invalid-feedback" id="accomplismentError_${divisionId}_${index}"></div>
                                    </div>
                                </div>
                            `;
                            targetContainer.append(targetHtml);


                            const AccomInput = $(`#accomplisment_${divisionId}_${index}`);
                            AccomInput.on('input', function() {
                            let totalAccomplisment = parseFloat($('#total_accomplishment').val()) || 0;
                            let currentAccomValue = parseFloat(AccomInput.data('initial-value')) || 0;

                            if (isNaN(totalAccomplisment)) {
                                totalAccomplisment = 0;
                            }

                            if (isNaN(currentAccomValue)) {
                                currentAccomValue = 0;
                            }

                            const newValue = parseFloat(AccomInput.val()) || 0;
                            totalAccomplisment = totalAccomplisment - currentAccomValue + newValue;

                            AccomInput.data('initial-value', newValue);
                            $('#total_accomplishment').val(totalAccomplisment);
                        });
                        // Store the initial budget value for accurate calculations
                        // AccomInput.data('initial-value', AccomValue);
                    } else {
                        $('.percent').removeClass('d-none');
                    }
                });
            }
        }


        $('#NewEntriesForm').on('submit', function(e) {
            e.preventDefault();
            showLoader();

            var formData = new FormData(this);

            // Re-disable the select field if needed

            $.ajax({
                url: '{{ route('entries.store') }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    hideLoader();
                    window.location.href = '/accomplishment';
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: false,
                        });

                        $('#NewEntriesForm')[0].reset(); // Reset the form
                    }
                },
                error: function(xhr) {
                    hideLoader();

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;

                        // Clear previous errors and remove red borders
                        $('.form-control').removeClass('is-invalid');
                        $('.invalid-feedback').html('').hide();

                        // Loop through the errors and update fields
                        for (let key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                const keyParts = key.split('.');
                                let fieldName = keyParts[0];

                                if (keyParts.length > 1) {
                                    const index = keyParts[1];
                                    fieldName = `${fieldName}_${index}`;
                                }

                                // Apply red border and show error message
                                $(`#${fieldName}`).addClass('is-invalid');
                                $(`#${fieldName}Error`).html(errors[key].join('<br>')).show();
                            }
                        }

                        // Show SweetAlert with a summary of errors
                        let errorMessages = '';
                        for (let key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                errorMessages += `<strong>${key}</strong>: ${errors[key].join('<br>')}<br>`;
                            }
                        }

                        if (errorMessages) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Errors!',
                                showConfirmButton: true,
                            });
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
