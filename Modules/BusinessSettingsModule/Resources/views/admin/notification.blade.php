@extends('adminmodule::layouts.master')

@section('title',translate('notification_setup'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/select.dataTables.min.css"/>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('notification_setup')}}</h2>
                    </div>

                    <div class="card mb-30">
                        <div class="card-body p-30">
                            <div class="table-responsive">
                                <table id="example" class="table align-middle">
                                    <thead>
                                    <tr>
                                        <th>{{translate('Notifications')}}</th>
                                        <th>{{translate('Push_Notification')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($array=['booking','tnc_update','pp_update','order'])
                                    @foreach($data_values->whereIn('key_name',$array)->all() as $value)
                                        @if($value['key_name']=='tnc_update')
                                            @php($name='terms & conditions update')
                                        @elseif($value['key_name']=='pp_update')
                                            @php($name='privacy policy update')
                                        @else
                                            @php($name=str_replace('_',' ',$value['key_name']))
                                        @endif
                                        <tr>
                                            <td class="text-capitalize">{{$name}}</td>

                                            <td>
                                                <label class="switcher">
                                                    <input class="switcher_input"
                                                            onclick="update_action_status('push_notification_{{$value['key_name']}}',$(this).is(':checked')===true?1:0)"
                                                            type="checkbox" {{$value->live_values['push_notification_'.$value['key_name']]?'checked':''}}>
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body p-30">
                            <div class="discount-type">
                                <h4 class="mb-5">{{translate('Firebase_Push_Notification_Setup')}}</h4>

                                <!-- Nav Tabs -->
                                <div class="mb-3">
                                    <ul class="nav nav--tabs nav--tabs__style2">
                                        <li class="nav-item">
                                            <button data-bs-toggle="tab" data-bs-target="#booking" class="nav-link active">
                                                {{translate('Booking')}}
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button data-bs-toggle="tab" data-bs-target="#order"
                                                    class="nav-link">
                                                {{translate('Order')}}
                                            </button>
                                        </li>
                                    </ul>
                                    <hr>
                                </div>
                                <!-- End Nav Tabs -->

                                <!-- Booking Tab -->
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="booking">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="mb-30">
                                                    <div class="mb-20 d-flex justify-content-between">
                                                        <b>{{translate('booking_place_message')}}</b>

                                                        <label class="switcher">
                                                            <input class="switcher_input"
                                                                    name="booking_place_status" id="booking_place_status"
                                                                    {{$data_values->where('key_name','booking_place')->first()->live_values['booking_place_status']?'checked':''}}
                                                                    onclick="update_message('booking_place')"
                                                                    type="checkbox">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </div>
                                                    <div class="form-floating">
                                                            <textarea class="form-control" id="booking_place_message"
                                                                        name="booking_place_message">{{$data_values->where('key_name','booking_place')->first()->live_values['booking_place_message']}}</textarea>
                                                    </div>
                                                    <div class="d-flex justify-content-end mt-10">
                                                        <button type="button"
                                                                onclick="update_message('booking_place')"
                                                                class="btn btn--primary">
                                                            {{translate('update')}}
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="mb-30">
                                                    <div class="mb-20 d-flex justify-content-between">
                                                        <b>{{translate('booking_service_complete')}}</b>

                                                        <label class="switcher">
                                                            <input class="switcher_input"
                                                                    name="booking_service_complete_status"
                                                                    id="booking_service_complete_status"
                                                                    {{$data_values->where('key_name','booking_service_complete')->first()->live_values['booking_service_complete_status']?'checked':''}}
                                                                    onclick="update_message('booking_service_complete')"
                                                                    type="checkbox">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </div>
                                                    <div class="form-floating">
                                                        <textarea class="form-control"
                                                                    id="booking_service_complete_message"
                                                                    name="booking_service_complete_message">{{$data_values->where('key_name','booking_service_complete')->first()->live_values['booking_service_complete_message']}}</textarea>
                                                    </div>
                                                    <div class="d-flex justify-content-end mt-10">
                                                        <button type="button"
                                                                onclick="update_message('booking_service_complete')"
                                                                class="btn btn--primary">
                                                            {{translate('update')}}
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-12 col-md-6">

                                                <div class="mb-30">
                                                    <div class="mb-20 d-flex justify-content-between">
                                                        <b>{{translate('booking_decline_message')}}</b>

                                                        <label class="switcher">
                                                            <input class="switcher_input"
                                                                    name="booking_cancel_status"
                                                                    id="booking_cancel_status"
                                                                    {{$data_values->where('key_name','booking_cancel')->first()->live_values['booking_cancel_status']?'checked':''}}
                                                                    onclick="update_message('booking_cancel')"
                                                                    type="checkbox">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </div>
                                                    <div class="form-floating">
                                                            <textarea class="form-control" id="booking_cancel_message"
                                                                        name="booking_cancel_message">{{$data_values->where('key_name','booking_cancel')->first()->live_values['booking_cancel_message']}}</textarea>
                                                    </div>
                                                    <div class="d-flex justify-content-end mt-10">
                                                        <button type="button"
                                                                onclick="update_message('booking_cancel')"
                                                                class="btn btn--primary">
                                                            {{translate('update')}}
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="mb-30">
                                                    <div style="display: flex; justify-content: space-between"
                                                            class="mb-20">
                                                        <b>{{translate('booking_accepted_message')}}</b>

                                                        <label class="switcher">
                                                            <input class="switcher_input"
                                                                    name="booking_accepted_status"
                                                                    id="booking_accepted_status"
                                                                    {{$data_values->where('key_name','booking_accepted')->first()->live_values['booking_accepted_status']?'checked':''}}
                                                                    onclick="update_message('booking_accepted')"
                                                                    type="checkbox">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </div>
                                                    <div class="form-floating">
                                                            <textarea class="form-control" id="booking_accepted_message"
                                                                        name="booking_accepted_message">{{$data_values->where('key_name','booking_accepted')->first()->live_values['booking_accepted_message']}}</textarea>
                                                    </div>
                                                    <div class="d-flex justify-content-end mt-10">
                                                        <button type="button"
                                                                onclick="update_message('booking_accepted')"
                                                                class="btn btn--primary">
                                                            {{translate('update')}}
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-12 col-md-6">
                                                @php($booking_ongoing = $data_values->where('key_name','booking_ongoing')->first())
                                                <div class="mb-30">
                                                    <div style="display: flex; justify-content: space-between"
                                                         class="mb-20">
                                                        <b>{{translate('booking_ongoing_message')}}</b>

                                                        <label class="switcher">
                                                            <input class="switcher_input"
                                                                   name="booking_ongoing_status"
                                                                   id="booking_ongoing_status"
                                                                   {{$booking_ongoing && $booking_ongoing->live_values['booking_ongoing_status']?'checked':''}}
                                                                   onclick="update_message('booking_ongoing')"
                                                                   type="checkbox">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </div>
                                                    <div class="form-floating">
                                                            <textarea class="form-control" id="booking_ongoing_message"
                                                                      name="booking_ongoing_message">{{$booking_ongoing && $booking_ongoing->live_values['booking_ongoing_message'] ? $booking_ongoing->live_values['booking_ongoing_message'] : ''}}</textarea>
                                                    </div>
                                                    <div class="d-flex justify-content-end mt-10">
                                                        <button type="button"
                                                                onclick="update_message('booking_ongoing')"
                                                                class="btn btn--primary">
                                                            {{translate('update')}}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Tab -->
                                <div class="tab-content">
                                    <div class="tab-pane fade" id="order">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="mb-30">
                                                    <div class="mb-20 d-flex justify-content-between">
                                                        <b>{{translate('order_place_message')}}</b>

                                                        <label class="switcher">
                                                            <input class="switcher_input"
                                                                   name="order_place_status" id="order_place_status"
                                                                   {{$data_values->where('key_name','order_place')->first()->live_values['order_place_status']?'checked':''}}
                                                                   onclick="update_order_message('order_place')"
                                                                   type="checkbox">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </div>
                                                    <div class="form-floating">
                                                            <textarea class="form-control" id="order_place_message"
                                                                      name="order_place_message">{{$data_values->where('key_name','order_place')->first()->live_values['order_place_message']}}</textarea>
                                                    </div>
                                                    <div class="d-flex justify-content-end mt-10">
                                                        <button type="button"
                                                                onclick="update_order_message('order_place')"
                                                                class="btn btn--primary">
                                                            {{translate('update')}}
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="mb-30">
                                                    <div class="mb-20 d-flex justify-content-between">
                                                        <b>{{translate('order_complete')}}</b>

                                                        <label class="switcher">
                                                            <input class="switcher_input"
                                                                   name="order_complete_status"
                                                                   id="order_complete_status"
                                                                   {{$data_values->where('key_name','order_complete')->first()->live_values['order_complete_status']?'checked':''}}
                                                                   onclick="update_order_message('order_complete')"
                                                                   type="checkbox">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </div>
                                                    <div class="form-floating">
                                                        <textarea class="form-control"
                                                                  id="order_complete_message"
                                                                  name="order_complete_message">{{$data_values->where('key_name','order_complete')->first()->live_values['order_complete_message']}}</textarea>
                                                    </div>
                                                    <div class="d-flex justify-content-end mt-10">
                                                        <button type="button"
                                                                onclick="update_order_message('order_complete')"
                                                                class="btn btn--primary">
                                                            {{translate('update')}}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">

                                                <div class="mb-30">
                                                    <div class="mb-20 d-flex justify-content-between">
                                                        <b>{{translate('Order_cancel_complete')}}</b>

                                                        <label class="switcher">
                                                            <input class="switcher_input"
                                                                   name="order_cancel_status"
                                                                   id="order_cancel_status"
                                                                   {{$data_values->where('key_name','order_cancel')->first()->live_values['order_cancel_status']?'checked':''}}
                                                                   onclick="update_order_message('order_cancel')"
                                                                   type="checkbox">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </div>
                                                    <div class="form-floating">
                                                            <textarea class="form-control" id="order_cancel_message"
                                                                      name="order_cancel_message">{{$data_values->where('key_name','order_cancel')->first()->live_values['order_cancel_message']}}</textarea>
                                                    </div>
                                                    <div class="d-flex justify-content-end mt-10">
                                                        <button type="button"
                                                                onclick="update_order_message('order_cancel')"
                                                                class="btn btn--primary">
                                                            {{translate('update')}}
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="mb-30">
                                                    <div style="display: flex; justify-content: space-between"
                                                         class="mb-20">
                                                        <b>{{translate('order_process_message')}}</b>

                                                        <label class="switcher">
                                                            <input class="switcher_input"
                                                                   name="order_process_status"
                                                                   id="order_process_status"
                                                                   {{$data_values->where('key_name','order_process')->first()->live_values['order_process_status']?'checked':''}}
                                                                   onclick="update_order_message('order_process')"
                                                                   type="checkbox">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </div>
                                                    <div class="form-floating">
                                                            <textarea class="form-control" id="order_process_message"
                                                                      name="order_process_message">{{$data_values->where('key_name','order_process')->first()->live_values['order_process_message']}}</textarea>
                                                    </div>
                                                    <div class="d-flex justify-content-end mt-10">
                                                        <button type="button"
                                                                onclick="update_order_message('order_process')"
                                                                class="btn btn--primary">
                                                            {{translate('update')}}
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-12 col-md-6">
                                                @php($order_shipped = $data_values->where('key_name','order_shipped')->first())
                                                <div class="mb-30">
                                                    <div style="display: flex; justify-content: space-between"
                                                         class="mb-20">
                                                        <b>{{translate('order_shipped_message')}}</b>

                                                        <label class="switcher">
                                                            <input class="switcher_input"
                                                                   name="order_shipped_status"
                                                                   id="order_shipped_status"
                                                                   {{$order_shipped && $order_shipped->live_values['order_shipped_status']?'checked':''}}
                                                                   onclick="update_order_message('order_shipped')"
                                                                   type="checkbox">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </div>
                                                    <div class="form-floating">
                                                            <textarea class="form-control" id="order_shipped_message"
                                                                      name="order_shipped_message">{{$order_shipped && $order_shipped->live_values['order_shipped_message'] ? $order_shipped->live_values['order_shipped_message'] : ''}}</textarea>
                                                    </div>
                                                    <div class="d-flex justify-content-end mt-10">
                                                        <button type="button"
                                                                onclick="update_order_message('order_shipped')"
                                                                class="btn btn--primary">
                                                            {{translate('update')}}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.js-select').select2();
        });
    </script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/dataTables.select.min.js"></script>

    <script>
        $('#business-info-update-form').on('submit', function (event) {
            event.preventDefault();

            var form = $('#business-info-update-form')[0];
            var formData = new FormData(form);
            // Set header if need any otherwise remove setup part
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business-settings.set-business-information')}}",
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function (response) {
                    toastr.success('{{translate('successfully_updated')}}')
                },
                error: function () {

                }
            });
        });

        function update_action_status(key_name, value) {
            Swal.fire({
                title: "{{translate('are_you_sure')}}?",
                text: '{{translate('want_to_update_status')}}',
                type: 'warning',
                showCloseButton: true,
                showCancelButton: true,
                cancelButtonColor: 'var(--c2)',
                confirmButtonColor: 'var(--c1)',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.configuration.set-notification-setting')}}",
                        data: {
                            key: key_name,
                            value: value,
                        },
                        type: 'put',
                        success: function (response) {
                            console.log(response)
                            toastr.success('{{translate('successfully_updated')}}')
                        },
                        error: function () {

                        }
                    });
                }
            })
        }

        function update_message(id) {
            Swal.fire({
                title: "{{translate('are_you_sure')}}?",
                text: '{{translate('want_to_update')}}',
                type: 'warning',
                showCloseButton: true,
                showCancelButton: true,
                cancelButtonColor: 'var(--c2)',
                confirmButtonColor: 'var(--c1)',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.configuration.set-message-setting')}}",
                        data: {
                            id: id,
                            status: $('#' + id + '_status').is(':checked') === true ? 1 : 0,
                            message: $('#' + id + '_message').val(),
                        },
                        type: 'PUT',
                        success: function (response) {
                            console.log(response)
                            toastr.success('{{translate('successfully_updated')}}')
                        },
                        error: function () {

                        }
                    });
                }
            })
        }

    //    Update order status
        function update_order_message(id) {
            Swal.fire({
                title: "{{translate('are_you_sure')}}?",
                text: '{{translate('want_to_update')}}',
                type: 'warning',
                showCloseButton: true,
                showCancelButton: true,
                cancelButtonColor: 'var(--c2)',
                confirmButtonColor: 'var(--c1)',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.configuration.set-order-message-setting')}}",
                        data: {
                            id: id,
                            status: $('#' + id + '_status').is(':checked') === true ? 1 : 0,
                            message: $('#' + id + '_message').val(),
                        },
                        type: 'PUT',
                        success: function (response) {
                            console.log(response)
                            toastr.success('{{translate('successfully_updated')}}')
                        },
                        error: function () {

                        }
                    });
                }
            })
        }
    </script>
@endpush
