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
                        <h2 class="page-title">{{translate('main_category_setup')}}</h2>
                    </div>

                    <!-- Category Setup -->
                    <div class="card category-setup mb-30">
                        <div class="card-body p-30">
                            <form action="{{route('admin.main-category.store')}}" method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-8 mb-5 mb-lg-0">
                                        <input type="hidden" name="group_id" value="{{$category_grp_id}}">
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
                                                               value="{{old('name')}}"
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
                                                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="js-select theme-input-style w-100" required name="eng_product_category_id"
                                                                    id="eng_product_category_id">
                                                                <option value="" selected disabled>{{translate('Select_Product_Category')}}</option>
                                                                @foreach($product_categories as $item)
                                                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-30">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-30">
                                                                <input type="text" name="button_name_1" class="form-control"
                                                                       value="{{old('first_button_text')}}"
                                                                       placeholder="{{translate('First_Button_Text')}}"
                                                                       required>
                                                                <label>{{translate('First_Button_Text')}}</label>
                                                                <span class="text-danger">Note : Do not enter Name with comma (,).</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-30">
                                                                <input type="text" name="button_name_2" class="form-control"
                                                                       value="{{old('second_button_text')}}"
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
                                                               value="{{old('name')}}"
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
                                                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="js-select theme-input-style w-100" required name="eng_product_category_id_arabic"
                                                                    id="eng_product_category_id_arabic">
                                                                <option value="" selected disabled>{{translate('Select_Product_Category_arabic')}}</option>
                                                                @foreach($product_categories_arabic as $item)
                                                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-30">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-30">
                                                                <input type="text" name="button_name_1_arabic" class="form-control"
                                                                       value="{{old('first_button_text')}}"
                                                                       placeholder="{{translate('First_Button_Text_Arabic')}}"
                                                                       required>
                                                                <label>{{translate('First_Button_Text_Arabic')}}</label>
                                                                <span class="text-danger">Note : Do not enter Name with comma (,).</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-30">
                                                                <input type="text" name="button_name_2_arabic" class="form-control"
                                                                       value="{{old('second_button_text')}}"
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
                                                           value="{{old('color')}}"
                                                           required>
                                                    <label>{{translate('Select_Color')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating mb-30">
                                                    <input type="text" name="color_code" id="hexcolor" class="form-control"
                                                           pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$"
                                                           value="{{old('color')}}"
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
                                                    <input type="file" class="upload-file__input" required name="image">
                                                    <div class="upload-file__img">
                                                        <img
                                                            src="{{asset('public/assets/admin-module')}}/img/media/upload-file.png"
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

                    <div
                        class="d-flex flex-wrap justify-content-between align-items-center border-bottom mx-lg-4 mb-10 gap-3">
                        <ul class="nav nav--tabs">
                            <li class="nav-item">
                                <a class="nav-link {{$status=='all'?'active':''}}"
                                   href="{{url()->current()}}?status=all">
                                    {{translate('all')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$status=='active'?'active':''}}"
                                   href="{{url()->current()}}?status=active">
                                    {{translate('active')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$status=='inactive'?'active':''}}"
                                   href="{{url()->current()}}?status=inactive">
                                    {{translate('inactive')}}
                                </a>
                            </li>
                        </ul>

                        <div class="d-flex gap-2 fw-medium">
                            <span class="opacity-75">{{translate('Total_Sub_Categories')}}:</span>
                            <span class="title-color">{{$maincategories->total()}}</span>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="all-tab-pane">
                            <div class="card">
                                <div class="card-body">
                                    <div class="data-table-top d-flex flex-wrap gap-10 justify-content-between">
                                        <form action="{{url()->current()}}?status={{$status}}"
                                              class="search-form search-form_style-two"
                                              method="POST">
                                            @csrf
                                            <div class="input-group search-form__input_group">
                                            <span class="search-form__icon">
                                                <span class="material-icons">search</span>
                                            </span>
                                                <input type="search" class="theme-input-style search-form__input"
                                                       value="{{$search}}" name="search"
                                                       placeholder="{{translate('search_here')}}">
                                            </div>
                                            <button type="submit"
                                                    class="btn btn--primary">{{translate('search')}}</button>
                                        </form>

                                        <div class="d-flex flex-wrap align-items-center gap-3">
                                            <div class="dropdown">
                                                <button type="button"
                                                        class="btn btn--secondary text-capitalize dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                    <span class="material-icons">file_download</span> download
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                    <li><a class="dropdown-item"
                                                           href="{{route('admin.sub-category.download')}}?search={{$search}}">{{translate('excel')}}</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="example" class="table align-middle">
                                            <thead class="text-nowrap">
                                            <tr>
                                                <th>{{translate('SL')}}</th>
                                                <th>{{translate('maincategory_image')}}</th>
                                                <th>{{translate('name') . '(' . ('english') . ')'}}</th>
                                                <th>{{translate('name') . '(' . ('arabic') . ')'}}</th>
                                                <th>{{translate('Service_Category')}}</th>
                                                <th>{{translate('Product_Category')}}</th>
                                                <th>{{translate('Color')}}</th>
                                                <th>{{translate('Hax')}}</th>
                                                <th>{{translate('status')}}</th>
                                                <th>{{translate('action')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php $i=1; @endphp
                                            @foreach($maincategories as $key=>$maincategory)
                                                @php
                                                    $service_id = (explode(',',$maincategory->service_id)[0]);
                                                    $product_id = (explode(',',$maincategory->product_id)[0]);
                                                    $service_name = $category_db->select('name')->where('id', $service_id)->first();
                                                    $product_name = $product_db->select('name')->where('id', $product_id)->first();
                                                @endphp
                                                <tr>
                                                    <td>{{$i++}}</td>
                                                    <td><img
                                                            width="70" height="70"
                                                            onerror="this.src='{{asset('public/assets/admin-module')}}/img/media/upload-file.png'"
                                                            src="{{asset('storage/app/public/main_category')}}/{{explode(',',$maincategory->image)[0]}}"
                                                            alt=""></td>
                                                    <td>{{!empty(explode(',',$maincategory->name)[0]) ? explode(',',$maincategory->name)[0] : ''}}</td>
                                                    <td>{{!empty(explode(',',$maincategory->name)[1]) ? explode(',',$maincategory->name)[1] : ''}}</td>
                                                    <td>{{$service_name->name}}</td>
                                                    <td>{{$product_name->name}}</td>
                                                    <td><input type="color" value="{{$maincategory->color}}" disabled></td>
                                                    <td>{{$maincategory->color}}</td>
                                                    <td>
                                                        <label class="switcher" data-bs-toggle="modal" data-bs-target="#deactivateAlertModal">
                                                            <input class="switcher_input"
                                                                   onclick="route_alert('{{route('admin.main-category.status-update',[$maincategory->group_id])}}','{{translate('want_to_update_status')}}')"
                                                                   type="checkbox" {{$maincategory->is_active?'checked':''}}>
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <div class="table-actions">
                                                            <a href="{{route('admin.main-category.edit',[$maincategory->id,$maincategory->group_id])}}"
                                                               class="table-actions_edit demo_check">
                                                                <span class="material-icons">edit</span>
                                                            </a>
                                                            <button type="button"
                                                                    onclick="form_alert('delete-{{$maincategory->group_id}}','{{translate('want_to_delete_this_category')}}?')"
                                                                    class="table-actions_delete bg-transparent border-0 p-0 demo_check">
                                                                <span class="material-icons">delete</span>
                                                            </button>
                                                            <form
                                                                action="{{route('admin.main-category.delete',[$maincategory->group_id])}}"
                                                                method="post" id="delete-{{$maincategory->group_id}}"
                                                                class="hidden">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {!! $maincategories->links() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

    <script>
        {{--$('#lang_id').val(1);--}}
        {{--var lang_id = $('#lang_id').val();--}}
        {{--if (lang_id == '1') {--}}
        {{--    var lang_id = $('#lang_id').val();--}}
        {{--    $.ajaxSetup({--}}
        {{--        headers: {--}}
        {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
        {{--        }--}}
        {{--    });--}}
        {{--    $.ajax({--}}
        {{--        url: "{{route('admin.sub-category.lang-translate')}}",--}}
        {{--        data: {lang_id: lang_id},--}}
        {{--        method: "GET",--}}
        {{--        dataType: 'json',--}}
        {{--        success: function (response) {--}}
        {{--            // console.log(response);--}}
        {{--            var len = 0;--}}
        {{--            if (response['data'] != null) {--}}
        {{--                len = response['data'].length;--}}
        {{--            }--}}
        {{--            // console.log(len);--}}
        {{--            if (len > 0) {--}}
        {{--                var eng_option = "";--}}
        {{--                var arab_option = "";--}}
        {{--                // Read data and create <option >--}}
        {{--                for (var i = 0; i < len; i++) {--}}
        {{--                    var id = response['data'][i].id;--}}
        {{--                    var name = response['data'][i].name;--}}

        {{--                    if (lang_id == '1') {--}}
        {{--                        eng_option += "<option value='" + id + "'>" + name + "</option>";--}}

        {{--                    } else if (lang_id == '2') {--}}
        {{--                        arab_option += "<option value='" + id + "'>" + name + "</option>";--}}
        {{--                    }--}}
        {{--                }--}}
        {{--                $("#eng_category_id").html(eng_option);--}}
        {{--                $("#arabic_category_id").html(arab_option);--}}
        {{--            }--}}
        {{--        }--}}
        {{--    });--}}
        {{--}--}}
        {{--$('#english_lang').click(function () {--}}
        {{--    $('#lang_id').val(1);--}}
        {{--    var lang_id = $('#lang_id').val();--}}
        {{--    // alert(lang_id)--}}
        {{--    $.ajaxSetup({--}}
        {{--        headers: {--}}
        {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
        {{--        }--}}
        {{--    });--}}
        {{--    $.ajax({--}}
        {{--        url: "{{route('admin.sub-category.lang-translate')}}",--}}
        {{--        data: {lang_id: lang_id},--}}
        {{--        method: "GET",--}}
        {{--        dataType: 'json',--}}
        {{--        success: function (response) {--}}
        {{--            console.log(response);--}}
        {{--            var len = 0;--}}
        {{--            if (response['data'] != null) {--}}
        {{--                len = response['data'].length;--}}
        {{--            }--}}
        {{--            console.log(len);--}}
        {{--            if (len > 0) {--}}
        {{--                var option = "";--}}
        {{--                for (var i = 0; i < len; i++) {--}}
        {{--                    var id = response['data'][i].id;--}}
        {{--                    var name = response['data'][i].name;--}}

        {{--                    if (lang_id == '1') {--}}
        {{--                        option += "<option value='" + id + "'>" + name + "</option>";--}}
        {{--                    }--}}
        {{--                }--}}
        {{--                $("#eng_category_id").html(option);--}}

        {{--            } else {--}}
        {{--                var option = "<option value=''>--No Category Added--</option>";--}}

        {{--                $("#eng_category_id").html(option);--}}
        {{--            }--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}
        {{--$('#arabic_lang').click(function () {--}}
        {{--    $('#lang_id').val(2);--}}
        {{--    var lang_id = $('#lang_id').val();--}}
        {{--    // alert(lang_id)--}}
        {{--    $.ajaxSetup({--}}
        {{--        headers: {--}}
        {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
        {{--        }--}}
        {{--    });--}}
        {{--    $.ajax({--}}
        {{--        url: "{{route('admin.sub-category.lang-translate')}}",--}}
        {{--        data: {lang_id: lang_id},--}}
        {{--        method: "GET",--}}
        {{--        dataType: 'json',--}}
        {{--        success: function (response) {--}}
        {{--            console.log(response);--}}
        {{--            var len = 0;--}}
        {{--            if (response['data'] != null) {--}}
        {{--                len = response['data'].length;--}}
        {{--            }--}}
        {{--            console.log(len);--}}
        {{--            if (len > 0) {--}}
        {{--                var option = "";--}}
        {{--                for (var i = 0; i < len; i++) {--}}
        {{--                    var id = response['data'][i].id;--}}
        {{--                    var name = response['data'][i].name;--}}

        {{--                    if (lang_id == '2') {--}}
        {{--                        option += "<option value='" + id + "'>" + name + "</option>";--}}
        {{--                    }--}}
        {{--                }--}}
        {{--                $("#arabic_category_id").html(option);--}}

        {{--            } else {--}}
        {{--                var option = "<option value=''>--No Category Added--</option>";--}}

        {{--                $("#arabic_category_id").html(option);--}}
        {{--            }--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}
    </script>
@endpush
