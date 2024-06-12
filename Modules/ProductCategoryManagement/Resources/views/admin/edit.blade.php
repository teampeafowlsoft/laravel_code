@extends('adminmodule::layouts.master')

@section('title',translate('product_category_update'))

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
                        <h2 class="page-title">{{translate('product_category_update')}}</h2>
                    </div>

                @php
                    // $category_id_eng = explode(',',$category[0]->id)[0];
                    // $category_id_arabic = explode(',',$category[0]->id)[1];
                    //  $id = explode(',',$category[0]->id);
                    //  $lang_id = explode(',',$category[0]->lang_id);
                    //  $name = !empty(explode(',',$category[0]->name)[0]) ? explode(',',$category[0]->name)[0] : '';
                    //   $arabic_name = !empty(explode(',',$category[0]->name)[1]) ? explode(',',$category[0]->name)[1] : '';
                    //   $description = !empty(explode(',',$category[0]->description)[0]) ? explode(',',$category[0]->description)[0] : '';
                    //   $arabic_description = !empty(explode(',',$category[0]->description)[1]) ? explode(',',$category[0]->description)[1] : '';
                        $name = !empty($category[0]->name) ? $category[0]->name : '';
                        $arabic_name = !empty($category[1]->name) ? $category[1]->name : '';
                        $description = !empty($category[0]->description) ? $category[0]->description : '';
                        $arabic_description = !empty($category[1]->description) ? $category[1]->description : '';
                @endphp

                <!-- Category Setup -->
                    <div class="card category-setup mb-30">
                        <div class="card-body p-30">
                            <form action="{{route('admin.productcategory.update',[$category[0]->group_id])}}" method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <!-- Nav Tabs -->
                                    <div class="col-lg-12 mb-4 mb-lg-0">
                                        <div class="mb-3">
                                            <div class="row">
                                                <div class="col-12 col-md-6">
                                                    <input type="hidden" name="lang_id" id="lang_id">
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
                                                           href="{{route('admin.productcategory.create')}}"><span
                                                                class="material-icons"
                                                                title="{{translate('category_setup')}}">chevron_left</span> {{translate('back')}}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                    <!-- End Nav Tabs -->
                                    <div class="col-lg-8 mb-5 mb-lg-0">
                                        <!-- Tab Content -->
                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="english">
                                                <div class="d-flex flex-column">
                                                    <label>{{translate('category_name')}} <span
                                                            class="text-danger">*</span></label>
                                                    <div class="form-floating mb-30">
                                                        <input type="text" name="name" class="form-control"
                                                               value="{{!empty($name) ? $name : ''}}"
                                                               placeholder="{{translate('category_name')}}" required>
                                                        <span class="text-danger">Note : Do not enter Name with comma (,) special character</span>
                                                    </div>

                                                    <div class="d-none">
                                                        <label>{{translate('Select_Zone')}} </label>
                                                        <div class="form-floating mb-30">
                                                            <select class="zone-select theme-input-style w-100"
                                                                    name="zone_ids[]"
                                                                    multiple="multiple">
                                                                @foreach($zones as $zone)
                                                                    <option
                                                                        value="{{$zone['id']}}" {{in_array($zone->id,$selected_zones->zones->pluck('id')->toArray())?'selected':''}}
                                                                    >{{$zone->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <label>{{translate('description')}} </label>
                                                    <div class="form-floating mb-30">
                                                        <textarea name="description" class="form-control"
                                                                  placeholder="{{translate('description')}}">{{!empty($description) ? $description : ''}}</textarea>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic">
                                                <div class="d-flex flex-column">
                                                    <label>{{translate('category_name')}} <span
                                                            class="text-danger">*</span></label>
                                                    <div class="form-floating mb-30">

                                                        <input type="text" name="arabic_name" class="form-control"
                                                               value="{{!empty($arabic_name) ? $arabic_name : ''}}"
                                                               placeholder="{{translate('category_name')}}" required>
                                                        <span class="text-danger">Note : Do not enter Name with comma (,) special character</span>

                                                    </div>

                                                    <div class="d-none">
                                                        <label>{{translate('Select_Zone')}} </label>
                                                        <div class="form-floating mb-30">
                                                            <select class="select-zone theme-input-style w-100"

                                                                    name="arabic_zone_ids[]"
                                                                    multiple="multiple">
                                                                @foreach($arabic_zones as $arabic_zone)
                                                                    <option
                                                                        value="{{$arabic_zone['id']}}" {{in_array($arabic_zone->id,$selected_zones_arabic->zones->pluck('id')->toArray())?'selected':''}}
                                                                    >{{$arabic_zone->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <label>{{translate('description')}} </label>
                                                    <div class="form-floating mb-30">
                                                        <textarea name="arabic_description" class="form-control"
                                                                  placeholder="{{translate('description')}}">{{!empty($arabic_description) ? $arabic_description : ''}}</textarea>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Tab Content -->
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="d-flex  gap-3 gap-xl-5">
                                            <div>
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input" name="image">
                                                    <div class="upload-file__img">
                                                        <img
                                                            onerror="this.src='{{asset('public/assets/admin-module')}}/img/media/upload-file.png'"
                                                            src="{{asset('storage/app/public/productcategory')}}/{{$category[0]->image}}"
                                                            alt="">
                                                    </div>
                                                    <span class="upload-file__edit">
                                                        <span class="material-icons">edit</span>
                                                    </span>
                                                </div>
                                                <p class="mt-3 opacity-75 max-w220">{{translate('image_format_-_jpg,_png,_jpeg,_gif_image
                                                size_-_
                                                maximum_size_2_MB_Image_Ratio_-_1:1')}}</p>
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
    <script>
        $(document).ready(function () {
            $('.zone-select').select2({
                placeholder: "Select Zone"
            });
            $('.select-zone').select2({placeholder: "Select  Zone"});
        });
    </script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/dataTables.select.min.js"></script>

    <script>
        $('#lang_id').val(1);
        var lang_id = $('#lang_id').val();
        if (lang_id == '1') {
            var lang_id = $('#lang_id').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.category.lang-translate')}}",
                data: {lang_id: lang_id},
                method: "GET",
                dataType: 'json',
                success: function (response) {
                    console.log(response);
                    var len = 0;
                    if (response['data'] != null) {
                        len = response['data'].length;
                    }
                    console.log(len);
                    if (len > 0) {
                        var eng_option = "";
                        var arab_option = "";
                        // Read data and create <option >
                        for (var i = 0; i < len; i++) {
                            var id = response['data'][i].id;
                            var name = response['data'][i].name;

                            if (lang_id == '1') {
                                eng_option += "<option value='" + id + "'>" + name + "</option>";

                            } else if (lang_id == '2') {
                                arab_option += "<option value='" + id + "'>" + name + "</option>";
                            }
                        }
                        $("#eng_zone_id").html(eng_option);
                        $("#arabic_zone_id").html(arab_option);
                    }
                }
            });
        }
        $('#arabic_lang').click(function () {
            $('#lang_id').val(2);
            var lang_id = $('#lang_id').val();
            // alert(lang_id)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.category.lang-translate')}}",
                data: {lang_id: lang_id},
                method: "GET",
                dataType: 'json',
                success: function (response) {
                    console.log(response);
                    var len = 0;
                    if (response['data'] != null) {
                        len = response['data'].length;
                    }
                    console.log(len);
                    if (len > 0) {
                        var option = "";
                        for (var i = 0; i < len; i++) {
                            var id = response['data'][i].id;
                            var name = response['data'][i].name;

                            if (lang_id == '2') {
                                option += "<option value='" + id + "'>" + name + "</option>";

                            }
                        }
                        $("#arabic_zone_id").html(option);

                    } else {
                        var option = "<option value=''>--No Zone Added--</option>";

                        $("#arabic_zone_id").html(option);
                    }
                }
            });
        });
        $('#english_lang').click(function () {
            $('#lang_id').val(1);
            var lang_id = $('#lang_id').val();
            // alert(lang_id)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.category.lang-translate')}}",
                data: {lang_id: lang_id},
                method: "GET",
                dataType: 'json',
                success: function (response) {
                    console.log(response);
                    var len = 0;
                    if (response['data'] != null) {
                        len = response['data'].length;
                    }
                    console.log(len);
                    if (len > 0) {
                        var option = "";
                        for (var i = 0; i < len; i++) {
                            var id = response['data'][i].id;
                            var name = response['data'][i].name;

                            if (lang_id == '1') {
                                option += "<option value='" + id + "'>" + name + "</option>";
                            }
                        }
                        $("#eng_zone_id").html(option);

                    } else {
                        var option = "<option value=''>--No Zone Added--</option>";

                        $("#eng_zone_id").html(option);
                    }
                }
            });
        });
    </script>
@endpush
