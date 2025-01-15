@extends('components.app')

@section('content')

<div class="container mt-5">

    <form id="NewEntriesForm" enctype="multipart/form-data">
        @csrf
        <div class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"> <a href="/entries" class="text-primary"><i class='bx bx-left-arrow-circle'></i></a>
                            Edit</h4>
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
                                <div class="col">
                                    <div class="mb-3">
                                        <div class="form-group">
                                            <input type="hidden" name="id" value="{{ $entries->id }}">
                                            <label for="edit_indicator_id" class="required">Indicator</label>
                                            <select id="edit_indicator_id" class="form-select capitalize" name="indicator_id" disabled>
                                                @if($entries)
                                                    <option value="{{ $entries->indicator_id }}" selected>{{ '('. $entries->indicator->target. ')'. '  ' .$entries->indicator->measures }}</option>
                                                @endif
                                            </select>
                                            <div id="indicator_idError" class="invalid-feedback"></div>

                                            <div class="mb-3">
                                                <div class="form-group">
                                                    <label for="accomplishment" class="required">Accomplishment</label>
                                                    <textarea type="text" id="accomplishment" class="form-control" name="accomplishment">
                                                        {{ $entries->accomplishment }}
                                                    </textarea>
                                                    <div id="accomplishmentError" class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                                            <label for="file" class="required">Upload</label>  <a href="{{ $fileUrl }}" target="_blank">Click here to download current file</a>
                                            <input class="form-control" name="file" type="file" id="file">
                                            <div id="fileError" class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
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

    $('#edit_indicator_id').select2({
            placeholder: 'Select an Option',
            // allowClear: true,
            ajax: {
                url: '{{ route('entries.getIndicator') }}',
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
                                text: '(' + item.target+ ')' + '  ' + item.measures                        };
                        })
                    };
                },
                cache: true
            }
        });

    // // Get today's date in the format "YYYY-MM-DD"
    // const today = new Date().toISOString().split('T')[0];

    // flatpickr("#date-received", {
    //     mode: "range",
    //     dateFormat: "m/d/Y",
    //     defaultDate: today,  // Set today's date as the default
    //     onReady: function(selectedDates, dateStr, instance) {
    //         instance.input.disabled = true;  // Disable the input field
    //     }
    // });

    

    $('#NewEntriesForm').on('submit', function(e) {
        e.preventDefault();
        showLoader();
        var formData = new FormData(this);

        $.ajax({
            url: '{{ route('entries.update') }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                hideLoader();
                window.location.href = '/entries';
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
