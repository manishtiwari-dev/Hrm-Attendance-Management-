<x-app-layout>
    @section('title', 'Holiday')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0 pb-0 fw-500">
            <li class="breadcrumb-item"><a href="#" class="text-dark tx-16">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hrm.holiday.index') }}">{{ __('hrm.holyday') }}</a></li>
        </ol>
    </nav>

    <div class="card contact-content-body">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="tx-15 mg-b-0">{{ __('hrm.holyday_list') }}</h6>

                <div>
                    <a href="#modal1" data-toggle="modal" id="add_title_btn" class="btn btn-sm btn-bg mg-r-5"><i
                            data-feather="plus"></i><span
                            class="d-none d-sm-inline mg-l-5">{{ __('hrm.mark_default_holyday') }}</span></a>

                    <a href="{{ route('hrm.holiday.create') }}" class="btn btn-sm btn-bg"><i
                            data-feather="plus"></i>{{ __('hrm.add_holyday') }}</a>
                </div>
            </div>
        </div>
        <div class="card-body">

            <div id='calendar'></div>
        </div>
        <!--Pagination Start-->

        <!--Pagination End-->
    </div>


    <!--------------Add Result Modal --------------->
    <div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content tx-14">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ __('hrm.mark_holiday') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" id="add_holiday" novalidate>

                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label">{{ __('hrm.mark_day_for_holiday') }}<span
                                        class="text-danger">*</span></label>
                                <div class="form-icon position-relative">

                                    @forelse ($holidaysArray as $key => $holidayData)
                                        <label class="form-label">{{ $holidayData }}</label>
                                        <input type="checkbox" name="office_holiday_days[]" id="{{ $key }}"
                                            value="{{ $key }}">
                                        <div class="invalid-feedback">
                                            <p>{{ __('seo.title_error') }}</p>
                                        </div>
                                    @endforeach


                                    @forelse ($holSatArray as $key => $holiSatData)
                                        <label class="form-label">{{ $holiSatData }}</label>
                                        <input type="checkbox" name="office_saturday_days[]" id="{{ $key }}"
                                            value="{{ $key }}">
                                        <div class="invalid-feedback">
                                            <p>{{ __('seo.title_error') }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-12 mt-4" required>
                                <input type="button" id="holiday_submit" class="btn btn-primary" value="Submit">
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <!-------------- Add Result Modal end here --------------->

    @push('scripts')
        @php
            $date = [];
            $occassion = $events_ary = [];
            
            foreach ($events as $event) {
                $events_ary_ele = [
                    'title' => $event->occassion,
                    'start' => $event->date,
                ];
                array_push($events_ary, $events_ary_ele);
            }
            
            $events_ary = json_encode($events_ary);
            
        @endphp

        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.6/index.global.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {

                    initialView: 'dayGridMonth',
                    initialDate: '2023-04-15',
                    events: @php echo $events_ary @endphp
                });
                calendar.render();
            });
        </script>
        <script>
            $(document).on("click", "#holiday_submit", function(e) {
                e.preventDefault();


                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('hrm.markDayHoliday') }}",
                    type: "POST",
                    data: $('#add_holiday').serialize(),
                    success: function(response) {

                        $('#modal1').removeClass('show');
                        $('#modal1').css('display', 'none');
                        if (response.success) {
                            Toaster(response.success);

                        } else {
                            Toaster(response.error);
                        }

                        setTimeout(function() {
                            location.reload(true);
                        }, 3000);

                        window.location.href = "{{ route('hrm.holiday.index') }}";

                    },


                });
            });
        </script>
    @endpush

</x-app-layout>
