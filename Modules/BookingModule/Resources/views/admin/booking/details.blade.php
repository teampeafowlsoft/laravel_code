@extends('adminmodule::layouts.master')

@section('title',translate('Booking_Details'))

@push('css_or_js')

@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('Booking_Details')}} </h2>
                    </div>

                    <ul class="nav nav--tabs nav--tabs__style2 mb-4">
                        <li class="nav-item">
                            <a class="nav-link {{$web_page=='details'?'active':''}}"
                               href="{{url()->current()}}?web_page=details">{{translate('details')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$web_page=='status'?'active':''}}"
                               href="{{url()->current()}}?web_page=status">{{translate('status')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$web_page=='images'?'active':''}}"
                               href="{{url()->current()}}?web_page=images">{{translate('images')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$web_page=='request'?'active':''}}"
                               href="{{url()->current()}}?web_page=request">{{translate('Request')}}</a>
                        </li>
                    </ul>

                    <div class="card mb-3">
                        <div class="card-body pb-5">
                            <div
                                class="border-bottom pb-3 d-flex justify-content-between align-items-center gap-3 flex-wrap">
                                <div>
                                    <h3 class="c1 mb-2">{{translate('Booking')}}
                                        # {{$booking['readable_id']}}</h3>
                                    <p class="opacity-75 fz-12">{{translate('Booking_Placed')}}
                                        : {{date('d-M-Y h:ia',strtotime($booking->created_at))}}</p>
                                </div>
                                <div class="d-flex flex-wrap flex-xxl-nowrap gap-3">
                                    <select class="js-select theme-input-style max-w220 min-w180 selected-item-c1"
                                            id="serviceman_assign">
                                        <option value="no_serviceman">{{translate('--Assign_Serviceman--')}}</option>
                                        @foreach($servicemen as $serviceman)
                                            <option
                                                value="{{$serviceman->id}}" {{$booking->serviceman_id == $serviceman->id ? 'selected' : ''}} >
                                                {{$serviceman->user ? Str::limit($serviceman->user->first_name.' '.$serviceman->user->last_name, 30):''}}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select class="js-select theme-input-style max-w220 min-w180 selected-item-c1"
                                            id="payment_status">
                                        <option value="0">{{translate('--Payment_Status--')}}</option>
                                        <option
                                            value="paid" {{$booking['is_paid'] ? 'selected' : ''}} >{{translate('Paid')}}</option>
                                        <option
                                            value="unpaid" {{!$booking['is_paid'] ? 'selected' : ''}} >{{translate('Unpaid')}}</option>
                                    </select>

                                    @php $provider_selected_ids = explode(',',$booking->provider_selected_ids) @endphp

                                    @php $subcategory_id = $booking->sub_category_id;
                                    @endphp

                                    <select
                                        class="js-select theme-input-style max-w220 min-w180 selected-item-c1 provider_option"
                                        id="booking_status_company">

                                        <option value="0">{{translate('--Provider_List--')}}</option>
                                        @if(($booking->booking_status == 'pending') || ($booking->booking_status == 'accepted') || ($booking->booking_status == 'ongoing') || ($booking->booking_status == 'canceled'))
                                            @foreach($providers as $pID)
                                                @foreach($booking_subcategory_id as $psc)
                                                    @if($pID->id == $psc->provider_id)
                                                        <option
                                                            value="{{$pID->id}}" {{($pID->id)==$booking->provider_id?'selected':''}}>
                                                            {{$pID->company_name}}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            {{--                                                <option value="accepted" {{$booking['booking_status'] == 'accepted' ? 'selected' : ''}} >{{translate('Accepted')}}</option>--}}
                                        @endif
                                    </select>
{{--                                    @if($booking->booking_status != 'pending')--}}
                                        <select class="js-select theme-input-style max-w220 min-w180 selected-item-c1"
                                                id="booking_status">
                                            <option value="0">{{translate('--Booking_status--')}}</option>
                                            <option
                                                value="ongoing" {{$booking['booking_status'] == 'ongoing' ? 'selected' : ''}}>{{translate('Ongoing')}}</option>
                                            <option
                                                value="completed" {{$booking['booking_status'] == 'completed' ? 'selected' : ''}}>{{translate('Completed')}}</option>
                                            <option
                                                value="canceled" {{$booking['booking_status'] == 'canceled' ? 'selected' : ''}}>{{translate('Canceled')}}</option>
                                        </select>
{{--                                    @endif--}}

                                    @if(!in_array($booking->booking_status,['ongoing','completed']))
                                        <button type="button" class="btn btn--primary" data-bs-toggle="modal"
                                                id="change_schedule"
                                                data-bs-target="#changeScheduleModal">
                                            <span class="material-icons">schedule</span>
                                            {{translate('CHANGE_SCHEDULE')}}
                                        </button>
                                    @endif

                                    <a href="{{route('admin.booking.invoice',[$booking->id])}}"
                                       class="btn btn-primary" target="_blank">
                                        <span class="material-icons">description</span>
                                        {{translate('Invoice')}}
                                    </a>
                                </div>
                            </div>
                            <div
                                class="border-bottom py-3 d-flex justify-content-between align-items-center gap-3 flex-wrap">
                                <div>
                                    <h4 class="mb-2">{{translate('Payment_Method')}}</h4>
                                    <h5 class="c1 mb-2">{{ translate($booking->payment_method) }}</h5>
                                    <p class="mb-2">
                                        <span>{{translate('Amount')}} : </span> {{with_currency_symbol($booking->total_booking_amount)}}
                                    </p>
                                    @if($booking->transaction_id != 'cash-payment')
                                        <p>
                                            <span>{{translate('Transaction_Id')}} : </span> {{($booking->transaction_id)}}
                                        </p>
                                    @endif
                                </div>
                                <div>
                                    <p class="mb-2"><span>{{translate('Booking_Status')}} :</span> <span
                                            class="c1 text-capitalize"
                                            id="booking_status__span">{{$booking->booking_status}}</span></p>
                                    <p class="mb-2"><span>{{translate('Payment_Status')}} : </span> <span
                                            class="text-{{$booking->is_paid ? 'success' : 'danger'}}"
                                            id="payment_status__span">{{$booking->is_paid ? translate('Paid') : translate('Unpaid')}}</span>
                                    </p>
                                    <h5>{{translate('Service_Schedule_Date')}} : <span
                                            id="service_schedule__span">{{date('d-M-Y h:ia',strtotime($booking->service_schedule))}}</span>
                                    </h5>
                                </div>
                            </div>
                            <div class="py-3 d-flex gap-3 flex-wrap mb-2">
                                <!-- Customer Info -->
                                <div class="c1-light-bg radius-10 py-3 px-4 flex-grow-1">
                                    <h4 class="mb-2">{{translate('Customer_Information')}}</h4>
                                    @if(isset($booking->customer))
                                        <h5 class="c1 mb-3">{{isset($booking->customer)?Str::limit($booking->customer->first_name. ' ' .$booking->customer->last_name, 30):''}}</h5>
                                        <ul class="list-info">
                                            <li>
                                                <span class="material-icons">phone_iphone</span>
                                                <a href="tel:88013756987564">{{isset($booking->customer)?$booking->customer->phone:''}}</a>
                                            </li>
                                            <li>
                                                <span class="material-icons">map</span>
                                                <p>{{Str::limit($booking->service_address->address??translate('not_available'), 100)}}</p>
                                            </li>
                                        </ul>
                                    @else
                                        <p class="text-muted text-center mt-30 fz-12">{{translate('No Customer Information')}}</p>
                                    @endif
                                </div>
                                <!-- End Customer Info -->

                                <!-- Provider Info -->
                                <div class="c1-light-bg radius-10 py-3 px-4 flex-grow-1">
                                    <h4 class="mb-2">{{translate('Provider Information')}}</h4>
                                    @if(isset($booking->provider))
                                        <h5 class="c1 mb-3">{{Str::limit($booking->provider->contact_person_name??'', 30)}}</h5>
                                        <ul class="list-info">
                                            <li>
                                                <span class="material-icons">phone_iphone</span>
                                                <a href="tel:88013756987564">{{$booking->provider->contact_person_phone??''}}</a>
                                            </li>
                                            <li>
                                                <span class="material-icons">map</span>
                                                <p>{{Str::limit($booking->provider->company_address??'', 100)}}</p>
                                            </li>
                                        </ul>
                                    @else
                                        <p class="text-muted text-center mt-30 fz-12">{{translate('No Provider Information')}}</p>
                                    @endif
                                </div>
                                <!-- End Provider Info -->

                                <!-- Lead Service Info -->
                                <div class="c1-light-bg radius-10 py-3 px-4 flex-grow-1">
                                    <h4 class="mb-2">{{translate('Lead_Service_Information')}}</h4>
                                    @if(isset($booking->serviceman))
                                        <h5 class="c1 mb-3">{{Str::limit($booking->serviceman && $booking->serviceman->user ? $booking->serviceman->user->first_name.' '.$booking->serviceman->user->last_name:'', 30)}}</h5>
                                        <ul class="list-info">
                                            <li>
                                                <span class="material-icons">phone_iphone</span>
                                                <a href="tel:{{$booking->serviceman && $booking->serviceman->user ? $booking->serviceman->user->phone:''}}">
                                                    {{$booking->serviceman && $booking->serviceman->user ? $booking->serviceman->user->phone:''}}
                                                </a>
                                            </li>
                                        </ul>
                                    @else
                                        <p class="text-muted text-center mt-30 fz-12">{{translate('No Serviceman Information')}}</p>
                                    @endif
                                </div>
                                <!-- End Lead Service Info -->
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h3 class="mb-3">{{translate('Booking_Summary')}}</h3>
                                </div>

                                <div class="col-md-6 add_item_div" style="text-align: right">
                                    <a href="#myModal" data-bs-toggle="modal"
                                       class="btn btn-primary">
                                        <span class="material-icons">add</span>
                                        {{translate('add_items')}}
                                    </a>
                                </div>
                            </div>

                            <div class="table-responsive border-bottom">
                                <table class="table text-nowrap align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th class="ps-lg-3">{{translate('Service')}}</th>
                                        <th>{{translate('Price')}}</th>
                                        <th>{{translate('Qty')}}</th>
                                        <th>{{translate('Discount')}}</th>
                                        @if($booking->is_paid)
                                            <th>{{translate('online_pay')}}</th>
                                            <th>{{translate('cash_pay')}}</th>
                                        @else
                                            <th>{{translate('paid_amount')}}</th>
                                            <th>{{translate('payable_amount')}}</th>
                                        @endif

                                        <th class="text-end">{{translate('Total')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($sub_total=0)
                                    @foreach($booking->detail as $detail)
                                        <tr>
                                            <td class="text-wrap ps-lg-3">
                                                @if(isset($detail->service))
                                                    <div class="d-flex flex-column">
{{--                                                        <a href="{{route('admin.service.detail',[$detail->service->id])}}"--}}
                                                        <span
                                                           class="fw-bold">{{Str::limit($detail->service_name, 30)}}</span>
                                                        <div>{{Str::limit($detail ? $detail->variant_key : '', 50)}}</div>

                                                        @if($detail->variation->invoice_item == 1)
                                                            <div>
                                                                <a id="" class="text-danger delete_item"
                                                                   title="Remove Item of Booking"
                                                                   style="cursor: pointer;"
                                                                   data-detail_id="{{$detail->id}}"
                                                                   data-service_id="{{$detail->service_id}}"
                                                                   data-variation_id="{{$detail->variation->id}}"
                                                                   data-service_cost="{{$detail->service_cost}}"
                                                                   data-discount_cost="{{$detail->discount_amount}}"
                                                                   data-qty="{{$detail->quantity}}"><span
                                                                        class="material-icons">delete</span>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span
                                                        class="badge badge-pill badge-danger">{{translate('Service_unavailable')}}</span>
                                                @endif
                                            </td>
                                            <td>{{with_currency_symbol($detail->service_cost)}}</td>
                                            <td>{{$detail->quantity}}</td>
                                            <td>{{with_currency_symbol($detail->discount_amount)}}</td>
                                            <td>
                                                {{($detail->is_paid == 1 && $detail->transaction_id != 'cash-payment')?with_currency_symbol($detail->total_cost):'0.000'}}
                                            </td>
                                            <td>
                                                {{($detail->transaction_id == 'cash-payment')?with_currency_symbol($detail->total_cost):'0.000'}}
                                            </td>
                                            <td class="text-end">{{with_currency_symbol($detail->total_cost)}}</td>
                                        </tr>
                                        @php($sub_total+=$detail->service_cost*$detail->quantity)
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="row justify-content-end mt-3">
                                <div class="col-sm-10 col-md-6 col-xl-5">
                                    <div class="table-responsive">
                                        <table class="table-sm title-color align-right w-100">
                                            <tbody>
                                            <tr>
                                                <td>{{translate('Sub_Total_(Vat _Excluded)')}}</td>
                                                <td>{{with_currency_symbol($sub_total)}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{translate('Discount')}}</td>
                                                <td>{{with_currency_symbol($booking->total_discount_amount)}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{translate('Coupon_Discount')}}</td>
                                                <td>{{with_currency_symbol($booking->total_coupon_discount_amount)}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{translate('Campaign_Discount')}}</td>
                                                <td>{{with_currency_symbol($booking->total_campaign_discount_amount)}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{translate('Vat')}}</td>
                                                <td>{{with_currency_symbol($booking->total_tax_amount)}}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{translate('Grand_Total')}}</strong></td>
                                                <td>
                                                    <strong>{{with_currency_symbol($booking->total_booking_amount)}}</strong>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->

    <!-- Modal -->
    <div class="modal fade" id="changeScheduleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="changeScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeScheduleModalLabel">{{translate('Change_Booking_Schedule')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="datetime-local" id="service_schedule" class="form-control" name="service_schedule"
                           value="{{$booking->service_schedule}}">
                </div>
                <div class="p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn--secondary"
                            data-bs-dismiss="modal">{{translate('Close')}}</button>
                    <button type="button" class="btn btn--primary"
                            id="service_schedule__submit">{{translate('Submit')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div id="myModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{route('admin.booking.store')}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="booking_id" value="{{$booking->id}}">
                        <input type="hidden" class="form-control" name="zone_id" value="{{$booking->zone_id}}">
                        <input type="hidden" class="form-control" name="service_id"
                               value="{{$booking->detail[0]['service']['id']}}">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">Service Item</label>
                                <div class="mb-3">
                                    <input type="text" name="service_name" required
                                           class="form-control">
                                    {{--                                    <select name="service_id" required--}}
                                    {{--                                            class="js-select theme-input-style max-w220 min-w180 selected-item-c1">--}}

                                    {{--                                        <option value="0">{{translate('service_list')}}</option>--}}
                                    {{--                                        @foreach($service_list as $sl)--}}
                                    {{--                                            <option--}}
                                    {{--                                                value="{{$sl->id}}">--}}
                                    {{--                                                {{$sl->name}}--}}
                                    {{--                                            </option>--}}
                                    {{--                                        @endforeach--}}
                                    {{--                                    </select>--}}
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Item variant</label>
                                <div class="mb-3">
                                    <input type="text" name="variant" required
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="form-label">Item Price</label>
                                    <input type="text" onkeypress="return validateFloatKeyPress(this,event);"
                                           name="service_cost" id="service_cost" required
                                           class="form-control">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="form-label">Qty</label>
                                    <input type="text" name="quantity" id="quantity" required
                                           onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="form-label">Discount</label>
                                    <input type="text" onkeypress="return validateFloatKeyPress(this,event);"
                                           name="discount_amount" id="discount_amount" required
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Total Price</label>
                                    <input type="text" name="total_cost" id="total_cost" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Require Repair Items</label>
                                    <input type="text" name="require_item" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn--primary" type="submit">{{translate('submit')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')

    <script>
        @if($booking->booking_status == 'pending')
        $(document).ready(function () {
            selectElementVisibility('serviceman_assign', false);
            selectElementVisibility('payment_status', false);
            $("#change_schedule").addClass('d-none');
        });
        @endif


        //Service schedule update
        $("#service_schedule__submit").click(function () {
            var service_schedule = $("#service_schedule").val();
            var route = '{{route('admin.booking.schedule_update',[$booking->id])}}' + '?service_schedule=' + service_schedule;

            update_booking_details(route, '{{translate('want_to_update_status')}}', 'service_schedule__submit', service_schedule);
        });

        //Booking status update
        $("#booking_status").change(function () {
            var booking_status = $("#booking_status option:selected").val();
            if (parseInt(booking_status) !== 0) {
                var route = '{{route('admin.booking.status_update',[$booking->id])}}' + '?booking_status=' + booking_status;
                update_booking_details(route, '{{translate('want_to_update_status')}}', 'booking_status', booking_status);
            } else {
                toastr.error('{{translate('choose_proper_status')}}');
            }
        });

        //Serviceman assign/update
        $("#serviceman_assign").change(function () {
            var serviceman_id = $("#serviceman_assign option:selected").val();
            if (serviceman_id !== 'no_serviceman') {
                var route = '{{route('admin.booking.serviceman_update',[$booking->id])}}' + '?serviceman_id=' + serviceman_id;

                update_booking_details(route, '{{translate('want_to_assign_the_serviceman')}}?', 'serviceman_assign', serviceman_id);
            } else {
                toastr.error('{{translate('choose_proper_serviceman')}}');
            }
        });

        //Payment status update
        $("#payment_status").change(function () {
            var payment_status = $("#payment_status option:selected").val();
            if (parseInt(payment_status) !== 0) {
                var route = '{{route('admin.booking.payment_update',[$booking->id])}}' + '?payment_status=' + payment_status;

                update_booking_details(route, '{{translate('want_to_update_status')}}', 'payment_status', payment_status);
            } else {
                toastr.error('{{translate('choose_proper_payment_status')}}');
            }
        });

        //Remove Item
        $(".delete_item").click(function () {
            var detail_id = $(this).attr("data-detail_id");
            var service_id = $(this).attr("data-service_id");
            var variation_id = $(this).attr("data-variation_id");
            var service_cost = $(this).attr("data-service_cost");
            var discount_cost = $(this).attr("data-discount_cost");
            var qty = $(this).attr("data-qty");

            var payment_status = $("#payment_status option:selected").val();
            if ((detail_id != '') || (service_id != '') || (variation_id != '')) {
                var route = '{{route('admin.booking.remove_item',[$booking->id])}}' + '?detail_id=' + detail_id + '&service_id=' + service_id + '&variation_id=' + variation_id + '&service_cost=' + service_cost + '&discount_cost=' + discount_cost + '&qty=' + qty;
                console.log(route)
                remove_item_details(route, '{{translate('want_to_remove_this_item')}}');
            } else {
                toastr.error('{{translate('delete_proper_item')}}');
            }
        });

        //remove ajax function
        function remove_item_details(route, message) {
            Swal.fire({
                title: "{{translate('are_you_sure')}}?",
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'var(--c2)',
                confirmButtonColor: 'var(--c1)',
                cancelButtonText: '{{translate('Cancel')}}',
                confirmButtonText: '{{translate('Yes')}}',
                reverseButtons: true
            }).then((result) => {
                console.log(result);
                if (result.value) {
                    $.get({
                        url: route,
                        dataType: 'json',
                        data: {},
                        beforeSend: function () {
                            /*$('#loading').show();*/
                        },
                        success: function (data) {
                            console.log(data);
                            // console.log('tt');return false;
                            toastr.success(data.message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                            location.reload();
                        },
                        complete: function () {
                            /*$('#loading').hide();*/
                        },
                    });
                }
            })
        }

        //update ajax function
        function update_booking_details(route, message, componentId, updatedValue) {
            Swal.fire({
                title: "{{translate('are_you_sure')}}?",
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'var(--c2)',
                confirmButtonColor: 'var(--c1)',
                cancelButtonText: '{{translate('Cancel')}}',
                confirmButtonText: '{{translate('Yes')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get({
                        url: route,
                        dataType: 'json',
                        data: {},
                        beforeSend: function () {
                            /*$('#loading').show();*/
                        },
                        success: function (data) {
                            // console.log('tt');return false;
                            update_component(componentId, updatedValue);
                            toastr.success(data.message, {
                                CloseButton: true,
                                ProgressBar: true
                            });

                            if (componentId === 'booking_status') {
                                location.reload();
                            }
                        },
                        complete: function () {
                            /*$('#loading').hide();*/
                        },
                    });
                }
            })
        }

        //update ajax function
        function update_booking_details_status(route, message, componentId, updatedValue, provider, provider_id) {
            Swal.fire({
                title: "{{translate('are_you_sure')}}?",
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'var(--c2)',
                confirmButtonColor: 'var(--c1)',
                cancelButtonText: '{{translate('Cancel')}}',
                confirmButtonText: '{{translate('Yes')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get({
                        url: route,
                        dataType: 'json',
                        data: {},
                        beforeSend: function () {
                            /*$('#loading').show();*/
                        },
                        success: function (data) {
                            console.log(data);

                            // console.log('tt');return false;
                            update_component(componentId, updatedValue);
                            toastr.success(data.message, {
                                CloseButton: true,
                                ProgressBar: true
                            });

                            if (componentId === 'booking_status') {
                                location.reload();
                            }
                        },
                        complete: function () {
                            /*$('#loading').hide();*/
                        },
                    });
                }
            })
        }

        //component update
        function update_component(componentId, updatedValue) {

            if (componentId === 'booking_status') {
                $("#booking_status__span").html(updatedValue);

                selectElementVisibility('serviceman_assign', true);
                selectElementVisibility('payment_status', true);
                if ($("#change_schedule").hasClass('d-none')) {
                    $("#change_schedule").removeClass('d-none');
                }

            } else if (componentId === 'payment_status') {
                $("#payment_status__span").html(updatedValue);
                if (updatedValue === 'paid') {
                    $("#payment_status__span").addClass('text-success').removeClass('text-danger');
                } else if (updatedValue === 'unpaid') {
                    $("#payment_status__span").addClass('text-danger').removeClass('text-success');
                }

            } else if (componentId === 'service_schedule__submit') {
                $('#changeScheduleModal').modal('hide');
                let date = new Date(updatedValue);
                $('#service_schedule__span').html(date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear() + " " +
                    date.getHours() + ":" + date.getMinutes());

            }
        }

        //component update
        function selectElementVisibility(componentId, visibility) {
            if (visibility === true) {
                $('#' + componentId).next(".select2-container").show();
            } else if (visibility === false) {
                $('#' + componentId).next(".select2-container").hide();
            } else {
            }
        }

        //Booking status update
        {{--$("#booking_status_company").change(function () {--}}
        {{--    --}}
        {{--    var booking_status_company = $("#booking_status_company option:selected").val();--}}
        {{--    if (parseInt(booking_status_company) !== 0) {--}}
        {{--        var route = '{{route('admin.booking.status_update',[$booking->id])}}' + '?booking_status=' + booking_status_company;--}}
        {{--        update_booking_details(route, '{{translate('want_to_update_status')}}', 'booking_status', booking_status_company);--}}
        {{--    } else {--}}
        {{--        toastr.error('{{translate('choose_proper_status')}}');--}}
        {{--    }--}}
        {{--});--}}
            if($("#booking_status_company option:selected").val() == 0){
                $("#booking_status").prop('disabled',true);
        }

        $("#booking_status_company").change(function () {
            var providersIds = $(this).val();
            var booking_status_company = $("#booking_status_company option:selected").val();
            if (parseInt(booking_status_company) !== 0) {
                $("#booking_status").prop('disabled',false);

{{--                @if($booking->booking_status == 'pending')--}}
{{--                var route = '{{route('admin.booking.status_provider_update',[$booking->id])}}' + '?booking_status=accepted&provider_id=' + booking_status_company;--}}
{{--                update_booking_details_status(route, '{{translate('want_to_update_status')}}', 'booking_status', 'accepted', 'provider', booking_status_company);--}}
                @if($booking->booking_status == 'pending')
                var route = '{{route('admin.booking.status_provider_update',[$booking->id])}}' + '?booking_status=pending&provider_id=' + booking_status_company +'&provider_selected_ids=' + providersIds;
                update_booking_details_status(route, '{{translate('want_to_update_status')}}', 'booking_status', 'pending', 'provider', booking_status_company);
                @elseif($booking->booking_status == 'accepted')
                var route = '{{route('admin.booking.status_provider_update',[$booking->id])}}' + '?booking_status=ongoing&provider_id=' + booking_status_company +'&provider_selected_ids=' + providersIds;
                update_booking_details_status(route, '{{translate('want_to_update_status')}}', 'booking_status', 'ongoing', 'provider', booking_status_company);
                @elseif($booking->booking_status == 'ongoing')
                var route = '{{route('admin.booking.status_provider_update',[$booking->id])}}' + '?booking_status=ongoing&provider_id=' + booking_status_company +'&provider_selected_ids=' + providersIds;
                update_booking_details_status(route, '{{translate('want_to_update_status')}}', 'booking_status', 'ongoing', 'provider', booking_status_company);
                @elseif($booking->booking_status == 'canceled')
                var route = '{{route('admin.booking.status_provider_update',[$booking->id])}}' + '?booking_status=canceled&provider_id=' + booking_status_company +'&provider_selected_ids=' + providersIds;
                update_booking_details_status(route, '{{translate('want_to_update_status')}}', 'booking_status', 'canceled', 'provider', booking_status_company);
                @endif
            } else {
                $("#booking_status").prop('disabled',true);
                toastr.error('{{translate('choose_proper_status')}}');
            }
        });
    </script>

    {{-- Booking Modal calculation --}}
    <script>
        $("#service_cost, #quantity, #discount_amount").change(function () {

            // var service_cost = $("#service_cost").val();
            var service_cost = parseFloat($('#service_cost').val()) || 0;
            var quantity = parseFloat($('#quantity').val()) || 1;
            var discount_amount = parseFloat($('#discount_amount').val()) || 0;
            var total_cost = (service_cost * quantity) - discount_amount;
            // alert(total_cost)
            $('#total_cost').val(total_cost);
        });

        var provider_option = $(".provider_option").val();
        if(provider_option != 0){
            $('.add_item_div').show();
        } else {
            $('.add_item_div').hide();
        }
    </script>

@endpush
