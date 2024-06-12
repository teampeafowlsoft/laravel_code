@extends('providermanagement::layouts.master')

@section('title',translate('Booking_Details'))

@push('css_or_js')
    <style>
        .service-img-list .service-img img {
            box-shadow: 0px 0.3125rem 0.625rem var(--shadow-color);
            height: 60% !important;
            width: 70% !important;
        }
    </style>
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
                    </ul>

                    <div class="card mb-3">
                        <div class="card-body pb-5">
                            <div
                                class="border-bottom pb-3 d-flex justify-content-between align-items-center gap-3 flex-wrap mb-2">
                                <div>
                                    <h3 class="c1 mb-2">{{translate('Service_images')}}</h3>
                                </div>
                            </div>

                            <!-- Service Images List -->
                            <div class="service-list mb-2">
                                @if(isset($booking->cart_images->first()->service_image))
                                    @foreach($booking->cart_images as $img)
                                        <div class="service-img-list">
                                            <div class="service-img">
                                                <a href="{{asset('storage/app/public/cart')}}/{{$img->service_image}}"
                                                   target="_blank">
                                                    <img
                                                        onerror="this.src='{{asset('public/assets/admin-module/img/media/service-details.png')}}'"
                                                        src="{{asset('storage/app/public/cart')}}/{{$img->service_image}}"
                                                        alt="">
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="py-3 d-flex gap-3 flex-wrap mb-2">
                                        <div class="c1-light-bg radius-10 py-3 px-4 flex-grow-1">
                                            <p class="text-muted text-center mt-30 fz-12">{{translate('No Cart_Images_Information')}}</p>
                                        </div>
                                    </div>
                                @endif
                                <!-- End Service Images List -->
                            </div>

                            <div
                                class="border-bottom pb-3 d-flex justify-content-between align-items-center gap-3 flex-wrap mt-3">
                                <div>
                                    <h3 class="c1 mb-2">{{translate('Service_videos')}}</h3>
                                </div>
                            </div>

                            <!-- Service Video List -->
                            <div class="service-list mb-2">
                                @if(isset($booking->cart_videos->first()->service_video))
                                    @foreach($booking->cart_videos as $vd)
                                        <div class="service-img-list">
                                            <div class="service-img">
                                                <a href="{{asset('storage/app/public/cartvideo')}}/{{$vd->service_video}}"
                                                   target="_blank">
                                                    <video width="320" height="240" controls>
                                                        <source
                                                            src="{{asset('storage/app/public/cartvideo')}}/{{$vd->service_video}}"
                                                            type="video/mp4">
                                                    </video>

                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="py-3 d-flex gap-3 flex-wrap mb-2">
                                        <div class="c1-light-bg radius-10 py-3 px-4 flex-grow-1">
                                            <p class="text-muted text-center mt-30 fz-12">{{translate('No Cart_Videos_Information')}}</p>
                                        </div>
                                    </div>
                                @endif
                                <!-- End Service Video List -->
                            </div>

                            <div
                                class="border-bottom pb-3 d-flex justify-content-between align-items-center gap-3 flex-wrap mt-3">
                                <div>
                                    <h3 class="c1 mb-2">{{translate('Service_pdf')}}</h3>
                                </div>
                            </div>

                            <!-- Cart PDF Info -->
                            <div class="py-3 d-flex gap-3 flex-wrap mb-2">
                                <div class="c1-light-bg radius-10 py-3 px-4 flex-grow-1">
                                    @if(isset($booking->cart_pdf->first()->service_pdf))
                                        <h5 class="c1 mb-3"><a
                                                href="{{asset('storage/app/public/cartpdf')}}/{{$booking->cart_pdf->first()->service_pdf}}"
                                                target="_blank">{{isset($booking->cart_pdf)?($booking->cart_pdf->first()->service_pdf ?? ''):''}}</a>
                                        </h5>
                                    @else
                                        <p class="text-muted text-center mt-30 fz-12">{{translate('No Cart_pdf_Information')}}</p>
                                    @endif
                                </div>
                            </div>
                            <!-- End Cart PDF Info -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

