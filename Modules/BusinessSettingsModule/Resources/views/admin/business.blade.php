@extends('adminmodule::layouts.master')

@section('title',translate('business_setup'))

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
                        <h2 class="page-title">{{translate('business_setup')}}</h2>
                    </div>

                    <!-- Nav Tabs -->
                    <div class="mb-3">
                        <ul class="nav nav--tabs nav--tabs__style2">
                            <li class="nav-item">
                                <a href="{{url()->current()}}?web_page=business_setup"
                                   class="nav-link {{$web_page=='business_setup'?'active':''}}">
                                    {{translate('business_Information_Setup')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url()->current()}}?web_page=service_setup"
                                   class="nav-link {{$web_page=='service_setup'?'active':''}}">
                                    {{translate('service_Setup')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{url()->current()}}?web_page=promotional_setup"
                                   class="nav-link {{$web_page=='promotional_setup'?'active':''}}">
                                    {{translate('promotional_Setup')}}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- End Nav Tabs -->

                    <!-- Tab Content -->
                    @if($web_page=='business_setup')
                        <div class="tab-content">
                            <div class="tab-pane fade {{$web_page=='business_setup'?'active show':''}}">
                                <div class="card">
                                    <div class="card-body p-30">
                                        <form action="javascript:void(0)" method="POST" id="business-info-update-form">
                                            @csrf
                                            @method('PUT')
                                            <div class="discount-type">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-30">
                                                            <div class="form-floating">
                                                                <input type="text" class="form-control"
                                                                       name="business_name"
                                                                       placeholder="{{translate('business_name')}} *"
                                                                       required=""
                                                                       value="{{$data_values->where('key_name','business_name')->first()->live_values}}">
                                                                <label>{{translate('business_name')}} *</label>
                                                            </div>
                                                        </div>

                                                        <div class="mb-30">
                                                            <div class="form-floating">
                                                                <input type="text" class="form-control"
                                                                       name="business_phone"
                                                                       placeholder="{{translate('business_phone')}} *"
                                                                       required=""
                                                                       oninput="this.value = this.value.replace(/[^+\d]+$/g, '').replace(/(\..*)\./g, '$1');"
                                                                       value="{{$data_values->where('key_name','business_phone')->first()->live_values}}">
                                                                <label>{{translate('business_phone')}} *</label>
                                                                <small class="d-block mt-1 text-danger">* ( {{translate('Country_Code_Required')}} )</small>
                                                            </div>
                                                        </div>
                                                        <div class="mb-30">
                                                            <div class="form-floating">
                                                                <input type="email" class="form-control"
                                                                       name="business_email"
                                                                       placeholder="{{translate('email')}} *"
                                                                       required=""
                                                                       value="{{$data_values->where('key_name','business_email')->first()->live_values}}">
                                                                <label>{{translate('email')}} *</label>
                                                            </div>
                                                        </div>
                                                        <div class="mb-30">
                                                            <div class="form-floating">
                                                            <textarea class="form-control" name="business_address"
                                                                      placeholder="{{translate('address')}} *"
                                                                      required="">{{$data_values->where('key_name','business_address')->first()->live_values}}</textarea>
                                                                <label>{{translate('address')}} *</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-30 d-flex flex-column align-items-center gap-2">
                                                                    <p class="title-color">{{translate('favicon')}}</p>
                                                                    <div class="upload-file mb-30">
                                                                        <input type="file" class="upload-file__input" name="business_favicon">
                                                                        <div class="upload-file__img">
                                                                            <img onerror="this.src='{{asset('public/assets/admin-module/img/media/upload-file.png')}}'" src="{{asset('storage/app/public/business')}}/{{$data_values->where('key_name','business_favicon')->first()->live_values}}"
                                                                                alt="">
                                                                        </div>
                                                                        <span class="upload-file__edit">
                                                                            <span class="material-icons">edit</span>
                                                                        </span>
                                                                    </div>
                                                                    <p class="opacity-75 max-w220">{{translate('Image format - jpg, png,
                                                                    jpeg, gif Image Size - maximum size 2 MB Image Ratio -
                                                                    1:1')}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-30 d-flex flex-column align-items-center gap-2">
                                                                    <p class="title-color">{{translate('logo')}}</p>
                                                                    <div class="upload-file mb-30 max-w-100">
                                                                        <input type="file"
                                                                                class="upload-file__input"
                                                                                name="business_logo">
                                                                        <div class="upload-file__img upload-file__img_banner ratio-none">
                                                                            <img onerror="this.src='{{asset('public/assets/admin-module/img/media/banner-upload-file.png')}}'"
                                                                                src="{{asset('storage/app/public/business')}}/{{$data_values->where('key_name','business_logo')->first()->live_values}}"
                                                                                alt="">
                                                                        </div>
                                                                        <span class="upload-file__edit">
                                                                            <span class="material-icons">edit</span>
                                                                        </span>
                                                                    </div>
                                                                    <p class="opacity-75 max-w220">{{translate('Image format - jpg, png, jpeg, gif Image Size - maximum size 2 MB Image Ratio - 3:1')}}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 col-12 mb-30">
                                                        @php($country_code=$data_values->where('key_name','country_code')->first()->live_values)
                                                        <select class="js-select theme-input-style w-100"
                                                                name="country_code">
                                                            <option value="0" selected disabled>{{translate('---Select_Country---')}}</option>
                                                            @foreach(COUNTRIES as $country)
                                                                <option
                                                                    value="{{$country['code']}}" {{$country_code==$country['code']?'selected':''}}>
                                                                    {{$country['name']}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-30">
                                                        @php($currency_code=$data_values->where('key_name','currency_code')->first()->live_values)
                                                        <select class="js-select theme-input-style w-100"
                                                                name="currency_code">
                                                            <option value="0" selected disabled>{{translate('---Select_Currency---')}}</option>
                                                            @foreach(CURRENCIES as $currency)
                                                                <option
                                                                    value="{{$currency['code']}}" {{$currency_code==$currency['code']?'selected':''}}>
                                                                    {{$currency['name']}} ( {{$currency['symbol']}} )
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-30">
                                                        @php($position=$data_values->where('key_name','currency_symbol_position')->first()->live_values)
                                                        <select class="js-select theme-input-style w-100"
                                                                name="currency_symbol_position">
                                                            <option value="0" selected disabled>{{translate('---Select_Corrency_Symbol_Position---')}}</option>
                                                            <option value="right" {{$position=='right'?'selected':''}}>
                                                                {{translate('right')}}
                                                            </option>
                                                            <option value="left" {{$position=='left'?'selected':''}}>
                                                                {{translate('left')}}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-30">
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control"
                                                                   name="currency_decimal_point"
                                                                   min="0"
                                                                   max="10"
                                                                   placeholder="{{translate('ex: 2')}} *"
                                                                   required=""
                                                                   value="{{$data_values->where('key_name','currency_decimal_point')->first()->live_values}}">
                                                            <label>{{translate('decimal_point_after_currency')}}
                                                                *</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-30">
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control"
                                                                   name="default_commission"
                                                                   min="0"
                                                                   max="100"
                                                                   step="any"
                                                                   placeholder="{{translate('ex: 2')}} *"
                                                                   required=""
                                                                   value="{{$data_values->where('key_name','default_commission')->first()->live_values}}">
                                                            <label>{{translate('default_commission_for_provider')}} ( %
                                                                )
                                                                *</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-30">
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control"
                                                                   name="pagination_limit"
                                                                   placeholder="{{translate('ex: 2')}} *"
                                                                   min="1"
                                                                   required=""
                                                                   value="{{$data_values->where('key_name','pagination_limit')->first()->live_values}}">
                                                            <label>{{translate('pagination_limit')}} *</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-30">
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control"
                                                                   name="minimum_withdraw_amount"
                                                                   placeholder="{{translate('ex: 100')}} *"
                                                                   min="1"
                                                                   step="any"
                                                                   required
                                                                   value="{{$data_values->where('key_name','minimum_withdraw_amount')->first()->live_values??''}}">
                                                            <label>{{translate('minimum_withdraw_amount')}} *</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-30">
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control"
                                                                   name="maximum_withdraw_amount"
                                                                   placeholder="{{translate('ex: 2000')}} *"
                                                                   min="1"
                                                                   step="any"
                                                                   required
                                                                   value="{{$data_values->where('key_name','maximum_withdraw_amount')->first()->live_values??''}}">
                                                            <label>{{translate('maximum_withdraw_amount')}} *</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-30">
                                                        @php($time_zone=$data_values->where('key_name','time_zone')->first()->live_values)
                                                        <select class="js-select theme-input-style w-100"
                                                                name="time_zone">
                                                            <option value="0" selected disabled>{{translate('---Select_Time_Zone---')}}</option>
                                                            @foreach(TIME_ZONES as $time)
                                                                <option
                                                                    value="{{$time['tzCode']}}" {{$time_zone==$time['tzCode']?'selected':''}}>
                                                                    {{$time['tzCode']}} UTC {{$time['utc']}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-30">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control" name="footer_text"
                                                                   placeholder="{{translate('ex:_al_right_reserved')}} *"
                                                                   required=""
                                                                   value="{{$data_values->where('key_name','footer_text')->first()->live_values}}">
                                                            <label>{{translate('footer_text')}} *</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2 justify-content-end">
                                                <button type="reset" class="btn btn-secondary">
                                                    {{translate('reset')}}
                                                </button>
                                                <button type="submit" class="btn btn--primary">
                                                    {{translate('update')}}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($web_page=='service_setup')
                        <div class="tab-content">
                            <div class="tab-pane fade {{$web_page=='service_setup'?'active show':''}}"
                                 id="business-info">
                                <div class="card">
                                    <div class="card-body p-30">
                                        <div class="table-responsive">
                                            <table id="example" class="table align-middle">
                                                <thead>
                                                <tr>
                                                    <th>{{translate('Actions')}}</th>
                                                    <th>{{translate('Status')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php($array=['provider_can_cancel_booking','service_man_can_cancel_booking','provider_self_registration'])
                                                @foreach($data_values->whereIn('key_name',$array)->all() as $value)
                                                    <tr>
                                                        <td class="text-capitalize">{{str_replace('_',' ',$value['key_name'])}}</td>

                                                        <td>
                                                            <label class="switcher">
                                                                <input class="switcher_input"
                                                                       onclick="update_action_status('{{$value['key_name']}}',$(this).is(':checked')===true?1:0)"
                                                                       type="checkbox" {{$value->live_values?'checked':''}}>
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
                            </div>
                        </div>
                    @endif

                    @if($web_page=='promotional_setup')
                        <div class="tab-content">
                            <div class="tab-pane fade {{$web_page=='promotional_setup'?'active show':''}}">
                                <div class="row">
                                    <!-- Normal Discount -->
                                    @php($data = $data_values->where('key_name', 'discount_cost_bearer')->first()->live_values ?? null)
                                    <div class="col-lg-6 col-12 mb-30">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="page-title d-flex align-items-center gap-2">
                                                    <i class="material-icons">redeem</i>
                                                    {{translate('Normal_Discount')}}
                                                </h4>
                                            </div>
                                            <div class="card-body p-30">
                                                <h5 class="pb-4">{{translate('Discount_Cost_Bearer')}}</h5>
                                                <form action="{{route('admin.business-settings.set-promotion-setup')}}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="d-flex flex-column flex-sm-row flex-wrap gap-3">
                                                        <div class="d-flex align-items-start flex-column gap-3 gap-xl-4 mb-30 flex-grow-1">
                                                            <div class="custom-radio">
                                                                <input type="radio" id="admin-select__discount" name="bearer" value="admin" {{isset($data) && $data['bearer'] == 'admin' ? 'checked' : ''}}>
                                                                <label for="admin-select__discount">{{translate('Admin')}}</label>
                                                            </div>
                                                            <div class="custom-radio">
                                                                <input type="radio" id="provider-select__discount" name="bearer" value="provider" {{isset($data) && $data['bearer'] == 'provider' ? 'checked' : ''}}>
                                                                <label for="provider-select__discount">{{translate('Provider')}}</label>
                                                            </div>
                                                            <div class="custom-radio">
                                                                <input type="radio" id="both-select__discount" name="bearer" value="both" {{isset($data) && $data['bearer'] == 'both' ? 'checked' : ''}}>
                                                                <label for="both-select__discount">{{translate('Both')}}</label>
                                                            </div>
                                                        </div>

                                                        <div class="flex-grow-1 {{isset($data) && ($data['bearer'] != 'admin' && $data['bearer'] != 'provider') ? '' : 'd-none'}}" id="bearer-section__discount">
                                                            <div class="mb-30">
                                                                <div class="form-floating">
                                                                    <input type="number" class="form-control"
                                                                           name="admin_percentage"
                                                                           id="admin_percentage__discount"
                                                                           placeholder="{{translate('Admin_Percentage')}} (%)"
                                                                           value="{{!is_null($data) ? $data['admin_percentage'] : ''}}"
                                                                           min="0" max="100" step="any">
                                                                    <label>{{translate('Admin_Percentage')}} (%)</label>
                                                                </div>
                                                            </div>
                                                            <div class="mb-30">
                                                                <div class="form-floating">
                                                                    <input type="number" class="form-control"
                                                                           name="provider_percentage"
                                                                           id="provider_percentage__discount"
                                                                           placeholder="{{translate('Provider_Percentage')}} (%)"
                                                                           value="{{!is_null($data) ? $data['provider_percentage'] : ''}}"
                                                                           min="0" max="100" step="any">
                                                                    <label>{{translate('Provider_Percentage')}} (%)</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="text" name="type" value="discount" class="d-none">

                                                    <div class="d-flex justify-content-end gap-20">
                                                        <button type="submit" class="btn btn--primary demo_check">
                                                            {{translate('update')}}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Campaign Discount -->
                                    @php($data = $data_values->where('key_name', 'campaign_cost_bearer')->first()->live_values ?? null)
                                    <div class="col-lg-6 col-12 mb-30">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="page-title d-flex align-items-center gap-2">
                                                    <i class="material-icons">campaign</i>
                                                    {{translate('Campaign_Discount')}}
                                                </h4>
                                            </div>
                                            <div class="card-body p-30">
                                                <h5 class="pb-4">{{translate('Campaign_Cost_Bearer')}}</h5>
                                                <form action="{{route('admin.business-settings.set-promotion-setup')}}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="d-flex flex-column flex-sm-row flex-wrap gap-3">
                                                        <div class="d-flex align-items-start flex-column gap-3 gap-xl-4 mb-30 flex-grow-1">
                                                            <div class="custom-radio">
                                                                <input type="radio" id="admin-select__campaign" name="bearer" value="admin" {{isset($data) && $data['bearer'] == 'admin' ? 'checked' : ''}}>
                                                                <label for="admin-select__campaign">{{translate('Admin')}}</label>
                                                            </div>
                                                            <div class="custom-radio">
                                                                <input type="radio" id="provider-select__campaign" name="bearer" value="provider" {{isset($data) && $data['bearer'] == 'provider' ? 'checked' : ''}}>
                                                                <label for="provider-select__campaign">{{translate('Provider')}}</label>
                                                            </div>
                                                            <div class="custom-radio">
                                                                <input type="radio" id="both-select__campaign" name="bearer" value="both" {{isset($data) && $data['bearer'] == 'both' ? 'checked' : ''}}>
                                                                <label for="both-select__campaign">{{translate('Both')}}</label>
                                                            </div>
                                                        </div>

                                                        <div class="flex-grow-1 {{isset($data) && ($data['bearer'] != 'admin' && $data['bearer'] != 'provider') ? '' : 'd-none'}}" id="bearer-section__campaign">
                                                            <div class="mb-30">
                                                                <div class="form-floating">
                                                                    <input type="number" class="form-control"
                                                                           name="admin_percentage"
                                                                           id="admin_percentage__campaign"
                                                                           placeholder="{{translate('Admin_Percentage')}} (%)"
                                                                           value="{{!is_null($data) ? $data['admin_percentage'] : ''}}"
                                                                           min="0" max="100" step="any">
                                                                    <label>{{translate('Admin_Percentage')}} (%)</label>
                                                                </div>
                                                            </div>
                                                            <div class="mb-30">
                                                                <div class="form-floating">
                                                                    <input type="number" class="form-control"
                                                                           name="provider_percentage"
                                                                           id="provider_percentage__campaign"
                                                                           placeholder="{{translate('Provider_Percentage')}} (%)"
                                                                           value="{{!is_null($data) ? $data['provider_percentage'] : ''}}"
                                                                           min="0" max="100" step="any">
                                                                    <label>{{translate('Provider_Percentage')}} (%)</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="text" name="type" value="campaign" class="d-none">

                                                    <div class="d-flex justify-content-end gap-20">
                                                        <button type="submit" class="btn btn--primary demo_check">
                                                            {{translate('update')}}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Coupon Discount -->
                                    @php($data = $data_values->where('key_name', 'coupon_cost_bearer')->first()->live_values ?? null)
                                    <div class="col-lg-6 col-12 mb-30">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="page-title d-flex align-items-center gap-2">
                                                    <i class="material-icons">sell</i>
                                                    {{translate('Coupon_Discount')}}
                                                </h4>
                                            </div>
                                            <div class="card-body p-30">
                                                <h5 class="pb-4">{{translate('Coupon_Cost_Bearer')}}</h5>
                                                <form action="{{route('admin.business-settings.set-promotion-setup')}}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="d-flex flex-column flex-sm-row flex-wrap gap-3">
                                                        <div class="d-flex align-items-start flex-column gap-3 gap-xl-4 mb-30 flex-grow-1">
                                                            <div class="custom-radio">
                                                                <input type="radio" id="admin-select__coupon" name="bearer" value="admin" {{isset($data) && $data['bearer'] == 'admin' ? 'checked' : ''}}>
                                                                <label for="admin-select__coupon">{{translate('Admin')}}</label>
                                                            </div>
                                                            <div class="custom-radio">
                                                                <input type="radio" id="provider-select__coupon" name="bearer" value="provider" {{isset($data) && $data['bearer'] == 'provider' ? 'checked' : ''}}>
                                                                <label for="provider-select__coupon">{{translate('Provider')}}</label>
                                                            </div>
                                                            <div class="custom-radio">
                                                                <input type="radio" id="both-select__coupon" name="bearer" value="both" {{isset($data) && $data['bearer'] == 'both' ? 'checked' : ''}}>
                                                                <label for="both-select__coupon">{{translate('Both')}}</label>
                                                            </div>
                                                        </div>

                                                        <div class="flex-grow-1 {{isset($data) && ($data['bearer'] != 'admin' && $data['bearer'] != 'provider') ? '' : 'd-none'}}" id="bearer-section__coupon">
                                                            <div class="mb-30">
                                                                <div class="form-floating">
                                                                    <input type="number" class="form-control"
                                                                           name="admin_percentage"
                                                                           id="admin_percentage__coupon"
                                                                           placeholder="{{translate('Admin_Percentage')}} (%)"
                                                                           value="{{!is_null($data) ? $data['admin_percentage'] : ''}}"
                                                                           min="0" max="100" step="any">
                                                                    <label>{{translate('Admin_Percentage')}} (%)</label>
                                                                </div>
                                                            </div>
                                                            <div class="mb-30">
                                                                <div class="form-floating">
                                                                    <input type="number" class="form-control"
                                                                           name="provider_percentage"
                                                                           id="provider_percentage__coupon"
                                                                           placeholder="{{translate('Provider_Percentage')}} (%)"
                                                                           value="{{!is_null($data) ? $data['provider_percentage'] : ''}}"
                                                                           min="0" max="100" step="any">
                                                                    <label>{{translate('Provider_Percentage')}} (%)</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="text" name="type" value="coupon" class="d-none">

                                                    <div class="d-flex justify-content-end gap-20">
                                                        <button type="submit" class="btn btn--primary demo_check">
                                                            {{translate('update')}}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                <!-- End Tab Content -->

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
                    toastr.success('{{translate('successfully_updated')}}');
                },
                error: function (jqXHR, exception) {
                    toastr.error(jqXHR.responseJSON.message);
                    setTimeout(location.reload.bind(location), 1000);
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
                        url: "{{route('admin.business-settings.set-service-setup')}}",
                        data: {
                            key: key_name,
                            value: value,
                        },
                        type: 'put',
                        success: function (response) {
                            toastr.success('{{translate('successfully_updated')}}')
                        },
                        error: function () {

                        }
                    });
                }
            })
        }
    </script>

    <script>
        $(window).on('load', function() {
            //DISCOUNT SECTION
            $("#admin-select__discount, #provider-select__discount").on('click', function (e) {
                $("#bearer-section__discount").addClass('d-none');
            })

            $("#both-select__discount").on('click', function (e) {
                $("#bearer-section__discount").removeClass('d-none');
            })

            $( "#admin_percentage__discount" ).keyup(function(e) {
                if(this.value >=0 && this.value <= 100) {
                    $( "#provider_percentage__discount" ).val( (100-this.value) );
                }
            });

            $( "#provider_percentage__discount" ).keyup(function(e) {
                if(this.value >=0 && this.value <= 100) {
                    $( "#admin_percentage__discount" ).val( (100-this.value) );
                }
            });

            //CAMPAIGN SECTION
            $("#admin-select__campaign, #provider-select__campaign").on('click', function (e) {
                $("#bearer-section__campaign").addClass('d-none');
            })

            $("#both-select__campaign").on('click', function (e) {
                $("#bearer-section__campaign").removeClass('d-none');
            })

            $( "#admin_percentage__campaign" ).keyup(function(e) {
                if(this.value >=0 && this.value <= 100) {
                    $( "#provider_percentage__campaign" ).val( (100-this.value) );
                }
            });

            $( "#provider_percentage__campaign" ).keyup(function(e) {
                if(this.value >=0 && this.value <= 100) {
                    $( "#admin_percentage__campaign" ).val( (100-this.value) );
                }
            });

            //COUPON SECTION
            $("#admin-select__coupon, #provider-select__coupon").on('click', function (e) {
                $("#bearer-section__coupon").addClass('d-none');
            })

            $("#both-select__coupon").on('click', function (e) {
                $("#bearer-section__coupon").removeClass('d-none');
            })

            $( "#admin_percentage__coupon" ).keyup(function(e) {
                if(this.value >=0 && this.value <= 100) {
                    $( "#provider_percentage__coupon" ).val( (100-this.value) );
                }
            });

            $( "#provider_percentage__coupon" ).keyup(function(e) {
                if(this.value >=0 && this.value <= 100) {
                    $( "#admin_percentage__coupon" ).val( (100-this.value) );
                }
            });
        })
    </script>
@endpush
