@extends('adminmodule::layouts.master')

@section('title',translate('main_category_setup'))

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
                        <h2 class="page-title">{{translate('main_category_update')}}</h2>
                    </div>
                    @php
                            $name = !empty($maincategory[0]->name) ? $maincategory[0]->name : '';
//                            echo $name;
                            $arabic_name = !empty($maincategory[1]->name) ? $maincategory[1]->name : '';
                            $first_button_text = !empty($maincategory[0]->first_button_text) ? $maincategory[0]->first_button_text : '';
                            $first_button_text_arabic = !empty($maincategory[1]->first_button_text) ? $maincategory[1]->first_button_text : '';
                            $second_button_text = !empty($maincategory[0]->second_button_text) ? $maincategory[0]->second_button_text : '';
                            $second_button_text_arabic = !empty($maincategory[1]->second_button_text) ? $maincategory[1]->second_button_text : '';
                            $color = !empty($maincategory[0]->color) ? $maincategory[0]->color : '';
                    @endphp
                    <!-- Category Setup -->
                    <div class="card category-setup mb-30">
                        <div class="card-body p-30">
                            <form action="{{route('admin.main-category.update',[$maincategory[0]->group_id])}}" method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-lg-8 mb-5 mb-lg-0">
                                        <!-- Nav Tabs -->
                                        <div class="mb-3">
                                            <input type="hidden" name="lang_id" id="lang_id">
                                            <ul class="nav nav--tabs nav--tabs__style2">
                                                <input type="hidden" name="eng_lang_id" id="eng_lang_id"
                                                       value="{{$languages[0]->language_master_id}}">
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#english"
                                                           class="nav-link active" id="english_lang">
                                                        {{$languages[0]->language_name}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <input type="hidden" name="arabic_lang_id" id="arabic_lang_id"
                                                           value="{{$languages[1]->language_master_id}}">
                                                    <label data-bs-toggle="tab" data-bs-target="#arabic"
                                                           class="nav-link" id="arabic_lang">
                                                        {{$languages[1]->language_name}}
                                                    </label>
                                                </li>
                                            </ul>
                                        </div>
                                        <hr>
                                        <!-- End Nav Tabs -->

                                        <!-- Tab Content -->
                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="english">
                                                <div class="d-flex flex-column">
                                                    <div class="form-floating mb-30">
                                                        <input type="text" name="name" class="form-control"
                                                               value="{{!empty($name) ? $name : ''}}"
                                                               placeholder="{{translate('main_category_name')}}"
                                                               required>
                                                        <label>{{translate('main_category_name')}}</label>
                                                        <span class="text-danger">Note : Do not enter Name with comma (,) special character</span>
                                                    </div>

                                                    <div class="row mb-30">
                                                        <div class="col-md-6">
                                                            <select class="js-select theme-input-style w-100" required name="eng_service_category_id"
                                                                    id="eng_service_category_id">
                                                                <option value="" selected disabled>{{translate('Select_Service_Category')}}</option>
                                                                @foreach($service_categories as $item)
                                                                    <option
                                                                        value="{{$item->id}}" {{$selected_service_categories[0]==$item->id?'selected':''}}>
                                                                        {{$item->name}}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="js-select theme-input-style w-100" required name="eng_product_category_id"
                                                                    id="eng_product_category_id">
                                                                <option value="" selected disabled>{{translate('Select_Product_Category')}}</option>
                                                                @foreach($product_categories as $item)
                                                                    <option
                                                                        value="{{$item->id}}" {{$selected_product_categories[0]==$item->id?'selected':''}}>
                                                                        {{$item->name}}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-30">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-30">
                                                                <input type="text" name="button_name_1" class="form-control"
                                                                       value="{{!empty($first_button_text) ? $first_button_text : ''}}"
                                                                       placeholder="{{translate('First_Button_Text')}}"
                                                                       required>
                                                                <label>{{translate('First_Button_Text')}}</label>
                                                                <span class="text-danger">Note : Do not enter Name with comma (,).</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-30">
                                                                <input type="text" name="button_name_2" class="form-control"
                                                                       value="{{!empty($second_button_text) ? $second_button_text : ''}}"
                                                                       placeholder="{{translate('Second_Button_Text')}}"
                                                                       required>
                                                                <label>{{translate('Second_Button_Text')}}</label>
                                                                <span class="text-danger">Note : Do not enter Name with comma (,).</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic">
                                                <div class="d-flex flex-column">
                                                    <div class="form-floating mb-30">
                                                        <input type="text" name="name_arabic" class="form-control"
                                                               value="{{!empty($arabic_name) ? $arabic_name : ''}}"
                                                               placeholder="{{translate('main_category_name_arabic')}}"
                                                               required>
                                                        <label>{{translate('main_category_name_arabic')}}</label>
                                                        <span class="text-danger">Note : Do not enter Name with comma (,) special character</span>
                                                    </div>

                                                    <div class="row mb-30">
                                                        <div class="col-md-6">
                                                            <select class="js-select theme-input-style w-100" required name="eng_service_category_id_arabic"
                                                                    id="eng_service_category_id_arabic">
                                                                <option value="" selected disabled>{{translate('Select_Service_Category_arabic')}}</option>
                                                                @foreach($service_categories_arabic as $item)
                                                                    <option
                                                                        value="{{$item->id}}" {{$selected_service_categories_arabic[0]==$item->id?'selected':''}}>
                                                                        {{$item->name}}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="js-select theme-input-style w-100" required name="eng_product_category_id_arabic"
                                                                    id="eng_product_category_id_arabic">
                                                                <option value="" selected disabled>{{translate('Select_Product_Category_arabic')}}</option>
                                                                @foreach($product_categories_arabic as $item)
                                                                    <option
                                                                        value="{{$item->id}}" {{$selected_product_categories_arabic[0]==$item->id?'selected':''}}>
                                                                        {{$item->name}}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-30">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-30">
                                                                <input type="text" name="button_name_1_arabic" class="form-control"
                                                                       value="{{!empty($first_button_text_arabic) ? $first_button_text_arabic : ''}}"
                                                                       placeholder="{{translate('First_Button_Text_Arabic')}}"
                                                                       required>
                                                                <label>{{translate('First_Button_Text_Arabic')}}</label>
                                                                <span class="text-danger">Note : Do not enter Name with comma (,).</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-30">
                                                                <input type="text" name="button_name_2_arabic" class="form-control"
                                                                       value="{{!empty($second_button_text_arabic) ? $second_button_text_arabic : ''}}"
                                                                       placeholder="{{translate('Second_Button_Text_Arabic')}}"
                                                                       required>
                                                                <label>{{translate('Second_Button_Text_Arabic')}}</label>
                                                                <span class="text-danger">Note : Do not enter Name with comma (,).</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-floating mb-30">
                                                    <input type="color" name="color" id="colorpicker" class="form-control"
                                                           pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$"
                                                           value="{{!empty($color) ? $color : ''}}"
                                                           required>
                                                    <label>{{translate('Select_Color')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating mb-30">
                                                    <input type="text" name="color_code" id="hexcolor" class="form-control"
                                                           pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$"
                                                           value="{{!empty($color) ? $color : ''}}"
                                                           required>
                                                    <label>{{translate('Color_Code')}}</label>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-4 mt-5">
                                        <div class="d-flex  gap-3 gap-xl-5">
                                            <div>
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input" name="image">
                                                    <div class="upload-file__img">
                                                        <img
                                                            onerror="this.src='{{asset('public/assets/admin-module')}}/img/media/upload-file.png'"
                                                            src="{{asset('storage/app/public/main_category')}}/{{$maincategory[0]->image}}"
                                                            alt="">
                                                        <span class="upload-file__edit">
                                                            <span class="material-icons">edit</span>
                                                        </span>
                                                    </div>

                                                </div>
                                                <p class="mt-3 opacity-75 max-w220">{{translate('image_format_-_jpg,_png,_jpeg,_gif_image
                                                size_-_maximum_size_2_MB_Image_Ratio_-_1:1')}}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-20 mt-30">
                                            <button class="btn btn--secondary"
                                                    type="reset">{{translate('reset')}}</button>
                                            <button class="btn btn--primary" type="submit">{{translate('submit')}}
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
    <script>
        $(document).ready(function () {
            $('.js-select').select2();
            $('#colorpicker').on('input', function() {
                $('#hexcolor').val(this.value);
            });
            $('#hexcolor').on('input', function() {
                $('#colorpicker').val(this.value);
            });
        });
    </script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/dataTables.select.min.js"></script>
@endpush
