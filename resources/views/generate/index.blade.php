
@extends('components.app')

@section('content')

<div class="row">
    <div class="col">

    </div>
</div>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Generate Report</h4>
                {{-- <p class="card-description"> Add class <code>.table-bordered</code> --}}
                </p>
                <div class="row">
                    <div class="col">
                        <form id="GenerateForm">
                            <div class="form-group mb-3">
                                <label for="">File Type:</label>
                                @can('generate_report_excel')
                                <div class="form-check form-check-inline excel">
                                    <input class="form-check-input" type="radio" name="fileType" id="excel" value="excel">
                                    <label class="form-check-label" for="excel">excel</label>
                                </div>
                                @endcan
                                @can('generate_report_pdf')
                                <div class="form-check form-check-inline pdf">
                                    <input class="form-check-input" type="radio" name="fileType" id="pdf" value="pdf">
                                    <label class="form-check-label" for="pdf">pdf</label>
                                </div>
                                @endcan
                                @can('generate_report_doc')
                                <div class="form-check form-check-inline word">
                                    <input class="form-check-input" type="radio" name="fileType" id="word" value="word">
                                    <label class="form-check-label" for="word">doc</label>
                                </div>
                                @endcan
                            </div>

                            @csrf
                            {{-- <div class="form-group mb-4">
                                <label for="flatpickr-date" class="form-label">Select a date range</label>
                                <input type="text" id="date-range-picker" name="created_at" class="form-control" placeholder="YYYY-MM-DD" id="flatpickr-date" />
                            </div> --}}

                            <div class="form-group mb-3 me-3">
                                <label for="year" class="required">Year</label>
                                <select id="year" class="form-select" name="year">
                                    <option value="">Select Year</option>
                                    @for ($i = date('Y'); $i >= 2020; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="form-group mb-3 me-3 d-none" id="period">
                                <label for="period">Period</label>
                                <select id="period" class="form-select" name="period">
                                    <option value="">Select Period</option>
                                    <optgroup label="Quarter">
                                        <option value="Q1">Q1 (Jan-Mar)</option>
                                        <option value="Q2">Q2 (Apr-Jun)</option>
                                        <option value="Q3">Q3 (Jul-Sep)</option>
                                        <option value="Q4">Q4 (Oct-Dec)</option>
                                    </optgroup>
                                    <optgroup label="Semestral" id="semestral-optgroup">
                                        <option value="H1">S1 (Jan-Jun)</option>
                                        <option value="H2">S2 (Jul-Dec)</option>
                                    </optgroup>
                                </select>
                            </div>

                            <div class="form-group mb-3 me-3 d-none" id= "division">
                                <label for="division_id">Division</label>
                                <select id="division_id" class="division-select form-select" name="division_id[]">
                                </select>
                                <div class="invalid-feedback" id="division_idError"></div>
                            </div>

                            <div class="d-flex justify-content-end">
                                @can('generate_report_pdf')
                                <button type="submit" class="btn btn-primary pdf-button" style="display: none;">Generate PDF</button>
                                @endcan
                                @can('generate_report_doc')
                                <button type="submit" class="btn btn-primary doc-button" style="display: none;">Generate Doc</button>
                                @endcan
                                @can('generate_report_excel')
                                <button type="submit" class="btn btn-primary excel-button" style="display: none;">Generate Excel</button>
                                @endcan
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('components.specific_page_scripts')

<script>
        flatpickr("#date-range-picker", {
            mode: "range",
            dateFormat: "m/d/Y",
            onChange: function(selectedDates, dateStr, instance) {
                // Check if both start and end dates are selected
                if (selectedDates.length === 2) {

                }
            }
        });

        $('#GenerateForm').on('submit', function(e) {
            e.preventDefault();
            showLoader();

            var year = $('#year').val();
            var divisions = $('#division_id').val();

            if (year === "") {
                hideLoader();
                Swal.fire({
                    icon: 'warning',
                    title: 'Year Required',
                    text: 'Please select a year before generating the report.',
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                });
                return;
            }

            $.ajax({
                url: '{{ route('generate.pdf') }}',
                type: 'POST',
                data: $(this).serialize(),
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(blob) {
                    hideLoader();

                    var filename = year + '-OPCR-RO5.pdf';
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'The report has been generated and downloaded.',
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    });
                },
                error: function(xhr) {
                    hideLoader();
                    console.log(xhr);
                }
            });
        });

        $('.doc-button').on('click', function(e) {
            e.preventDefault();
            showLoader();

            var year = $('#year').val();
            var divisions = $('#division_id').val();
            var period = $('#period').val();

            if (year === "") {
                hideLoader();
                Swal.fire({
                    icon: 'warning',
                    title: 'Year Required',
                    text: 'Please select a year before generating the report.',
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                });
                return;
            }

            $.ajax({
                url: '{{ route('generate.word') }}',
                type: 'POST',
                data: {
                    year: year,
                    division_id: divisions,
                    period: period,
                    _token: '{{ csrf_token() }}'
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(blob) {
                    hideLoader();

                    var filename = year + '-OPCR-RO5.docx'; // Changed from .pdf to .docx as per the controller logic
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'The report has been generated and downloaded.',
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    });
                },
                error: function(xhr) {
                    hideLoader();
                    console.log(xhr);
                }
            });
        });

        $('.excel-button').on('click', function(e) {
            e.preventDefault();
            showLoader();

            var year = $('#year').val();
            var divisions = $('#division_id').val();

            if (year === "") {
                hideLoader();
                Swal.fire({
                    icon: 'warning',
                    title: 'Year Required',
                    text: 'Please select a year before generating the report.',
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                });
                return;
            }

            var formData = $('#GenerateForm').serialize();

            $.ajax({
                url: '{{ route('export') }}',
                type: 'GET',
                data: formData,
                xhrFields: {
                    _token: '{{ csrf_token() }}',
                    responseType: 'blob'
                },
                success: function(blob) {
                    hideLoader();

                    var filename =year +'-Physical Accomplishment.xlsx';
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'The report has been generated and downloaded.',
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    });
                },
                error: function(xhr) {
                    hideLoader();
                    console.log(xhr);
                }
            });
        });

</script>


<script>
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

</script>


<script>
    $(document).ready(function() {
        // var semestralOptgroup = $('#semestral-optgroup').detach();
        $('#division').removeClass('d-none');
        $('#period').removeClass('d-none');

        // On radio button change event
        $('input[name="fileType"]').on('change', function() {
            var selectedType = $('input[name="fileType"]:checked').val();

            // Show corresponding button based on the selected radio button
            if (selectedType === 'excel') {
                $('.excel-button').show();
                $('.pdf-button').hide();
                $('.doc-button').hide();

                // $('#semestral-optgroup').remove();

                $('#division').addClass('d-none');
                $('#period').addClass('d-none');

            } else if (selectedType === 'pdf') {
                $('.pdf-button').show();
                $('.excel-button').hide();
                $('.doc-button').hide();


                // if (!$('#semestral-optgroup').length) {
                //     $('#period').append(semestralOptgroup);
                // }
                $('#division').removeClass('d-none');
                $('#period').removeClass('d-none');

            }else if (selectedType === 'word') {
                $('.pdf-button').hide();
                $('.doc-button').show();
                $('.excel-button').hide();

                // if (!$('#semestral-optgroup').length) {
                //     $('#period').append(semestralOptgroup);
                // }
                $('#division').removeClass('d-none');
                $('#period').removeClass('d-none');

            }
        });
    });
</script>
@endsection
