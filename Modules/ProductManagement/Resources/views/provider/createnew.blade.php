@extends('providermanagement::layouts.master')

@section('title',translate('product_setup'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/select.dataTables.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/wysiwyg-editor/froala_editor.min.css"/>
    <style>
        /*Styling for errors on form*/
        .form_error span {
            width: 80%;
            height: 35px;
            font-size: 1.1em;
            color: #D83D5A;
        }

        .form_error input {
            border: 1px solid #D83D5A;
        }

        /*Styling in case no errors on form*/
        .form_success span {
            width: 80%;
            height: 35px;
            margin: 3px 10%;
            font-size: 1.1em;
            color: green;
        }

        .form_success input {
            border: 1px solid green;
        }
    </style>
@endpush
@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                {{--                <div class="col-12">--}}
                <div class="page-title-wrap mb-3">
                    <h2 class="page-title">{{translate('add_new_product')}}</h2>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-30">
                            <!-- Nav Tabs -->
                            <div class="mb-3">
                                <input type="hidden" name="lang_id" id="lang_id">
                                <ul class="nav nav--tabs nav--tabs__style2">
                                    <li class="nav-item">
                                        <label data-bs-toggle="tab" data-bs-target="#english"
                                               class="nav-link active" id="english_lang">
                                            {{$languages[0]->language_name}}
                                        </label>
                                    </li>
                                    <li class="nav-item">
                                        <label data-bs-toggle="tab" data-bs-target="#arabic"
                                               class="nav-link" id="arabic_lang">
                                            {{$languages[1]->language_name}}
                                        </label>
                                    </li>
                                </ul>
                            </div>
                            <hr>
                            <!-- End Nav Tabs -->

                            <form action="{{route('provider.product.store')}}" method="post"
                                  enctype="multipart/form-data"
                                  id="service-add-form">
                                @csrf
                                <input type="hidden" name="group_id" value="{{$product_grp_id}}">
                                <input type="hidden" name="eng_lang_id" id="eng_lang_id"
                                       value="{{$languages[0]->language_master_id}}">
                                <input type="hidden" name="arabic_lang_id" id="arabic_lang_id"
                                       value="{{$languages[1]->language_master_id}}">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="english">
                                        <div class="row">
                                            <div class="col-lg-12 mb-5 mb-lg-0">
                                                <div class="form-group mb-30">
                                                    <label for="name"><strong
                                                            class="text-black">{{translate('product_name')}} <span
                                                                class="text-danger">*</span></strong></label>
                                                    <input type="text" class="form-control" name="name" id="name"
                                                           placeholder="{{translate('product_name')}} *">
                                                </div>
                                            </div>
                                            <div class="col-6 mb-5 mb-lg-0">
                                                <div class="form-group mb-30">
                                                    <label for="description"><strong
                                                            class="text-black"> {{translate('description')}} <span
                                                                class="text-danger">*</span></strong></label>
                                                    <section id="editor">
                                                            <textarea class="ckeditor"
                                                                      name="description">{{old('description')}}</textarea>
                                                    </section>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-5 mb-lg-0">
                                                <div class="form-group mb-30">
                                                    <label for="category_id"><strong
                                                            class="text-black">{{translate('Category')}} <span
                                                                class="text-danger">*</span></strong></label>
                                                    <select class="form-control theme-input-style w-100"
                                                            name="category_id"
                                                            onchange="ajax_switch_category('{{url('/')}}/provider/product/ajax-childes-multiple/'+this.value)">
                                                        <option
                                                            value="">{{translate('choose_Category')}}</option>
                                                        @foreach($categories as $category)
                                                            <option
                                                                value="{{$category->id}}">{{$category->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <label><strong>{{translate('Sub_Category_Name')}}</strong></label>
                                                <div class="mb-30" id="sub-category-selector">
                                                    <select class="subcategory-select theme-input-style w-100"
                                                            name="sub_category_id[]" multiple="multiple">

                                                    </select>
                                                </div>

                                                <div class="form-group mb-30">
                                                    <label for="sku"><strong
                                                            class="text-black">{{translate('reference_code/sku')}}</strong><i
                                                            class="text-danger asterik">*</i></label>
                                                    <input type="text" class="form-control" name="sku"
                                                           id="sku">
                                                    <span></span>
                                                </div>
                                            </div>

                                            <!-- Nav Tabs -->
                                            <div class="mb-3 mt-5">
                                                <ul class="nav nav--tabs nav--tabs__style2">
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab" data-bs-target="#pricing"
                                                               class="nav-link active"
                                                               id="english_lang_pricing">{{translate('pricingvariants')}}
                                                        </label>
                                                    </li>
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab" data-bs-target="#attributes"
                                                               class="nav-link" id="english_lang_attributes">
                                                            {{translate('attribute')}}
                                                        </label>
                                                    </li>
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab" data-bs-target="#features"
                                                               class="nav-link" id="english_lang_features">
                                                            {{translate('features')}}
                                                        </label>
                                                    </li>
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab" data-bs-target="#specification"
                                                               class="nav-link" id="english_lang_specification">
                                                            {{translate('specification')}}
                                                        </label>
                                                    </li>
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab" data-bs-target="#media"
                                                               class="nav-link" id="english_lang_media">
                                                            {{translate('media')}}
                                                        </label>
                                                    </li>
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab" data-bs-target="#shipping"
                                                               class="nav-link" id="english_lang_shipping">
                                                            {{translate('shipping')}}
                                                        </label>
                                                    </li>
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab" data-bs-target="#seo"
                                                               class="nav-link" id="english_lang_seo">
                                                            {{translate('seo')}}
                                                        </label>
                                                    </li>
                                                </ul>
                                            </div>
                                            <hr style="width: 98%;">
                                            <!-- End Nav Tabs -->

                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="pricing">
                                                    <div class="row">
                                                        <div class="d-flex flex-wrap gap-20 mb-3">
                                                            <div id="packate_div">
                                                                <div class="row">
                                                                    <div class="mb-30 col-md-2">
                                                                        <label
                                                                            for="unit"><strong>{{translate('attribute')}}</strong></label>
                                                                        <div class="form-floating packate_div">
                                                                            <select
                                                                                class="js-select theme-input-style w-100"
                                                                                onchange="ajax_switch_attribute(this.value)"
                                                                                name="packate_measurement_attribute_id[]">
                                                                                <option value="0">Select Attribute
                                                                                </option>
                                                                                @foreach($attribute as $att)
                                                                                    <option
                                                                                        value="{{$att->id}}">{{$att->attribute_name}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-30 col-md-2" id="attribute_valueID">
                                                                        <label
                                                                            for="unit"><strong>{{translate('attribute_value')}}</strong></label>
                                                                        <div class="form-floating packate_div">
                                                                            <select
                                                                                class="js-select theme-input-style w-100"
                                                                                name="packate_measurement_attribute_value[]">
                                                                                {{--                                                                                <option value="0">Atribute Value</option>--}}
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group mb-30 col-md-2">
                                                                        <label
                                                                            for="packate_measurement_cost_price"><strong
                                                                                class="text-black">{{translate('cost_price')}}</strong></label>
                                                                        <div class="packate_div">
                                                                            <input type="text" class="form-control"
                                                                                   name="packate_measurement_cost_price[]" id="cost_price_id"
                                                                                   onkeypress="return validateFloatKeyPress(this,event);"
                                                                                   placeholder="{{translate('cost_price')}} *"
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group mb-30 col-md-2">
                                                                        <label
                                                                            for="packate_measurement_sell_price"><strong
                                                                                class="text-black">{{translate('selling_price')}}</strong></label>
                                                                        <div class="packate_div">
                                                                            <input type="text" class="form-control"
                                                                                   name="packate_measurement_sell_price[]" id="sell_price_id"
                                                                                   onkeypress="return validateFloatKeyPress(this,event);"
                                                                                   placeholder="{{translate('selling_price')}} *"
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group mb-30 col-md-2 d-none">
                                                                        <label
                                                                            for="packate_measurement_discount_price"><strong
                                                                                class="text-black">{{translate('discount_price')}}</strong></label>
                                                                        <div class="packate_div">
                                                                            <input type="text" class="form-control"
                                                                                   name="packate_measurement_discount_price[]" id="discount_price_id"
                                                                                   onkeypress="return validateFloatKeyPress(this,event);"
                                                                                   placeholder="{{translate('discount_price')}} *"
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group mb-30 col-md-2">
                                                                        <label
                                                                            for="packate_measurement_qty"><strong
                                                                                class="text-black">{{translate('stocks/Qty')}}</strong></label>
                                                                        <div class="packate_div">
                                                                            <input type="text" class="form-control"
                                                                                   name="packate_measurement_qty[]" id="qty_id"
                                                                                   onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"
                                                                                   placeholder="{{translate('stocks/Qty')}} *">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="mb-30 col-md-2">
                                                                        <label
                                                                            for="unit"><strong>{{translate('shelf_life_unit')}}</strong></label>
                                                                        <div class="form-floating packate_div">
                                                                            <select
                                                                                class="js-select theme-input-style w-100"
                                                                                name="packate_measurement_shelf_life_unit[]">
                                                                                <option value="0">Select Shelf Life Unit
                                                                                </option>
                                                                                <option value="month">Month</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-30 col-md-2">
                                                                        <label
                                                                            for="packate_measurement_shelf_life_val"><strong>{{translate('shelf_life_value')}}</strong>
                                                                        </label>
                                                                        <div class="form-floating packate_div">
                                                                            <input type="text" class="form-control"
                                                                                   name="packate_measurement_shelf_life_val[]" id="shelf_life_id"
                                                                                   placeholder="{{translate('product_name')}} *">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-30 col-md-2">
                                                                        <label
                                                                            for="barcode"><strong>{{translate('barcode')}}</strong></label>
                                                                        <div class="form-floating packate_div">
                                                                            <input type="text" class="form-control"
                                                                                   name="packate_measurement_barcode[]" id="barcode_id"
                                                                                   placeholder="{{translate('product_name')}} *">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-30 col-md-2">
                                                                        <label
                                                                            for="packate_measurement_fssai_number"><strong>{{translate('fssai_number')}}</strong></label>
                                                                        <div class="form-floating packate_div">
                                                                            <input type="text" class="form-control"
                                                                                   name="packate_measurement_fssai_number[]" id="fssai_number_id"
                                                                                   placeholder="{{translate('product_name')}} *">
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group mb-30 col-md-3">
                                                                        <label
                                                                            for="packate_measurement_images"><strong
                                                                                class="text-black">{{translate('product_img')}}</strong></label>
                                                                        <div class="packate_div">
                                                                            <input type="file" class="form-control"
                                                                                   name="packate_measurement_images[]"
                                                                                   placeholder="{{translate('product_name')}} *"
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1 text-center">
                                                                        <div class="form-group mt-4">
                                                                            <a id="add_packate_variation"
                                                                               title="Add variation of product"
                                                                               class="btn btn-success btn-xs text-white"
                                                                               style="cursor: pointer;"><span
                                                                                    class="material-icons">add</span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="variations"></div>
                                                        </div>

                                                        <hr>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-content">
                                                <div class="tab-pane fade" id="attributes">
                                                    <div class="row">
                                                        <div class="col-lg-4 mb-30">
                                                            <label for=""><strong>{{translate('product_type')}}</strong></label>
                                                            <div class="form-floating">
                                                                <select name="indicator" id="indicator"
                                                                        class="js-select theme-input-style w-100"
                                                                        required>
                                                                    <option value="1" selected>Single</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4 mb-30">
                                                            <label for=""><strong>{{translate('product_tags')}}</strong></label>
                                                            <div class="form-floating">
                                                                <div class="select2-purple">
                                                                    <input type="text" data-role="tagsinput"
                                                                           name="tags[]"
                                                                           class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('made_in')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" name="made_in" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        {{--                                                        <div class="col-lg-4 mb-30">--}}
                                                        {{--                                                            <label--}}
                                                        {{--                                                                for='brand_ids'><strong>{{translate('sold_by')}}</strong><i--}}
                                                        {{--                                                                    class="text-danger asterik">*</i></label>--}}
                                                        {{--                                                            <div class="form-group">--}}
                                                        {{--                                                                <select name='vendor' id='vendor'--}}
                                                        {{--                                                                        class='js-select theme-input-style w-100'--}}
                                                        {{--                                                                        required>--}}
                                                        {{--                                                                    <option value="0">--Select Vendor--</option>--}}
                                                        {{--                                                                    <option value="1">Vendor 1</option>--}}
                                                        {{--                                                                </select>--}}
                                                        {{--                                                            </div>--}}
                                                        {{--                                                        </div>--}}

                                                        <div class="col-lg-4 mb-30">
                                                            <label for=""><strong>{{translate('manufacturer')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" name="manufacturer"
                                                                       class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('manufacturer_part_no')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" name="manufacturer_part_no"
                                                                       id="manufacturer_part_no"
                                                                       class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 mb-30">
                                                            <label
                                                                for='brand_ids'><strong>{{translate('brand')}}</strong></label>
                                                            <div class="form-floating">
                                                                <select name='brand_ids' id='brand_ids'
                                                                        class='js-select theme-input-style w-100'>
                                                                    <option value="">--Select Brand--</option>
                                                                    <option value="1">
                                                                        Brand1
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-3 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('weight')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" id="weight"
                                                                       class="form-control integer"
                                                                       name="weight"
                                                                       step="1" placeholder="0 Kg">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('length')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" id="length"
                                                                       class="form-control integer" name="length"
                                                                       step="1" placeholder="0 cm">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('width')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" id="width"
                                                                       class="form-control integer"
                                                                       name="width"
                                                                       step="1" placeholder="0 cm">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('height')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" id="height"
                                                                       class="form-control integer"
                                                                       name="height"
                                                                       step="1" placeholder="0 cm">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-lg-3 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('is_return')}}</strong></label><br>
                                                            <div class="form-floating">
                                                                <input type="checkbox" id="return_status_button"
                                                                       class="js-switch">
                                                                <input type="hidden" id="return_status"
                                                                       name="return_status">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('is_promo')}}</strong></label><br>
                                                            <div class="form-floating">
                                                                <input type="checkbox" id="promo_status_button"
                                                                       class="js-switch">
                                                                <input type="hidden" id="promo_status"
                                                                       name="promo_status">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('is_cancel')}}</strong></label><br>
                                                            <div class="form-floating">
                                                                <input type="checkbox" id="cancelable_button"
                                                                       class="js-switch">
                                                                <input type="hidden" id="cancelable_status"
                                                                       name="cancelable_status">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 mb-30" id="till-status"
                                                             style="display:none">
                                                            <label for=""><strong>{{translate('till_status')}}</strong></label>
                                                            <i
                                                                class="text-danger asterik">*</i>
                                                            <br>
                                                            <div class="form-floating">
                                                                <select id="till_status" name="till_status"
                                                                        class="js-select theme-input-style w-100">
                                                                    <option value="">Select</option>
                                                                    <option value="received">Received</option>
                                                                    <option value="processed">Processed</option>
                                                                    <option value="shipped">Shipped</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row d-none">
                                                        <div class="col-lg-6 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('product_buy_status')}}</strong></label>
                                                            <div class="icheck-primary d-inline">
                                                                <input type="radio" id="radioPrimary1"
                                                                       name="bstatus" value="1" checked>
                                                                <label for="radioPrimary1">Add to cart
                                                                </label>
                                                            </div>
                                                            <div class="icheck-primary d-inline">
                                                                <input type="radio" id="radioPrimary2"
                                                                       name="bstatus" value="2">
                                                                <label for="radioPrimary2">For Inquiry
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-content">
                                                <div class="tab-pane fade" id="features">
                                                    <div class="row" id="pcontent">
                                                        <div class="col-lg-8">
                                                            <div class="form-floating" id="policy">
                                                                <label
                                                                    class="text-dark"><strong>{{translate('product_features')}}</strong></label>
                                                                <div class="controls">
                                                                    <div class="input-group">
                                                                        <input type="text"
                                                                               class="form-control sKeyFeatures"
                                                                               placeholder="Enter Feature"
                                                                               name="sKeyFeatures"
                                                                               id="exampleInputuname2">
                                                                        <button type="button"
                                                                                class="btn btn--primary input-group-addon"
                                                                                id="btn_feature" value="add"
                                                                                data-type="add"
                                                                                data-id='[]'>
                                                                            <span class="material-icons">add</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="view_table" class="d-none">
                                                                <div class="table-responsive tbl_cat br-5">
                                                                    <table
                                                                        class="table color-table table-bordered card-1">
                                                                        <thead class="bg-thead">
                                                                        <tr class="bg--primary">
                                                                            <th width="10%">#</th>
                                                                            <th width="80%">Feature</th>
                                                                            <th width="10%">Action</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody id="tablebody">
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-content">
                                                <div class="tab-pane fade" id="specification">
                                                    <div class="row" id="pspecific">
                                                        <div class="col-md-12">
                                                            <div class="form-group" id="policy">
                                                                <div class="controls">
                                                                    <div class="input-group">
                                                                        <div class="col-lg-4"
                                                                             style="padding-right: 10px;">
                                                                            <div class="form-group">
                                                                                <input type="text"
                                                                                       class="form-control specification_type"
                                                                                       placeholder="Enter Type"
                                                                                       name="specification_type"
                                                                                       id="exampleInputuname2">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <input type="text"
                                                                                       class="form-control specification_name"
                                                                                       placeholder="Enter Name"
                                                                                       name="specification_name"
                                                                                       id="exampleInputuname2">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-1">
                                                                            <div class="form-group">
                                                                                <button type="button"
                                                                                        class="btn btn--primary input-group-addon"
                                                                                        id="btn_specific" value="add"
                                                                                        data-type="add"
                                                                                        data-id='[]'>
                                                                                    <span
                                                                                        class="material-icons">add</span>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="view_specific_table" class="d-none"
                                                            >
                                                                <div class="table-responsive tbl_cat br-5">
                                                                    <table
                                                                        class="table color-table table-bordered card-1">
                                                                        <thead class="bg-thead">
                                                                        <tr class="bg--primary">
                                                                            <th width="10%">#</th>
                                                                            <th width="30%">Type</th>
                                                                            <th width="50%">Name</th>
                                                                            <th width="10%">Action</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody id="specifictablebody">
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-content">
                                                <div class="tab-pane fade" id="media">
                                                    <div class="row">
                                                        <div class="col-lg-6 mb-5 mb-lg-0">
                                                            <div class="mb-30">
                                                                <label for="image"
                                                                       class="mb-3"><strong>{{translate('main_image')}}</strong><i
                                                                        class="text-danger asterik">*</i></label>
                                                                <div class="form-floating">
                                                                    <div class="field">
                                                                        <input type="file" name="image" id="image"
                                                                               class="form-control file_upload"
                                                                               accept=".png,.jpeg,.jpg" required>
                                                                    </div>
                                                                    <div class="view_img w-25"></div>
                                                                </div>
                                                            </div>
                                                            <div class="mb-30">
                                                                <label
                                                                    class=""><strong>{{translate('product_video_url')}}</strong></label>
                                                                <input type="text" name="videoURL" id="videoURL"
                                                                       class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 mb-5 mb-lg-0">
                                                            <div class="mb-30">
                                                                <label
                                                                    class="mb-3"><strong>{{translate('add_multi_img')}}</strong></label>
                                                                <div class="form-floating">
                                                                    <div class="field">
                                                                        <input type="file" id="fileupload"
                                                                               name="other_images[]"
                                                                               class="form-control"
                                                                               accept=".png,.jpeg,.jpg" multiple/>
                                                                    </div>
                                                                </div>
                                                                <div id="dvPreview"></div>
                                                            </div>
                                                            <div class="mb-30">
                                                                <label
                                                                    for="image"><strong>{{translate('product_brochure')}}</strong></label>
                                                                <div class="form-floating">
                                                                    <input type="file" name="brochure" id="brochure"
                                                                           class="form-control"
                                                                           accept=".pdf">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-content">
                                                <div class="tab-pane fade" id="shipping">

                                                    <style>
                                                        .dataTables_wrapper .dataTables_filter {
                                                            display: block !important;
                                                        }
                                                    </style>
                                                    <script language="JavaScript"
                                                            src="https://code.jquery.com/jquery-1.11.1.min.js"
                                                            type="text/javascript"></script>
                                                    <script>
                                                        $(document).ready(function () {
                                                            // $('#datatable').dataTable();
                                                            $('.display').dataTable();
                                                            $("[data-toggle=tooltip]").tooltip();
                                                        });

                                                    </script>

                                                    <div class="row">
                                                        @csrf
                                                        <div class="col-lg-12 mb-5 mb-lg-0">
                                                            <div class="table-responsive">
                                                                <table id=""
                                                                       class="display table table-striped table-bordered"
                                                                       cellspacing="0" width="100%">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>{{translate('zone_area_name')}}</th>
                                                                        <th>{{translate('delivery_charge')}}</th>
                                                                    </tr>
                                                                    </thead>

                                                                    <tbody>
                                                                    @php $i=1; @endphp
                                                                    <input type="text" class="d-none" name="total_zone"
                                                                           value="{{!empty($zones) ? count($zones) : 0}}">
                                                                    @foreach($zones as $key=>$zone)
                                                                        <tr>
                                                                            <td>{{$zone->name}}</td>
                                                                            <td><input type="text" class="form-control"
                                                                                       value="0"
                                                                                       placeholder="Enter Delivery charge"
                                                                                       name="delivery_charge[{{$i}}]">
                                                                                <input
                                                                                    type="hidden"
                                                                                    class="form-control"
                                                                                    name="zone_id[{{$i}}]"
                                                                                    value="{{$zone->id}}"
                                                                                    placeholder="Enter Delivery charge">
                                                                            </td>

                                                                        </tr>

                                                                        @php $i++; @endphp
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-content">
                                                <div class="tab-pane fade" id="seo">
                                                    <div class="row">
                                                        <div class="col-lg-12 mb-5 mb-lg-0">
                                                            <div class="mb-30">
                                                                <label
                                                                    class=""><strong>{{translate('search_engine_friendly_page_name')}}</strong></label>
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control"
                                                                           name="seoPageNm"
                                                                           placeholder="{{translate('search_engine_friendly_page_name')}}">
                                                                </div>
                                                            </div>
                                                            <div class="mb-30">
                                                                <label
                                                                    class=""><strong>{{translate('meta_title')}}</strong></label>
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control"
                                                                           name="sMetaTitle"
                                                                           placeholder="{{translate('meta_title')}}">
                                                                </div>
                                                            </div>
                                                            <div class="mb-30">
                                                                <label
                                                                    class=""><strong>{{translate('meta_keywords')}}</strong></label>
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control"
                                                                           name="sMetaKeywords"
                                                                           placeholder="{{translate('meta_keywords')}}">
                                                                </div>
                                                            </div>
                                                            <div class="mb-30">
                                                                <label
                                                                    class=""><strong>{{translate('meta_description')}}</strong></label>
                                                                <div class="form-floating">
                                                                    <textarea type="text" class="form-control"
                                                                              name="sMetaDescription" rows="4"
                                                                              placeholder="{{translate('meta_description')}}"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Category - arabic fields-->
                                <div class="tab-content">
                                    <div class="tab-pane fade" id="arabic">
                                        <div class="row">
                                            <div class="col-lg-12 mb-5 mb-lg-0">
                                                <div class="form-group mb-30">
                                                    <label for="arabic_name"><strong
                                                            class="text-black">{{translate('arabic_product_name')}}
                                                            <span
                                                                class="text-danger">*</span></strong></label>
                                                    <input type="text" class="form-control"
                                                           name="arabic_name" id="arabic_name"
                                                           placeholder="{{translate('arabic_product_name')}} *">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mb-30">
                                                    <label
                                                        for="arabic_description"><strong class="text-black">{{translate('description_arabic')}}
                                                            <span
                                                                class="text-danger">*</span></strong></label>
                                                    <section id="editor">
                                                            <textarea class="ckeditor" id="arabic_description"
                                                                      name="arabic_description">{{old('description')}}</textarea>
                                                    </section>
                                                </div>
                                            </div>

                                            <div class="col-lg-6 mb-5 mb-lg-0">
                                                <div class="form-group mb-30">
                                                    <label for="arabic_category_id"><strong class="text-black">{{translate('arabic_category')}} <span
                                                                class="text-danger">*</span></strong></label>
                                                        <select class="form-control theme-input-style w-100"
                                                                name="arabic_category_id" id="arabic_category_id"
                                                                onchange="ajax_switch_category2('{{url('/')}}/provider/product/ajax-childes-arabic-multiple/'+this.value)">
                                                            <option
                                                                value="">{{translate('choose_category_arabic')}}</option>
                                                            @foreach($arabic_categories as $abcategory)
                                                                <option
                                                                    value="{{$abcategory->id}}">{{$abcategory->name}}</option>
                                                            @endforeach
                                                        </select>
                                                </div>
                                                <div class="mb-30" id="sub-category-selector-2">
                                                    <label><strong>{{translate('sub_category_name_arabic')}}</strong></label>
                                                    <select class="subcategory-select-arabic theme-input-style w-100"
                                                            name="arabic_sub_category_id[]" multiple="multiple">
                                                    </select>
                                                </div>

                                                <div class="form-group mb-30">
                                                    <label
                                                        for="sku_arabic"><strong class="text-black">{{translate('reference_code/sku_arabic')}}</strong><i
                                                            class="text-danger asterik">*</i></label>
                                                        <input type="text" class="form-control" name="sku_arabic"
                                                               id="sku_arabic">
                                                        <span></span>
                                                </div>
                                            </div>

                                            <!-- Nav Tabs -->
                                            <div class="mb-3 mt-5">
                                                <ul class="nav nav--tabs nav--tabs__style2">
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab" data-bs-target="#arabic_pricing"
                                                               class="nav-link active"
                                                               id="arabic_lang_pricing">{{translate('arabic_pricingvariants')}}
                                                        </label>
                                                    </li>
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab" data-bs-target="#arabic_attributes"
                                                               class="nav-link" id="arabic_lang_attributes">
                                                            {{translate('arabic_attribute')}}
                                                        </label>
                                                    </li>
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab" data-bs-target="#arabic_features"
                                                               class="nav-link" id="arabic_lang_features">
                                                            {{translate('arabic_features')}}
                                                        </label>
                                                    </li>
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab"
                                                               data-bs-target="#arabic_specification"
                                                               class="nav-link" id="arabic_lang_specification">
                                                            {{translate('arabic_specification')}}
                                                        </label>
                                                    </li>
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab" data-bs-target="#arabic_media"
                                                               class="nav-link" id="arabic_lang_media">
                                                            {{translate('arabic_media')}}
                                                        </label>
                                                    </li>
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab" data-bs-target="#arabic_shipping"
                                                               class="nav-link" id="arabic_lang_shipping">
                                                            {{translate('arabic_shipping')}}
                                                        </label>
                                                    </li>
                                                    <li class="nav-item">
                                                        <label data-bs-toggle="tab" data-bs-target="#arabic_seo"
                                                               class="nav-link" id="arabic_lang_seo">
                                                            {{translate('سيو')}}
                                                        </label>
                                                    </li>
                                                </ul>
                                            </div>
                                            <hr style="width: 98%;">
                                            <!-- End Nav Tabs -->

                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="arabic_pricing">
                                                    <div class="row">
                                                        <div class="d-flex flex-wrap gap-20 mb-3">
                                                            <div id="arabic_packate_div">
                                                                <div class="row">
                                                                    <div class="mb-30 col-md-2">
                                                                        <label
                                                                            for="unit"><strong>{{translate('arabic_attribute')}}</strong></label>
                                                                        <div class="form-floating arabic_packate_div">
                                                                            <select
                                                                                class="js-select theme-input-style w-100"
                                                                                name="arabic_packate_measurement_attribute_id[]"
                                                                                onchange="arabic_ajax_switch_attribute(this.value)">
                                                                                {{--                                                                                arabic_ajax_switch_attribute('{{url('/')}}/admin/product/arabic-ajax-switch-attribute/'+this.value)--}}
                                                                                @foreach($arabic_attribute as $aratt)
                                                                                    <option
                                                                                        value="{{$aratt->id}}">{{$aratt->attribute_name}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-30 col-md-2"
                                                                         id="arabic_attribute_valueID">
                                                                        <label
                                                                            for="unit"><strong>{{translate('arabic_attribute_value')}}</strong></label>
                                                                        <div class="form-floating arabic_packate_div">
                                                                            <select
                                                                                class="js-select theme-input-style w-100"
                                                                                name="arabic_packate_measurement_attribute_value[]">
                                                                                {{--                                                                                <option value="">{{translate('arabic_attribute_value')}}</option>--}}
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group mb-30 col-md-2">
                                                                        <label
                                                                            for="arabic_packate_measurement_cost_price"><strong class="text-black">{{translate('arabic_cost_price')}}</strong></label>
                                                                        <div class="arabic_packate_div">
                                                                            <input type="text" class="form-control"
                                                                                   name="arabic_packate_measurement_cost_price[]" id="arabic_cost_price_id"
                                                                                   onkeypress="return validateFloatKeyPress(this,event);"
                                                                                   placeholder="{{translate('arabic_cost_price')}} *"
                                                                                   required="">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group mb-30 col-md-2">
                                                                        <label
                                                                            for="arabic_packate_measurement_sell_price"><strong class="text-black">{{translate('arabic_selling_price')}}</strong></label>
                                                                        <div class="arabic_packate_div">
                                                                            <input type="text" class="form-control"
                                                                                   name="arabic_packate_measurement_sell_price[]" id="arabic_sell_price_id"
                                                                                   onkeypress="return validateFloatKeyPress(this,event);"
                                                                                   placeholder="{{translate('arabic_selling_price')}} *"
                                                                                   >
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group mb-30 col-md-2 d-none">
                                                                        <label
                                                                            for="arabic_packate_measurement_discount_price"><strong class="text-black">{{translate('arabic_discount_price')}}</strong></label>
                                                                        <div class="arabic_packate_div">
                                                                            <input type="text" class="form-control"
                                                                                   name="arabic_packate_measurement_discount_price[]" id="arabic_discount_price_id"
                                                                                   onkeypress="return validateFloatKeyPress(this,event);"
                                                                                   placeholder="{{translate('arabic_discount_price')}} *"
                                                                                   >
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group mb-30 col-md-2">
                                                                        <label
                                                                            for="arabic_packate_measurement_qty"><strong class="text-black">{{translate('arabic_stocks/qty')}}</strong></label>
                                                                        <div class="arabic_packate_div">
                                                                            <input type="text" class="form-control"
                                                                                   name="arabic_packate_measurement_qty[]" id="arabic_qty_id"
                                                                                   onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"
                                                                                   placeholder="">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="mb-30 col-md-2">
                                                                        <label
                                                                            for="unit"><strong>{{translate('arabic_shelf_life_unit')}}</strong></label>
                                                                        <div class="form-floating packate_div">
                                                                            <select
                                                                                class="js-select theme-input-style w-100"
                                                                                name="arabic_packate_measurement_shelf_life_unit[]">
                                                                                <option
                                                                                    value="0">{{translate('arabic_select_shelf_life_unit')}}</option>
                                                                                <option value="month">Month</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-30 col-md-2">
                                                                        <label
                                                                            for="packate_measurement_shelf_life_val"><strong>{{translate('arabic_shelf_life_value')}}</strong></label>
                                                                        <div class="form-floating arabic_packate_div">
                                                                            <input type="text" class="form-control"
                                                                                   name="arabic_packate_measurement_shelf_life_val[]" id="arabic_shelf_life_id"
                                                                                   placeholder="{{translate('arabic_shelf_life_value')}} *">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-30 col-md-2">
                                                                        <label
                                                                            for="barcode"><strong>{{translate('arabic_barcode')}}</strong></label>
                                                                        <div class="form-floating arabic_packate_div">
                                                                            <input type="text" class="form-control"
                                                                                   name="arabic_packate_measurement_barcode[]" id="arabic_barcode_id"
                                                                                   placeholder="{{translate('arabic_barcode')}} *">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-30 col-md-2">
                                                                        <label
                                                                            for="packate_measurement_fssai_number"><strong>{{translate('arabic_fssai_number')}}</strong></label>
                                                                        <div class="form-floating arabic_packate_div">
                                                                            <input type="text" class="form-control"
                                                                                   name="arabic_packate_measurement_fssai_number[]" id="arabic_fssai_number_id"
                                                                                   placeholder="{{translate('arabic_fssai_number')}} *">
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group mb-30 col-md-3">
                                                                        <label
                                                                            for="arabic_packate_measurement_images"><strong class="text-black">{{translate('arabic_images')}}</strong></label>
                                                                        <div class="arabic_packate_div">
                                                                            <input type="file" class="form-control"
                                                                                   name="arabic_packate_measurement_images[]"
                                                                                   placeholder="{{translate('arabic_images')}} *">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1 text-center">
                                                                        <div class="form-group mt-4">
                                                                            <a id="add_arabic_packate_variation"
                                                                               title="Add variation of product"
                                                                               class="btn btn-success btn-xs text-white"
                                                                               style="cursor: pointer;"><span
                                                                                    class="material-icons">add</span>
                                                                            </a>
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

                                            <div class="tab-content">
                                                <div class="tab-pane fade" id="arabic_attributes">
                                                    <div class="row">
                                                        <div class="col-lg-4 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('product_type_arabic')}}</strong></label>
                                                            <div class="form-floating">
                                                                <select name="indicator_arabic" id="indicator"
                                                                        class="js-select theme-input-style w-100"
                                                                        required>
                                                                    <option value="1" selected>Single</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('product_tags_arabic')}}</strong></label>
                                                            <div class="form-floating">
                                                                <div class="select2-purple">
                                                                    <input type="text" data-role="tagsinput"
                                                                           name="tags_arabic[]"
                                                                           class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('made_in_arabic')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" name="made_in_arabic"
                                                                       class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        {{--                                                        <div class="col-lg-4 mb-30">--}}
                                                        {{--                                                            <label><strong>{{translate('sold_by_arabic')}}</strong><i--}}
                                                        {{--                                                                    class="text-danger asterik">*</i></label>--}}
                                                        {{--                                                            <div class="form-group">--}}
                                                        {{--                                                                <select name='vendor_arabic' id='vendor'--}}
                                                        {{--                                                                        class='js-select theme-input-style w-100'>--}}
                                                        {{--                                                                    <option--}}
                                                        {{--                                                                        value="">{{translate('--Select Vendor--')}}</option>--}}
                                                        {{--                                                                    <option value="1">Vendor 1</option>--}}
                                                        {{--                                                                </select>--}}
                                                        {{--                                                            </div>--}}
                                                        {{--                                                        </div>--}}

                                                        <div class="col-lg-4 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('manufacturer_arabic')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" name="manufacturer_arabic"
                                                                       class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('manufacturer_part_no_arabic')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" name="manufacturer_part_no_arabic"
                                                                       id="manufacturer_part_no_arabic"
                                                                       class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 mb-30">
                                                            <label
                                                                for='brand_ids'><strong>{{translate('brand_arabic')}}</strong></label>
                                                            <div class="form-floating">
                                                                <select name='brand_ids_arabic' id='brand_ids'
                                                                        class='js-select theme-input-style w-100'>
                                                                    <option
                                                                        value="">{{translate('--Select Brand--_arabic')}}</option>
                                                                    <option value="1">
                                                                        Brand1
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-3 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('weight_arabic')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" id="weight_arabic"
                                                                       class="form-control integer"
                                                                       name="weight_arabic"
                                                                       step="1" placeholder="0 Kg">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('length_arabic')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" id="length_arabic"
                                                                       class="form-control integer" name="length_arabic"
                                                                       step="1" placeholder="0 cm">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 mb-30">
                                                            <label for=""><strong>{{translate('width_arabic')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" id="width_arabic"
                                                                       class="form-control integer"
                                                                       name="width_arabic"
                                                                       step="1" placeholder="0 cm">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('height_arabic')}}</strong></label>
                                                            <div class="form-floating">
                                                                <input type="text" id="height_arabic"
                                                                       class="form-control integer"
                                                                       name="height_arabic"
                                                                       step="1" placeholder="0 cm">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-lg-3 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('is_return_arabic')}}</strong></label><br>
                                                            <div class="form-floating">
                                                                <input type="checkbox" id="return_status_button_arabic"
                                                                       class="js-switch">
                                                                <input type="hidden" id="return_status_arabic"
                                                                       name="return_status_arabic">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('is_promo_arabic')}}</strong></label><br>
                                                            <div class="form-floating">
                                                                <input type="checkbox" id="promo_status_button_arabic"
                                                                       class="js-switch">
                                                                <input type="hidden" id="promo_status_arabic"
                                                                       name="promo_status_arabic">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('is_cancel_arabic')}}</strong></label><br>
                                                            <div class="form-floating">
                                                                <input type="checkbox" id="cancelable_button_arabic"
                                                                       class="js-switch">
                                                                <input type="hidden" id="cancelable_status_arabic"
                                                                       name="cancelable_status_arabic">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 mb-30" id="till-status_arabic"
                                                             style="display:none">
                                                            <label
                                                                for=""><strong>{{translate('till_status_arabic')}}</strong></label>
                                                            <i
                                                                class="text-danger asterik">*</i>
                                                            <br>
                                                            <div class="form-floating">
                                                                <select id="till_status_arabic"
                                                                        name="till_status_arabic"
                                                                        class="js-select theme-input-style w-100">
                                                                    <option
                                                                        value="">{{translate('Select_arabic')}}</option>
                                                                    <option value="received">Received</option>
                                                                    <option value="processed">Processed</option>
                                                                    <option value="shipped">Shipped</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row d-none">
                                                        <div class="col-lg-6 mb-30">
                                                            <label
                                                                for=""><strong>{{translate('product_buy_status_arabic')}}</strong></label>
                                                            <div class="icheck-primary d-inline">
                                                                <input type="radio" id="radioPrimary1"
                                                                       name="bstatus_arabic" value="1" checked>
                                                                <label
                                                                    for="radioPrimary1">{{translate('add_to_cart_arabic')}}
                                                                </label>
                                                            </div>
                                                            <div class="icheck-primary d-inline">
                                                                <input type="radio" id="radioPrimary2"
                                                                       name="bstatus_arabic" value="2">
                                                                <label
                                                                    for="radioPrimary2">{{translate('for_inquiry_arabic')}}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-content">
                                                <div class="tab-pane fade" id="arabic_features">
                                                    <div class="row" id="pcontent_arabic">
                                                        <div class="col-lg-8">
                                                            <div class="form-floating" id="policy">
                                                                <label
                                                                    class="text-dark"><strong>{{translate('product_features_arabic')}}</strong></label>
                                                                <div class="controls">
                                                                    <div class="input-group">
                                                                        <input type="text"
                                                                               class="form-control sKeyFeatures_arabic"
                                                                               placeholder="{{translate('enter_feature')}}"
                                                                               name="sKeyFeatures_arabic"
                                                                               id="exampleInputuname2">
                                                                        <button type="button"
                                                                                class="btn btn--primary input-group-addon"
                                                                                id="btn_feature_arabic" value="add"
                                                                                data-type="add"
                                                                                data-id='[]'>
                                                                            <span class="material-icons">add</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="view_table_arabic" class="d-none">
                                                                <div class="table-responsive tbl_cat br-5">
                                                                    <table
                                                                        class="table color-table table-bordered card-1">
                                                                        <thead class="bg-thead">
                                                                        <tr class="bg--primary">
                                                                            <th width="10%">#</th>
                                                                            <th width="80%">Feature</th>
                                                                            <th width="10%">Action</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody id="tablebody_arabic">
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-content">
                                                <div class="tab-pane fade" id="arabic_specification">
                                                    <div class="row" id="pspecific_arabic">
                                                        <div class="col-md-12">
                                                            <div class="form-group" id="policy">
                                                                <div class="controls">
                                                                    <div class="input-group">
                                                                        <div class="col-lg-4"
                                                                             style="padding-right: 10px;">
                                                                            <div class="form-group">
                                                                                <input type="text"
                                                                                       class="form-control specification_type_arabic"
                                                                                       placeholder="{{translate('enter_type')}}"
                                                                                       name="specification_type_arabic"
                                                                                       id="exampleInputuname2">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <input type="text"
                                                                                       class="form-control specification_name_arabic"
                                                                                       placeholder="{{translate('enter_name')}}"
                                                                                       name="specification_name_arabic"
                                                                                       id="exampleInputuname2">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-1">
                                                                            <div class="form-group">
                                                                                <button type="button"
                                                                                        class="btn btn--primary input-group-addon"
                                                                                        id="btn_specific_arabic"
                                                                                        value="add"
                                                                                        data-type="add"
                                                                                        data-id='[]'>
                                                                                    <span
                                                                                        class="material-icons">add</span>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="view_specific_table_arabic" class="d-none">
                                                                <div class="table-responsive tbl_cat br-5">
                                                                    <table
                                                                        class="table color-table table-bordered card-1">
                                                                        <thead class="bg-thead">
                                                                        <tr class="bg--primary">
                                                                            <th width="10%">#</th>
                                                                            <th width="30%">Type</th>
                                                                            <th width="50%">Name</th>
                                                                            <th width="10%">Action</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody id="specifictablebody_arabic">
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-content">
                                                <div class="tab-pane fade" id="arabic_media">
                                                    <div class="row">
                                                        <div class="col-lg-6 mb-5 mb-lg-0">
                                                            <div class="form-group mb-30">
                                                                <label for="image"
                                                                       class="mb-3"><strong class="text-black">{{translate('main_image')}}</strong><i
                                                                        class="text-danger asterik">*</i></label>
                                                                    <div class="field">
                                                                        <input type="file" name="arabic_image"
                                                                               id="arabic_image"
                                                                               class="form-control file_upload_arabic"
                                                                               accept=".png,.jpeg,.jpg" required>
                                                                    </div>
                                                                    <div class="view_img_arabic w-25"></div>
                                                            </div>
                                                            <div class="mb-30">
                                                                <label
                                                                    class=""><strong>{{translate('product_video_url')}}</strong></label>
                                                                <input type="text" name="videoURL_arabic" id="videoURL"
                                                                       class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 mb-5 mb-lg-0">
                                                            <div class="mb-30">
                                                                <label
                                                                    class="mb-3"><strong>{{translate('add_multi_img')}}</strong></label>
                                                                <div class="form-floating">
                                                                    <div class="field">
                                                                        <input type="file" id="fileupload_arabic"
                                                                               name="other_images_arabic[]"
                                                                               class="form-control"
                                                                               accept=".png,.jpeg,.jpg" multiple/>
                                                                    </div>
                                                                </div>
                                                                <div id="dvPreview_arabic"></div>
                                                            </div>
                                                            <div class="mb-30">
                                                                <label
                                                                    for="image"><strong>{{translate('product_brochure')}}</strong></label>
                                                                <div class="form-floating">
                                                                    <input type="file" name="brochure_arabic"
                                                                           id="brochure"
                                                                           class="form-control"
                                                                           accept=".pdf">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-content">
                                                <div class="tab-pane fade" id="arabic_shipping">
                                                    <div class="row">
                                                        <div class="col-lg-12 mb-5 mb-lg-0">
                                                            <div class="table-responsive">
                                                                <table id=""
                                                                       class="display table table-striped table-bordered"
                                                                       cellspacing="0" width="100%">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>{{translate('zone_area_name')}}</th>
                                                                        <th>{{translate('delivery_charge')}}</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @php $i=1; @endphp
                                                                    <input type="text" class="d-none" name="arabic_total_zone"
                                                                           value="{{!empty($arabic_zones) ? count($arabic_zones) : 0}}">
                                                                    @foreach($arabic_zones as $key=>$arabiczone)
                                                                        <tr>
                                                                            <td>{{$arabiczone->name}}</td>
                                                                            <td><input type="text" class="form-control"
                                                                                       value="0"
                                                                                       placeholder="Enter Delivery charge"
                                                                                       name="arabic_delivery_charge[{{$i}}]"><input
                                                                                    type="hidden"
                                                                                    class="form-control"
                                                                                    name="arabic_zone_id[{{$i}}]"
                                                                                    value="{{$arabiczone->id}}"
                                                                                    placeholder="Enter Delivery charge">
                                                                            </td>
                                                                        </tr>
                                                                        @php $i++; @endphp
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-content">
                                                <div class="tab-pane fade" id="arabic_seo">
                                                    <div class="row">
                                                        <div class="col-lg-12 mb-5 mb-lg-0">
                                                            <div class="mb-30">
                                                                <label
                                                                    class=""><strong>{{translate('search_engine_friendly_page_name_arabic')}}</strong></label>
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control"
                                                                           name="arabic_seoPageNm"
                                                                           placeholder="{{translate('search_engine_friendly_page_name_arabic')}}">
                                                                </div>
                                                            </div>
                                                            <div class="mb-30">
                                                                <label
                                                                    class=""><strong>{{translate('meta_title_arabic')}}</strong></label>
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control"
                                                                           name="arabic_sMetaTitle"
                                                                           placeholder="{{translate('meta_title_arabic')}}">
                                                                </div>
                                                            </div>
                                                            <div class="mb-30">
                                                                <label
                                                                    class=""><strong>{{translate('meta_keywords_arabic')}}</strong></label>
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control"
                                                                           name="arabic_sMetaKeywords"
                                                                           placeholder="{{translate('meta_keywords_arabic')}}">
                                                                </div>
                                                            </div>
                                                            <div class="mb-30">
                                                                <label
                                                                    class=""><strong>{{translate('meta_description_arabic')}}</strong></label>
                                                                <div class="form-floating">
                                                                    <textarea type="text" class="form-control"
                                                                              name="arabic_sMetaDescription" rows="4"
                                                                              placeholder="{{translate('meta_description_arabic')}}"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>

                                                    </div>
                                                </div>
                                            </div>

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
            $('.subcategory-select-arabic').select2({
                placeholder: "{{translate('arabic_choose_subcategory')}}"
            });
            $('.subcategory-select').select2({
                placeholder: "{{translate('choose_subcategory')}}"
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
                    console.log(response);
                    // 25-05-23 Pc1
                    $('.select2-results').remove();
                    // 25-05-23 Pc1 Close
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
                    console.log(response)

                    // console.log(response.template_for_variant);
                    console.log(response.template)
                    // 25-05-23 Pc1
                    $('.select2-results').remove();
                    // 25-05-23 Pc1 Close
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
            //Arabic Multiple Images View
            var fileupload_arabic = document.getElementById("fileupload_arabic");
            fileupload_arabic.onchange = function () {
                if (typeof (FileReader) != "undefined") {
                    var dvPreview_arabic = document.getElementById("dvPreview_arabic");
                    dvPreview_arabic.innerHTML = "";
                    var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp)$/;
                    for (var i = 0; i < fileupload_arabic.files.length; i++) {
                        var file = fileupload_arabic.files[i];
                        if (regex.test(file.name.toLowerCase())) {
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                var span = document.createElement('span');
                                span.innerHTML = ['<span id="butt"><img class="thumb img-responsive" style="max-width: 20% !important; padding: 10px 10px 10px 10px !important;" src="', e.target.result, '"/><i class="fa fa-trash text-danger" aria-hidden="true"></i></div>'].join('');
                                document.getElementById('dvPreview_arabic').insertBefore(span, null);

                                $('#dvPreview_arabic').on('click', '#butt', function () {
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
                            dvPreview_arabic.innerHTML = "";
                            return false;
                        }
                    }
                } else {
                    alert("This browser does not support HTML5 FileReader.");
                }
            }

            //English Multiple Images View
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

        $('.view_img_arabic').css('display', 'none');
        $(".file_upload_arabic").change(function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var img = $('<img>').attr('src', e.target.result).addClass('img-show');
                    // $('.fileuplod').css('display','block');
                    $('.view_img_arabic').css('display', 'block').html(img);
                };

                reader.readAsDataURL(this.files[0]);
            } else {
                $(".view_img_arabic img:last-child").remove();
            }
        });

        $('.view_img').css('display', 'none');
        $(".file_upload").change(function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var img = $('<img>').attr('src', e.target.result).addClass('img-show');
                    // $('.fileuplod').css('display','block');
                    $('.view_img').css('display', 'block').html(img);
                };

                reader.readAsDataURL(this.files[0]);
            } else {
                $(".view_img img:last-child").remove();
            }
        });
    </script>

    <script>
        var num = 2;
        var count = 0;
        $('#add_packate_variation').on('click', function () {
            count++;
            html = '<div class="variant_tbl"><hr class="hr-variant"><div class="row"><div class="mb-30 col-md-2"> <label for="packate_measurement_attribute_id"><strong>Attribute</strong></label><div class="form-floating packate_div"><select class="js-select theme-input-style w-100 engAID" name="packate_measurement_attribute_id[]" id="eng_attrb_ID' + count + '" required><option value="0">Select Atribute</option>@foreach($attribute as $att)<option value="{{$att->id}}">{{$att->attribute_name}}</option> @endforeach</select></div></div>' +
                '<div class="mb-30 col-md-2" id="attribute_valueID' + count + '"><label for="packate_measurement_attribute_value"><strong>Attribute Value</strong></label><div class="form-floating packate_div"><select class="js-select theme-input-style w-100" name="packate_measurement_attribute_value[]"></select></div></div><div class="mb-30 col-md-2"><label for="unit"><strong>Selling Price</strong></label><div class="form-floating packate_div"><input type="text" class="form-control" name="packate_measurement_sell_price[]" id="sell_priceID' + count + '" onkeypress="return validateFloatKeyPress(this,event);" placeholder="product_name *" required=""></div></div><div class="mb-30 col-md-2"><label for="unit"><strong>Cost Price</strong></label><div class="form-floating packate_div"><input type="text" class="form-control" name="packate_measurement_cost_price[]" id="cost_priceID' + count + '" onkeypress="return validateFloatKeyPress(this,event);" placeholder="product_name" required=""></div></div><div class="mb-30 col-md-2"><label for="packate_measurement_discount_price"><strong>Discount Price</strong></label><div class="form-floating packate_div"><input type="text" class="form-control" name="packate_measurement_discount_price[]" id="discount_priceID' + count + '" onkeypress="return validateFloatKeyPress(this,event);" placeholder="discount_price" required=""></div></div><div class="mb-30 col-md-2"><label for="packate_measurement_qty"><strong>Stocks/Qty</strong></label><div class="form-floating packate_div"><input type="text" class="form-control" name="packate_measurement_qty[]"  id="qtyID' + count + '" placeholder="product_name *" required></div></div></div>' +
                '<div class="row"><div class="mb-30 col-md-2"><label for="unit"><strong>Shelf Life Unit</strong></label><div class="form-floating packate_div"><select class="js-select theme-input-style w-100" name="packate_measurement_shelf_life_unit[]"><option value="0">Select Shelf Life Unit</option><option value="month">Month</option></select></div> </div><div class="mb-30 col-md-2"><label for="packate_measurement_shelf_life_val"><strong>Shelf Life Value</strong></label><div class="form-floating packate_div"><input type="text" class="form-control" name="packate_measurement_shelf_life_val[]" id="shelf_life_valID' + count + '" placeholder="product_name *"> </div></div><div class="mb-30 col-md-2"><label for="barcode"><strong>Barcode</strong></label><div class="form-floating packate_div"><input type="text" class="form-control" name="packate_measurement_barcode[]" id="barcodeID' + count + '" placeholder="product_name *"></div></div><div class="mb-30 col-md-2"><label for="packate_measurement_fssai_number"><strong>Fssai number</strong></label><div class="form-floating packate_div"><input type="text" class="form-control" name="packate_measurement_fssai_number[]" id="fssaiID' + count + '" placeholder="product_name *"></div></div><div class="mb-30 col-md-3"><label for="packate_measurement_images"><strong>Images</strong></label><div class="form-floating packate_div"><input type="file" class="form-control" name="packate_measurement_images[]" placeholder="product_name *" required> </div></div>' +
                '<div class="col-md-1 text-center" style="display: grid;"><label><strong>Action</strong></label><a class="remove_variation text-danger" title="Remove variation of product" style="cursor: pointer;"><span class="material-icons">delete</span></a></div></div></div>';

            $('#variations').append(html);
            // $('#add_product_form').validate();

            html_arabic = '<div class="variant_tbl"><hr class="hr-variant"><div class="row"><div class="mb-30 col-md-2"> <label for="arabic_packate_measurement_attribute_id"><strong>{{translate('arabic_attribute')}}</strong></label><div class="form-floating arabic_packate_div"><select class="js-select theme-input-style w-100 aID" name="arabic_packate_measurement_attribute_id[]" id="attrb_ID' + count + '" required data-id="' + count + '"><option value="0">{{translate('select_attribute')}}</option>@foreach($arabic_attribute as $aratt)<option value="{{$aratt->id}}">{{$aratt->attribute_name}}</option> @endforeach</select></div></div>' +
                '<div class="mb-30 col-md-2" id="arabic_attribute_valueID' + count + '"><label for="arabic_packate_measurement_attribute_value"><strong>{{translate('arabic_attribute_value')}}</strong></label><div class="form-floating arabic_packate_div"><select class="js-select theme-input-style w-100" id="attrbVal' + count + '" name="arabic_packate_measurement_attribute_value[]"> </select></div></div><div class="mb-30 col-md-2"><label for="unit"><strong>{{translate('arabic_selling_price')}}</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_sell_price[]" id="arabic_sell_priceID' + count + '" onkeypress="return validateFloatKeyPress(this,event);" placeholder="{{translate('arabic_selling_price')}} *" required=""></div></div><div class="mb-30 col-md-2"><label for="unit"><strong>{{translate('arabic_cost_price')}}</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_cost_price[]" id="arabic_cost_priceID' + count + '" onkeypress="return validateFloatKeyPress(this,event);" placeholder="{{translate('arabic_cost_price')}}" required=""></div></div><div class="mb-30 col-md-2"><label for="unit"><strong>{{translate('arabic_discount_price')}}</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_discount_price[]" id="arabic_discount_priceID' + count + '" onkeypress="return validateFloatKeyPress(this,event);" placeholder="{{translate('arabic_discount_price')}} *" required=""></div></div><div class="mb-30 col-md-2"><label for="arabic_packate_measurement_qty"><strong>{{translate('arabic_stocks/qty')}}</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_qty[]" id="arabic_qtyID' + count + '" placeholder="{{translate('arabic_qty')}} *" required></div></div></div>' +
                '<div class="row"><div class="mb-30 col-md-2"><label for="unit"><strong>{{translate('arabic_shelf_life_unit')}}</strong></label><div class="form-floating packate_div"><select class="js-select theme-input-style w-100" name="arabic_packate_measurement_shelf_life_unit[]"><option value="0">{{translate('arabic_select_shelf_life_unit')}}</option><option value="month">Month</option> </select></div></div><div class="mb-30 col-md-2"><label for="arabic_packate_measurement_shelf_life_val"><strong>{{translate('arabic_shelf_life_value')}}</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_shelf_life_val[]" id="arabic_shelf_life_valID' + count + '" placeholder="{{translate('arabic_shelf_life_value')}} *"> </div></div><div class="mb-30 col-md-2"><label for="barcode"><strong>{{translate('arabic_barcode')}}</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_barcode[]" id="arabic_barcodeID' + count + '" placeholder="{{translate('arabic_barcode')}} *"></div></div><div class="mb-30 col-md-2"><label for="arabic_packate_measurement_fssai_number"><strong>{{translate('arabic_fssai_number')}}</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_fssai_number[]" id="arabic_fssaiID' + count + '" placeholder="{{translate('arabic_fssai_number')}} *"></div></div><div class="mb-30 col-md-3"><label for="arabic_packate_measurement_images"><strong>{{translate('arabic_images')}}</strong></label><div class="form-floating arabic_packate_div"><input type="file" class="form-control" name="arabic_packate_measurement_images[]" placeholder="{{translate('arabic_images')}} *" required> </div></div>' +
                '<div class="col-md-1 text-center" style="display: grid;"><label><strong>Action</strong></label><a class="remove_variation text-danger" title="Remove variation of product" style="cursor: pointer;"><span class="material-icons">delete</span></a></div></div></div>';

            $('#arabic_variations').append(html_arabic);
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
            html = '<div class="variant_tbl"><hr class="hr-variant"><div class="row"><div class="mb-30 col-md-2"> <label for="arabic_packate_measurement_attribute_id"><strong>{{translate('arabic_attribute')}}</strong></label><div class="form-floating arabic_packate_div"><select class="js-select theme-input-style w-100 aID" name="arabic_packate_measurement_attribute_id[]" id="attrb_ID' + count + '" required data-id="' + count + '"><option value="0">{{translate('select_attribute')}}</option>@foreach($arabic_attribute as $aratt)<option value="{{$aratt->id}}">{{$aratt->attribute_name}}</option> @endforeach</select></div></div>' +
                '<div class="mb-30 col-md-2" id="arabic_attribute_valueID' + count + '"><label for="arabic_packate_measurement_attribute_value"><strong>{{translate('arabic_attribute_value')}}</strong></label><div class="form-floating arabic_packate_div"><select class="js-select theme-input-style w-100" id="attrbVal' + count + '" name="arabic_packate_measurement_attribute_value[]"> </select></div></div><div class="mb-30 col-md-2"><label for="unit"><strong>{{translate('arabic_selling_price')}}</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_sell_price[]" id="arabic_sell_priceID' + count + '" onkeypress="return validateFloatKeyPress(this,event);" placeholder="{{translate('arabic_selling_price')}} *" required=""></div></div><div class="mb-30 col-md-2"><label for="unit"><strong>{{translate('arabic_cost_price')}}</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_cost_price[]" id="arabic_cost_priceID' + count + '" onkeypress="return validateFloatKeyPress(this,event);" placeholder="{{translate('arabic_cost_price')}}" required=""></div></div><div class="mb-30 col-md-2"><label for="unit"><strong>{{translate('arabic_discount_price')}}</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_discount_price[]" id="arabic_discount_priceID' + count + '" onkeypress="return validateFloatKeyPress(this,event);" placeholder="{{translate('arabic_discount_price')}} *" required=""></div></div><div class="mb-30 col-md-2"><label for="arabic_packate_measurement_qty"><strong>{{translate('arabic_stocks/qty')}}</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_qty[]" id="arabic_qtyID' + count + '" placeholder="{{translate('arabic_qty')}} *" required></div></div></div>' +
                '<div class="row"><div class="mb-30 col-md-2"><label for="unit"><strong>{{translate('arabic_shelf_life_unit')}}</strong></label><div class="form-floating packate_div"><select class="js-select theme-input-style w-100" name="arabic_packate_measurement_shelf_life_unit[]"><option value="0">{{translate('arabic_select_shelf_life_unit')}}</option><option value="month">Month</option> </select></div></div><div class="mb-30 col-md-2"><label for="arabic_packate_measurement_shelf_life_val"><strong>{{translate('arabic_shelf_life_value')}}</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_shelf_life_val[]" id="arabic_shelf_life_valID' + count + '" placeholder="{{translate('arabic_shelf_life_value')}} *"> </div></div><div class="mb-30 col-md-2"><label for="barcode"><strong>{{translate('arabic_barcode')}}</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_barcode[]" id="arabic_barcodeID' + count + '" placeholder="{{translate('arabic_barcode')}} *"></div></div><div class="mb-30 col-md-2"><label for="arabic_packate_measurement_fssai_number"><strong>{{translate('arabic_fssai_number')}}</strong></label><div class="form-floating arabic_packate_div"><input type="text" class="form-control" name="arabic_packate_measurement_fssai_number[]" id="arabic_fssaiID' + count + '" placeholder="{{translate('arabic_fssai_number')}} *"></div></div><div class="mb-30 col-md-3"><label for="arabic_packate_measurement_images"><strong>{{translate('arabic_images')}}</strong></label><div class="form-floating arabic_packate_div"><input type="file" class="form-control" name="arabic_packate_measurement_images[]" placeholder="{{translate('arabic_images')}} *" required> </div></div>' +
                '<div class="col-md-1 text-center" style="display: grid;"><label><strong>Action</strong></label><a class="remove_variation text-danger" title="Remove variation of product" style="cursor: pointer;"><span class="material-icons">delete</span></a></div></div></div>';

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

    <style>
        .bootstrap-tagsinput .tag {
            color: white !important;
            background-color: #4153b3 !important;
        }

        .bootstrap-tagsinput {
            display: block !important;
        }
    </style>

    {{-- Product Tags Input css & js --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css"
          integrity="sha512-xmGTNt20S0t62wHLmQec2DauG9T+owP9e6VU8GigI0anN7OXLip9i7IwEhelasml2osdxX71XcYm6BQunTQeQg=="
          crossorigin="anonymous"/>
    {{--    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"
            integrity="sha512-VvWznBcyBJK71YKEKDMpZ0pCVxjNuKwApp4zLF3ul+CiflQi6aIJR+aZCP/qWsoFBA28avL5T5HA+RE+zrGQYg=="
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-angular.min.js"
            integrity="sha512-KT0oYlhnDf0XQfjuCS/QIw4sjTHdkefv8rOJY5HHdNEZ6AmOh1DW/ZdSqpipe+2AEXym5D0khNu95Mtmw9VNKg=="
            crossorigin="anonymous"></script>

    <!--switchery css-->
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css"
          integrity="sha256-2kJr1Z0C1y5z0jnhr/mCu46J3R6Uud+qCQHA39i1eYo=" crossorigin="anonymous"/>
    <!--switchery js-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"
            integrity="sha256-CgrKEb54KXipsoTitWV+7z/CVYrQ0ZagFB3JOvq2yjo=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function (){
            $('#sell_price_id').keyup(function (){
                var sellPrice = $("#sell_price_id").val();
                $("#discount_price_id").val(sellPrice);
            });
            $('#arabic_sell_price_id').keyup(function (){
                var arbicsellPrice = $("#arabic_sell_price_id").val();
                $("#arabic_discount_price_id").val(arbicsellPrice);
            });
        });
    </script>
    <script>
        var changeCheckbox = document.querySelector('#return_status_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#return_status').val(1);
            } else {
                $('#return_status').val(0);
            }
        };

        var changeCheckbox = document.querySelector('#return_status_button_arabic');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#return_status_arabic').val(1);
            } else {
                $('#return_status_arabic').val(0);
            }
        };
    </script>
    <script>
        var changeCheckbox = document.querySelector('#cancelable_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#cancelable_status').val(1);
                $('#till-status').show();

            } else {
                $('#cancelable_status').val(0);
                $('#till-status').hide();
                $('#till_status').val('');
            }
        };

        var changeCheckbox = document.querySelector('#cancelable_button_arabic');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#cancelable_status_arabic').val(1);
                $('#till-status_arabic').show();

            } else {
                $('#cancelable_status_arabic').val(0);
                $('#till-status_arabic').hide();
                $('#till_status_arabic').val('');
            }
        };
    </script>
    <script>
        var changeCheckbox = document.querySelector('#promo_status_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#promo_status').val(1);
            } else {
                $('#promo_status').val(0);
            }
        };

        var changeCheckbox = document.querySelector('#promo_status_button_arabic');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#promo_status_arabic').val(1);
            } else {
                $('#promo_status_arabic').val(0);
            }
        };
    </script>

    <script>
        //Key Features js for Product page
        var add_cat = {};
        add_cat.clean = function () {
            $('.sKeyFeatures').val("");
        };

        function add_method(arr) {
            if ($('.sKeyFeatures').val() != "") {
                var dataArr = arr;
                if (dataArr.length == 0) {
                    dataArr.push({
                        'nPurID': 0,
                        'sKeyFeatures': $('.sKeyFeatures').val(),
                        'Action': 'Add'
                    });
                    add_cat.clean();
                } else {
                    dataArr.concat(
                        dataArr.push({
                            'nPurID': dataArr.length,
                            'sKeyFeatures': $('.sKeyFeatures').val(),
                            'Action': 'Add'
                        })
                    );
                    add_cat.clean();
                }
                if (dataArr.length != 0) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('provider.product.provider-access-method')}}",
                        data: {ar: dataArr, pn: "add_action"},
                        method: "GET",
                        dataType: 'json',
                        success: function (result) {
                            if (result != "") {
                                console.log(result);
                                $("#tablebody").empty();
                                for (var i = 0; i < result.length; i++) {
                                    console.log(result[i].sKeyFeatures);
                                    $('#view_table').add('d-block').removeClass('d-none');
                                    $("#tablebody").append("<tr><td>" + (i + 1) + "</td><td>" + result[i].sKeyFeatures + "</td>" + "<td><a class='btn btn-danger btn_icon btn-sm' onclick='delTable(" + i + "," + result + ");' ><span class='material-icons'>delete</span></a></td></tr>");
                                }
                            } else {
                                $('#view_table').addClass('d-none').removeClass('d-block');
                            }
                        }
                    });
                } else {
                    $.toast({
                        heading: 'Welcome to Monster admin',
                        text: 'Use the predefined ones, or specify a custom position object.',
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'error',
                        hideAfter: 3500
                    });
                }
            } else {
                $.toast({
                    heading: 'Welcome to Monster admin',
                    text: 'Use the predefined ones, or specify a custom position object.',
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'error',
                    hideAfter: 3500
                });
            }
        }

        function delTable(id, arr) {
            console.log(arr);
            // HoldOn.open({
            //     theme: 'sk-rect',
            //     message: "<h4>Data Checking...</h4>"
            // });
            if (arr.length != 0) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                {{--$.ajax({--}}
                {{--    url: "{{route('admin.product.access-method')}}",--}}
                {{--    data: {ar: arr, id: id, pn: "del_action"},--}}
                {{--    method: "GET",--}}
                {{--    dataType: 'json',--}}
                {{--    success: function (result) {--}}
                {{--        console.log(result);--}}

                {{--        if (result != "") {--}}
                {{--            $('#view_table').addClass('d-block').removeClass('d-none');--}}
                {{--            var obj = JSON.parse(result);--}}
                {{--            $("#tablebody").empty();--}}
                {{--            for (var i = 0; i < obj.length; i++) {--}}
                {{--                $("#tablebody").append("<tr><td>" + (i + 1) + "</td><td>" + obj[i]['sKeyFeatures'] + "</td>" + "<td><a class='btn btn-danger btn_icon btn-sm' onclick='delTable(" + i + "," + result + ");'><i class='fa fa-trash font-weight-bold text-white'></i></a></td></tr>");--}}
                {{--            }--}}
                {{--            $('#btn_feature').data("id", obj);--}}
                {{--        } else {--}}
                {{--            $("#tablebody").empty();--}}
                {{--            $('#btn_feature').data("id", []);--}}
                {{--            $('#view_table').removeClass('d-block').addClass('d-none');--}}
                {{--        }--}}
                {{--    }--}}
                {{--});--}}
            }
            // HoldOn.close();
        }

        $("#btn_feature").on("click", function () {
            var required = [0];
            var validate = true;
            $('#pcontent :input').each(function (index) {
                if ($.inArray(index, required) != -1) {
                    if ($(this).val() == "") {
                        validate = false;
                    }
                }
            });
            if (validate) {
                var type = $(this).data('type');
                if (type == 'add') {
                    var arr = $(this).data('id');
                    // HoldOn.open({
                    //     theme: 'sk-rect',
                    //     message: "<h4>Data Checking...</h4>"
                    // });

                    add_method(arr);
                    // HoldOn.close();
                }
            } else {
                toastr.error('* fields must be required.', 'Required!');
            }
        });

        //Key Features Arabic js for Product page
        var add_cat_arabic = {};
        add_cat_arabic.clean = function () {
            $('.sKeyFeatures_arabic').val("");
        };

        function add_method_arabic(arr) {
            if ($('.sKeyFeatures_arabic').val() != "") {
                var dataArr = arr;
                if (dataArr.length == 0) {
                    dataArr.push({
                        'nPurID': 0,
                        'sKeyFeatures_arabic': $('.sKeyFeatures_arabic').val(),
                        'Action': 'Add'
                    });
                    add_cat_arabic.clean();
                } else {
                    dataArr.concat(
                        dataArr.push({
                            'nPurID': dataArr.length,
                            'sKeyFeatures_arabic': $('.sKeyFeatures_arabic').val(),
                            'Action': 'Add'
                        })
                    );
                    add_cat_arabic.clean();
                }
                if (dataArr.length != 0) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('provider.product.provider-access-method')}}",
                        data: {ar: dataArr, pn: "add_action_arabic"},
                        method: "GET",
                        dataType: 'json',
                        success: function (result) {
                            if (result != "") {
                                console.log(result);
                                $("#tablebody_arabic").empty();
                                for (var i = 0; i < result.length; i++) {
                                    console.log(result[i].sKeyFeatures_arabic);
                                    $('#view_table_arabic').add('d-block').removeClass('d-none');
                                    $("#tablebody_arabic").append("<tr><td>" + (i + 1) + "</td><td>" + result[i].sKeyFeatures_arabic + "</td>" + "<td><a class='btn btn-danger btn_icon btn-sm' onclick='delTable_arabic(" + i + "," + result + ");' ><span class='material-icons'>delete</span></a></td></tr>");
                                }
                            } else {
                                $('#view_table_arabic').addClass('d-none').removeClass('d-block');
                            }
                        }
                    });
                } else {
                    $.toast({
                        heading: 'Welcome to Monster admin',
                        text: 'Use the predefined ones, or specify a custom position object.',
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'error',
                        hideAfter: 3500
                    });
                }
            } else {
                $.toast({
                    heading: 'Welcome to Monster admin',
                    text: 'Use the predefined ones, or specify a custom position object.',
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'error',
                    hideAfter: 3500
                });
            }
        }

        function delTable_arabic(id, arr) {
            console.log(arr);
            // HoldOn.open({
            //     theme: 'sk-rect',
            //     message: "<h4>Data Checking...</h4>"
            // });
            if (arr.length != 0) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                {{--$.ajax({--}}
                {{--    url: "{{route('admin.product.access-method')}}",--}}
                {{--    data: {ar: arr, id: id, pn: "del_action"},--}}
                {{--    method: "GET",--}}
                {{--    dataType: 'json',--}}
                {{--    success: function (result) {--}}
                {{--        console.log(result);--}}

                {{--        if (result != "") {--}}
                {{--            $('#view_table_arabic').addClass('d-block').removeClass('d-none');--}}
                {{--            var obj = JSON.parse(result);--}}
                {{--            $("#tablebody_arabic").empty();--}}
                {{--            for (var i = 0; i < obj.length; i++) {--}}
                {{--                $("#tablebody_arabic").append("<tr><td>" + (i + 1) + "</td><td>" + obj[i]['sKeyFeatures'] + "</td>" + "<td><a class='btn btn-danger btn_icon btn-sm' onclick='delTable_arabic(" + i + "," + result + ");'><i class='fa fa-trash font-weight-bold text-white'></i></a></td></tr>");--}}
                {{--            }--}}
                {{--            $('#btn_feature_arabic').data("id", obj);--}}
                {{--        } else {--}}
                {{--            $("#tablebody_arabic").empty();--}}
                {{--            $('#btn_feature_arabic').data("id", []);--}}
                {{--            $('#view_table_arabic').removeClass('d-block').addClass('d-none');--}}
                {{--        }--}}
                {{--    }--}}
                {{--});--}}
            }
            // HoldOn.close();
        }

        $("#btn_feature_arabic").on("click", function () {
            var required = [0];
            var validate = true;
            $('#pcontent_arabic :input').each(function (index) {
                if ($.inArray(index, required) != -1) {
                    if ($(this).val() == "") {
                        validate = false;
                    }
                }
            });
            if (validate) {
                var type = $(this).data('type');
                if (type == 'add') {
                    var arr = $(this).data('id');
                    // HoldOn.open({
                    //     theme: 'sk-rect',
                    //     message: "<h4>Data Checking...</h4>"
                    // });

                    add_method_arabic(arr);
                    // HoldOn.close();
                }
            } else {
                toastr.error('* fields must be required.', 'Required!');
            }
        });

        //Product Specification js for Product page
        var add_specific_cat = {};
        add_specific_cat.clean = function () {
            $('.specification_type').val("");
            $('.specification_name').val("");
        };

        function add_specific_method(arr) {
            if ($('.specification_type').val() != "" && ($('.specification_name').val() != "")) {
                var dataArr = arr;
                if (dataArr.length == 0) {
                    dataArr.push({
                        'nPurID': 0,
                        'specification_type': $('.specification_type').val(),
                        'specification_name': $('.specification_name').val(),
                        'Action': 'Add'
                    });
                    add_specific_cat.clean();
                } else {
                    dataArr.concat(
                        dataArr.push({
                            'nPurID': dataArr.length,
                            'specification_type': $('.specification_type').val(),
                            'specification_name': $('.specification_name').val(),
                            'Action': 'Add'
                        })
                    );
                    add_specific_cat.clean();
                }
                if (dataArr.length != 0) {
                    $.ajax({
                        url: "{{route('provider.product.provider-access-method')}}",
                        data: {sp: dataArr, pn: "add_specific_action"},
                        method: "GET",
                        dataType: 'json',
                        success: function (result) {
                            if (result != "") {
                                // var obj = JSON.parse(result);
                                $("#specifictablebody").empty();
                                for (var i = 0; i < result.length; i++) {
                                    $('#view_specific_table').add('d-block').removeClass('d-none');
                                    $("#specifictablebody").append("<tr><td>" + (i + 1) + "</td><td>" + result[i].specification_type + "</td><td>" + result[i].specification_name + "</td><td><a class='btn btn-danger btn_icon btn-sm' onclick='del_specific_Table(" + i + "," + result + ");'><span class='material-icons'>delete</span></a></td></tr>");
                                }
                            } else {
                                $('#view_specific_table').addClass('d-none').removeClass('d-block');
                            }
                        }
                    });
                } else {
                    $.toast({
                        heading: 'Welcome to Monster admin',
                        text: 'Use the predefined ones, or specify a custom position object.',
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'error',
                        hideAfter: 3500
                    });
                }
            } else {
                $.toast({
                    heading: 'Welcome to Monster admin',
                    text: 'Use the predefined ones, or specify a custom position object.',
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'error',
                    hideAfter: 3500
                });
            }
        }

        function del_specific_Table(id, arr) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            if (arr.length != 0) {
                $.ajax({
                    url: "{{route('provider.product.provider-access-method')}}",
                    type: "POST",
                    data: {sp: arr, id: id, pn: "del_specific_action"},
                    success: function (result) {
                        if (result != "") {
                            $('#view_specific_table').addClass('d-block').removeClass('d-none');
                            var obj = JSON.parse(result);
                            $("#specifictablebody").empty();
                            for (var i = 0; i < obj.length; i++) {
                                $("#specifictablebody").append("<tr><td>" + (i + 1) + "</td><td>" + obj[i]['specification_type'] + "</td><td>" + obj[i]['specification_name'] + "</td><td><a class='btn btn-danger btn_icon btn-sm' onclick='del_specific_Table(" + i + "," + result + ");'><span class='material-icons'>delete</span></a></td></tr>");
                            }
                            $('#btn_specific').data("id", obj);
                        } else {
                            $("#specifictablebody").empty();
                            $('#btn_specific').data("id", []);
                            $('#view_specific_table').removeClass('d-block').addClass('d-none');
                        }
                    }
                });
            }
            // HoldOn.close();
        }

        $("#btn_specific").on("click", function () {
            var required = [0];
            var validate = true;
            var validate2 = true;
            $('#pspecific :input').each(function (index) {
                if ($.inArray(index, required) != -1) {
                    if ($(this).val() == "") {
                        validate = false;
                    }
                }
            });
            $('.specification_name').each(function (index) {
                if ($.inArray(index, required) != -1) {
                    if ($(this).val() == "") {
                        validate2 = false;
                    }
                }
            });
            if (validate && validate2) {
                var type = $(this).data('type');
                if (type == 'add') {
                    var arr = $(this).data('id');
                    // HoldOn.open({
                    //     theme: 'sk-rect',
                    //     message: "<h4>Data Checking...</h4>"
                    // });
                    add_specific_method(arr);
                    // HoldOn.close();
                }
            } else {
                toastr.error('* fields must be required.', 'Required!');
            }
        });

        //Product Specification Arabic js for Product page
        var add_specific_cat_arabic = {};
        add_specific_cat_arabic.clean = function () {
            $('.specification_type_arabic').val("");
            $('.specification_name_arabic').val("");
        };

        function add_specific_method_arabic(arr) {
            if ($('.specification_type_arabic').val() != "" && ($('.specification_name_arabic').val() != "")) {
                var dataArr = arr;
                if (dataArr.length == 0) {
                    dataArr.push({
                        'nPurID': 0,
                        'specification_type_arabic': $('.specification_type_arabic').val(),
                        'specification_name_arabic': $('.specification_name_arabic').val(),
                        'Action': 'Add'
                    });
                    add_specific_cat_arabic.clean();
                } else {
                    dataArr.concat(
                        dataArr.push({
                            'nPurID': dataArr.length,
                            'specification_type_arabic': $('.specification_type_arabic').val(),
                            'specification_name_arabic': $('.specification_name_arabic').val(),
                            'Action': 'Add'
                        })
                    );
                    add_specific_cat_arabic.clean();
                }
                if (dataArr.length != 0) {
                    $.ajax({
                        url: "{{route('provider.product.provider-access-method')}}",
                        data: {sp: dataArr, pn: "add_specific_action_arabic"},
                        method: "GET",
                        dataType: 'json',
                        success: function (result) {
                            if (result != "") {
                                console.log(result);
                                // var obj = JSON.parse(result);
                                $("#specifictablebody_arabic").empty();
                                for (var i = 0; i < result.length; i++) {
                                    $('#view_specific_table_arabic').add('d-block').removeClass('d-none');
                                    $("#specifictablebody_arabic").append("<tr><td>" + (i + 1) + "</td><td>" + result[i].specification_type_arabic + "</td><td>" + result[i].specification_name_arabic + "</td><td><a class='btn btn-danger btn_icon btn-sm' onclick='del_specific_Table_arabic(" + i + "," + result + ");'><span class='material-icons'>delete</span></a></td></tr>");
                                }
                            } else {
                                $('#view_specific_table_arabic').addClass('d-none').removeClass('d-block');
                            }
                        }
                    });
                } else {
                    $.toast({
                        heading: 'Welcome to Monster admin',
                        text: 'Use the predefined ones, or specify a custom position object.',
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'error',
                        hideAfter: 3500
                    });
                }
            } else {
                $.toast({
                    heading: 'Welcome to Monster admin',
                    text: 'Use the predefined ones, or specify a custom position object.',
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'error',
                    hideAfter: 3500
                });
            }
        }

        function del_specific_Table_arabic(id, arr) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            if (arr.length != 0) {
                $.ajax({
                    url: "{{route('provider.product.provider-access-method')}}",
                    type: "POST",
                    data: {sp: arr, id: id, pn: "del_specific_action"},
                    success: function (result) {
                        if (result != "") {
                            $('#view_specific_table_arabic').addClass('d-block').removeClass('d-none');
                            var obj = JSON.parse(result);
                            $("#specifictablebody_arabic").empty();
                            for (var i = 0; i < obj.length; i++) {
                                $("#specifictablebody_arabic").append("<tr><td>" + (i + 1) + "</td><td>" + obj[i]['specification_type'] + "</td><td>" + obj[i]['specification_name'] + "</td><td><a class='btn btn-danger btn_icon btn-sm' onclick='del_specific_Table(" + i + "," + result + ");'><span class='material-icons'>delete</span></a></td></tr>");
                            }
                            $('#btn_specific_arabic').data("id", obj);
                        } else {
                            $("#specifictablebody_arabic").empty();
                            $('#btn_specific_arabic').data("id", []);
                            $('#view_specific_table_arabic').removeClass('d-block').addClass('d-none');
                        }
                    }
                });
            }
            // HoldOn.close();
        }

        $("#btn_specific_arabic").on("click", function () {
            var required = [0];
            var validate = true;
            var validate2 = true;
            $('#pspecific_arabic :input').each(function (index) {
                if ($.inArray(index, required) != -1) {
                    if ($(this).val() == "") {
                        validate = false;
                    }
                }
            });
            $('.specification_name_arabic').each(function (index) {
                if ($.inArray(index, required) != -1) {
                    if ($(this).val() == "") {
                        validate2 = false;
                    }
                }
            });
            if (validate && validate2) {
                var type = $(this).data('type');
                if (type == 'add') {
                    var arr = $(this).data('id');
                    add_specific_method_arabic(arr);
                }
            } else {
                toastr.error('* fields must be required.', 'Required!');
            }
        });

    </script>
    <script>
        $('document').ready(function () {
            var sku_state = false;
            $('#sku').on('blur', function () {
                var sku = $('#sku').val();
                if (sku == '') {
                    sku_state = false;
                    return;
                }
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{route('provider.product.provider-get-sku')}}",
                    data: {sku: sku, sku_check: 1},
                    method: "GET",
                    dataType: 'json',

                    success: function (response) {
                        console.log(response);
                        if (response == 'taken') {
                            sku_state = false;
                            $('#sku').parent().removeClass();
                            $('#sku').parent().addClass("form_error");
                            $('#sku').siblings("span").text('Sorry... Reference Code already taken');
                        } else if (response == 'not_taken') {
                            sku_state = true;
                            $('#sku').parent().removeClass();
                            $('#sku').parent().addClass("form_success");
                            $('#sku').siblings("span").text('');
                        }
                    }
                });
            });

            //Arabic SKU
            var sku_arabic_state = false;
            $('#sku_arabic').on('blur', function () {
                var sku_arabic = $('#sku_arabic').val();
                if (sku_arabic == '') {
                    sku_arabic_state = false;
                    return;
                }
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{route('provider.product.provider-get-sku-arabic')}}",
                    data: {sku_arabic: sku_arabic, sku_arabic_check: 1},
                    method: "GET",
                    dataType: 'json',

                    success: function (response) {
                        console.log(response);
                        if (response == 'taken') {
                            sku_arabic_state = false;
                            $('#sku_arabic').parent().removeClass();
                            $('#sku_arabic').parent().addClass("form_error");
                            $('#sku_arabic').siblings("span").text('Sorry... Reference Code already taken');
                        } else if (response == 'not_taken') {
                            sku_arabic_state = true;
                            $('#sku_arabic').parent().removeClass();
                            $('#sku_arabic').parent().addClass("form_success");
                            $('#sku_arabic').siblings("span").text('');
                        }
                    }
                });
            });

        });

        //Get Attribute Value as per attribute ID
        $(document).change('.engAID', function () {
            var attrb_data = $('.engAID').data('id');
            var attributeID = $('#eng_attrb_ID' + count).val()

            $.ajax({
                url: "{{route('provider.product.provider-ajax-switch-attribute')}}",
                dataType: 'json',
                method: "GET",
                data: {attributeID: attributeID},
                beforeSend: function () {
                    /*$('#loading').show();*/
                },
                success: function (response) {
                    $('#attribute_valueID' + count).html(response.template);
                },
                complete: function () {
                    /*$('#loading').hide();*/
                },
            });

        });

        $(document).change('.aID', function () {
            var attrb_data = $('.aID').data('id');
            var attributeID = $('#attrb_ID' + count).val()
            var attrbVal = $('#attrbVal' + count).val()

            $.ajax({
                // url: route,
                url: "{{route('provider.product.provider-arabic-ajax-switch-attribute')}}",
                dataType: 'json',
                method: "GET",
                data: {attributeID: attributeID},
                beforeSend: function () {
                    /*$('#loading').show();*/
                },
                success: function (response) {
                    console.log(response.template)
                    $('#arabic_attribute_valueID' + count).html(response.template);
                    // $('.arabic-attribute-valueClass').html(response.template);
                },
                complete: function () {
                    /*$('#loading').hide();*/
                },
            });

        });

        function ajax_switch_attribute(attributeID) {
            $.get({
                url: "{{route('provider.product.provider-ajax-switch-attribute')}}", dataType: 'json',
                method: "GET",
                data: {attributeID: attributeID},
                beforeSend: function () {
                    /*$('#loading').show();*/
                },
                success: function (response) {
                    // 26-05-23 Pc1
                    $('.select2-results__option,.select2-search__field,.select2-search--dropdown').remove();
                    // 26-05-23 Pc1 Close
                    $('#attribute_valueID').html(response.template);
                },
                complete: function () {
                    /*$('#loading').hide();*/
                },
            });
        }

        function arabic_ajax_switch_attribute(attributeID) {
            $.ajax({
                // url: route,
                url: "{{route('provider.product.provider-arabic-ajax-switch-attribute')}}",
                dataType: 'json',
                method: "GET",
                data: {attributeID: attributeID},
                beforeSend: function () {
                    /*$('#loading').show();*/
                },
                success: function (response) {
                    // console.log(response)
                    // 26-05-23 Pc1
                    $('.select2-results__option,.select2-search__field,.select2-search--dropdown').remove();
                    // 26-05-23 Pc1 Close
                    $('#arabic_attribute_valueID').html(response.template);
                },
                complete: function () {
                    /*$('#loading').hide();*/
                },
            });
        }
    </script>

    <script>
        $('#lang_id').val(1);
        $('#english_lang').click(function () {
            var lang_id = $('#lang_id').val(1);
        });
        $('#arabic_lang').click(function () {
            var lang_id = $('#lang_id').val(2);
            var sku = $('#sku').val();
            $('#sku_arabic').val(sku);
            var manufacturer_part_no = $('#manufacturer_part_no').val();
            $('#manufacturer_part_no_arabic').val(manufacturer_part_no);
            var weight = $('#weight').val();
            $('#weight_arabic').val(weight);
            var length = $('#length').val();
            $('#length_arabic').val(length);
            var width = $('#width').val();
            $('#width_arabic').val(width);
            var height = $('#height').val();
            $('#height_arabic').val(height);
            var sell_price_id = $('#sell_price_id').val();
            $('#arabic_sell_price_id').val(sell_price_id);
            var cost_price_id = $('#cost_price_id').val();
            $('#arabic_cost_price_id').val(cost_price_id);
            var discount_price_id = $('#discount_price_id').val();
            $('#arabic_discount_price_id').val(discount_price_id);
            var qty_id = $('#qty_id').val();
            $('#arabic_qty_id').val(qty_id);
            var shelf_life_id = $('#shelf_life_id').val();
            $('#arabic_shelf_life_id').val(shelf_life_id);
            var barcode_id = $('#barcode_id').val();
            $('#arabic_barcode_id').val(barcode_id);
            var fssai_number_id = $('#fssai_number_id').val();
            $('#arabic_fssai_number_id').val(fssai_number_id);
            var sell_priceID = $('#sell_priceID' + count).val();
            $('#arabic_sell_priceID' + count).val(sell_priceID);
            var cost_priceID = $('#cost_priceID' + count).val();
            $('#arabic_cost_priceID' + count).val(cost_priceID);
            var discount_priceID = $('#discount_priceID' + count).val();
            $('#arabic_discount_priceID' + count).val(discount_priceID);
            var qtyID = $('#qtyID' + count).val();
            $('#arabic_qtyID' + count).val(qtyID);
            var shelf_life_valID = $('#shelf_life_valID' + count).val();
            $('#arabic_shelf_life_valID' + count).val(shelf_life_valID);
            var barcodeID = $('#barcodeID' + count).val();
            $('#arabic_barcodeID' + count).val(barcodeID);
            var fssaiID = $('#fssaiID' + count).val();
            $('#arabic_fssaiID' + count).val(fssaiID);

        });

    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#service-add-form").validate({
                rules: {
                    name: "required",
                    arabic_name: "required",
                    description: "required",
                    arabic_description: "required",
                    category_id: "required",
                    arabic_category_id: "required",
                    sku: "required",
                    sku_arabic: "required",
                    "packate_measurement_sell_price[]": "required",
                    "arabic_packate_measurement_sell_price[]": "required",
                    "packate_measurement_cost_price[]": "required",
                    "arabic_packate_measurement_cost_price[]": "required",
                    "packate_measurement_discount_price[]": "required",
                    "arabic_packate_measurement_discount_price[]": "required",
                    "packate_measurement_qty[]": "required",
                    "arabic_packate_measurement_qty[]": "required",
                    "packate_measurement_images[]": "required",
                    "arabic_packate_measurement_images[]": "required",
                    image: "required",
                    arabic_image: "required",
                    // other_images: "required",
                },
                messages: {
                    name: "Product name is required",
                    arabic_name: "اسم المنتج مطلوب",
                    description: "Description is required",
                    arabic_description: "الوصف مطلوب",
                    category_id: "Category is required",
                    arabic_category_id: "اختيار القسم مطلوب",
                    sku: "SKU is required",
                    sku_arabic: "SKU مطلوب",
                    "packate_measurement_sell_price[]": "Sell Price is required",
                    "arabic_packate_measurement_sell_price[]": "سعر البيع مطلوب",
                    "packate_measurement_cost_price[]": "Cost Price is required",
                    "arabic_packate_measurement_cost_price[]": "سعر التكلفة مطلوب",
                    "packate_measurement_discount_price[]": "Discount Price is required",
                    "arabic_packate_measurement_discount_price[]": "مطلوب سعر الخصم",
                    "packate_measurement_qty[]": "Qty is required",
                    "arabic_packate_measurement_qty[]": "الكمية مطلوبة",
                    "packate_measurement_images[]": "Image is required",
                    "arabic_packate_measurement_images[]": "الصورة مطلوبة",
                    image: "Image is required",
                    arabic_image: "الصورة مطلوبة",
                    // other_images: "Images are required",
                }
            });
        });
    </script>
    {{-- 24-05-23 Pc1 --}}
    <script>
        $('.subcategory-select').on('select2:open', function (e) {
            const hideElements = $('.subcategory-select option').length <= 1;
            $('.select2-search, .select2-results').toggle(!hideElements);
        });

        $('.subcategory-select-arabic').on('select2:open', function (e) {
            const hideElements = $('.subcategory-select-arabic option').length <= 1;
            $('.select2-search, .select2-results').toggle(!hideElements);
        });
    </script>
    {{-- Close 24-05-23 Pc1 --}}
@endpush
