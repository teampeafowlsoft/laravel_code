@extends('adminmodule::layouts.master')

@section('title',translate('country_update'))

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
                        <h2 class="page-title">{{translate('country_update')}}</h2>
                    </div>

                @php
                    $name = explode(',',$country[0]->name)[0];
                    $arabic_name = explode(',',$country[0]->name)[1];
                    $country_code = explode(',',$country[0]->country_code)[0];
                    $arabic_country_code = explode(',',$country[0]->country_code)[1];
                    $currency_name = explode(',',$country[0]->currency_name)[0];
                    $arabic_currency_name = explode(',',$country[0]->currency_name)[1];
                    $currency_code = explode(',',$country[0]->currency_code)[0];
                    $arabic_currency_code = explode(',',$country[0]->currency_code)[1];
                    $country_flag = explode(',',$country[0]->country_flag)[0];
                @endphp

                <!-- Country Setup -->
                    <div class="card category-setup mb-30">
                        <div class="card-body p-30">
                            <form action="{{route('admin.country.update',[$country[0]->group_id])}}" method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <!-- Nav Tabs -->
                                    <div class="col-lg-12 mb-4 mb-lg-0">
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <ul class="nav nav--tabs nav--tabs__style2">
                                                    <input type="hidden" name="eng_lang_id" id="eng_lang_id"
                                                           value="{{$languages[0]->language_master_id}}">
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab" data-bs-target="#english"
                                                               class="nav-link active" id="english_lang">
                                                            {{$languages[0]->language_name}}                                                    </label>
                                                    </li>
                                                    <li class="nav-item">
                                                        <input type="hidden" name="arabic_lang_id"
                                                               id="arabic_lang_id"
                                                               value="{{$languages[1]->language_master_id}}">
                                                        <label data-bs-toggle="tab" data-bs-target="#arabic"
                                                               class="nav-link" id="arabic_lang">
                                                            {{$languages[1]->language_name}}
                                                        </label>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="float-end">
                                                    <a class="btn btn--secondary btn-sm"
                                                       href="{{route('admin.country.create')}}"><span
                                                            class="material-icons"
                                                            title="{{translate('Country')}}">chevron_left</span> {{translate('back')}}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    </div>
                                    <!-- End Nav Tabs -->
                                    <div class="col-lg-6 mb-4 mb-lg-0">
                                        <!-- Tab Content -->
                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="english">
                                                <div class="form-floating mb-30">
                                                    <select class="theme-input-style w-100"
                                                            name="country_type" required>
                                                        <option value="">--Select Country Type--</option>
                                                        <option
                                                            value="1" {{$country[0]->country_type == 1 ? 'selected' : ''}}>
                                                            GCC
                                                        </option>
                                                        <option
                                                            value="2" {{$country[0]->country_type == 2 ? 'selected' : ''}}>
                                                            Other
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="" name="name"
                                                           value="{{$name}}"
                                                           placeholder="Country Name" required="">
                                                    <label for="floatingInput">{{translate('Name')}}</label>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="floatingInput"
                                                           value="{{$country_code}}"
                                                           name="country_code"
                                                           placeholder="Country Code" required="">
                                                    <label for="floatingInput">{{translate('country_code')}}</label>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="floatingInput"
                                                           value="{{$currency_name}}"
                                                           name="currency_name"
                                                           placeholder="Currency Name" required="">
                                                    <label for="floatingInput">{{translate('currency_name')}}</label>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="floatingInput"
                                                           name="currency_code" value="{{$currency_code}}"
                                                           placeholder="Currency Code" required="">
                                                    <label for="floatingInput">{{translate('currency_code')}}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic">
                                                <div class="form-floating mb-30">
                                                    <select class="theme-input-style w-100"
                                                            name="arabic_country_type" required>
                                                        <option value="">{{translate('Select_any_one')}}</option>
                                                        <option
                                                            value="1" {{$country[0]->country_type == 1 ? 'selected' : ''}}>
                                                            GCC
                                                        </option>
                                                        <option
                                                            value="2" {{$country[0]->country_type == 2 ? 'selected' : ''}}>
                                                            Other
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="" name="arabic_name"
                                                           value="{{$arabic_name}}"
                                                           placeholder="Country Name" required="">
                                                    <label for="floatingInput">{{translate('arabic_Name')}}</label>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="floatingInput"
                                                           value="{{$arabic_country_code}}"
                                                           name="arabic_country_code"
                                                           placeholder="Country Code" required="">
                                                    <label for="floatingInput">{{translate('arabic_country_code')}}</label>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="floatingInput"
                                                           value="{{$arabic_currency_name}}"
                                                           name="arabic_currency_name"
                                                           placeholder="Currency Name" required="">
                                                    <label for="floatingInput">{{translate('arabic_currency_name')}}</label>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="floatingInput"
                                                           name="arabic_currency_code" value="{{$arabic_currency_code}}"
                                                           placeholder="Currency Code" required="">
                                                    <label for="floatingInput">{{translate('arabic_currency_code')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="d-flex  gap-3 gap-xl-5">
                                            <p class="opacity-75 max-w220">{{translate('image_format_-_jpg,_png,_jpeg,_gif_image
                                                size_-_
                                                maximum_size_2_MB_Image_Ratio_-_1:1')}}</p>
                                            <div>
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input" name="country_flag">
                                                    <div class="upload-file__img">
                                                        <img
                                                            onerror="this.src='{{asset('public/assets/admin-module')}}/img/media/upload-file.png'"
                                                            src="{{asset('storage/app/public/country')}}/{{$country_flag}}"
                                                            alt="">
                                                    </div>
                                                    <span class="upload-file__edit">
                                                        <span class="material-icons">edit</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-20 mt-30">
                                            <button class="btn btn--secondary"
                                                    type="reset">{{translate('reset')}}</button>
                                            <button class="btn btn--primary" type="submit">{{translate('update')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End Category Setup -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/dataTables.select.min.js"></script>
@endpush
