@extends('paymentmodule::layouts.master')

@push('script')

@endpush

@section('content')
    <style>
        body{
            background-color: #99999b !important;
        }
        .loading {
            /*position: absolute;*/
            /*left: 0;*/
            /*right: 0;*/
            /*top: 50%;*/
            width: 100px;
            color: #dc3545!important;
            font-weight: bold;
            margin: auto;
            -webkit-transform: translateY(-50%);
            -moz-transform: translateY(-50%);
            -o-transform: translateY(-50%);
            transform: translateY(-50%);
        }
        .loading span {
            position: absolute;
            height: 10px;
            width: 84px;
            overflow: hidden;
        }
        .loading span > i {
            position: absolute;
            height: 4px;
            width: 4px;
            border-radius: 50%;
            -webkit-animation: wait 4s infinite;
            -moz-animation: wait 4s infinite;
            -o-animation: wait 4s infinite;
            animation: wait 4s infinite;
        }
        .loading span > i:nth-of-type(1) {
            left: -28px;
            background: #0099ff;
        }
        .loading span > i:nth-of-type(2) {
            left: -21px;
            -webkit-animation-delay: 0.8s;
            animation-delay: 0.8s;
            background: #1f1f1f;
        }

        @-webkit-keyframes wait {
            0%   { left: -7px  }
            30%  { left: 52px  }
            60%  { left: 22px  }
            100% { left: 100px }
        }
        @-moz-keyframes wait {
            0%   { left: -7px  }
            30%  { left: 52px  }
            60%  { left: 22px  }
            100% { left: 100px }
        }
        @-o-keyframes wait {
            0%   { left: -7px  }
            30%  { left: 52px  }
            60%  { left: 22px  }
            100% { left: 100px }
        }
        @keyframes wait {
            0%   { left: -7px  }
            30%  { left: 52px  }
            60%  { left: 22px  }
            100% { left: 100px }
        }
    </style>
    <div class="container-fluid mt-20">
        <div class="row mb-5"></div>
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12 col-md-12 mb-30">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="page-title text-center">Tap pay</h4>
                            </div>
                            <div class="card-body p-30">
                                <form method="POST" action="{!! route('tap-pay.payment',['token'=>$token]) !!}" accept-charset="UTF-8"
                                      class="form-horizontal"
                                      role="form">

                                    <div class="form-floating mb-30 pb-4 text-center">
                                        <img class="img-fluid" style="height: 80px !important;" src="https://www.idloom.com/application/files/3016/7585/3832/TapLogo_Gray_H.png">
                                    </div>

                                        <div class="form-floating mb-30 mt-30">
                                            <label>Transaction Amount</label>
                                            <input type="text" class="form-control" name="amount" readonly placeholder="" value="{{$order_amount}}">
                                        </div>
                                    <div class="form-floating mb-30 mt-30">
                                        <label>Currency</label>
                                        <input type="text" class="form-control" name="currency" readonly placeholder="" value="KWD">
                                    </div>
                                    <div class="form-floating mb-30 mt-30">
                                        <label>Transaction ID</label>
                                        <input type="text" class="form-control" name="id" readonly placeholder="" value="{{$userid}}">
                                    </div>
                                    <div class="form-floating mb-30 mt-30">
                                        <label>Transaction For</label>
                                        <input type="text" class="form-control" name="call_from" readonly placeholder="" value="{{$call_from}}">
                                    </div>
                                    <div class="form-floating mt-3  text-center">
                                        <label class="text-danger font-weight-bold">Please do not refresh this page...</label>
                                        <div class="loading">
                                            <p>Please wait</p>
                                            <span><i></i><i></i></span>
                                        </div>
                                    </div>
                                    <div class="form-floating mb-30 mt-30">
                                        <input type="hidden" name="first_name" value={{$customer->first_name}}>
                                        <input type="hidden" name="email" value={{$customer->email}}>
                                        <input type="hidden" name="zone_id" value={{$zone_id}}>
                                        <input type="hidden" name="address_id" value={{$address_id}}>
                                        <input type="hidden" name="service_schedule" value={{$service_schedule}}>
                                        <button class="btn btn-block d-none" id="pay-button" type="submit">Payment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Tab Content -->
            </div>
        </div>
    </div>

    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("pay-button").click();
        });
    </script>

@endsection
