@php
    
    $api_token = App\Helper\Helper::getCurrentuserToken();
    $userdata = Cache::get('userdata-' . $api_token);
@endphp
{{-- @dd($data_list) --}}
<x-app-layout>
    @section('title', 'Attendence')
    <style>
        #attendence_listing th,
        #attendence_listing td {
            white-space: nowrap;
        }
    </style>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0 pb-0 fw-500">
            <li class="breadcrumb-item"><a href="#" class="text-dark tx-16">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hrm.attendance') }}">{{ __('hrm.attendence') }}</a></li>
        </ol>
    </nav>

    <div class="card contact-content-body">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="tx-15 mg-b-0">{{ __('hrm.attendence_list') }}</h6>
                <a href="{{ route('hrm.attendance-create') }}" class="btn btn-sm btn-bg"><i
                        data-feather="plus"></i>{{ __('hrm.mark_attendence') }}</a>
            </div>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group  col-md-4">
                    <select class="form-control selectsearch" id="user_id" name="user_id">
                        <option value="all">All</option>
                        @if (!empty($user_data))
                            @foreach ($user_data as $key => $user_list)
                                <option value="{{ $user_list->id }}">{{ $user_list->first_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <select class="form-control selectsearch" id="month" name="month">
                        <option value="all">Select Month</option>
                        <option @if ($month == '01') selected @endif value="1">January</option>
                        <option @if ($month == '02') selected @endif value="2">Feb</option>
                        <option @if ($month == '03') selected @endif value="3">Mar</option>
                        <option @if ($month == '04') selected @endif value="4">Apr</option>
                        <option @if ($month == '05') selected @endif value="5">May</option>
                        <option @if ($month == '06') selected @endif value="6">June</option>
                        <option @if ($month == '07') selected @endif value="7">July</option>
                        <option @if ($month == '08') selected @endif value="8">Aug</option>
                        <option @if ($month == '09') selected @endif value="9">Sept</option>
                        <option @if ($month == '10') selected @endif value="10">Oct</option>
                        <option @if ($month == '11') selected @endif value="11">Nov</option>
                        <option @if ($month == '12') selected @endif value="12">Dec</option>

                    </select>
                </div>
                <div class="form-group   col-md-4">
                    <select class="form-control selectsearch" name="year" id="year">
                        <option value="all">Select Year</option>
                        @for ($i = $year; $i >= $year - 4; $i--)
                            <option @if ($i == $year) selected @endif value="{{ $i }}">
                                {{ $i }}</option>
                        @endfor
                    </select>
                </div>

            </div>
            <div class="table-responsive">
                <table class="table border table_wrapper" id="attendence_listing">
                    <thead>
                        @php
                            $date = 0;
                        @endphp

                        <tr>
                            <th>{{ __('hrm.employee_name') }}</th>
                            @for ($date = 1; $date <= $days; $date++)
                                <th>{{ $date }}</th>
                            @endfor

                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($employeeAttendence))
                            @foreach ($employeeAttendence as $key => $values)
                                {{-- @dd($employeeAttendence) --}}
                                @php
                                    $input = explode('#', $key);
                                @endphp
                                <tr>
                                    <td>{{ $input[1] }}</td>

                                    @foreach ($values as $date => $status)
                                        <td>
                                            @if ($status == 'Present')
                                                <i class="fa fa-check text-primary" aria-hidden="true"></i>
                                            @elseif($status == 'Absent')
                                                <i class="fa fa-times text-muted" aria-hidden="true"></i>
                                            @elseif($status == 'Leave')
                                                <i class="fa fa-leaf text-warning" aria-hidden="true"></i>
                                            @elseif($status == 'Holiday')
                                                <i class="fa fa-star text-warning" aria-hidden="true"></i>
                                            @else
                                                {{ $status }}
                                            @endif
                                        </td>
                                    @endforeach

                                </tr>

                                <tr>
                                    {{-- <td class="w-30">{{ $values->first_name }}</td>
                                    <td>
                                        <i class="{{ $value->status == 'present' ? 'fa fa-check' : 'fa fa-times' }}"
                                                aria-hidden="true"></i>

                                    </td> --}}
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('scripts')
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

            $(document).ready(function() {

                $('#user_id,#month,#year').on('change', function(e, data) {

                    if ($('#user_id').val() != "all") {

                        ajaxSubsmisstionData();
                    } else if ($('#month').val() != "all") {

                        ajaxSubsmisstionData();
                    } else if ($('#year').val() != "all") {

                        ajaxSubsmisstionData();
                    } else {

                        ajaxSubsmisstionData();
                    }

                });
            });


            function ajaxSubsmisstionData() {
                var user_id = $('#user_id').val();
                var month = $('#month').val();
                var year = $('#year').val();
                $("#attendence_listing").html('');
                tableWebContent(user_id, month, year);
            }

            function tableWebContent(user_id, month, year) {

                const url = "{{ route('hrm.attendance-data') }}";
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        user_id: user_id,
                        month: month,
                        year: year,

                    },
                    dataType: "json",
                    success: function(result) {
                        console.log(result);
                        var html = `
                                <table class="table table-center bg-white mb-0">
                                <thead>
                                <th class="wd-15p" style="">Employee Name</th>`;

                        for (let i = 0; i <= result.daysInMonth; i++) {


                            html += ` <th>${ i }</th>`;

                        };
                        html += `</thead>`;

                        $.each(result.employeeAttendence, function(key, values) {
                            var result = key.split('#');

                            html += `<tr>
                                        <td>${ result[1] }</td> `;

                            $.each(values, function(date, status) {

                                html += `<td>
                                                 
                                           ${(status == 'Present') ? '<i class="fa fa-check text-primary" aria-hidden="true"></i>' : (status == 'Absent')? '<i class="fa fa-times text-muted" aria-hidden="true"></i>' : (status == 'Leave') ? '<i class="fa fa-leaf text-warning" aria-hidden="true"></i>': (status == 'Holiday') ? '<i class="fa fa-star text-warning" aria-hidden="true"></i>': status  } 
                                                    
                                                </td>`;
                            });

                            html += `</tr>`;
                        });

                        html += `</table>
                        </div>`;

                        $("#attendence_listing").html(html);


                    }

                });

            }
        </script>
    @endpush
</x-app-layout>
