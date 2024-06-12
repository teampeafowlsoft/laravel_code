<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Page Title -->
    <title>{{translate('Provider_Registration')}}</title>

    <!-- Meta Data -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />

    <!-- Favicon -->
    <link rel="shortcut icon"
          href="{{asset('storage/app/public/business')}}/{{(business_config('business_favicon', 'business_information'))->live_values ?? null}}"/>

    <!-- Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet"/>

    <!-- ======= BEGIN GLOBAL MANDATORY STYLES ======= -->
    <link href="{{asset('public/assets/provider-module')}}/css/material-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('public/assets/provider-module')}}/css/bootstrap.min.css" />
    <link rel="stylesheet" href="{{asset('public/assets/provider-module')}}/plugins/perfect-scrollbar/perfect-scrollbar.min.css"/>
    <!-- ======= END BEGIN GLOBAL MANDATORY STYLES ======= -->

    <!-- ======= MAIN STYLES ======= -->
    <link rel="stylesheet" href="{{asset('public/assets/provider-module')}}/css/style.css" />
    <!-- ======= END MAIN STYLES ======= -->
    <link rel="stylesheet" href="{{asset('public/assets/provider-module')}}/css/toastr.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.css">

</head>

<body>
<!-- Preloader -->
<div class="preloader"></div>
<!-- End Preloader -->

<!-- Login Form -->
<div class="register-form dark-support" data-bg-img="{{asset('public/assets/provider-module')}}/img/media/login-bg.png">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <form action="{{route('provider.auth.sign-up-submit')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card my-5 ov-hidden">
                        <div class="register-wrap">
                            <div class="register-left">
                                <img class="login-img" src="{{asset('public/assets/provider-module')}}/img/media/login-img.png"
                                     alt="">
                            </div>
                            <div class="register-right-wrap card-body px-xl-5">
                                <div class="text-center mb-5">
                                    <img class="login-img login-logo mb-2"
                                         src="{{asset('storage/app/public/business')}}/{{business_config('business_logo', 'business_information')->live_values??''}}"
                                         onerror="this.src='{{asset('public/assets/provider-module/img/logo.png')}}'"
                                         alt="">
                                    <h5 class="text-uppercase c1 mb-3">{{ (business_config('business_name', 'business_information'))->live_values ?? 'Demandium' }}</h5>
                                </div>

                                <div id="register-vertical-steps">
                                    <h3>Step 1 <span class="step-info">{{translate('General_Information')}}</span></h3>
                                    <section>
                                        <div class="">
                                            <div class="mb-4">
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control"
                                                               value="{{old('company_name')}}" name="company_name"
                                                               placeholder="{{translate('Company_/_Individual_Name_english')}}">
                                                        <label>{{translate('Company_/_Individual_Name_english')}}</label>
                                                    </div>
                                                </div>
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control"
                                                               value="{{old('company_name_arabic')}}"
                                                               name="company_name_arabic"
                                                               placeholder="{{translate('Company_/_Individual_Name_arabic')}}">
                                                        <label>{{translate('Company_/_Individual_Name_arabic')}}</label>
                                                    </div>
                                                </div>
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="tel" class="form-control" value="{{old('company_phone')}}" name="company_phone" placeholder="{{translate('Phone')}}">
                                                        <label>{{translate('Company_Phone')}}</label>
                                                        <small class="text-danger d-flex mt-1">{{translate('* ( Country_Code_Required )')}}</small>
                                                    </div>
                                                </div>
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="email" class="form-control"
                                                               value="{{old('company_email')}}" name="company_email"
                                                               placeholder="{{translate('Email')}}">
                                                        <label>{{translate('Company_Email')}}</label>
                                                    </div>
                                                </div>
                                                <div class="mb-30">
                                                    <select name="zone_id" id="zone_id" class="form-control">
                                                        <option value="0" selected disabled>{{translate('Select_Zone')}}</option>
                                                        @foreach($zones as $zone)
                                                            <option value="{{$zone->id}}" {{old('zone_id')==$zone->id?'selected':''}}>{{$zone->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-floating">
                                                        <textarea class="form-control" name="company_address" placeholder="{{translate('Address')}}">{{old('company_address')}}</textarea>
                                                        <label>{{translate('Address')}}</label>
                                                    </div>
                                                </div>
                                                <div class="mb-3" id="category-selector">
                                                    <select class="category-select theme-input-style w-100"
                                                            name="category_id[]" id="category_id" multiple="multiple"
                                                            required
                                                            onChange="getCategory();">
                                                        <option value="0">{{translate('Category_Name')}}</option>
                                                        @foreach($categories as $category)
                                                            <option
                                                                value="{{$category->id}}">{{$category->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3" id="sub-category-selector">
                                                    <select class="subcategory-select theme-input-style w-100"
                                                            name="sub_category_id[]" id="sub_category_id"
                                                            multiple="multiple" required>
                                                        <option value="0">{{translate('Sub_Category_Name')}}</option>
                                                    </select>
                                                </div>
                                                <div class="mb-4">
                                                    <div class="d-flex flex-wrap justify-content-between gap-3">
                                                        <p class="opacity-75 max-w220">{{translate('Image_format_-_jpg,_png,_jpeg,_gif_Image_Size_-_maximum_size_2_MB_Image_Ratio_-_1:1')}}</p>

                                                        <div>
                                                            <div class="upload-file">
                                                                <input type="file" class="upload-file__input" name="logo">
                                                                <span class="upload-file__edit">
                                                                    <span class="material-icons">edit</span>
                                                                </span>
                                                                <div class="upload-file__img">
                                                                    <img src="{{asset('public/assets/provider-module')}}/img/media/upload-file.png"
                                                                         alt="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    <h3>Step 2 <span class="step-info">{{translate('Contact_Person_Info')}}</span></h3>
                                    <section>
                                        <div class="">
                                            <div class="mb-4">
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" value="{{old('contact_person_name')}}" name="contact_person_name" placeholder="{{translate('Contact_Person_Name')}}">
                                                        <label>{{translate('Name')}}</label>
                                                    </div>
                                                </div>
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="tel" class="form-control" value="{{old('contact_person_phone')}}" name="contact_person_phone" placeholder="{{translate('Contact_Person_Phone')}}">
                                                        <label>{{translate('Phone')}}</label>
                                                        <small class="text-danger d-flex mt-1">{{translate('* ( Country_Code_Required )')}}</small>
                                                    </div>
                                                </div>
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="email" class="form-control" value="{{old('contact_person_email')}}" name="contact_person_email" placeholder="{{translate('Email')}}">
                                                        <label>{{translate('Email')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    <h3>Step 3 <span class="step-info">{{translate('company_information')}}</span></h3>
                                    <section>
                                        <div class="">
                                            <div class="mb-4">
                                                <div class="mb-3">
                                                    <section id="editor">
                                                <textarea class="ckeditor form-control" required
                                                          name="about_company"
                                                          placeholder="{{translate('about_company')}}"></textarea>
                                                    </section>
                                                </div>
                                                <div class="mb-3 form-floating">
                                                    <input class="form-control" type="text" id="time"
                                                           placeholder="hh:mm:ss"
                                                           name="start_time" value="{{old('start_time')}}"/>
                                                    <label>{{translate('start_time')}}</label>
                                                </div>
                                                <div class="mb-3 form-floating">
                                                    <input class="form-control" type="text" id="end_time"
                                                           placeholder="hh:mm:ss" name="end_time"
                                                           value="{{old('end_time')}}"/>
                                                    <label>{{translate('end_time')}}</label>
                                                </div>
                                                <div class="mb-3 form-floating">
                                                    <input type="text" class="form-control" name="working_with"
                                                           value="{{old('working_with')}}"
                                                           placeholder="{{translate('working_with')}}">
                                                    <label>{{translate('working_with')}}</label>
                                                </div>
                                                <div class="mb-2 d-none"><strong>{{translate('company_commission')}} <span
                                                            class="text-danger">*</span></strong></div>
                                                <div class="d-flex flex-wrap align-items-center gap-4 mb-3 d-none">
                                                    <div class="custom-radio col-md-3">
                                                        <input type="radio" id="fixed" name="company_commission_type"
                                                               value="fixed"
                                                               checked>
                                                        <label for="fixed">{{translate('fixed')}}</label>
                                                    </div>
                                                    <div class="custom-radio col-md-3">
                                                        <input type="radio" id="percentage"
                                                               name="company_commission_type"
                                                               value="percentage">
                                                        <label for="percentage">{{translate('percentage')}}</label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control"
                                                               name="company_commission"
                                                               placeholder="{{translate('company_commission')}}"
                                                               id="company_commission"
                                                               min="0" max="100" value="0">
                                                    </div>
                                                </div>
                                                <div class="mb-2 d-none"><strong>{{translate('estimation_commission')}} <span
                                                            class="text-danger">*</span></strong></div>
                                                <div class="d-flex flex-wrap align-items-center gap-4 mb-3 d-none">
                                                    <div class="custom-radio col-md-3">
                                                        <input type="radio" id="estfixed"
                                                               name="estimation_commission_type"
                                                               value="fixed"
                                                               checked>
                                                        <label for="estfixed">{{translate('fixed')}}</label>
                                                    </div>
                                                    <div class="custom-radio col-md-3">
                                                        <input type="radio" id="estpercentage"
                                                               name="estimation_commission_type"
                                                               value="percentage">
                                                        <label for="estpercentage">{{translate('percentage')}}</label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control"
                                                               name="estimation_commission"
                                                               placeholder="{{translate('estimation_commission')}}"
                                                               id="estimation_commission"
                                                               min="0" max="100" value="0">
                                                    </div>
                                                </div>
                                                <div class="mb-30">
                                                    <select name="identity_type" id="identity_type" class="form-control">
                                                        <option value="0" selected disabled>{{translate('Identity_Type')}}</option>
                                                        <option value="passport" {{old('identity_type')=='passport'?'selected':''}}>{{translate('passport')}}</option>
                                                        <option value="nid" {{old('identity_type')=='nid'?'selected':''}}>{{translate('nid')}}</option>
                                                        <option value="driving_licence" {{old('identity_type')=='driving_licence'?'selected':''}}>{{translate('driving_licence')}}</option>
                                                        <option value="trade_license" {{old('identity_type')=='trade_license'?'selected':''}}>{{translate('trade_license')}}</option>
                                                        <option value="company_id" {{old('identity_type')=='company_id'?'selected':''}}>{{translate('company_id')}}</option>
                                                    </select>
                                                </div>
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" value="{{old('identity_number')}}" name="identity_number" placeholder="{{translate('Identity_Number')}}">
                                                        <label>{{translate('Identity_Number')}}</label>
                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <div class="mb-3">{{translate('Identity_Image')}}</div>
                                                    <div id="multi_image_picker2" class="row"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    <h3>Step 4 <span class="step-info">{{translate('Account_Information')}}</span></h3>
                                    <section>
                                        <div class="">
                                            <div class="mb-4">
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" value="{{old('account_first_name')}}" name="account_first_name" placeholder="{{translate('First_Name')}}">
                                                        <label>{{translate('First_Name')}}</label>
                                                    </div>
                                                </div>
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" value="{{old('account_last_name')}}" name="account_last_name" placeholder="{{translate('Last_Name')}}">
                                                        <label>{{translate('Last_Name')}}</label>
                                                    </div>
                                                </div>
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="tel" class="form-control" value="{{old('account_phone')}}" name="account_phone" placeholder="{{translate('Phone')}}">
                                                        <label>{{translate('Phone')}}</label>
                                                        <small class="text-danger d-flex mt-1">* ( {{translate('country_code_required')}} )</small>
                                                    </div>
                                                </div>
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="email" class="form-control" value="{{old('account_email')}}" name="account_email" placeholder="{{translate('Email')}}">
                                                        <label>{{translate('Email')}}</label>
                                                    </div>
                                                </div>
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="password" class="form-control" value=""
                                                               name="password" placeholder="{{translate('Password')}}">
                                                        <label>{{translate('Password')}}</label>
                                                        <span
                                                            class="material-icons togglePassword">visibility_off</span>
                                                    </div>
                                                </div>
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="password" class="form-control" value=""
                                                               name="confirm_password"
                                                               placeholder="{{translate('Confirm_Password')}}">
                                                        <label>{{translate('Confirm_Password')}}</label>
                                                        <span
                                                            class="material-icons togglePassword">visibility_off</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" style="display:none;"></button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Login Form -->
<!-- ======= TIMEPICKER SCRIPT ======= -->
<script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<link
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css"
    rel="stylesheet">
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script>
    $('#time').datetimepicker({
        format: 'hh:mm:ss',
    });
    $('#end_time').datetimepicker({
        format: 'hh:mm:ss',
    });
</script>

<!-- ======= BEGIN GLOBAL MANDATORY SCRIPTS ======= -->
<script src="{{asset('public/assets/provider-module')}}/js/jquery-3.6.0.min.js"></script>
<script src="{{asset('public/assets/provider-module')}}/js/bootstrap.bundle.min.js"></script>
<script src="{{asset('public/assets/provider-module')}}/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="{{asset('public/assets/provider-module')}}/js/main.js"></script>
<!-- ======= BEGIN GLOBAL MANDATORY SCRIPTS ======= -->

{{--toastr and sweetalert--}}
<script src="{{asset('public/assets/provider-module')}}/js/sweet_alert.js"></script>
<script src="{{asset('public/assets/provider-module')}}/js/toastr.js"></script>
{!! Toastr::message() !!}

<!-- ======= BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS ======= -->
<script src="{{asset('public/assets/provider-module')}}/plugins/jquery-steps/jquery.steps.min.js"></script>

<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script src="{{asset('public/assets/ckeditor/jquery.js')}}"></script>
<script src="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('textarea.ckeditor').each(function () {
            // var config = {};
            // config.placeholder = 'some value';
            // CKEDITOR.replace("myeditor" , config );

            CKEDITOR.replace($(this).attr('id'));
        });

        $('.category-select').select2({
            placeholder: "{{translate('choose_category')}}"
        });
        $('.subcategory-select').select2({
            placeholder: "{{translate('choose_subcategory')}}"
        });
    });

    function getCategory() {
        var str = '';
        var val = document.getElementById('category_id');
        for (i = 0; i < val.length; i++) {
            if (val[i].selected) {
                str += val[i].value + ',';
            }
        }
        var str = str.slice(0, str.length - 1);
        console.log(str);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "GET",
            url: "{{route('provider.auth.get-sub-cat-ids')}}",
            data: 'category_id=' + str,
            success: function (data) {
                console.log(data);
                $("#sub_category_id").html(data);
            }
        });
    }
</script>

<script>
    (function ($) {
        "use strict";

        $("#register-vertical-steps").steps({
            headerTag: "h3",
            bodyTag: "section",
            transitionEffect: "fade",
            stepsOrientation: "vertical",
            autoFocus: true,
            onFinished: function (event, currentIndex) {
                $('button[type="submit"]').trigger('click');
            }
        });

    })(jQuery);
</script>

<script src="{{asset('public/assets/provider-module')}}/js//tags-input.min.js"></script>
<script src="{{asset('public/assets/provider-module')}}/js/spartan-multi-image-picker.js"></script>
<script>
    $("#multi_image_picker2").spartanMultiImagePicker({
        fieldName: 'identity_images[]',
        maxCount: 2,
        allowedExt: 'png|jpg|jpeg',
        rowHeight: 'auto',
        groupClassName: 'col-6',
        //maxFileSize: '',
        dropFileLabel: "{{translate('Drop_here')}}",
        placeholderImage: {
            image: '{{asset('public/assets/admin-module')}}/img/media/banner-upload-file.png',
            width: '100%',
        },

        onRenderedPreview: function (index) {
            toastr.success('{{translate('Image_added')}}', {
                CloseButton: true,
                ProgressBar: true
            });
        },
        onRemoveRow: function (index) {
            console.log(index);
        },
        onExtensionErr: function (index, file) {
            toastr.error('{{translate('Please_only_input_png_or_jpg_type_file')}}', {
                CloseButton: true,
                ProgressBar: true
            });
        },
        onSizeErr: function (index, file) {
            toastr.error('{{translate('File_size_too_big')}}', {
                CloseButton: true,
                ProgressBar: true
            });
        }

    });
</script>
@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif
<!-- ======= End BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS ======= -->
</body>
</html>
