@php
    
    $api_token = App\Helper\Helper::getCurrentuserToken();
    $userdata = Cache::get('userdata-' . $api_token);
    
@endphp

<x-app-layout>
    @section('title', 'Attendence')

    <div class="contact-content">
        <div class="layout-specing">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent px-0">

                    <li class="breadcrumb-item"><a href="{{ url('hrm/attendance') }}">{{ __('hrm.attendence') }}</a></li>
                </ol>
            </nav>
            <div class="card contact-content-body">
                <form action="{{ route('hrm.attendance-store') }}" id="userForm" method="POST" class="needs-validation"
                    novalidate>
                    @csrf
                    <div class="card-header d-flex align-items-center justify-content-between py-2 px-3">
                        <h6 class="tx-15 mg-b-0">{{ __('hrm.mark_attendence') }}</h6>

                    </div>
                    <div class="card-body">
                        <div data-label="Example" class="df-example demo-forms">
                            <div class="form-row">
                                <div class="form-group col-md-4 ">
                                    <label class="form-label">{{ __('hrm.department') }} <span
                                            class="text-danger mg-l-5">*</span></label>
                                    <select
                                        class="form-control department @error('leave_type_id') is-invalid @enderror "
                                        id="leave_type_id" name="" required>
                                        <option selected disable value="" disabled>
                                            {{ __('hrm.department_select') }}</option>

                                        @if (!empty($dept_list))
                                            @foreach ($dept_list as $dept)
                                                <option value="{{ $dept->department_id }}">
                                                    {{ $dept->dept_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                    <div class="invalid-feedback">
                                        {{ __('hrm.department_error') }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4 ">
                                    <label class="form-label">{{ __('hrm.user') }} </label>
                                    <select
                                        class="form-control selectsearch  selectuser @error('id') is-invalid @enderror "
                                        multiple="multiple" id="user_id" name="user_id[]">
                                        <option selected disable value="" disabled>{{ __('hrm.user_id_select') }}
                                        </option>

                                    </select>
                                </div>

                                <div class="form-group col-md-4 ">
                                    <label class="form-label">{{ __('hrm.working') }} <span
                                            class="text-danger mg-l-5">*</span></label>

                                    <select class="form-control selectsearch @error('id') is-invalid @enderror "
                                        id="status" name="working_from" "
                                         >
                                    <option value ="">{{ __('hrm.select_working') }}</option>
                                        <option value =" Office">
                                        Office
                                        </option>
                                        <option value="Home">
                                            Home
                                        </option>


                                    </select>
                                    <div class="invalid-feedback">
                                        {{ __('hrm.reason_error') }}
                                    </div>
                                </div>

                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="form-label">{{ __('hrm.mark_attendence') }} <span
                                            class="text-danger mg-l-5"></label> </br>

                                    <input type="radio" id="months" name="mark_attendence" value="1">
                                    <label for="Choice1">Month</label>
                                    <input type="radio" id="date_select" name="mark_attendence" value="0">
                                    <label for="Choice2">Date</label>

                                    <div class="invalid-feedback">
                                        {{ __('hrm.reason_error') }}
                                    </div>
                                </div>
                                <div class="form-group col-md-3" id="select_date">
                                    <label class="form-label">{{ __('hrm.select_date') }} <span
                                            class="text-danger mg-l-5">*</span></label>
                                    <input name="attendence_date" value="" id="" type=""
                                        class="form-control @error('start_date') is-invalid @enderror datepicker1"
                                        placeholder="{{ __('seo.start_date_placeholder') }}">

                                    <div class="invalid-feedback">
                                        {{ __('hrm.from_date_error') }}
                                    </div>
                                </div>
                                <div class="form-group col-md-3 year">

                                    <label class="form-label">{{ __('hrm.year') }} <span
                                            class="text-danger mg-l-5"></label>

                                    <select class="form-control selectsearch @error('id') is-invalid @enderror "
                                        id="year" name="year">
                                        <option value="">Select Year</option>
                                        @for ($i = $year; $i >= $year - 4; $i--)
                                            <option @if ($i == $year) selected @endif
                                                value="{{ $i }}">{{ $i }}</option>
                                        @endfor


                                    </select>
                                    <div class="invalid-feedback">
                                        {{ __('hrm.reason_error') }}
                                    </div>
                                </div>
                                <div class="form-group col-md-3 month">
                                    <label class="form-label">{{ __('hrm.month') }} <span
                                            class="text-danger mg-l-5">*</span></label>

                                    <select class="form-control selectsearch @error('id') is-invalid @enderror "
                                        id="month" name="month">
                                        <option value="">Select Month</option>
                                        <option value="1">January</option>
                                        <option value="2">Feb</option>
                                        <option value="3">Mar</option>
                                        <option value="4">Apr</option>
                                        <option value="5">May</option>
                                        <option value="6">June</option>
                                        <option value="7">July</option>
                                        <option value="8">Aug</option>
                                        <option value="9">Sept</option>
                                        <option value="10">Oct</option>
                                        <option value="11">Nov</option>
                                        <option value="12">Dec</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        {{ __('hrm.reason_error') }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">

                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('hrm.clock_in') }}<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <input type="text" id="check_in" name="clock_in_time"
                                            class="form-control @error('check_in') is-invalid @enderror"
                                            placeholder="{{ __('campaign.chose_from_time') }}"
                                            aria-label="Recipient's username" aria-describedby="basic-addon2"
                                            required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary"
                                                onclick="showpickers('check_in',24)" type="button"><i
                                                    class="fa fa-clock-o"></i></button>
                                        </div>
                                    </div>
                                    <div class="timepicker"></div>
                                    <span class="text-danger">
                                        @error('check_in')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                    <div class="invalid-feedback">
                                        {{ __('campaign.from_time_error') }}
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('hrm.clock_out') }}<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <input type="text" id="check_out" name="clock_out_time"
                                            class="form-control @error('check_out') is-invalid @enderror"
                                            placeholder="{{ __('campaign.chose_to_time') }}"
                                            aria-label="Recipient's username" aria-describedby="basic-addon2"
                                            required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary"
                                                onclick="showpickers('check_out',24)" type="button"><i
                                                    class="fa fa-clock-o"></i></button>
                                        </div>
                                    </div>
                                    <div class="timepicker"></div>
                                    <span class="text-danger">
                                        @error('check_out')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                    <div class="invalid-feedback">
                                        {{ __('campaign.to_time_error') }}
                                    </div>
                                </div>
                                <div class="form-group col-md-2 mt-4 ">
                                    <label class="form-label">{{ __('hrm.late') }} <span
                                            class="text-danger mg-l-5"></label><br />
                                    <input type="radio" id="contactChoice1" name="late" value="yes">
                                    <label for="Choice1">Yes</label>
                                    <input type="radio" id="contactChoice2" name="late" value="no">
                                    <label for="Choice2">No</label>

                                    <div class="invalid-feedback">
                                        {{ __('hrm.reason_error') }}
                                    </div>
                                </div>
                                <div class="form-group col-md-2 mt-4">
                                    <label class="form-label">{{ __('hrm.half_day') }}</label><br />

                                    <input type="radio" id="contactChoice1" name="half_day" value="yes">
                                    <label for="Choice1">Yes</label>
                                    <input type="radio" id="contactChoice2" name="half_day" value="no">
                                    <label for="Choice2">No</label>

                                    <div class="invalid-feedback">
                                        {{ __('campaign.from_time_error') }}
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="">
                        <div class="col-sm-12 mb-3 mx-3 p-0">
                            <input type="submit" id="submit" name="send" class="btn btn-primary"
                                value="Submit">
                            <a href="{{ url('hrm/attendance') }}" class="btn btn-secondary mx-1">Cancel</a>
                        </div>
                    </div>
                </form>


            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(function() {
                $('.datepicker1').datepicker({
                    multidate: true,
                    format: 'dd/mm/yy',
                    onSelect: function() {
                        var selected = $(this).datepicker("getDate");
                    }


                });

            });


            $(document).ready(function() {
                $("#months").click(function(event) {
                    $("#select_date").hide();
                    $(".year").show();
                    $(".month").show();

                });
            });
            //select date
            $(document).ready(function() {
                $("#date_select").click(function(event) {

                    $(".year").hide();
                    $(".month").hide();
                    $("#select_date").show();

                });
            });
        </script>


        <script type="text/javascript">
            $('.selectsearch').select2({
                searchInputPlaceholder: 'Search options'
            });


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
        </script>

        <script type="text/javascript">
            // change status in ajax code start
            $('.department').change(function() {
                let dept_id = $(this).val();


                console.log(dept_id);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('hrm.attendance-dept') }}",

                    data: {

                        dept_id: dept_id
                    },
                    success: function(response) {
                        console.log(response[0].user_list)
                        $('.selectuser').empty();
                        var html = ``;
                        if ((response[0].user_list != '')) {

                            $.each(response[0].user_list, function(key, user) {
                                console.log(user);
                                html += `  <option value="${user.user_id } ">  
                                    
                                        ${user.staff_name }
                                   
                                    </option>`;

                            });
                        }

                        $(".selectuser").append(html);
                    },
                    error: function(error) {

                    }
                });
            });
            // chenge status in ajax code end  
        </script>
    @endpush
</x-app-layout>
