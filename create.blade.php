@php
    
    $api_token = App\Helper\Helper::getCurrentuserToken();
    $userdata = Cache::get('userdata-' . $api_token);
    
@endphp

<x-app-layout>
    @section('title', 'Leave')

    <div class="contact-content">
        <div class="layout-specing">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent px-0">

                    <li class="breadcrumb-item"><a href="{{ url('hrm/leave') }}">{{ __('hrm.leave') }}</a></li>
                </ol>
            </nav>
            <div class="card contact-content-body">
                <form action="{{ route('hrm.leave-store') }}" id="userForm" method="POST" class="needs-validation"
                    novalidate>
                    @csrf
                    <div class="card-header d-flex align-items-center justify-content-between py-2 px-3">
                        <h6 class="tx-15 mg-b-0">{{ __('hrm.apply_for_leave') }}</h6>

                    </div>
                    <div class="card-body">
                        <div data-label="Example" class="df-example demo-forms">
                            <div class="form-row">
                                <div class="form-group col-md-6 col-6">
                                    <label class="form-label">{{ __('hrm.select_date') }} <span
                                            class="text-danger mg-l-5">*</span></label>
                                    <input name="leave_date" value="" id="from_date" type=""
                                        class="form-control @error('start_date') is-invalid @enderror datepicker1"
                                        placeholder="{{ __('seo.start_date_placeholder') }}" required>

                                    <div class="invalid-feedback">
                                        {{ __('hrm.from_date_error') }}
                                    </div>
                                </div>

                                <div class="form-group col-md-6 ">
                                    <label class="form-label">{{ __('hrm.leave_type') }} <span
                                            class="text-danger mg-l-5">*</span></label>
                                    <select
                                        class="form-control selectsearch @error('leave_type_id') is-invalid @enderror "
                                        id="leave_type_id" name="leave_type_id" required>
                                        <option selected disable value="" disabled>
                                            {{ __('hrm.leave_type_select') }}</option>

                                        @if (!empty($leave_type))
                                            @foreach ($leave_type as $type)
                                                <option value="{{ $type->leave_type_id }}">
                                                    {{ $type->leave_type_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                    <div class="invalid-feedback">
                                        {{ __('hrm.leave_type_error') }}
                                    </div>
                                </div>

                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-6 ">
                                    <label class="form-label">{{ __('hrm.reason') }} <span
                                            class="text-danger mg-l-5">*</span></label>

                                    <textarea name="reason" rows="2" value="" class="form-control" required></textarea>
                                    <div class="invalid-feedback">
                                        {{ __('hrm.reason_error') }}
                                    </div>
                                </div>

                                @if (!empty($userdata->userType == 'subscriber'))
                                    <div class="form-group col-md-6 ">
                                        <label class="form-label">{{ __('hrm.user') }} </label>
                                        <select class="form-control selectsearch @error('id') is-invalid @enderror "
                                            id="user_id" name="user_id">
                                            <option selected disable value="" disabled>
                                                {{ __('hrm.user_id_select') }}</option>

                                            @if (!empty($user_list))
                                                @foreach ($user_list as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->first_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                @else
                                    <input name="user_id" value="{{ $userdata->userId }}" id="from_date"
                                        type="hidden">
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div class="col-sm-12 mb-3 mx-3 p-0">
                            <input type="submit" id="submit" name="send" class="btn btn-primary" value="Submit">
                            <a href="{{ url('hrm/leave') }}" class="btn btn-secondary mx-1">Cancel</a>
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
                    format: 'dd/mm/yy'
                    // onSelect: function() {
                    //     var selected = $(this).datepicker("getDate");
                    // }
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
    @endpush
</x-app-layout>
