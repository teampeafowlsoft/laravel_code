@extends('adminmodule::layouts.master')

@section('title',translate('category_setup'))

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
                        <h2 class="page-title">{{translate('category_setup')}}</h2>
                    </div>

                    <!-- Category Setup -->
                    <div class="card category-setup mb-30">
                        <div class="card-body p-30">
                            <form action="{{route('admin.category.store')}}" method="post"
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
                                                    <label>{{translate('category_name')}} <span
                                                            class="text-danger">*</span></label>
                                                    <div class="form-floating mb-30">
                                                        <input type="text" name="name" class="form-control"
                                                               value="{{old('name')}}"
                                                               placeholder="{{translate('category_name')}}" required>
                                                        <span class="text-danger">Note : Do not enter Name with comma (,) special character</span>
                                                    </div>

                                                    <label>{{translate('Select_Zone')}}</label>
                                                    <div class="form-floating mb-30">
                                                        <select class="select-zone theme-input-style w-100"
                                                                id="eng_zone_id"
                                                                name="zone_ids[]"
                                                                multiple="multiple">
                                                        </select>
                                                    </div>

                                                    <label>{{translate('description')}} </label>
                                                    <div class="form-floating mb-30">
                                                        <textarea name="description" class="form-control"
                                                                  placeholder="{{translate('description')}}"></textarea>
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
                                                               value="{{old('name')}}"
                                                               placeholder="{{translate('category_name')}}" required>
                                                        <span class="text-danger">Note : Do not enter Name with comma (,) special character</span>

                                                    </div>

                                                    <label>{{translate('Select_Zone')}} </label>
                                                    <div class="form-floating mb-30">
                                                        <select class="select-zone theme-input-style w-100"
                                                                id="arabic_zone_id"
                                                                name="arabic_zone_ids[]"
                                                                multiple="multiple">
                                                        </select>
                                                    </div>

                                                    <label>{{translate('description')}} </label>
                                                    <div class="form-floating mb-30">
                                                        <textarea name="arabic_description" class="form-control"
                                                                  placeholder="{{translate('description')}}"></textarea>

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
                                                    <input type="file" class="upload-file__input" required name="image">
                                                    <div class="upload-file__img">
                                                        <img
                                                            onerror="this.src='{{asset('public/assets/admin-module/img/media/upload-file.png')}}'"
                                                            src="{{asset('public/assets/admin-module')}}/img/media/upload-file.png"
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
                            <span class="opacity-75">{{translate('Total_Categories')}}:</span>
                            <span class="title-color">{{$categories->total()}}</span>
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
                                                           href="{{route('admin.category.download')}}?search={{$search}}">{{translate('excel')}}</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="example" class="table align-middle">
                                            <thead class="align-middle">
                                            <tr>
                                                <th>{{translate('SL')}}</th>
                                                <th>{{translate('category_image')}}</th>
                                                <th>{{translate('category_name') . '(' . ('english') . ')'}}</th>
                                                <th>{{translate('category_name') . '(' . ('arabic') . ')'}}</th>
                                                <th>{{translate('sub_category_count')}}</th>
                                                <th>{{translate('zone_count')}}</th>
                                                <th>{{translate('status')}}</th>
                                                <th>{{translate('action')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php $i=1; @endphp
                                            @foreach($categories as $key=>$category)
                                                @php
                                                    $subcategory = '';
                                                    $zone = '';
                                                @endphp
                                                @foreach($children as $key=>$child)
                                                    @php
                                                        if ($child->id == (explode(',',$category->id)[0])) {
                                                              $subcategory .= $child->children_count;
                                                              $zone .= $child->zones_count;
                                                              }
                                                    @endphp
                                                @endforeach
                                                <tr>
                                                    <td>{{$i++}}</td>
                                                    <td><img
                                                            width="70" height="70"
                                                            onerror="this.src='{{asset('public/assets/admin-module')}}/img/media/upload-file.png'"
                                                            src="{{asset('storage/app/public/category')}}/{{explode(',',$category->image)[0]}}"
                                                            alt=""></td>
                                                    <td>{{explode(',',$category->name)[0]}}</td>
                                                    <td>{{!empty(explode(',',$category->name)[1]) ? explode(',',$category->name)[1] : ''}}</td>
{{--                                                    <td>{{$category->children_count}}</td>--}}
                                                    <td>{{$subcategory}}</td>
{{--                                                    <td>{{$category->zones_count}}</td>--}}
                                                    <td>{{$zone}}</td>
                                                    <td>
                                                        <label class="switcher" data-bs-toggle="modal" data-bs-target="#deactivateAlertModal">
                                                            <input class="switcher_input"
                                                                   onclick="route_alert('{{route('admin.category.status-update',[$category->group_id])}}','{{translate('want_to_update_status')}}')"
                                                                   type="checkbox" {{$category->is_active?'checked':''}}>
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <div class="table-actions">
                                                            <a href="{{route('admin.category.edit',[$category->id,$category->group_id])}}"
                                                               class="table-actions_edit demo_check">
                                                                <span class="material-icons">edit</span>
                                                            </a>
                                                            <button type="button"
                                                                    @if(env('APP_ENV')!='demo')
                                                                    onclick="form_alert('delete-{{$category->group_id}}','{{translate('want_to_delete_this_category')}}?')"
                                                                    @endif
                                                                    class="table-actions_delete bg-transparent border-0 p-0 demo_check">
                                                                <span class="material-icons">delete</span>
                                                            </button>
                                                            <form
                                                                action="{{route('admin.category.delete',[$category->group_id])}}"
                                                                method="post" id="delete-{{$category->group_id}}"
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
                                        {!! $categories->links() !!}
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
