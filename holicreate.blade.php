<x-app-layout>
    @section('title', 'Holiday')

    <div class="contact-content">
        <div class="layout-specing">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent px-0">

                    <li class="breadcrumb-item"><a href="{{ url('hrm/leave') }}">{{ __('hrm.holyday') }}</a></li>
                </ol>
            </nav>
            <div class="card contact-content-body">
                <form action="{{ route('hrm.holiday.store') }}" id="userForm" method="POST" class="needs-validation"
                    novalidate>
                    @csrf
                    <div class="card-header d-flex align-items-center justify-content-between py-2 px-3">
                        <h6 class="tx-15 mg-b-0">{{ __('hrm.apply_for_holiday') }}</h6>

                    </div>
                    <div class="card-body">
                        <div data-label="Example" class="df-example demo-forms">
                            <div id="education_fields">
                                <div class="form-row">
                                    <div class="form-group col-md-5">
                                        <label class="form-label">{{ __('hrm.select_date') }} <span
                                                class="text-danger mg-l-5">*</span></label>
                                        <input name="holiday_date[]" value="" id="from_date" type=""
                                            class="form-control @error('start_date') is-invalid @enderror datepicker1"
                                            placeholder="{{ __('seo.start_date_placeholder') }}" required>

                                        <div class="invalid-feedback">
                                            {{ __('hrm.from_date_error') }}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-5 ">
                                        <label class="form-label">{{ __('hrm.occasion') }} <span
                                                class="text-danger mg-l-5">*</span></label>

                                        <input name="occasion[]" class="form-control" id="occasion" type="text"
                                            required>

                                        <div class="invalid-feedback">
                                            {{ __('hrm.occasion_type_error') }}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2 mt-4">
                                        <button class="btn btn-primary" type="button" id="add-more"><i
                                                class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div class="col-sm-12 mb-3 mx-3 p-0">
                            <input type="submit" id="submit" name="send" class="btn btn-primary" value="Submit">
                            <a href="{{ url('hrm/holiday') }}" class="btn btn-secondary mx-1">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')

        <script type="text/javascript">
           

            // Example starter JavaScript for disabling form submissions if there are invalid fields
            (function() {
                'use strict'
                var forms = document.querySelectorAll('.needs-validation')
                // Loop over them and prevent submission
                Array.prototype.slice.call(forms)
                    .forEach(function(form) {
                        form.addEventListener('submit', function(event) {
                            if (!form.checkValidity()) {
                                event.preventDefault()
                                event.stopPropagation()
                            }

                            form.classList.add('was-validated')
                        }, false)
                    })
            })()

            var room = 1;

            $('#add-more').click(function() {
                room++;

                var divtest = `
                <div class="row removeclass${room}">
                <div class="form-group col-md-5">
                    <label class="form-label">{{ __('hrm.select_date') }} <span class="text-danger mg-l-5">*</span></label>
                        <input name="holiday_date[]" value="" id="datepicker${room}" type="" class="form-control" placeholder="{{ __('seo.start_date_placeholder') }}" required>
                            <div class="invalid-feedback">
                                {{ __('hrm.from_date_error') }}
                            </div>
                </div>

                <div class="form-group col-md-5 ">
                    <label class="form-label">{{ __('hrm.occasion') }} <span class="text-danger mg-l-5">*</span></label>
                        <input name="occasion[]" class="form-control" id="occasion" type="text" required>
                        <div class="invalid-feedback">
                            {{ __('hrm.occasion_type_error') }}
                        </div>
                </div>
                <div class="form-group col-md-2 mt-4">
                    <button class="btn btn-danger" type="button" onclick="remove_education_fields(${room});">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
                    <div class="clear"></div>
                </div>`;

                $('#education_fields').append(divtest);
                $(`.removeclass${room} input`).focus();

                $(function() {
                $(`#datepicker${room}`).datepicker({
                    multidate: true,
                    format: 'dd/mm/yy'
                });

            });
            
            })

            function remove_education_fields(rid) {
                $('.removeclass' + rid).remove();
            }


            $(function() {
                $('.datepicker1').datepicker({
                    multidate: true,
                    format: 'dd/mm/yy'
                });

            });

            $('.selectsearch').select2({
                searchInputPlaceholder: 'Search options'
            });
        </script>
    @endpush
</x-app-layout>
