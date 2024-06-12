@extends('adminmodule::layouts.master')

@section('title',translate('add_company'))

@push('css_or_js')
    {{--  Int ph  --}}
    <!-- CSS -->
    {{--<link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' rel='stylesheet' type='text/css'>--}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.3/css/intlTelInput.min.css"/>
    <!-- JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.3/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.3/js/utils.min.js"></script>

    <style>
        .iti {
            width: 100%;
        }

        /*.iti__arrow { border: none; }*/
    </style>

@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 pb-4">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('Add_New_Provider')}}</h2>
                    </div>

                    <form action="{{route('admin.provider.store')}}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="c1 mb-20">{{translate('General_Information')}}</h4>
                                        <div class="mb-2"><strong>{{translate('Company_/_Individual_Name_english')}} <span class="text-danger">*</span></strong></div>
                                        <div class="mb-30">
                                            <input type="text" class="form-control" value="{{old('company_name')}}"
                                                   name="company_name"
                                                   placeholder="{{translate('Company_/_Individual_Name_english')}}" required>
                                        </div>
                                        <div class="mb-2"><strong>{{translate('Company_/_Individual_Name_arabic')}} <span class="text-danger">*</span></strong></div>
                                        <div class="mb-30">
                                            <input type="text" class="form-control" value="{{old('company_name_arabic')}}"
                                                   name="company_name_arabic"
                                                   placeholder="{{translate('Company_/_Individual_Name_arabic')}}" required>
                                        </div>
                                        <div class="mb-2"><strong>{{translate('Phone')}} <span class="text-danger">*</span></strong></div>
                                        <div class="mb-30">
                                            <input type="tel" class="form-control"
                                                   name="company_phone" value="{{old('company_phone')}}"
                                                   placeholder="{{translate('Phone')}}" required>
                                            <small class="text-danger d-flex mt-1">*
                                                ( {{translate('country_code_required')}} )</small>
                                        </div>
                                        <div class="mb-2"><strong>{{translate('Email')}} <span class="text-danger">*</span></strong></div>
                                        <div class="mb-30">
                                            <input type="email" class="form-control"
                                                   name="company_email" value="{{old('company_email')}}"
                                                   placeholder="{{translate('Email')}}" required>
                                        </div>
                                        <div class="mb-30">
                                            <label class="mb-2"><strong>{{translate('Select_Zone')}} <span class="text-danger">*</span></strong></label>
                                            <select class="select-identity theme-input-style w-100" name="zone_id"
                                                    required>
                                                <option>{{translate('Select_Zone')}}</option>
                                                @foreach($zones as $zone)
                                                    <option value="{{$zone->id}}"
                                                        {{old('identity_type') == $zone->id ? 'selected': ''}}>
                                                        {{$zone->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2"><strong>{{translate('Address')}} <span class="text-danger">*</span></strong></div>
                                        <div class="mb-30">
                                            <textarea class="form-control" placeholder="{{translate('Address')}}"
                                                      name="company_address"
                                                      required>{{old('company_address')}}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex flex-column align-items-center gap-3">
                                            <h3 class="mb-0">{{translate('Company_Logo')}}</h3>
                                            <div>
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input" name="logo" required>
                                                    <div class="upload-file__img">
                                                        <img
                                                            src="{{asset('storage/app/public/provider/logo')}}/{{old('logo')}}"
                                                            onerror="this.src='{{asset('public/assets/admin-module')}}/img/media/upload-file.png'"
                                                            alt="">
                                                    </div>
                                                    <span class="upload-file__edit">
                                                        <span class="material-icons">edit</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <p class="opacity-75 max-w220 mx-auto">Image format - jpg, png,
                                                jpeg,
                                                gif</p>
                                        </div>
{{--                                        <div class="mb-30 d-none" id="category-selector">--}}
{{--                                            <label class="mb-2"><strong>{{translate('Category_Name')}} <span class="text-danger">*</span></strong></label>--}}
{{--                                            <select class="category-select theme-input-style w-100"--}}
{{--                                                    name="category_id[]" id="category_id" multiple="multiple" required--}}
{{--                                                    onChange="getCategory();">--}}
{{--                                                @foreach($categories as $category)--}}
{{--                                                    <option value="{{$category->id}}">{{$category->name}}</option>--}}
{{--                                                @endforeach--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                        <div class="mb-30 d-none" id="sub-category-selector">--}}
{{--                                            <label class="mb-2"><strong>{{translate('Sub_Category_Name')}} <span class="text-danger">*</span></strong></label>--}}
{{--                                            <select class="subcategory-select theme-input-style w-100"--}}
{{--                                                    name="sub_category_id[]" id="sub_category_id" multiple="multiple" required>--}}

{{--                                            </select>--}}
{{--                                        </div>--}}

                                        <!-- New Code 29-11-2023 Pc1 -->
                                        <div class="mb-2 mt-3"><strong>{{translate('destination_id')}}</strong></div>
                                        <div class="mb-30">
                                            <input type="text" class="form-control" name="destination_id"
                                                   value="{{old('destination_id')}}"
                                                   placeholder="{{translate('destination_id')}}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');">
                                        </div>
                                        <div class="mb-2"><strong>{{translate('response_business_id')}}</strong></div>
                                        <div class="mb-30">
                                            <input type="text" class="form-control"
                                                   name="response_business_id" value="{{old('response_business_id')}}"
                                                   placeholder="{{translate('response_business_id')}}">
                                        </div>
                                        <div class="mb-2"><strong>{{translate('business_entity_id')}}</strong></div>
                                        <div class="mb-30">
                                            <input type="text" class="form-control"
                                                   name="business_entity_id" value="{{old('business_entity_id')}}"
                                                   placeholder="{{translate('business_entity_id')}}">
                                        </div>
                                        <!-- Close New Code 29-11-2023 Pc1 -->

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row gx-2 mt-2">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h4 class="c1 mb-20">{{translate('company_information')}}</h4>
                                        <div class="mb-2"><strong>{{translate('short_description')}} <span class="text-danger">*</span></strong></div>
                                        <div class="mb-30">
{{--                                                <input type="text" class="form-control" required--}}
{{--                                                          name="short_description">{{old('about_company')}}--}}
                                            <textarea rows="3" class="form-control" required
                                                      name="short_description">{{old('short_description')}}</textarea>
                                        </div>

                                        <div class="mb-2"><strong>{{translate('about_company')}} <span class="text-danger">*</span></strong></div>
                                        <div class="mb-30">
                                            <section id="editor">
                                                <textarea class="ckeditor form-control" required
                                                          name="about_company">{{old('about_company')}}</textarea>
                                            </section>
                                        </div>

                                        <script type="text/javascript"
                                                src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
                                        <link
                                            href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css"
                                            rel="stylesheet">
                                        <script
                                            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
                                        <div class="mb-2"><strong>{{translate('start_time')}} <span class="text-danger">*</span></strong></div>
                                        <div class="mb-30">
                                            <input class="form-control" type="text" id="time" placeholder="hh:mm:ss" required
                                                   name="start_time" value="{{old('start_time')}}"/>
                                        </div>

                                        <div class="mb-2"><strong>{{translate('end_time')}} <span class="text-danger">*</span></strong></div>
                                        <div class="mb-30">
                                            <input class="form-control" type="text" id="end_time" placeholder="hh:mm:ss" required
                                                   name="end_time" value="{{old('end_time')}}"/>
                                        </div>
                                        <script>
                                            $('#time').datetimepicker({
                                                format: 'hh:mm:ss',
                                            });
                                            $('#end_time').datetimepicker({
                                                format: 'hh:mm:ss',
                                            });
                                        </script>
                                        <div class="mb-2"><strong>{{translate('working_with')}}</strong></div>
                                        <div class="mb-30">
                                            <input type="text" class="form-control" name="working_with"
                                                   value="{{old('working_with')}}"
                                                   placeholder="{{translate('working_with')}}">
                                        </div>
{{--                                        <div class="mb-2"><strong>{{translate('destination_id')}}</strong></div>--}}
{{--                                        <div class="mb-30">--}}
{{--                                            <input type="text" class="form-control" name="destination_id"--}}
{{--                                                   value="{{old('destination_id')}}"--}}
{{--                                                   placeholder="{{translate('destination_id')}}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');">--}}
{{--                                        </div>--}}

                                        <div class="mb-2 d-none"><strong>{{translate('company_commission')}} <span class="text-danger">*</span></strong></div>
                                        <div class="d-flex flex-wrap align-items-center gap-4 mb-30 d-none">
                                            <div class="custom-radio col-md-3">
                                                <input type="radio" id="fixed" name="company_commission_type"
                                                       value="fixed"
                                                       checked>
                                                <label for="fixed">{{translate('fixed')}}</label>
                                            </div>
                                            <div class="custom-radio col-md-3">
                                                <input type="radio" id="percentage" name="company_commission_type"
                                                       value="percentage">
                                                <label for="percentage">{{translate('percentage')}}</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" class="form-control" name="company_commission"
                                                       placeholder="{{translate('company_commission')}}"
                                                       id="company_commission"
                                                       min="0" max="100" value="0">
                                            </div>
                                        </div>

                                        <div class="mb-2 d-none"><strong>{{translate('estimation_commission')}} <span class="text-danger">*</span></strong></div>
                                        <div class="d-flex flex-wrap align-items-center gap-4 mb-30 d-none">
                                            <div class="custom-radio col-md-3">
                                                <input type="radio" id="estfixed" name="estimation_commission_type"
                                                       value="fixed"
                                                       checked>
                                                <label for="estfixed">{{translate('fixed')}}</label>
                                            </div>
                                            <div class="custom-radio col-md-3">
                                                <input type="radio" id="estpercentage" name="estimation_commission_type"
                                                       value="percentage">
                                                <label for="estpercentage">{{translate('percentage')}}</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" class="form-control" name="estimation_commission"
                                                       placeholder="{{translate('estimation_commission')}}"
                                                       id="estimation_commission"
                                                       min="0" max="100" value="0">

                                            </div>
                                        </div>

                                        <div class="mb-30">
                                            <label class="mb-2"><strong>{{translate('Select_Identity_Type')}} <span class="text-danger">*</span></strong></label>
                                            <select class="select-identity theme-input-style w-100" required
                                                    name="identity_type">
                                                <option>{{translate('Select_Identity_Type')}}</option>
                                                <option value="passport"
                                                    {{old('identity_type') == 'passport' ? 'selected': ''}}>
                                                    {{translate('Passport')}}</option>
                                                <option value="driving_license"
                                                    {{old('identity_type') == 'driving_license' ? 'selected': ''}}>
                                                    {{translate('Driving_License')}}</option>
                                                <option value="company_id"
                                                    {{old('identity_type') == 'company_id' ? 'selected': ''}}>
                                                    {{translate('Company_Id')}}</option>
                                                <option value="nid"
                                                    {{old('identity_type') == 'passport' ? 'selected': ''}}>
                                                    {{translate('nid')}}</option>
                                                <option value="trade_license"
                                                    {{old('identity_type') == 'nid' ? 'selected': ''}}>
                                                    {{translate('Trade_License')}}</option>
                                            </select>
                                        </div>

                                        <div class="mb-2"><strong>{{translate('Identity_Number')}} <span class="text-danger">*</span></strong></div>
                                        <div class="mb-30">
                                            <input type="text" class="form-control" name="identity_number"
                                                   value="{{old('identity_number')}}"
                                                   placeholder="{{translate('Identity_Number')}}" required>
                                        </div>

                                        <div class="upload-file w-100">
                                            <h3 class="mb-3">{{translate('Identification_Image')}}</h3>
                                            <div id="multi_image_picker"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap justify-content-between gap-3 mb-20">
                                            <h4 class="c1">{{translate('Contact_Person')}}</h4>
                                        </div>
                                        <div class="mb-2"><strong>{{translate('Name')}} <span class="text-danger">*</span></strong></div>
                                        <div class="mb-30">
                                            <input type="text" class="form-control" name="contact_person_name"
                                                   value="{{old('contact_person_name')}}" placeholder="name" required>
                                        </div>
                                        <div class="row gx-2">
                                            <div class="col-lg-6">
                                                <div class="mb-2"><strong>{{translate('Phone')}} <span class="text-danger">*</span></strong></div>
                                                <div class="mb-30">
                                                    <input type="tel" class="form-control" name="contact_person_phone"
                                                           value="{{old('contact_person_phone')}}"
                                                           placeholder="{{translate('Phone')}}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2"><strong>{{translate('Email')}} <span class="text-danger">*</span></strong></div>
                                                <div class="mb-30">
                                                    <input type="email" class="form-control" name="contact_person_email"
                                                           value="{{old('contact_person_email')}}"
                                                           placeholder="{{translate('Email')}}" required>
                                                </div>
                                            </div>
                                        </div>

                                        <h4 class="c1 mb-20">{{translate('Account_Information')}}</h4>
                                        <div class="row gx-2">
                                            <div class="col-lg-6">
                                                <div class="mb-2"><strong>{{translate('First_Name')}} <span class="text-danger">*</span></strong></div>
                                                <div class="mb-30">
                                                    <input type="text" class="form-control" name="account_first_name"
                                                           value="{{old('account_first_name')}}"
                                                           placeholder="{{translate('first_name')}}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2"><strong>{{translate('Last_Name')}} <span class="text-danger">*</span></strong></div>
                                                <div class="mb-30">
                                                    <input type="text" class="form-control" name="account_last_name"
                                                           value="{{old('account_last_name')}}"
                                                           placeholder="{{translate('last_name')}}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-2"><strong>{{translate('Email')}} <span class="text-danger">*</span></strong></div>
                                        <div class="mb-30">
                                            <input type="email" class="form-control" name="account_email"
                                                   value="{{old('account_email')}}"
                                                   placeholder="{{translate('Email')}}"
                                                   required>
                                        </div>
                                        <div class="mb-2"><strong>{{translate('Phone')}} <span class="text-danger">*</span></strong></div>
                                        <div class="row mb-30">
                                            <div class="col-1">
                                                <div class="sign">
                                                    <input type="text" readonly class="form-control disabled px-0  text-center" name="sign"
                                                           placeholder="+"
                                                           required="" value="+">
                                                </div>
                                            </div>
                                            <div class="col-3 px-0">
                                                <div class="countryCode">
                                                    <input type="text" class="form-control" name="country_code" id="country_code"
                                                           placeholder="{{translate('country_code')}} *"
                                                           required="" value="{{old('country_code')}}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');">
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="accountMobile">
                                                    <input type="text" class="form-control" name="account_phone" id="account_phone"
                                                           value="{{old('account_phone')}}"
                                                           placeholder="{{translate('Phone')}}"
                                                           required oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');">
                                                    <input type="hidden" id="phoneNumber" name="signaccountphone">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row gx-2">
                                            <div class="col-lg-6">
                                                <div class="mb-2"><strong>{{translate('Password')}} <span class="text-danger">*</span></strong></div>
                                                <div class="form-floating mb-30">
                                                    <input type="password" class="form-control" name="password"
                                                           placeholder="{{translate('Password')}}" required>
                                                    <span class="material-icons togglePassword">visibility_off</span>
                                                    <small class="text-danger d-flex mt-1">*
                                                        ( {{translate('The password must be at least 8 characters.')}} )</small>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-2"><strong>{{translate('Confirm_Password')}} <span class="text-danger">*</span></strong></div>
                                                <div class="form-floating mb-30">
                                                    <input type="password" class="form-control" name="confirm_password"
                                                           placeholder="{{translate('Confirm_Password')}}" required>
                                                    <span class="material-icons togglePassword">visibility_off</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-4 flex-wrap justify-content-end mt-20">
                            <button type="reset" class="btn btn--secondary">{{translate('Reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('Submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')

    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    <script src="{{asset('public/assets/ckeditor/jquery.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('textarea.ckeditor').each(function () {
                CKEDITOR.replace($(this).attr('id'));
            });

            $('.category-select').select2({
                placeholder: "{{translate('choose_category')}}"
            });
            $('.subcategory-select').select2({
                placeholder: "{{translate('choose_subcategory')}}"
            });
        });
    </script>



    <script src="{{asset('public/assets/provider-module')}}/js//tags-input.min.js"></script>
    <script src="{{asset('public/assets/provider-module')}}/js/spartan-multi-image-picker.js"></script>
    <script>
        $("#multi_image_picker").spartanMultiImagePicker({
            fieldName: 'identity_images[]',
            maxCount: 100,
            allowedExt: 'png|jpg|jpeg|gif',
            rowHeight: 'auto',
            groupClassName: 'item',
            //maxFileSize: '100000', //in KB
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

    <script>
        var input = document.querySelector("#phone");
        intlTelInput(input, {
            preferredCountries: ['bd', 'us'],
            initialCountry: "auto",
            geoIpLookup: function (success, failure) {
                $.get("https://ipinfo.io", function () {
                }, "jsonp").always(function (resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "bd";
                    success(countryCode);
                });
            },
        });
    </script>

    <script>
        // $("select[name=sub_category_id]").focus(function () {
        //     // Store the current value on focus, before it changes
        //     var str = '';
        //     var val = document.getElementById('category_id');
        //     console.log(val)
        //     for (i = 0; i < val.length; i++) {
        //         if (val[i].selected) {
        //             str += val[i].value + ',';
        //         }
        //     }
        //     var str = str.slice(0, str.length - 1);
        //
        //     // $.ajax({
        //     //     type: "GET",
        //     //     url: "public/ajaxData.php",
        //     //     data: 'wcat_id=' + str,
        //     //     success: function (data) {
        //     //         console.log(data)
        //     //         $("#sub_category_id").html(data);
        //     //     }
        //     // });
        // })

        {{--function getCategory() {--}}
        {{--    var str = '';--}}
        {{--    var val = document.getElementById('category_id');--}}
        {{--    for (i = 0; i < val.length; i++) {--}}
        {{--        if (val[i].selected) {--}}
        {{--            str += val[i].value + ',';--}}
        {{--        }--}}
        {{--    }--}}
        {{--    var str = str.slice(0, str.length - 1);--}}
        {{--    console.log(str);--}}
        {{--    $.ajaxSetup({--}}
        {{--        headers: {--}}
        {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
        {{--        }--}}
        {{--    });--}}
        {{--    $.ajax({--}}
        {{--        type: "GET",--}}
        {{--        url: "{{route('admin.provider.get-sub-cat-ids')}}",--}}
        {{--        data: 'category_id=' + str,--}}
        {{--        success: function (data) {--}}
        {{--            console.log(data);--}}
        {{--            $("#sub_category_id").html(data);--}}
        {{--        }--}}
        {{--    });--}}
        {{--}--}}
    </script>

    <script>
        function validateForm() {
            var editorData = CKEDITOR.instances.about_company.getData().trim();
            if (editorData === '') {
                toastr.error('{{translate('please_about_company')}}', {
                    CloseButton: true,
                    ProgressBar: true
                });
                return false;
            }
        }
    </script>

    <script>
        $(document).ready(function (){
            $("#country_code,#account_phone").keyup(function (){
                var countryCode = $("#country_code").val();
                var mobileNumber = $("#account_phone").val();
                $("#phoneNumber").val("+"+countryCode+mobileNumber);
            });
        });
    </script>
@endpush
