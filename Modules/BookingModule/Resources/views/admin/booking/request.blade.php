@extends('adminmodule::layouts.master')

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
                        <li class="nav-item">
                            <a class="nav-link {{$web_page=='request'?'active':''}}"
                               href="{{url()->current()}}?web_page=request">{{translate('Request')}}</a>
                        </li>
                    </ul>

                    <div class="card mb-3">
                        <div class="card-body pb-5">
                            <div
                                class="border-bottom pb-3 d-flex justify-content-between align-items-center gap-3 flex-wrap mb-2">
                                <div>
                                    <h3 class="c1 mb-2">{{translate('Provider Information')}}</h3>
                                </div>
                            </div>

                            <!-- Provider Imformation -->
                            <div class="service-lists mb-2">
                                <div class="c1-light-bg radius-10 py-3 px-4 flex-grow-1">
                                    @if(isset($providers))
                                        <div class="row">
                                            @foreach($providers as $providers_select_ids)
                                                <div class="provides mb-20 col-12 col-md-3">
                                                    <h5 class="c1 mb-3">{{Str::limit($providers_select_ids->company_name??'', 30)}}</h5>
                                                    <ul class="list-info">
                                                        <li>
                                                            <span class="material-icons">phone_iphone</span>
                                                            <a href="tel:88013756987564">{{$providers_select_ids->contact_person_phone??''}}</a>
                                                        </li>
                                                        <li>
                                                            <span class="material-icons">map</span>
                                                            <p>{{Str::limit($providers_select_ids->company_address??'', 100)}}</p>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted text-center mt-30 fz-12">{{translate('No Provider Information')}}</p>
                                    @endif
                                </div>
                                <!-- End Service Images List -->
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection
