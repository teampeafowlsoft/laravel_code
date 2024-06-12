@extends('adminmodule::layouts.master')

@section('title',translate('product_sub_category_update'))

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
                        <h2 class="page-title">{{translate('product_sub_category_update')}}</h2>
                    </div>

                @php
                   // $main_category_id_eng = explode(',',$sub_category->parent_id)[0];
                    // $main_category_id_arabic = explode(',',$sub_category->parent_id)[1];
                    // $category_id_eng = explode(',',$sub_category->id)[0];
                    // $category_id_arabic = explode(',',$sub_category->id)[1];
                    // $id = explode(',',$sub_category->id);
                    // $eng_lang_id = explode(',',$sub_category->lang_id)[0];
                    // $arabic_lang_id = explode(',',$sub_category->lang_id)[1];
                    // $name = !empty(explode(',',$sub_category->name)[0]) ? explode(',',$sub_category->name)[0] : '';
                    // $arabic_name = !empty(explode(',',$sub_category->name)[1]) ? explode(',',$sub_category->name)[1] : '';
                    // $description = !empty(explode(',',$sub_category->description)[0]) ? explode(',',$sub_category->description)[0] : '';
                    // $arabic_description = !empty(explode(',',$sub_category->description)[1]) ? explode(',',$sub_category->description)[1] : '';

                    $main_category_id_eng = $sub_category[0]->parent_id;
                    $main_category_id_arabic = $sub_category[1]->parent_id;
                    $category_id_eng = $sub_category[0]->id;
                    $category_id_arabic = $sub_category[1]->id;
                    $id = $sub_category[0]->id;
                    $eng_lang_id = $sub_category[0]->lang_id;
                    $arabic_lang_id = $sub_category[1]->lang_id;
                    $name = !empty($sub_category[0]->name) ? $sub_category[0]->name : '';
                    $arabic_name = !empty($sub_category[1]->name) ? $sub_category[1]->name : '';
                    $description = !empty($sub_category[0]->description) ? $sub_category[0]->description : '';
                    $arabic_description = !empty($sub_category[1]->description) ? $sub_category[1]->description : '';
                @endphp
                    <!-- Category Setup -->
                    <div class="card category-setup mb-30">
                        <div class="card-body p-30">
                            <form action="{{route('admin.productsub-category.update',[$sub_category[0]->group_id])}}"
                                  method="post"
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
                                                           href="{{route('admin.productsub-category.create')}}"><span
                                                                class="material-icons"
                                                                title="{{translate('sub_category_setup')}}">chevron_left</span> {{translate('back')}}
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
                                                    <select class="js-select theme-input-style w-100" name="parent_id">
                                                        <option value="0" selected disabled>
                                                            {{translate('Select_Category_Name')}}
                                                        </option>
                                                        @foreach($main_categories as $item)
                                                            <option
                                                                value="{{$item['id']}}" {{$main_category_id_eng==$item->id?'selected':''}}>
                                                                {{$item->name}}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    <div class="form-floating mb-30 mt-30">
                                                        <input type="text" name="name" class="form-control"
                                                               value="{{$name}}"
                                                               placeholder="{{translate('sub_category_name')}}"
                                                               required>
                                                        <label>{{translate('sub_category_name')}}</label>
                                                        <span class="text-danger">Note : Do not enter Name with comma (,) special character</span>
                                                    </div>

                                                    <div class="form-floating mb-30">
                                                <textarea type="text" name="short_description" class="form-control"
                                                          required>{{$description}}</textarea>
                                                        <label>{{translate('short_description')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic">
                                                <div class="d-flex flex-column">
                                                    <select class="js-select theme-input-style w-100"
                                                            name="arabic_parent_id" id="arabic_category_id">
                                                        <option value="" selected
                                                                disabled>{{translate('Select_Category_Name_arabic')}}</option>
                                                        @foreach($arabic_main_categories as $item)
                                                            <option
                                                                value="{{$item['id']}}" {{$main_category_id_arabic==$item->id?'selected':''}}>
                                                                {{$item->name}}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    <div class="form-floating mb-30 mt-30">
                                                        <input type="text" name="arabic_name" class="form-control"
                                                               value="{{$arabic_name}}"
                                                               placeholder="{{translate('sub_category_name_arabic')}}"
                                                               required>
                                                        <label>{{translate('sub_category_name_arabic')}}</label>
                                                        <span class="text-danger">Note : Do not enter Name with comma (,) special character</span>
                                                    </div>

                                                    <div class="form-floating mb-30">
                                                <textarea type="text" name="arabic_short_description"
                                                          class="form-control"
                                                          required>{{$arabic_description}}</textarea>
                                                        <label>{{translate('short_description_arabic')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="d-flex  gap-3 gap-xl-5">
                                            <div>
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input" name="image">
                                                    <div class="upload-file__img">
                                                        <img
                                                            src="{{asset('storage/app/public/productcategory')}}/{{$sub_category[0]->image}}"
                                                            onerror="this.src='{{asset('public/assets/admin-module')}}/img/media/upload-file.png'"
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
            $('.js-select').select2();
        });
    </script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/dataTables.select.min.js"></script>

@endpush
