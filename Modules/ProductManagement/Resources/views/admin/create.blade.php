@extends('adminmodule::layouts.master')

@section('title',translate('product_setup'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/select.dataTables.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/wysiwyg-editor/froala_editor.min.css"/>
@endpush
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                {{--                <div class="col-12">--}}
                <div class="page-title-wrap mb-3">
                    <h2 class="page-title">{{translate('add_new_product')}}</h2>
                </div>

                <div class="col-2">
                    <div class="card">
                        {{--                            <div class="card-body">--}}
                        <div class="vertical-tabs">
                            <ul class="nav nav-tabs nav--tabs__style2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#basic_details"
                                       role="tab"
                                       aria-controls="home">{{translate('basic_details')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#category" role="tab"
                                       aria-controls="profile">{{translate('category')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#attributes" role="tab"
                                       aria-controls="messages">{{translate('attributes')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#media" role="tab"
                                       aria-controls="settings">{{translate('media')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#delivery_charge"
                                       role="tab"
                                       aria-controls="settings">{{translate('delivery_charge')}}</a>
                                </li>
                            </ul>
                        </div>
                        {{--                            </div>--}}
                    </div>
                </div>
                <div class="col-10">
                    <div class="card">
                        <div class="card-body p-30">
                            <div class="tab-content">
                                <div class="tab-pane active" id="basic_details" role="tabpanel">
                                    <!-- Nav Tabs -->
                                    <div class="mb-3">
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

                                    <form action="{{route('admin.service.store')}}" method="post"
                                          enctype="multipart/form-data"
                                          id="service-add-form">
                                        @csrf
                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="english">
                                                <div class="row">
                                                    <div class="col-lg-6 mb-5 mb-lg-0">
                                                        <div class="mb-30">
                                                            <label><strong>{{translate('product_name')}} <span
                                                                        class="text-danger">*</span></strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" class="form-control" name="name"
                                                                       placeholder="{{translate('product_name')}} *"
                                                                       required="">
                                                            </div>
                                                        </div>
                                                        <div class="mb-30">
                                                            <label><strong>{{translate('display_name')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" class="form-control"
                                                                       name="display_name"
                                                                       placeholder="{{translate('display_name')}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-5 mb-lg-0">
                                                        <div class="mb-30">
                                                            <label><strong>{{translate('reference_code')}} <span
                                                                        class="text-danger">*</span></strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" class="form-control"
                                                                       name="reference_code"
                                                                       placeholder="{{translate('reference_code')}} *"
                                                                       required="">
                                                            </div>
                                                        </div>
                                                        <div class="mb-30">
                                                            <label><strong>{{translate('select_shop')}} <span
                                                                        class="text-danger">*</span></strong></label>
                                                            <select class="js-select theme-input-style w-100"
                                                                    name="shop" required>
                                                                <option value="0">{{translate('select_shop')}}</option>
                                                                {{--                                                                    @foreach($categories as $category)--}}
                                                                {{--                                                                        <option value="{{$category->id}}">{{$category->name}}</option>--}}
                                                                {{--                                                                    @endforeach--}}
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <label><strong>{{translate('description')}}</strong></label>
                                                        <section id="editor">
                                                            <textarea class="ckeditor"
                                                                      name="description">{{old('description')}}</textarea>
                                                        </section>
                                                    </div>
                                                    <div class="col-12 mt-3">
                                                        <div class="form-group text-danger">
                                                            &nbsp;&nbsp;&nbsp;<b>** </b>Here Price is Original Price of
                                                            Product , and Stock is actual Quantites of Product which
                                                            have no attributes or Combinations. Price and Stock is also
                                                            manage at Supplier level.
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic">
                                                <div class="row">
                                                    <div class="col-lg-6 mb-5 mb-lg-0">
                                                        <div class="mb-30">
                                                            <label><strong>{{translate('product_name')}} <span
                                                                        class="text-danger">*</span></strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" class="form-control"
                                                                       name="arabic_name"
                                                                       placeholder="{{translate('product_name')}} *"
                                                                       required="">
                                                            </div>
                                                        </div>
                                                        <div class="mb-30">
                                                            <label><strong>{{translate('display_name')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" class="form-control"
                                                                       name="arabic_display_name"
                                                                       placeholder="{{translate('display_name')}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-5 mb-lg-0">
                                                        <div class="mb-30">
                                                            <label><strong>{{translate('reference_code')}} <span
                                                                        class="text-danger">*</span></strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" class="form-control"
                                                                       name="arabic_reference_code"
                                                                       placeholder="{{translate('reference_code')}} *"
                                                                       required="">
                                                            </div>
                                                        </div>
                                                        <div class="mb-30">
                                                            <label><strong>{{translate('select_shop')}} <span
                                                                        class="text-danger">*</span></strong></label>
                                                            <select class="js-select theme-input-style w-100"
                                                                    name="arabic_shop" required>
                                                                <option value="0">{{translate('select_shop')}}</option>
                                                                {{--                                                                    @foreach($categories as $category)--}}
                                                                {{--                                                                        <option value="{{$category->id}}">{{$category->name}}</option>--}}
                                                                {{--                                                                    @endforeach--}}
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <label><strong>{{translate('description')}}</strong></label>
                                                        <section id="editor">
                                                            <textarea class="ckeditor"
                                                                      name="arabic_description">{{old('description')}}</textarea>
                                                        </section>
                                                    </div>
                                                    <div class="col-12 mt-3">
                                                        <div class="form-group text-danger">
                                                            &nbsp;&nbsp;&nbsp;<b>** </b>Here Price is Original Price of
                                                            Product , and Stock is actual Quantites of Product which
                                                            have no attributes or Combinations. Price and Stock is also
                                                            manage at Supplier level.
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end gap-20 mt-30">
                                                <button class="btn btn--secondary"
                                                        type="reset">{{translate('reset')}}</button>
                                                <button class="btn btn--primary"
                                                        type="submit">{{translate('submit')}}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="category" role="tabpanel">
                                    <!-- Nav Tabs -->
                                    <div class="mb-3">
                                        <ul class="nav nav--tabs nav--tabs__style2">
                                            <input type="hidden" name="eng_lang_id" id="eng_lang_id"
                                                   value="{{$languages[0]->language_master_id}}">
                                            <li class="nav-item">
                                                <label data-bs-toggle="tab" data-bs-target="#english_category"
                                                       class="nav-link active" id="english_lang_category">
                                                    {{$languages[0]->language_name}}
                                                </label>
                                            </li>
                                            <li class="nav-item">
                                                <input type="hidden" name="arabic_lang_id" id="arabic_lang_id"
                                                       value="{{$languages[1]->language_master_id}}">
                                                <label data-bs-toggle="tab" data-bs-target="#arabic_category"
                                                       class="nav-link" id="arabic_lang_category">
                                                    {{$languages[1]->language_name}}
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                    <hr>
                                    <!-- End Nav Tabs -->
                                    <form action="{{route('admin.service.store')}}" method="post"
                                          enctype="multipart/form-data"
                                          id="service-add-form">
                                        @csrf
                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="english_category">
                                                <div class="row">
                                                    <div class="col-lg-5 mb-5 mb-lg-0">
                                                        <div class="mb-30">
                                                            <label><strong>{{translate('Category')}} <span
                                                                        class="text-danger">*</span></strong></label>
                                                            <div class="form-floating">
                                                                <select class="js-select theme-input-style w-100"
                                                                        name="category_id"
                                                                        onchange="ajax_switch_category2('{{url('/')}}/admin/category/ajax-childes/'+this.value)">
                                                                    <option
                                                                        value="0">{{translate('choose_Category')}}</option>
                                                                    @foreach($categories as $category)
                                                                        <option
                                                                            value="{{$category->id}}">{{$category->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-5 mb-5 mb-lg-0">
                                                        <div class="mb-30">
                                                            <label><strong>{{translate('Sub_Category_Name')}}</strong></label>
                                                            <select class="subcategory-select theme-input-style w-100"
                                                                    name="sub_category_id[]" multiple="multiple">
                                                                <option value="1">Test Sub category 1</option>
                                                                <option value="2">Test Sub category 2</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 mb-5 mt-4 mb-lg-0">
                                                        <label></label>
                                                        <button type="button" class="btn btn--primary"
                                                                onclick="ajax_variation_category('{{route('admin.product.ajax-add-category')}}','variation-table')">
                                                            <span class="material-icons">add</span>
                                                            {{translate('add')}}
                                                        </button>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic_category">
                                                <div class="row">
                                                    <div class="col-lg-5 mb-5 mb-lg-0">
                                                        <div class="mb-30">
                                                            <label><strong>{{translate('Category')}} <span
                                                                        class="text-danger">*</span></strong></label>
                                                            <div class="form-floating">
                                                                <select class="js-select theme-input-style w-100"
                                                                        name="arabic_category_id"
                                                                        onchange="ajax_switch_category2('{{url('/')}}/admin/category/ajax-childes/'+this.value)">
                                                                    <option
                                                                        value="0">{{translate('choose_Category')}}</option>
                                                                    @foreach($categories as $category)
                                                                        <option
                                                                            value="{{$category->id}}">{{$category->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-5 mb-5 mb-lg-0">
                                                        <div class="mb-30">
                                                            <label><strong>{{translate('Sub_Category_Name')}}</strong></label>
                                                            <select class="subcategory-select theme-input-style w-100"
                                                                    name="arabic_sub_category_id[]" multiple="multiple">
                                                                <option value="1">Test Sub category 1</option>
                                                                <option value="2">Test Sub category 2</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 mb-5 mt-4 mb-lg-0">
                                                        <button type="button" class="btn btn--primary"
                                                                onclick="ajax_variation_category('{{route('admin.product.ajax-add-category')}}','variation-table')">
                                                            <span class="material-icons">add</span>
                                                            {{translate('add')}}
                                                        </button>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end gap-20 mt-30">
                                                <button class="btn btn--secondary"
                                                        type="reset">{{translate('reset')}}</button>
                                                <button class="btn btn--primary"
                                                        type="submit">{{translate('submit')}}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="attributes" role="tabpanel">
                                    <!-- Nav Tabs -->
                                    <div class="mb-3">
                                        <ul class="nav nav--tabs nav--tabs__style2">
                                            <input type="hidden" name="eng_lang_id" id="eng_lang_id"
                                                   value="{{$languages[0]->language_master_id}}">
                                            <li class="nav-item">
                                                <label data-bs-toggle="tab" data-bs-target="#english_attributes"
                                                       class="nav-link active" id="english_lang_attributes">
                                                    {{$languages[0]->language_name}}
                                                </label>
                                            </li>
                                            <li class="nav-item">
                                                <input type="hidden" name="arabic_lang_id" id="arabic_lang_id"
                                                       value="{{$languages[1]->language_master_id}}">
                                                <label data-bs-toggle="tab" data-bs-target="#arabic_attributes"
                                                       class="nav-link" id="arabic_lang_attributes">
                                                    {{$languages[1]->language_name}}
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                    <hr>
                                    <!-- End Nav Tabs -->
                                    <form action="{{route('admin.service.store')}}" method="post"
                                          enctype="multipart/form-data"
                                          id="service-add-form">
                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="english_attributes">
                                                <div class="row">
                                                    <div class="d-flex flex-wrap gap-20 mb-3">
                                                        <div id="packate_div" >
                                                            <div class="row">
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="unit"><strong>{{translate('attribute')}}</strong></label>
                                                                    <div class="form-floating packate_div">
                                                                        <select class="js-select theme-input-style w-100"
                                                                                name="packate_measurement_attribute_id[]">
                                                                            <option value="0">Select Atribute</option>
                                                                            <option value="1">Grams</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="unit"><strong>{{translate('attribute_value')}}</strong></label>
                                                                    <div class="form-floating packate_div">
                                                                        <select class="js-select theme-input-style w-100"
                                                                                name="packate_measurement_attribute_value[]" multiple="multiple">
                                                                            <option value="0">Atribute Value</option>
                                                                            <option value="1.234">1.234</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="unit"><strong>{{translate('selling_price')}}</strong></label>
                                                                    <div class="form-floating packate_div">
                                                                        <input type="text" class="form-control"
                                                                               name="packate_measurement_sell_price[]"
                                                                               placeholder="{{translate('product_name')}} *"
                                                                               required="">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="unit"><strong>{{translate('cost_price')}}</strong></label>
                                                                    <div class="form-floating packate_div">
                                                                        <input type="text" class="form-control"
                                                                               name="packate_measurement_cost_price[]"
                                                                               placeholder="{{translate('product_name')}} *"
                                                                               required="">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="packate_measurement_shelf_life_val"><strong>{{translate('shelf_life_value')}}</strong> </label>
                                                                    <div class="form-floating packate_div">
                                                                        <input type="text" class="form-control"
                                                                               name="packate_measurement_shelf_life_val[]"
                                                                               placeholder="{{translate('product_name')}} *">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="barcode"><strong>{{translate('barcode')}}</strong></label>
                                                                    <div class="form-floating packate_div">
                                                                        <input type="text" class="form-control"
                                                                               name="packate_measurement_barcode[]"
                                                                               placeholder="{{translate('product_name')}} *">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="packate_measurement_fssai_number"><strong>{{translate('fssai_number')}}</strong></label>
                                                                    <div class="form-floating packate_div">
                                                                        <input type="text" class="form-control"
                                                                               name="packate_measurement_fssai_number[]"
                                                                               placeholder="{{translate('product_name')}} *">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="packate_measurement_qty"><strong>{{translate('qty')}}</strong></label>
                                                                    <div class="form-floating packate_div">
                                                                        <input type="text" class="form-control"
                                                                               name="packate_measurement_qty[]" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" placeholder="{{translate('product_name')}} *" required>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-30 col-md-4">
                                                                    <label for="packate_measurement_images"><strong>{{translate('product_img')}}</strong></label>
                                                                    <div class="form-floating packate_div">
                                                                        <input type="file" class="form-control"
                                                                               name="packate_measurement_images[]"
                                                                               placeholder="{{translate('product_name')}} *" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <div class="form-group mt-3">
                                                                        <a id="add_packate_variation" title="Add variation of product"
                                                                           class="btn btn-success btn-xs text-white"
                                                                           style="cursor: pointer;"><span class="material-icons">add</span> </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="variations"></div>
                                                    </div>

{{--                                                    <div class="table-responsive p-01">--}}
{{--                                                        <table class="table align-middle table-variation">--}}
{{--                                                            <thead id="category-wise-zone">--}}
{{--                                                            @include('productmanagement::admin.partials._category-wise-zone',['zones'=>session()->has('category_wise_zones')?session('category_wise_zones'):[]])--}}
{{--                                                            </thead>--}}
{{--                                                            <tbody id="variation-table">--}}
{{--                                                            @include('productmanagement::admin.partials._variant-data',['zones'=>session()->has('category_wise_zones')?session('category_wise_zones'):[]])--}}
{{--                                                            </tbody>--}}
{{--                                                        </table>--}}
{{--                                                    </div>--}}
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic_attributes">
                                                <div class="row">
                                                    <div class="d-flex flex-wrap gap-20 mb-3">
                                                        <div id="arabic_packate_div" >
                                                            <div class="row">
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="unit"><strong>{{translate('attribute')}}</strong></label>
                                                                    <div class="form-floating arabic_packate_div">
                                                                        <select class="js-select theme-input-style w-100"
                                                                                name="arabic_packate_measurement_attribute_id[]">
                                                                            <option value="0">Select Atribute</option>
                                                                            <option value="1">Grams</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="unit"><strong>{{translate('attribute_value')}}</strong></label>
                                                                    <div class="form-floating arabic_packate_div">
                                                                        <select class="js-select theme-input-style w-100"
                                                                                name="arabic_packate_measurement_attribute_value[]" multiple="multiple">
                                                                            <option value="0">Atribute Value</option>
                                                                            <option value="1.234">1.234</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="unit"><strong>{{translate('selling_price')}}</strong></label>
                                                                    <div class="form-floating arabic_packate_div">
                                                                        <input type="text" class="form-control"
                                                                               name="arabic_packate_measurement_sell_price[]"
                                                                               placeholder="{{translate('product_name')}} *"
                                                                               required="">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="unit"><strong>{{translate('cost_price')}}</strong></label>
                                                                    <div class="form-floating arabic_packate_div">
                                                                        <input type="text" class="form-control"
                                                                               name="arabic_packate_measurement_cost_price[]"
                                                                               placeholder="{{translate('product_name')}} *"
                                                                               required="">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="packate_measurement_shelf_life_val"><strong>{{translate('shelf_life_value')}}</strong></label>
                                                                    <div class="form-floating arabic_packate_div">
                                                                        <input type="text" class="form-control"
                                                                               name="arabic_packate_measurement_shelf_life_val[]"
                                                                               placeholder="{{translate('product_name')}} *">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="barcode"><strong>{{translate('barcode')}}</strong></label>
                                                                    <div class="form-floating arabic_packate_div">
                                                                        <input type="text" class="form-control"
                                                                               name="arabic_packate_measurement_barcode[]"
                                                                               placeholder="{{translate('product_name')}} *">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="packate_measurement_fssai_number"><strong>{{translate('fssai_number')}}</strong></label>
                                                                    <div class="form-floating arabic_packate_div">
                                                                        <input type="text" class="form-control"
                                                                               name="arabic_packate_measurement_fssai_number[]"
                                                                               placeholder="{{translate('product_name')}} *">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-30 col-md-2">
                                                                    <label for="arabic_packate_measurement_qty"><strong>{{translate('qty')}}</strong></label>
                                                                    <div class="form-floating arabic_packate_div">
                                                                        <input type="text" class="form-control"
                                                                               name="arabic_packate_measurement_qty[]" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"
                                                                               placeholder="" required>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-30 col-md-4">
                                                                    <label for="arabic_packate_measurement_images"><strong>Images</strong></label>
                                                                    <div class="form-floating arabic_packate_div">
                                                                        <input type="file" class="form-control"
                                                                               name="arabic_packate_measurement_images[]"
                                                                               placeholder="{{translate('product_name')}} *" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2 text-center">
                                                                    <div class="form-group mt-3">
                                                                        <a id="add_arabic_packate_variation" title="Add variation of product"
                                                                           class="btn btn-success btn-xs text-white"
                                                                           style="cursor: pointer;"><span class="material-icons">add</span> </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="arabic_variations"></div>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end gap-20 mt-30">
                                                <button class="btn btn--secondary"
                                                        type="reset">{{translate('reset')}}</button>
                                                <button class="btn btn--primary"
                                                        type="submit">{{translate('submit')}}
                                                </button>
                                            </div>
                                        </div>
                                        @csrf
                                    </form>
                                </div>
                                <div class="tab-pane" id="media" role="tabpanel">
                                    <form action="{{route('admin.service.store')}}" method="post"
                                          enctype="multipart/form-data"
                                          id="service-add-form">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-12 mb-5 mb-lg-0">
                                                <div class="mb-30">
                                                    <label class="mb-3"><strong>{{translate('add_multi_img')}}</strong></label>
                                                    <div class="form-floating">
                                                        <div class="field">
                                                            <input type="file" id="fileupload" name="other_images[]"
                                                                   accept=".png,.jpeg,.jpg" multiple/>
                                                        </div>
                                                    </div>
                                                    <div id="dvPreview"></div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="col-12">
                                                <div class="d-flex justify-content-end gap-20 mt-30">
                                                    <button class="btn btn--secondary"
                                                            type="reset">{{translate('reset')}}</button>
                                                    <button class="btn btn--primary"
                                                            type="submit">{{translate('submit')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="delivery_charge" role="tabpanel">
                                    <!-- Nav Tabs -->
                                    <div class="mb-3 d-none">
                                        <ul class="nav nav--tabs nav--tabs__style2">
                                            <input type="hidden" name="eng_lang_id" id="eng_lang_id"
                                                   value="{{$languages[0]->language_master_id}}">
                                            <li class="nav-item">
                                                <label data-bs-toggle="tab" data-bs-target="#english_delivery_charge"
                                                       class="nav-link active" id="english_lang_delivery_charge">
                                                    {{$languages[0]->language_name}}
                                                </label>
                                            </li>
                                            <li class="nav-item">
                                                <input type="hidden" name="arabic_lang_id" id="arabic_lang_id"
                                                       value="{{$languages[1]->language_master_id}}">
                                                <label data-bs-toggle="tab" data-bs-target="#arabic_delivery_charge"
                                                       class="nav-link" id="arabic_lang_delivery_charge">
                                                    {{$languages[1]->language_name}}
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
{{--                                    <hr>--}}
                                    <!-- End Nav Tabs -->
                                    <div class="tab-pane fade show active" id="english_delivery_charge">
                                        <form action="{{route('admin.service.store')}}" method="post"
                                              enctype="multipart/form-data"
                                              id="service-add-form">
                                            @csrf
                                            <div class="col-lg-4 mb-5 mb-lg-0">
                                                <div
                                                    class="data-table-top d-flex flex-wrap gap-10 justify-content-between">
                                                    <form action="{{url()->current()}}?status={{$status}}"
                                                          class="search-form search-form_style-two"
                                                          method="POST">
                                                        @csrf
                                                        <div class="input-group search-form__input_group">
                                            <span class="search-form__icon">
                                                <span class="material-icons">search</span>
                                            </span>
                                                            <input type="search"
                                                                   class="theme-input-style search-form__input"
                                                                   value="{{$search}}" name="search"
                                                                   placeholder="{{translate('search_here')}}">
                                                            <button type="submit"
                                                                    class="btn btn--primary">{{translate('search')}}</button>
                                                        </div>

                                                    </form>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-5 mb-lg-0">
                                                <div class="table-responsive">
                                                    <table id="example" class="table align-middle">
                                                        <thead>
                                                        <tr>
                                                            <th>{{translate('zone_area_name')}}</th>
                                                            <th>{{translate('delivery_charge')}}</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($zones as $key=>$zone)
                                                            <tr>
                                                                <td>{{$zone->name}}</td>
                                                                <td><input type="text" class="form-control" value="0" placeholder="Enter Delivery charge" name="delivery_charge"><input type="hidden" class="form-control" name="zone_id" value="{{$zone->id}}" placeholder="Enter Delivery charge"></td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="col-12">
                                                <div class="d-flex justify-content-end gap-20 mt-30">
                                                    <button class="btn btn--secondary"
                                                            type="reset">{{translate('reset')}}</button>
                                                    <button class="btn btn--primary"
                                                            type="submit">{{translate('submit')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
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
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/dataTables.select.min.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.js-select').select2();
            $('.subcategory-select').select2({
                placeholder: "Choose Subcategory"
            });
        });
    </script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/jquery-steps/jquery.steps.min.js"></script>
    <script>
        $("#form-wizard").steps({
            headerTag: "h3",
            bodyTag: "section",
            transitionEffect: "slideLeft",
            autoFocus: true,
            onFinished: function (event, currentIndex) {
                $("#service-add-form")[0].submit();
            }
        });
    </script>

    <script>
        <!--
        Category_Tab_Code_Start -->
        function ajax_variation_category(route, id) {
            let name = $('#variant-name').val();
            let price = $('#variant-price').val();

            if (name.length > 0 && price >= 0) {
                $.get({
                    url: route,
                    dataType: 'json',
                    data: {
                        name: $('#variant-name').val(),
                        price: $('#variant-price').val(),
                    },
                    beforeSend: function () {
                        /*$('#loading').show();*/
                    },
                    success: function (response) {
                        console.log(response.template)
                        if (response.flag == 0) {
                            toastr.info('Already added');
                        } else {
                            $('#new-variations-table').show();
                            $('#' + id).html(response.template);
                            $('#variant-name').val("");
                            $('#variant-price').val(0);
                        }
                    },
                    complete: function () {
                        /*$('#loading').hide();*/
                    },
                });
            } else {
                toastr.warning('{{translate('fields_are_required')}}');
            }
        }

        <!--Category_Tab_Code_End -->

        function ajax_variation(route, id) {
            let name = $('#variant-name').val();
            let price = $('#variant-price').val();

            if (name.length > 0 && price >= 0) {
                $.get({
                    url: route,
                    dataType: 'json',
                    data: {
                        name: $('#variant-name').val(),
                        price: $('#variant-price').val(),
                    },
                    beforeSend: function () {
                        /*$('#loading').show();*/
                    },
                    success: function (response) {
                        console.log(response.template)
                        if (response.flag == 0) {
                            toastr.info('Already added');
                        } else {
                            $('#new-variations-table').show();
                            $('#' + id).html(response.template);
                            $('#variant-name').val("");
                            $('#variant-price').val(0);
                        }
                    },
                    complete: function () {
                        /*$('#loading').hide();*/
                    },
                });
            } else {
                toastr.warning('{{translate('fields_are_required')}}');
            }
        }

        function ajax_remove_variant(route, id) {
            Swal.fire({
                title: "{{translate('are_you_sure')}}?",
                text: "{{translate('want_to_remove_this_variation')}}",
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
                    $.get({
                        url: route,
                        dataType: 'json',
                        data: {},
                        beforeSend: function () {
                            /*$('#loading').show();*/
                        },
                        success: function (response) {
                            console.log(response.template)
                            $('#' + id).html(response.template);
                        },
                        complete: function () {
                            /*$('#loading').hide();*/
                        },
                    });
                }
            })
        }

        function ajax_switch_category(route) {
            $.get({
                url: route,
                dataType: 'json',
                data: {},
                beforeSend: function () {
                    /*$('#loading').show();*/
                },
                success: function (response) {
                    console.log(response.template_for_variant);
                    /* console.log(response.template) */
                    $('#sub-category-selector').html(response.template);
                    $('#category-wise-zone').html(response.template_for_zone);
                    $('#variation-table').html(response.template_for_variant);
                },
                complete: function () {
                    /*$('#loading').hide();*/
                },
            });
        }

        function ajax_switch_category2(route) {
            $.get({
                url: route,
                dataType: 'json',
                data: {},
                beforeSend: function () {
                    /*$('#loading').show();*/
                },
                success: function (response) {
                    console.log(response.template_for_variant);
                    /* console.log(response.template) */
                    $('#sub-category-selector-2').html(response.template);
                    // $('#category-wise-zone').html(response.template_for_zone);
                    // $('#variation-table').html(response.template_for_variant);
                },
                complete: function () {
                    /*$('#loading').hide();*/
                },
            });
        }

    </script>

    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    {{--<script src="{{asset('public/assets/ckeditor/ckeditor.js')}}"></script>--}}
    <script src="{{asset('public/assets/ckeditor/jquery.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('textarea.ckeditor').each(function () {
                CKEDITOR.replace($(this).attr('id'));
            });
        });
    </script>

    <script language="javascript" type="text/javascript">
        window.onload = function () {
            var fileUpload = document.getElementById("fileupload");
            fileUpload.onchange = function () {
                if (typeof (FileReader) != "undefined") {
                    var dvPreview = document.getElementById("dvPreview");
                    dvPreview.innerHTML = "";
                    var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp)$/;
                    for (var i = 0; i < fileUpload.files.length; i++) {
                        var file = fileUpload.files[i];
                        if (regex.test(file.name.toLowerCase())) {
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                var span = document.createElement('span');
                                span.innerHTML = ['<span id="butt"><img class="thumb img-responsive" style="max-width: 20% !important; padding: 10px 10px 10px 10px !important;" src="', e.target.result, '"/><i class="fa fa-trash text-danger" aria-hidden="true"></i></div>'].join('');
                                document.getElementById('dvPreview').insertBefore(span, null);

                                $('#dvPreview').on('click', '#butt', function () {
                                    $(this).parent('span').remove();

                                    var i = array.indexOf($(this));
                                    if (i != -1) {
                                        array.splice(i, 1);
                                    }

                                    //$(this).parent('span').splice( 1, 1 );

                                    count--;
                                });
                            }
                            reader.readAsDataURL(file);
                        } else {
                            alert(file.name + " is not a valid image file.");
                            dvPreview.innerHTML = "";
                            return false;
                        }
                    }
                } else {
                    alert("This browser does not support HTML5 FileReader.");
                }
            }
        };
    </script>

    <script>
        var num = 2;
        var count = 0;
        $('#add_packate_variation').on('click', function () {
            count++;
            html = '<div class="variant_tbl"><hr class="hr-variant"><div class="row"><div class="mb-30 col-md-2"> <label for="packate_measurement_attribute_id"><strong>Attribute</strong></label><div class="form-floating packate_div"><select class="js-select theme-input-style w-100" name="packate_measurement_attribute_id[]" required><option value="0">Select Atribute</option><option value="1">Grams</option></select></div></div>'+
'<div class="mb-30 col-md-2"><label for="packate_measurement_attribute_value"><strong>Attribute Value</strong></label><div class="form-floating packate_div"><select class="js-select theme-input-style w-100" name="packate_measurement_attribute_value[]" multiple="multiple"><option value="0">Atribute Value</option> <option value="1.234">1.234</option></select></div></div><div class="mb-30 col-md-2"><label for="unit"><strong>Selling Price</strong></label><div class="form-floating packate_div"><input type="text" class="form-control" name="packate_measurement_sell_price[]" placeholder="product_name *" required=""></div></div><div class="mb-30 col-md-2"><label for="unit"><strong>Cost Price</strong></label><div class="form-floating packate_div"><input type="text" class="form-control" name="packate_measurement_cost_price[]" placeholder="product_name" required=""></div></div><div class="mb-30 col-md-2"><label for="packate_measurement_shelf_life_val"><strong>Shelf Life Value</strong></label><div class="form-floating packate_div"><input type="text" class="form-control" name="packate_measurement_shelf_life_val[]" placeholder="product_name *"> </div></div><div class="mb-30 col-md-2"><label for="barcode"><strong>Barcode</strong></label><div class="form-floating packate_div"><input type="text" class="form-control" name="packate_measurement_barcode[]" placeholder="product_name *"></div></div>' +
                '</div>' +
                '<div class="row"><div class="mb-30 col-md-2"><label for="packate_measurement_fssai_number"><strong>Fssai number</strong></label><div class="form-floating packate_div"><input type="text" class="form-control" name="packate_measurement_fssai_number[]" placeholder="product_name *"></div></div><div class="mb-30 col-md-2"><label for="packate_measurement_qty"><strong>Qty</strong></label><div class="form-floating packate_div"><input type="text" class="form-control" name="packate_measurement_qty[]" placeholder="product_name *" required></div></div><div class="mb-30 col-md-4"><label for="packate_measurement_images"><strong>Images</strong></label><div class="form-floating packate_div"><input type="file" class="form-control" name="packate_measurement_images[]" placeholder="product_name *" required> </div></div>' +
  '<div class="col-md-2 text-center" style="display: grid;"><label><strong>Action</strong></label><a class="remove_variation text-danger" title="Remove variation of product" style="cursor: pointer;"><span class="material-icons">delete</span></a></div></div></div>';

            $('#variations').append(html);
            $('#add_product_form').validate();
        });

        $(document).on('click', '.remove_variation', function () {
            $(this).closest('.variant_tbl').remove();
        });
    </script>
    <script>
        var num = 2;
        var count = 0;
        $('#add_arabic_packate_variation').on('click', function () {
            count++;
            html = '<div class="variant_tbl"><hr class="hr-variant"><div class="row"><div class="mb-30 col-md-2"> <label for="arabic_packate_measurement_attribute_id"><strong>Attribute</strong></label><div class="form-floating arabic_packate_div"><select class="js-select theme-input-style w-100" name="arabic_packate_measurement_attribute_id[]" required><option value="0">Select Atribute</option><option value="1">Grams</option></select></div></div>'+
                '<div class="mb-30 col-md-2"><label for="arabic_packate_measurement_attribute_value"><strong>Attribute Value</strong></label><div class="form-floating arabic_packate_div"><select class="js-select theme-input-style w-100" name="arabic_packate_measurement_attribute_value[]" multiple="multiple"><option value="0">Atribute Value</option> <option value="1.234">1.234</option></select></div></div><div class="mb-30 col-md-2"><label for="unit"><strong>Selling Price</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_sell_price[]" placeholder="product_name *" required=""></div></div><div class="mb-30 col-md-2"><label for="unit"><strong>Cost Price</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_cost_price[]" placeholder="product_name" required=""></div></div><div class="mb-30 col-md-2"><label for="arabic_packate_measurement_shelf_life_val"><strong>Shelf Life Value</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_shelf_life_val[]" placeholder="product_name *"> </div></div><div class="mb-30 col-md-2"><label for="barcode"><strong>Barcode</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_barcode[]" placeholder="product_name *"></div></div>' +
                '</div>' +
                '<div class="row"><div class="mb-30 col-md-2"><label for="arabic_packate_measurement_fssai_number"><strong>Fssai number</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_fssai_number[]" placeholder="product_name *"></div></div><div class="mb-30 col-md-2"><label for="arabic_packate_measurement_qty"><strong>Qty</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_qty[]" placeholder="product_name *" required></div></div><div class="mb-30 col-md-4"><label for="arabic_packate_measurement_images"><strong>Images</strong></label><div class="form-floating arabic_packate_div"><input type="file" class="form-control" name="arabic_packate_measurement_images[]" placeholder="product_name *" required> </div></div>' +
                '<div class="col-md-2 text-center" style="display: grid;"><label><strong>Action</strong></label><a class="remove_variation text-danger" title="Remove variation of product" style="cursor: pointer;"><span class="material-icons">delete</span></a></div></div></div>';

            $('#arabic_variations').append(html);
            $('#add_product_form').validate();
        });

        $(document).on('click', '.remove_variation', function () {
            $(this).closest('.variant_tbl').remove();
        });
    </script>
    <script>
        if ($('#packate').prop('checked')) {
            $('#packate_div').show();
            $('#packate_server_hide').hide();
            $('.loose_div').children(":input").prop('disabled', true);
            $('#loose_stock_div').children(":input").prop('disabled', true);
        }

        $.validator.addMethod('lessThanEqual', function (value, element, param) {
            return this.optional(element) || parseInt(value) < parseInt($(param).val());
        }, "Discounted Price should be lesser than Price");

        $(document).on('change', '#packate', function () {
            $('#variations').html("");
            $('#packate_div').show();
            $('#packate_server_hide').hide();
            $('.packate_div').children(":input").prop('disabled', false);
            $('#loose_div').hide();
            $('.loose_div').children(":input").prop('disabled', true);
            $('#loose_stock_div').hide();
            $('#loose_stock_div').children(":input").prop('disabled', true);

        });
    </script>

    <script>
        if ($('#arabic_packate').prop('checked')) {
            $('#arabic_packate_div').show();
            $('#arabic_packate_server_hide').hide();
        }

        $.validator.addMethod('lessThanEqual', function (value, element, param) {
            return this.optional(element) || parseInt(value) < parseInt($(param).val());
        }, "Discounted Price should be lesser than Price");

        $(document).on('change', '#arabic_packate', function () {
            $('#arabic_variations').html("");
            $('#arabic_packate_div').show();
            $('#arabic_packate_server_hide').hide();
            $('.arabic_packate_div').children(":input").prop('disabled', false);
        });
    </script>
@endpush
