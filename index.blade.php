@php
    
    $api_token = App\Helper\Helper::getCurrentuserToken();
    $userdata = Cache::get('userdata-' . $api_token);
@endphp

<x-app-layout>
    @section('title', 'Leave')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0 pb-0 fw-500">
            <li class="breadcrumb-item"><a href="#" class="text-dark tx-16">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hrm.leave') }}">{{ __('hrm.leave') }}</a></li>
        </ol>
    </nav>

    <div class="card contact-content-body">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="tx-15 mg-b-0">{{ __('hrm.leave_apply_list') }}</h6>
                @if (!empty($userdata->userType == 'subscriber'))
                    <a href="{{ route('hrm.leave-create') }}" class="btn btn-sm btn-bg"><i
                            data-feather="plus"></i>{{ __('hrm.assign_for_leave') }}</a>
                @else
                    <a href="{{ route('hrm.leave-create') }}" class="btn btn-sm btn-bg"><i
                            data-feather="plus"></i>{{ __('hrm.apply_for_leave') }}</a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-2 col-lg-1 col-sm-3">
                    <select class="form-control">
                        <option>10</option>
                        <option>20</option>
                        <option>30</option>
                        <option>40</option>
                        <option>50</option>
                    </select>
                </div>
                <div class="form-group mg-l-5">
                    <input type="text" class="form-control" id="searchbar" placeholder="Search">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table border table_wrapper">
                    <thead>
                        <tr>
                            <th>{{ __('common.sl_no') }}</th>
                            <th>{{ __('hrm.employee_name') }}</th>
                            <th>{{ __('hrm.from_date') }}</th>

                            <th>{{ __('hrm.leave_type') }}</th>
                            <th>{{ __('hrm.status') }}</th>

                            <th class="wd-10p text-center">{{ __('common.action') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if (!empty($leave_list))

                            @foreach ($leave_list as $key => $leave) 
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $leave->user->first_name }}{{ $leave->user->last_name }}</td>
                                    <td>{{ $leave->leave_date }}</td>

                                    <td>
                                        @if (!empty($leave->leave_type))
                                            {{ $leave->leave_type->leave_type_name }}
                                        @endif
                                    </td>
                                    <td>

                                        {{ $leave->status }}
                                    </td>

                                    <td class="d-flex align-items-center">
                                        <a href="{{ url('hrm/leave/edit/' . $leave->id) }}"
                                            data-task-id="{{ $leave->id }}"
                                            class="btn btn-sm btn-white d-flex align-items-center mg-r-5"><i
                                                data-feather="edit-2"></i><span
                                                class="d-none d-sm-inline mg-l-5"></span></a>

                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
          <!--Pagination Start-->
          {!! \App\Helper\Helper::make_pagination(
            $total_records,
            $per_page,
            $current_page,
            $total_page,
            url('hrm/leave'),
        ) !!}
        <!--Pagination End-->
    </div>

</x-app-layout>
