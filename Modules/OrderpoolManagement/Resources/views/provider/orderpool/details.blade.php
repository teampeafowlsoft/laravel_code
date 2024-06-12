@extends('providermanagement::layouts.master')

@section('title',translate('order_detail'))

@push('css_or_js')

@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('order_detail')}} </h2>
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
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="order_details_details">
                            <div class="card mb-3">
                                <div class="card-body pb-5">
                                    <div
                                        class="border-bottom pb-3 d-flex justify-content-between align-items-center gap-3 flex-wrap">
                                        <div>
                                            <h3 class="c1 mb-2">{{translate('Order')}}
                                                # {{$booking['readable_id']}}</h3>
                                            <p class="opacity-75 fz-12">{{translate('Order_Placed')}}
                                                : {{date('d-M-Y h:ia',strtotime($booking->created_at))}}</p>
                                        </div>
                                        <div class="d-flex flex-wrap flex-xxl-nowrap gap-3">
                                            <select
                                                class="js-select theme-input-style max-w220 min-w180 selected-item-c1"
                                                id="payment_status">
                                                @if(!$booking['is_paid'])
                                                    <option value="0">{{translate('--Payment_Status--')}}</option>
                                                    <option value="paid" {{$booking['is_paid'] ? 'selected' : ''}} >{{translate('Paid')}}</option>
                                                    <option value="unpaid" {{!$booking['is_paid'] ? 'selected' : ''}} >{{translate('Unpaid')}}</option>
                                                @else
                                                    <option value="paid" {{$booking['is_paid'] ? 'selected' : ''}} >{{translate('Paid')}}</option>
                                                @endif
                                            </select>
                                            <select class="js-select theme-input-style max-w220 min-w180 selected-item-c1"
                                                    id="booking_status">
                                                <option value="0">{{translate('--Order_Status--')}}</option>
                                                <option
                                                    value="received" {{$booking['booking_status'] == 'received' ? 'selected' : ''}}>{{translate('order_received')}}</option>
                                                <option
                                                    value="processed" {{$booking['booking_status'] == 'processed' ? 'selected' : ''}}>{{translate('order_processed')}}</option>
                                                <option
                                                    value="shipped" {{$booking['booking_status'] == 'shipped' ? 'selected' : ''}}>{{translate('order_shipped')}}</option>
                                                <option
                                                    value="delivered" {{$booking['booking_status'] == 'delivered' ? 'selected' : ''}}>{{translate('order_delivered')}}</option>
                                                <option
                                                    value="cancelled" {{$booking['booking_status'] == 'cancelled' ? 'selected' : ''}}>{{translate('order_cancelled')}}</option>
                                            </select>

                                            <a href="{{route('provider.orderpool.invoice',[$booking->id])}}"
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
                                        <div class="text-end">
                                            <p class="mb-2"><span>{{translate('Order_Status')}} :</span> <span
                                                    class="c1 text-capitalize"
                                                    id="booking_status__span">{{$booking->booking_status}}</span></p>
                                            <p class="mb-2"><span>{{translate('Payment_Status')}} : </span> <span
                                                    class="text-{{$booking->is_paid ? 'success' : 'danger'}}"
                                                    id="payment_status__span">{{$booking->is_paid ? translate('Paid') : translate('Unpaid')}}</span>
                                            </p>
                                            <h5>{{translate('Order_Delivery_Date')}} : <span
                                                    id="service_schedule__span">{{($booking->service_schedule) ? date('d-M-Y h:ia',strtotime($booking->service_schedule)) : '--'}}</span>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="py-3 d-flex gap-3 flex-wrap mb-2">
                                        <!-- Customer Info -->
                                        <div class="c1-light-bg radius-10 py-3 px-4 flex-grow-1">
                                            <h4 class="mb-2">{{translate('Customer_Information')}}</h4>
                                            <h5 class="c1 mb-2">{{isset($booking->customer) ? Str::limit($booking->customer->first_name. ' ' .$booking->customer->last_name, 30) : ''}}</h5>
                                            <ul class="list-info">
                                                <li>
                                                    <span class="material-icons">phone_iphone</span>
                                                    <a href="tel:88013756987564">{{isset($booking->customer) ? $booking->customer->phone : ''}}</a>
                                                </li>
                                                <li>
                                                    <span class="material-icons">map</span>
                                                    <p>{{Str::limit($booking->service_address->address??translate('not_available'), 100)}}</p>
                                                </li>
                                            </ul>
                                        </div>
                                        <!-- End Customer Info -->

                                        <!-- Provider Info -->
                                        <div class="c1-light-bg radius-10 py-3 px-4 flex-grow-1">
                                            <h4 class="mb-2">{{translate('Provider Information')}}</h4>
                                            @if(isset($booking->detail[0]->service))
                                                <h5 class="c1 mb-3">{{Str::limit($booking->detail[0]->service->contact_person_name??'', 30)}}</h5>
                                                <ul class="list-info">
                                                    <li>
                                                        <span class="material-icons">phone_iphone</span>
                                                        <a href="tel:88013756987564">{{$booking->detail[0]->service->contact_person_phone??''}}</a>
                                                    </li>
                                                    <li>
                                                        <span class="material-icons">map</span>
                                                        <p>{{Str::limit($booking->detail[0]->service->company_address??'', 100)}}</p>
                                                    </li>
                                                </ul>
                                            @else
                                                <p class="text-muted text-center mt-30 fz-12">{{translate('No Provider Information')}}</p>
                                            @endif
                                        </div>
                                        <!-- End Provider Info -->
                                    </div>
                                    <h3 class="mb-3">{{translate('Order_Summary')}}</h3>
                                    <div class="table-responsive border-bottom">
                                        <table class="table text-nowrap align-right align-middle mb-0">
                                            <thead>
                                            <tr>
                                                <th>{{translate('Image')}}</th>
                                                <th class="ps-lg-3">{{translate('Product')}}</th>
                                                <th>{{translate('Price')}}</th>
                                                <th>{{translate('Qty')}}</th>
                                                <th>{{translate('final_cost')}}</th>
                                                <th class="text-end">{{translate('Total')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php($sub_total=0)
                                            @foreach($booking->detail as $detail)
                                                <tr>
                                                    <td><img
                                                            width="70" height="70"
                                                            onerror="this.src='{{asset('public/assets/admin-module')}}/img/media/upload-file.png'"
                                                            src="{{asset('storage/app/public/product')}}/{{$detail->service != null ? $detail->service->image : ''}}"
                                                            alt=""></td>
                                                    <td class="text-wrap ps-lg-3">
                                                        @if(isset($detail->service))
                                                            <div class="d-flex flex-column">
                                                                <a href="{{route('admin.service.detail',[$detail->service->id])}}"
                                                                   class="fw-bold">{{Str::limit($detail->service->name, 30)}}</a>
                                                                {{--                                                        <div>{{Str::limit($detail ? $detail->variant_key : '', 50)}}</div>--}}
                                                                <div>{{Str::limit($detail->variation != null ? $detail->variation->attribute_value . ' ' . $detail->variation->attribute_name : '', 50)}}</div>

                                                            </div>
                                                        @else
                                                            <span
                                                                class="badge badge-pill badge-danger">{{translate('Service_unavailable')}}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{with_currency_symbol($detail->service_cost)}}</td>
                                                    <td>{{$detail->quantity}}</td>
                                                    <td>{{with_currency_symbol($detail->discount_amount)}}</td>
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
{{--                                                    <tr>--}}
{{--                                                        <td>{{translate('Discount')}}</td>--}}
{{--                                                        <td>{{with_currency_symbol($booking->total_discount_amount)}}</td>--}}
{{--                                                    </tr>--}}
                                                    <tr>
                                                        <td>{{translate('Campaign_Discount')}}</td>
                                                        <td>(-) {{with_currency_symbol($booking->total_campaign_discount_amount)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{translate('Coupon_Discount')}}</td>
                                                        <td>(-) {{with_currency_symbol($booking->total_coupon_discount_amount)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{translate('delivery_charge')}}</td>
                                                        <td>(+) {{with_currency_symbol($booking->total_delivery_charge + $booking->total_shipping_charge)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{translate('VAT')}}</td>
                                                        <td>(+) {{with_currency_symbol($booking->total_tax_amount)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{translate('Grand_Total')}}</strong></td>
                                                        <td>
                                                            <strong>{{with_currency_symbol(($sub_total + $booking->total_tax_amount + $booking->total_delivery_charge + $booking->total_shipping_charge) - ($booking->total_coupon_discount_amount + $booking->total_campaign_discount_amount))}}</strong>
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
                    <input type="datetime-local" id="service_schedule" name="service_schedule" class="form-control"
                           value="{{$booking->service_schedule}}">
                </div>
                <div class="p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{translate('Close')}}</button>
                    <button type="button" class="btn btn-primary"
                            id="service_schedule__submit">{{translate('Submit')}}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

    <script>
        //Booking status update
        $("#booking_status").change(function () {
            var booking_status = $("#booking_status option:selected").val();
            if (parseInt(booking_status) !== 0) {
                var route = '{{route('provider.orderpool.status_update',[$booking->id])}}' + '?booking_status=' + booking_status;
                update_booking_details(route, '{{translate('want_to_update_status')}}', 'booking_status', booking_status);
            } else {
                toastr.error('{{translate('choose_proper_status')}}');
            }
        });

        //Serviceman assign/update
        $("#serviceman_assign").change(function () {
            var serviceman_id = $("#serviceman_assign option:selected").val();
            if (serviceman_id !== 'no_serviceman') {
                var route = '{{route('provider.booking.serviceman_update',[$booking->id])}}' + '?serviceman_id=' + serviceman_id;

                update_booking_details(route, '{{translate('want_to_assign_the_serviceman')}}?', 'serviceman_assign', serviceman_id);
            } else {
                toastr.error('{{translate('choose_proper_serviceman')}}');
            }
        });

        //Payment status update
        $("#payment_status").change(function () {
            var payment_status = $("#payment_status option:selected").val();
            if (parseInt(payment_status) !== 0) {
                var route = '{{route('provider.orderpool.payment_update',[$booking->id])}}' + '?payment_status=' + payment_status;

                update_booking_details(route, '{{translate('want_to_update_status')}}', 'payment_status', payment_status);
            } else {
                toastr.error('{{translate('choose_proper_payment_status')}}');
            }
        });


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
                            // console.log('tt');return false;
                            update_component(componentId, updatedValue);
                            toastr.success(data.message, {
                                CloseButton: true,
                                ProgressBar: true
                            });

                            if (componentId === 'booking_status' || componentId === 'payment_status') {
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
                if (updatedValue === 'accepted') {
                    location.reload();
                }

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
    </script>

@endpush
