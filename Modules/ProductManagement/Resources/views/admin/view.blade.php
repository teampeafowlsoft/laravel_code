@extends('adminmodule::layouts.master')

@section('title',translate('product_view'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/select.dataTables.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/wysiwyg-editor/froala_editor.min.css"/>
@endpush

<style>
    .d-inline-block {
        display: inline-block !important
    }
    .product-image-thumbs {
        -webkit-align-items: stretch;
        -ms-flex-align: stretch;
        align-items: stretch;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        margin-top: 2rem
    }

    .product-image-thumb {
        box-shadow: 0 1px 2px rgba(0, 0, 0, .075);
        border-radius: .25rem;
        background-color: #fff;
        border: 1px solid #dee2e6;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        margin-right: 1rem;
        max-width: 7rem;
        padding: .5rem
    }

    .product-image-thumb img {
        max-width: 100%;
        height: auto;
        -webkit-align-self: center;
        -ms-flex-item-align: center;
        align-self: center
    }

    .product-image-thumb:hover {
        opacity: .5
    }

    @media (min-width: 576px) {
        .d-sm-none {
            display: none !important
        }

        .d-sm-inline {
            display: inline !important
        }

        .d-sm-inline-block {
            display: inline-block !important
        }

        .d-sm-block {
            display: block !important
        }

        .d-sm-table {
            display: table !important
        }

        .d-sm-table-row {
            display: table-row !important
        }

        .d-sm-table-cell {
            display: table-cell !important
        }

        .d-sm-flex {
            display: -webkit-flex !important;
            display: -ms-flexbox !important;
            display: flex !important
        }

        .d-sm-inline-flex {
            display: -webkit-inline-flex !important;
            display: -ms-inline-flexbox !important;
            display: inline-flex !important
        }
    }

    .product-image {
        /*max-width: 100%;*/
        height: 250px !important;
        /*width: 100%;*/
    }
</style>

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('product_view')}}</h2>
                    </div>

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
                            @php
                                $id = $products[0]->id;
                                $arabic_id = $products[1]->id;
                                $lang_id = $products[0]->lang_id;
                                $name = !empty($products[0]->name) ? $products[0]->name : '';
                                $arabic_name = !empty($products[1]->name) ? $products[1]->name : '';
                                $category_id = !empty($products[0]->category_id) ? $products[0]->category_id : '';
                                $arabic_category_id = !empty($products[1]->category_id) ? $products[1]->category_id : '';
                                $vendor_id = !empty($products[0]->vendor) ? $products[0]->vendor : '';
                                $arabic_vendor_id = !empty($products[1]->vendor) ? $products[1]->vendor : '';
                                $sub_category = !empty($products[0]->sub_category_id) ? $products[0]->sub_category_id : '';
                                $arabic_sub_category = !empty($products[1]->sub_category_id) ? $products[1]->sub_category_id : '';
                                $description = !empty($products[0]->description) ? $products[0]->description : '';
                                $arabic_description = !empty($products[1]->description) ? $products[1]->description : '';
                                $sku = !empty($products[0]->sku) ? $products[0]->sku : '';
                                $arabic_sku = !empty($products[1]->sku) ? $products[1]->sku : '';
                                $indicator = !empty($products[0]->indicator) ? $products[0]->indicator : '';
                                $arabic_indicator = !empty($products[1]->sku) ? $products[1]->indicator : '';
                                $tags = !empty($products[0]->tags) ? $products[0]->tags : '';
                                $arabic_tags = !empty($products[0]->tags) ? $products[0]->tags : '';
                                 $made_in = !empty($products[0]->made_in) ? $products[0]->made_in : '';
                                 $arabic_made_in = !empty($products[1]->made_in) ? $products[1]->made_in : '';
                                 $manufacturer = !empty($products[0]->manufacturer) ? $products[0]->manufacturer : '';
                                 $arabic_manufacturer = !empty($products[1]->manufacturer) ? $products[1]->manufacturer : '';                                                   $manufacturer_part_no = !empty($products[0]->manufacturer_part_no) ? $products[0]->manufacturer_part_no : '';
                                 $arabic_manufacturer_part_no = !empty($products[1]->manufacturer_part_no) ? $products[1]->manufacturer_part_no : '';
                                 $brand = !empty($products[0]->brand_ids) ? $products[0]->brand_ids : '';
                                 $arabic_brand = !empty($products[1]->brand_ids) ? $products[1]->brand_ids : '';
                                 $weight = !empty($products[0]->weight) ? $products[0]->weight : '';
                                 $arabic_weight = !empty($products[1]->weight) ? $products[1]->weight : '';
                                 $length = !empty($products[0]->length) ? $products[0]->length : '';
                                 $arabic_length = !empty($products[1]->length) ? $products[1]->length : '';
                                 $width = !empty($products[0]->width) ? $products[0]->width : '';
                                 $arabic_width = !empty($products[1]->width) ? $products[1]->width : '';
                                 $height = !empty($products[0]->height) ? $products[0]->height : '';
                                 $arabic_height = !empty($products[1]->height) ? $products[1]->height : '';
                                 $return_status = !empty($products[0]->return_status) ? $products[0]->return_status : '';
                                 $arabic_return_status = !empty($products[1]->return_status) ? $products[1]->return_status : '';                                                $promo_status = !empty($products[0]->promo_status) ?$products[0]->promo_status : '';
                                 $arabic_promo_status = !empty($products[1]->promo_status) ? $products[1]->promo_status : '';
                                 $cancelable_status = !empty($products[0]->cancelable_status) ? $products[0]->cancelable_status : '';
                                 $arabic_cancelable_status = !empty($products[1]->cancelable_status) ? $products[1]->cancelable_status : '';
                                 $till_status = !empty($products[0]->till_status) ? $products[0]->till_status : '';
                                 $arabic_till_status = !empty($products[1]->till_status) ? $products[1]->till_status : '';
                                 $bstatus = !empty($products[0]->bstatus) ? $products[0]->bstatus : '';
                                 $arabic_bstatus = !empty($products[1]->bstatus) ? $products[1]->bstatus : '';
                                 $image = !empty($products[0]->image) ? $products[0]->image : '';
                                 $arabic_image = !empty($products[1]->image) ? $products[1]->image : '';
                                 $videoURL = !empty($products[0]->videoURL) ? $products[0]->videoURL : '';
                                 $arabic_videoURL = !empty($products[1]->videoURL)? $products[1]->videoURL : '';
                                 $brochure = !empty($products[0]->brochure) ? $products[0]->brochure : '';
                                 $arabic_brochure = !empty($products[1]->brochure) ? $products[1]->brochure : '';
                                 $seoPageNm = !empty($products[0]->seoPageNm) ? $products[0]->seoPageNm : '';
                                 $arabic_seoPageNm = !empty($products[1]->seoPageNm) ? $products[1]->seoPageNm : '';
                                 $sMetaTitle = !empty($products[0]->sMetaTitle) ? $products[0]->sMetaTitle : '';
                                 $arabic_sMetaTitle = !empty($products[1]->sMetaTitle) ? $products[1]->sMetaTitle : '';
                                 $sMetaKeywords = !empty($products[0]->sMetaKeywords) ? $products[0]->sMetaKeywords : '';
                                 $arabic_sMetaKeywords = !empty($products[1]->sMetaKeywords) ? $products[1]->sMetaKeywords : '';
                                 $sMetaDescription = !empty($products[0]->sMetaDescription) ? $products[0]->sMetaDescription : '';
                                 $arabic_sMetaDescription = !empty($products[1]->sMetaDescription) ? $products[1]->sMetaDescription : '';

                                $product_subcat = !empty($products_sub_cat) ? explode(',',$products_sub_cat[0]->subcategory_id) : '';

                                $approve_status = !empty($products[0]->approve_status) ? $products[0]->approve_status : '';
                                 $arabic_approve_status = !empty($products[1]->approve_status) ? $products[1]->approve_status : '';
                                 $published_status = !empty($products[0]->published_status) ? $products[0]->published_status : '';
                                 $arabic_published_status = !empty($products[1]->published_status) ? $products[1]->published_status : '';
                                 $show_home_page_status = !empty($products[0]->show_home_page_status) ? $products[0]->show_home_page_status : '';
                                 $arabic_show_home_page_status = !empty($products[1]->show_home_page_status) ? $products[1]->show_home_page_status : '';
                                 $review_status = !empty($products[0]->review_status) ? $products[0]->review_status : '';
                                 $arabic_review_status = !empty($products[1]->review_status) ? $products[1]->review_status : '';
                                 $mark_as_new_status = !empty($products[0]->mark_as_new_status) ? $products[0]->mark_as_new_status : '';
                                 $arabic_mark_as_new_status = !empty($products[1]->mark_as_new_status) ? $products[1]->mark_as_new_status : '';
                                 $topseller_status = !empty($products[0]->topseller_status) ? $products[0]->topseller_status : '';
                                 $arabic_topseller_status = !empty($products[1]->topseller_status) ? $products[1]->topseller_status : '';
                                 $indemand_status = !empty($products[0]->indemand_status) ? $products[0]->indemand_status : '';
                                 $arabic_indemand_status = !empty($products[1]->indemand_status) ? $products[1]->indemand_status : '';
                                 $bapprovalst = !empty($products[0]->bapprovalst) ? $products[0]->bapprovalst : '';
                                 $arabic_bapprovalst = !empty($products[1]->bapprovalst) ? $products[1]->bapprovalst : '';
                                 $block_product_status = !empty($products[0]->block_product_status) ? $products[0]->block_product_status : '';
                                 $arabic_block_product_status = !empty($products[1]->block_product_status) ? $products[1]->block_product_status : '';
                                 $block_comment = !empty($products[0]->block_comment) ? $products[0]->block_comment : '';
                                 $arabic_block_comment = !empty($products[1]->block_comment) ? $products[1]->block_comment : '';
                                 $adminComment = !empty($products[0]->adminComment) ? $products[0]->adminComment : '';
                                 $arabic_adminComment = !empty($products[1]->adminComment) ? $products[1]->adminComment : '';

                            @endphp
                            <form action="{{route('admin.product.review-update',[$products[0]->group_id])}}" method="post"
                                  enctype="multipart/form-data"
                                  id="product-add-form">
                                @csrf
                                @method('put')
                                <input type="hidden" name="group_id" value="{{$products[0]->group_id}}">
                                <input type="hidden" name="eng_lang_id" id="eng_lang_id"
                                       value="{{$languages[0]->language_master_id}}">
                                <input type="hidden" name="arabic_lang_id" id="arabic_lang_id"
                                       value="{{$languages[1]->language_master_id}}">

                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="english">
                                        <div class="row">
                                            <div class="col-12 col-sm-4">
                                                <h3 class="d-inline-block d-sm-none"></h3>
                                                <div class="col-12">
                                                    <img src="{{asset('storage/app/public/product')}}/{{$image}}" class="product-image"
                                                         alt="Product Image">
                                                </div>
                                                <div class="col-12 product-image-thumbs">
                                                    <div class="product-image-thumb active"><img
                                                            src="{{asset('storage/app/public/product')}}/{{$image}}"
                                                            alt="Product Image"></div>

                                                        @if (!empty($products_other_images))

                                                        @for ($i = 0; $i < count($products_other_images); $i++)
                                                    <div class="product-image-thumb active"><img
                                                            src="{{asset('storage/app/public/product')}}/{{$products_other_images[$i]->other_images}}"
                                                            alt="Product Image"></div>
                                                    @endfor
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-8">
                                                <h3 class="my-3">{{$name}}</h3>
                                                <p>{!! html_entity_decode($description) !!}</p>
                                                <p><strong>Product Approval
                                                        : </strong>
                                                    @php echo $products[0]->bapprovalst == 1 ? '<label class="badge badge-success"><strong>Approve</strong></label>' : ($products[0]->bapprovalst == 2 ? '<label class="badge badge-danger"><strong>Not Approve</strong></label>' : ($products[0]->bapprovalst == 3 ? '<label class="badge badge-primary"><strong>Cancel</strong></label>' : '-N/A-')); @endphp
                                                </p>
                                                <p><strong>Price
                                                        : </strong> <span class="">₹{{$products_variant[0]->packate_measurement_discount_price}}</span>
                                                    <small><del class="text-xs">₹{{$products_variant[0]->packate_measurement_sell_price}}</del></small>
                                                </p>
                                                <p><strong>SKU
                                                        : </strong>{{$sku != '' ? $sku : '-NA-'}}
                                                </p>
                                                <?php $tags = explode(",", str_replace('"', '', str_replace(']', '', str_replace('[', '', $products[0]->tags)))); ?>
                                                <p><strong>Product Tags : </strong>
                                                    <?php foreach ($tags as $tg) { ?>
                                                    <span class="badge badge-info"><?php echo $tg; ?></span>
                                                    <?php } ?>
                                                </p>
                                            </div>
                                        </div>
                                        <hr>

                                        <div class="mb-3">
                                            <ul class="nav nav--tabs nav--tabs__style2">
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#detail"
                                                           class="nav-link active" id="details">
                                                        {{translate('detail')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#variant"
                                                           class="nav-link" id="variants">
                                                        {{translate('pricing_variats')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#attribute"
                                                           class="nav-link" id="attributes">
                                                        {{translate('attribute')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#parameter"
                                                           class="nav-link" id="parameters">
                                                        {{translate('parameters')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#feature"
                                                           class="nav-link" id="features">
                                                        {{translate('features')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#specify"
                                                           class="nav-link" id="specifications">
                                                        {{translate('specifications')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#media"
                                                           class="nav-link" id="medias">
                                                        {{translate('media')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#shipping"
                                                           class="nav-link" id="shippings">
                                                        {{translate('shipping')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#seo"
                                                           class="nav-link" id="SEO">
                                                        {{translate('seo')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#review"
                                                           class="nav-link" id="reviews">
                                                        {{translate('review')}}
                                                    </label>
                                                </li>
                                            </ul>
                                        </div>
                                        <hr>

                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="detail">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('category')}}</strong></label></td>
                                                                    <td>@foreach($categories as $category)
                                                                             {{($category->id)==$category_id ? $category->name : ''}}
                                                                        @endforeach</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('subcategory')}}</strong></label></td>
                                                                    <td> @foreach($sub_categories as $sc)
                                                                            @foreach($product_subcat as $psc)
                                                                                @if($sc->id == $psc)
                                                                                    {{($sc->id)==$psc ? $sc->name : ''}}
                                                                                @endif
                                                                            @endforeach
                                                                        @endforeach</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('admin_comment')}} </strong></label></td>
                                                                    <td>{{$adminComment != '' ? $adminComment : ''}}</td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="variant">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">{{translate('product_variants_detail')}}</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th style="width: 10px">#</th>
                                                                    <th>{{translate('attribute')}}</th>
                                                                    <th>{{translate('attribute_value')}}</th>
                                                                    <th>{{translate('selling_price')}}(₹)</th>
                                                                    <th>{{translate('cost_price')}}(₹)</th>
                                                                    <th>{{translate('discount_price')}}(₹)</th>
                                                                    <th>{{translate('stocks/Qty')}}</th>
                                                                    <th>{{translate('shelf_life_unit')}}</th>
                                                                    <th>{{translate('shelf_life_value')}}</th>
                                                                    <th>{{translate('barcode')}}</th>
                                                                    <th>{{translate('fssai_number')}}</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @php $count = 1; @endphp
                                                                @foreach ($products_variant as $pv)
                                                                <tr>
                                                                    <td>{{$count++}} </td>
                                                                    <td> @foreach($attribute as $att)
                                                                            {{$pv->packate_measurement_attribute_id == $att->id ? $att->attribute_name : ''}}
                                                                        @endforeach</td>
                                                                    <td>@foreach($attribute_value as $attVal)
                                                                            {{$pv->packate_measurement_attribute_value == $attVal->id ? $attVal->attribute_value: ''}}
                                                                        @endforeach</td>
                                                                    <td>{{$pv->packate_measurement_sell_price}}</td>
                                                                    <td>{{$pv->packate_measurement_cost_price}}</td>
                                                                    <td>{{$pv->packate_measurement_discount_price}}</td>
                                                                    <td>{{$pv->packate_measurement_qty}}</td>
                                                                    <td>{{$pv->packate_measurement_shelf_life_unit}}</td>
                                                                    <td>{{$pv->packate_measurement_shelf_life_val}}</td>
                                                                    <td>{{$pv->packate_measurement_barcode}}</td>
                                                                    <td>{{$pv->packate_measurement_fssai_number}}</td>
                                                                </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="attribute">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('product_type')}}</strong></label></td>
                                                                    <td>{{$indicator == 1 ? 'Single' : '--'}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('sold_by')}}</strong></label></td>
                                                                    <td>@foreach($provider as $company)
                                                                            {{($company->id)==$vendor_id ? $company->company_name :''}}
                                                                        @endforeach</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('made_in')}}</strong></label></td>
                                                                    <td>{{$made_in}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('manufacturer')}}</strong></label></td>
                                                                    <td>{{$manufacturer}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('manufacturer_part_no')}}</strong></label>
                                                                    </td>
                                                                    <td>{{$manufacturer_part_no}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('brand')}}</strong></label></td>
                                                                    <td>{{$brand}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('weight')}}</strong></label></td>
                                                                    <td>{{$weight}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('length')}}</strong></label></td>
                                                                    <td>{{$length}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('width')}}</strong></label></td>
                                                                    <td>{{$width}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('height')}}</strong></label></td>
                                                                    <td>{{$height}}</td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="parameter">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('is_return')}}</strong></label></td>
                                                                    <td>@php echo $return_status == 1 ? '<label class="badge badge-success">YES</label>' : '<label class="badge badge-danger">NO</label>'; @endphp</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('is_cancel')}}</strong></label></td>
                                                                    <td>@php echo $cancelable_status == 1 ? '<label class="badge badge-success">YES</label>' : '<label class="badge badge-danger">NO</label>'; @endphp </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('till_status')}}</strong></label></td>
                                                                    <td><?php echo $cancelable_status == '1' ? $till_status : "<label class='badge badge-info'>Not Applicable</label>"; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('product_approval')}}</strong></label></td>
                                                                    <td><?php echo $approve_status == 1 ? "YES" : "NO";; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('published')}}</strong></label></td>
                                                                    <td><?php echo $published_status == 1 ? "YES" : "NO"; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('product_show_in_home_page')}}</strong> </label></td>
                                                                    <td><?php echo $show_home_page_status == 1 ? "YES" : "NO"; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('allow_review')}}</strong></label></td>
                                                                    <td><?php echo $review_status == 1 ? "YES" : "NO"; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('mark_as_new')}}</strong></label></td>
                                                                    <td><?php echo $mark_as_new_status == 1 ? "YES" : "NO"; ?></td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="feature">
                                                <div id="view_table"
                                                     class="{{ (count($products_features)) ? "d-block" : "d-none"}}">
                                                    <div class="table-responsive tbl_cat br-5">
                                                        <table
                                                            class="table color-table table-bordered card-1">
                                                            <thead class="bg-thead">
                                                            <tr class="bg--primary">
                                                                <th width="10%">#</th>
                                                                <th width="80%">Feature</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="tablebody">
                                                            @php                                                               if(count($products_features) > 0) {
                    $design_str = "";
                    $json = "";
                    if (!empty($products_features)) {
                        $cnt = 1;
                        foreach ($products_features as $dt) {
                            $json .= json_encode(array("nPurID" => ($cnt - 1), "features_name" => $dt->features_name)) . ",";
                            $cnt++;
                        }
                        $cnt = 1;
                        foreach ($products_features as $dt) {
                            $design_str .= "<tr>
                                                <td>$cnt</td>
                                                <td>" . $dt->features_name . "</td>
                                            </tr>";
                            $cnt++;
                        }
                        echo $design_str;
                    } else {
                        echo "<tr><td colspan='3'></td></tr>";
                    }
                } @endphp
                                                            </tbody>
                                                        </table>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="specify">
                                                <div id="view_specific_table"
                                                     class="{{ (count($products_specification)) ? "d-block" : "d-none"}}"
                                                >
                                                    <div class="table-responsive tbl_cat br-5">
                                                        <table
                                                            class="table color-table table-bordered card-1">
                                                            <thead class="bg-thead">
                                                            <tr class="bg--primary">
                                                                <th width="10%">#</th>
                                                                <th width="30%">Type</th>
                                                                <th width="50%">Name</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="specifictablebody">
                                                            @php                                                               if(count($products_specification) > 0) {
                    $design_str = "";
                    $json = "";
                    if (!empty($products_specification)) {
                        $cnt = 1;
                        foreach ($products_specification as $dt) {
                            $json .= json_encode(array("nPurID" => ($cnt - 1),"specification_type" => $dt->specification_type, "specification_name" => $dt->specification_name)) . ",";
                            $cnt++;
                        }
                        $cnt = 1;
                        foreach ($products_specification as $dt) {
                            $design_str .= "<tr>
                                                <td>$cnt</td>
                                                <td>" . $dt->specification_type . "</td>
                                                <td>" . $dt->specification_name . "</td>
                                            </tr>";
                            $cnt++;
                        }
                        echo $design_str;
                    } else {
                        echo "<tr><td colspan='4'></td></tr>";
                    }
                } @endphp
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="media">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('product_video_url')}}</strong></label></td>
                                                                    <td><?php echo $videoURL != '' ? '<a target="_blank" href="' . $videoURL . '">' . $videoURL . '</a>' : '--'; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('product_brochure')}}</strong></label></td>
                                                                    <td><a href="{{asset('storage/app/public/product')}}/{{$brochure}}">{{$brochure}}</a></td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="shipping">
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
                                                                @php
                                                                    $i=1; @endphp

                                                                @foreach($zones as $key=>$zone)
                                                                    @php $shipping_charge = '0'; @endphp
                                                                    @foreach($products_shipping as $key=>$shipping)
                                                                        @if($zone->id == $shipping->zone_id)
                                                                            @php
                                                                                $shipping_charge = $shipping->delivery_charge;
                                                                            @endphp
                                                                        @endif
                                                                    @endforeach

                                                                    <tr>
                                                                        <td>{{$zone->name}}</td>
                                                                        <td><span>{{$shipping_charge}}</span>
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
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('search_engine_friendly_page_name')}}</strong> </label></td>
                                                                    <td>{{$seoPageNm}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('meta_title')}}</strong></label></td>
                                                                    <td>{{$sMetaTitle}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('meta_keywords')}}</strong></label></td>
                                                                    <td>{{$sMetaKeywords}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('meta_description')}}</strong></label></td>
                                                                    <td>{{$sMetaDescription}}</td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="review">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6  mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('published')}} :</strong></label><br>
                                                                    <input type="checkbox" id="published_status_button"
                                                                           class="js-switch" {{$published_status == 1 ? 'checked' : '' }}>
                                                                    <input type="hidden" id="published_status"
                                                                           name="published_status"
                                                                           value="{{$published_status == 1 ? 1 : 0}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('top_seller')}}</strong> :</label><br>
                                                                    <input type="checkbox" id="topseller_status_button"
                                                                           class="js-switch" {{$topseller_status == 1 ? 'checked' : ''}}>
                                                                    <input type="hidden" id="topseller_status"
                                                                           name="topseller_status"
                                                                           value="{{$topseller_status == 1 ? 1 : 0}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('product_show_in_home_page')}}</strong> :</label><br>
                                                                    <input type="checkbox" id="show_home_page_button"
                                                                           class="js-switch"
                                                                           {{$show_home_page_status == 1 ? 'checked' : '' }}>
                                                                    <input type="hidden" id="show_home_page_status"
                                                                           name="show_home_page_status"
                                                                           value="{{$show_home_page_status == 1 ? 1 : 0}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('in_demand')}}</strong> :</label><br>
                                                                    <input type="checkbox" id="indemand_status_button"
                                                                           class="js-switch" {{$indemand_status == 1 ? 'checked' : '' }}>
                                                                    <input type="hidden" id="indemand_status"
                                                                           name="indemand_status"
                                                                           value="{{$indemand_status == 1 ? 1 : 0}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('allow_review')}}</strong> :</label><br>
                                                                    <input type="checkbox" id="review_status_button"
                                                                           class="js-switch" {{$review_status == 1 ? 'checked' : ''}}>
                                                                    <input type="hidden" id="review_status" name="review_status"
                                                                           value="{{$review_status == 1 ? 1 : 0}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('approval_status')}}</strong> :</label><br>
                                                                    <select name="bapprovalst" id="bapprovalst"
                                                                            class="js-select theme-input-style w-100">
                                                                        <option value="0">-Select Status-</option>
                                                                        <option value="1" {{$bapprovalst == "1" ? "selected" : ""}}>
                                                                            Approved
                                                                        </option>
                                                                        <option value="2" {{$bapprovalst == "2" ? "selected" : ""}}>
                                                                            Not Approved
                                                                        </option>
                                                                        <option value="3" {{$bapprovalst == "3" ? "selected" : ""}}>
                                                                            Cancel
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('mark_as_new')}}</strong> :</label><br>
                                                                    <input type="checkbox" id="mark_as_new_status_button"
                                                                           class="js-switch"
                                                                           {{$mark_as_new_status == 1 ? 'checked' : ''}}>
                                                                    <input type="hidden" id="mark_as_new_status"
                                                                           name="mark_as_new_status"
                                                                           value="{{$mark_as_new_status == 1 ? 1 : 0}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('approval_date')}} :</strong></label><br>
                                                                    <input type="hidden" name="approvalDt" readonly="readonly"
                                                                           value="{{date('d-m-Y')}}"/>
                                                                    <label class="badge badge-info">{{date('d-m-Y')}}</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('block_product')}}</strong> :</label><br>
                                                                    <span class="text-danger text-sm"> Note : Once this product is block than it is unlisted from website and application. Block product note is readable to vendor.</span><br>
                                                                    <input type="checkbox" id="block_product_status_button"
                                                                           class="js-switch" {{$block_product_status == 1 ? 'checked' : ''}}>
                                                                    <input type="hidden" id="block_product_status"
                                                                           name="block_product_status"
                                                                           value="{{$block_product_status == 1 ? 1 : 0}}">
                                                                </div>
                                                            </div>
                                                            @php
                                                            $style = $block_product_status == 1 ? "" : "display:none;";
                                                           @endphp
                                                            <div class="col-md-6 mb-5 mb-lg-0" id="block_comment_area" style="{{$style}}">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('block_product_cmnt')}}</strong>: </label><br>
                                                                    <textarea name="block_comment" id="block_comment" class="form-control"
                                                                              rows="3" placeholder="Enter Block Product Comment">{{$block_comment != '' ? $block_comment : ''}}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('admin_comment')}}</strong> :</label><br>
                                                                    <textarea name="adminComment" id="adminComment" class="form-control"
                                                                              rows="3">{{$adminComment != '' ? $adminComment : ''}}</textarea>
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
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="arabic">
                                        <div class="row">
                                            <div class="col-12 col-sm-4">
                                                <h3 class="d-inline-block d-sm-none"></h3>
                                                <div class="col-12">
                                                    <img src="{{asset('storage/app/public/product')}}/{{$arabic_image}}" class="product-image"
                                                         alt="Product Image">
                                                </div>
                                                <div class="col-12 product-image-thumbs">
                                                    <div class="product-image-thumb active"><img
                                                            src="{{asset('storage/app/public/product')}}/{{$arabic_image}}"
                                                            alt="Product Image"></div>

                                                    @if (!empty($products_other_images_arabic))

                                                        @for ($i = 0; $i < count($products_other_images_arabic); $i++)
                                                            <div class="product-image-thumb active"><img
                                                                    src="{{asset('storage/app/public/product')}}/{{$products_other_images_arabic[$i]->other_images}}"
                                                                    alt="Product Image"></div>
                                                        @endfor
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-8">
                                                <h3 class="my-3">{{$arabic_name}}</h3>
                                                <p>{!! html_entity_decode($arabic_description) !!}</p>
                                                <p><strong>Product Approval
                                                        : </strong>
                                                    @php echo $products[1]->bapprovalst == 1 ? '<label class="badge badge-success"><strong>Approve</strong></label>' : ($products[1]->bapprovalst == 2 ? '<label class="badge badge-danger"><strong>Not Approve</strong></label>' : ($products[1]->bapprovalst == 3 ? '<label class="badge badge-primary"><strong>Cancel</strong></label>' : '-N/A-')); @endphp
                                                </p>
                                                <p><strong>Price
                                                        : </strong> <span class="">₹{{$products_variant_arabic[0]->packate_measurement_discount_price}}</span>
                                                    <small><del class="text-xs">₹{{$products_variant_arabic[0]->packate_measurement_sell_price}}</del></small>
                                                </p>
                                                <p><strong>SKU
                                                        : </strong>{{$sku != '' ? $sku : '-NA-'}}
                                                </p>
                                                <?php $tags = explode(",", str_replace('"', '', str_replace(']', '', str_replace('[', '', $products[0]->tags)))); ?>
                                                <p><strong>Product Tags : </strong>
                                                    <?php foreach ($tags as $tg) { ?>
                                                    <span class="badge badge-info"><?php echo $tg; ?></span>
                                                    <?php } ?>
                                                </p>
                                            </div>
                                        </div>
                                        <hr>

                                        <div class="mb-3">
                                            <ul class="nav nav--tabs nav--tabs__style2">
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#arabic_detail"
                                                           class="nav-link active" id="arabic_details">
                                                        {{translate('arabic_detail')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#arabic_variant"
                                                           class="nav-link" id="arabic_variants">
                                                        {{translate('arabic_pricing_variats')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#arabic_attribute"
                                                           class="nav-link" id="arabic_attributes">
                                                        {{translate('arabic_attribute')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#arabic_parameter"
                                                           class="nav-link" id="arabic_parameters">
                                                        {{translate('arabic_parameters')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#arabic_feature"
                                                           class="nav-link" id="arabic_features">
                                                        {{translate('arabic_features')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#arabic_specify"
                                                           class="nav-link" id="arabic_specifications">
                                                        {{translate('arabic_specification')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#arabic_media"
                                                           class="nav-link" id="arabic_medias">
                                                        {{translate('arabic_media')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#arabic_shipping"
                                                           class="nav-link" id="arabic_shippings">
                                                        {{translate('arabic_shipping')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#arabic_seo"
                                                           class="nav-link" id="arabic_SEO">
                                                        {{translate('arabic_seo')}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#arabic_review"
                                                           class="nav-link" id="arabic_reviews">
                                                        {{translate('arabic_review')}}
                                                    </label>
                                                </li>
                                            </ul>
                                        </div>
                                        <hr>

                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="arabic_detail">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('category')}}</strong></label></td>
                                                                    <td>@foreach($arabic_categories as $abcategory)
                                                                            {{($abcategory->id)==$arabic_category_id ? $abcategory->name : ''}}
                                                                        @endforeach</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('subcategory')}}</strong></label></td>
                                                                    <td> @foreach($sub_categories_arabic as $sca)
                                                                            @foreach($product_subcat as $psc)
                                                                                @if($sca->id == $psc)
                                                                                    {{($sca->id)==$psc ? $sca->name : ''}}

                                                                                @endif
                                                                            @endforeach
                                                                        @endforeach</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('admin_comment')}} </strong></label></td>
                                                                    <td>{{$arabic_adminComment != '' ? $arabic_adminComment : ''}}</td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic_variant">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">{{translate('product_variants_detail')}}</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th style="width: 10px">#</th>
                                                                    <th>{{translate('attribute')}}</th>
                                                                    <th>{{translate('attribute_value')}}</th>
                                                                    <th>{{translate('selling_price')}}(₹)</th>
                                                                    <th>{{translate('cost_price')}}(₹)</th>
                                                                    <th>{{translate('discount_price')}}(₹)</th>
                                                                    <th>{{translate('stocks/Qty')}}</th>
                                                                    <th>{{translate('shelf_life_unit')}}</th>
                                                                    <th>{{translate('shelf_life_value')}}</th>
                                                                    <th>{{translate('barcode')}}</th>
                                                                    <th>{{translate('fssai_number')}}</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @php $count = 1; @endphp
                                                                @foreach ($products_variant_arabic as $pva)
                                                                    <tr>
                                                                        <td>{{$count++}} </td>
                                                                        <td> @foreach($arabic_attribute as $aratt)
                                                                                {{$pva->packate_measurement_attribute_id == $aratt->id ? $aratt->attribute_name : ''}}
                                                                            @endforeach</td>
                                                                        <td>@foreach($arabic_attribute_value as $arattVal)
                                                                                {{$pva->packate_measurement_attribute_value == $arattVal->id ? $arattVal->attribute_value: ''}}
                                                                            @endforeach</td>
                                                                        <td>{{$pva->packate_measurement_sell_price}}</td>
                                                                        <td>{{$pva->packate_measurement_cost_price}}</td>
                                                                        <td>{{$pva->packate_measurement_discount_price}}</td>
                                                                        <td>{{$pva->packate_measurement_qty}}</td>
                                                                        <td>{{$pva->packate_measurement_shelf_life_unit}}</td>
                                                                        <td>{{$pva->packate_measurement_shelf_life_val}}</td>
                                                                        <td>{{$pva->packate_measurement_barcode}}</td>
                                                                        <td>{{$pva->packate_measurement_fssai_number}}</td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic_attribute">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('product_type')}}</strong></label></td>
                                                                    <td>{{$arabic_indicator == 1 ? 'Single' : '--'}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('sold_by')}}</strong></label></td>
                                                                    <td>@foreach($provider as $company)
                                                                            {{($company->id)==$arabic_vendor_id ? $company->company_name :''}}
                                                                        @endforeach</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('made_in')}}</strong></label></td>
                                                                    <td>{{$arabic_made_in}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('manufacturer')}}</strong></label></td>
                                                                    <td>{{$arabic_manufacturer}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('manufacturer_part_no')}}</strong></label>
                                                                    </td>
                                                                    <td>{{$arabic_manufacturer_part_no}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('brand')}}</strong></label></td>
                                                                    <td>{{$arabic_brand}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('weight')}}</strong></label></td>
                                                                    <td>{{$arabic_weight}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('length')}}</strong></label></td>
                                                                    <td>{{$arabic_length}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('width')}}</strong></label></td>
                                                                    <td>{{$arabic_width}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('height')}}</strong></label></td>
                                                                    <td>{{$arabic_height}}</td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic_parameter">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('is_return')}}</strong></label></td>
                                                                    <td>@php echo $arabic_return_status == 1 ? '<label class="badge badge-success">YES</label>' : '<label class="badge badge-danger">NO</label>'; @endphp</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('is_cancel')}}</strong></label></td>
                                                                    <td>@php echo $cancelable_status == 1 ? '<label class="badge badge-success">YES</label>' : '<label class="badge badge-danger">NO</label>'; @endphp </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('till_status')}}</strong></label></td>
                                                                    <td><?php echo $arabic_cancelable_status == '1' ? $till_status : "<label class='badge badge-info'>Not Applicable</label>"; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('product_approval')}}</strong></label></td>
                                                                    <td><?php echo $arabic_approve_status == 1 ? "YES" : "NO";; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('published')}}</strong></label></td>
                                                                    <td><?php echo $arabic_published_status == 1 ? "YES" : "NO"; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('product_show_in_home_page')}}</strong> </label></td>
                                                                    <td><?php echo $arabic_show_home_page_status == 1 ? "YES" : "NO"; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('allow_review')}}</strong></label></td>
                                                                    <td><?php echo $arabic_review_status == 1 ? "YES" : "NO"; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('mark_as_new')}}</strong></label></td>
                                                                    <td><?php echo $arabic_mark_as_new_status == 1 ? "YES" : "NO"; ?></td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic_feature">
                                                <div id="view_table"
                                                     class="{{ (count($products_features_arabic)) ? "d-block" : "d-none"}}">
                                                    <div class="table-responsive tbl_cat br-5">
                                                        <table
                                                            class="table color-table table-bordered card-1">
                                                            <thead class="bg-thead">
                                                            <tr class="bg--primary">
                                                                <th width="10%">#</th>
                                                                <th width="80%">Feature</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="tablebody">
                                                            @php                                                               if(count($products_features_arabic) > 0) {
                    $design_str = "";
                    $json = "";
                    if (!empty($products_features_arabic)) {
                        $cnt = 1;
                        foreach ($products_features_arabic as $dt) {
                            $json .= json_encode(array("nPurID" => ($cnt - 1), "features_name" => $dt->features_name)) . ",";
                            $cnt++;
                        }
                        $cnt = 1;
                        foreach ($products_features_arabic as $dt) {
                            $design_str .= "<tr>
                                                <td>$cnt</td>
                                                <td>" . $dt->features_name . "</td>
                                            </tr>";
                            $cnt++;
                        }
                        echo $design_str;
                    } else {
                        echo "<tr><td colspan='3'></td></tr>";
                    }
                } @endphp
                                                            </tbody>
                                                        </table>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic_specify">
                                                <div id="view_specific_table"
                                                     class="{{ (count($products_specification_arabic)) ? "d-block" : "d-none"}}"
                                                >
                                                    <div class="table-responsive tbl_cat br-5">
                                                        <table
                                                            class="table color-table table-bordered card-1">
                                                            <thead class="bg-thead">
                                                            <tr class="bg--primary">
                                                                <th width="10%">#</th>
                                                                <th width="30%">Type</th>
                                                                <th width="50%">Name</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="specifictablebody">
                                                            @php                                                               if(count($products_specification_arabic) > 0) {
                    $design_str = "";
                    $json = "";
                    if (!empty($products_specification_arabic)) {
                        $cnt = 1;
                        foreach ($products_specification_arabic as $dt) {
                            $json .= json_encode(array("nPurID" => ($cnt - 1),"specification_type" => $dt->specification_type, "specification_name" => $dt->specification_name)) . ",";
                            $cnt++;
                        }
                        $cnt = 1;
                        foreach ($products_specification_arabic as $dt) {
                            $design_str .= "<tr>
                                                <td>$cnt</td>
                                                <td>" . $dt->specification_type . "</td>
                                                <td>" . $dt->specification_name . "</td>
                                            </tr>";
                            $cnt++;
                        }
                        echo $design_str;
                    } else {
                        echo "<tr><td colspan='4'></td></tr>";
                    }
                } @endphp
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic_media">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('product_video_url')}}</strong></label></td>
                                                                    <td><?php echo $arabic_videoURL != '' ? '<a target="_blank" href="' . $arabic_videoURL . '">' . $arabic_videoURL . '</a>' : '--'; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('product_brochure')}}</strong></label></td>
                                                                    <td><a href="{{asset('storage/app/public/product')}}/{{$arabic_brochure}}">{{$arabic_brochure}}</a></td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
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
                                                                @php
                                                                    $i=1; @endphp

                                                                @foreach($arabic_zones as $key=>$arabiczone)
                                                                    @php $shipping_charge_arabic = '0'; @endphp
                                                                    @foreach($products_shipping_arabic as $key=>$shipping_arabic)
                                                                        @if($arabiczone->id == $shipping_arabic->zone_id)
                                                                            @php
                                                                                $shipping_charge_arabic = $shipping_arabic->delivery_charge;
                                                                            @endphp
                                                                        @endif
                                                                    @endforeach

                                                                    <tr>
                                                                        <td>{{$arabiczone->name}}</td>
                                                                        <td><span>{{$shipping_charge_arabic}}</span>
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
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('search_engine_friendly_page_name')}}</strong> </label></td>
                                                                    <td>{{$arabic_seoPageNm}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('meta_title')}}</strong></label></td>
                                                                    <td>{{$arabic_sMetaTitle}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('meta_keywords')}}</strong></label></td>
                                                                    <td>{{$arabic_sMetaKeywords}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 280px"><label><strong>{{translate('meta_description')}}</strong></label></td>
                                                                    <td>{{$arabic_sMetaDescription}}</td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic_review">

                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6  mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('published')}} :</strong></label><br>
                                                                    <input type="checkbox" id="arabic_published_status_button"
                                                                           class="js-switch" {{$arabic_published_status == 1 ? 'checked' : '' }}>
                                                                    <input type="hidden" id="arabic_published_status"
                                                                           name="arabic_published_status"
                                                                           value="{{$arabic_published_status == 1 ? 1 : 0}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('top_seller')}}</strong> :</label><br>
                                                                    <input type="checkbox" id="arabic_topseller_status_button"
                                                                           class="js-switch" {{$arabic_topseller_status == 1 ? 'checked' : ''}}>
                                                                    <input type="hidden" id="arabic_topseller_status"
                                                                           name="arabic_topseller_status"
                                                                           value="{{$arabic_topseller_status == 1 ? 1 : 0}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('product_show_in_home_page')}}</strong> :</label><br>
                                                                    <input type="checkbox" id="arabic_show_home_page_button"
                                                                           class="js-switch"
                                                                        {{$arabic_show_home_page_status == 1 ? 'checked' : '' }}>
                                                                    <input type="hidden" id="arabic_show_home_page_status"
                                                                           name="arabic_show_home_page_status"
                                                                           value="{{$arabic_show_home_page_status == 1 ? 1 : 0}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('in_demand')}}</strong> :</label><br>
                                                                    <input type="checkbox" id="arabic_indemand_status_button"
                                                                           class="js-switch" {{$arabic_indemand_status == 1 ? 'checked' : '' }}>
                                                                    <input type="hidden" id="arabic_indemand_status"
                                                                           name="arabic_indemand_status"
                                                                           value="{{$arabic_indemand_status == 1 ? 1 : 0}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('allow_review')}}</strong> :</label><br>
                                                                    <input type="checkbox" id="arabic_review_status_button"
                                                                           class="js-switch" {{$arabic_review_status == 1 ? 'checked' : ''}}>
                                                                    <input type="hidden" id="arabic_review_status" name="arabic_review_status"
                                                                           value="{{$arabic_review_status == 1 ? 1 : 0}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('approval_status')}}</strong> :</label><br>
                                                                    <select name="arabic_bapprovalst" id="arabic_bapprovalst"
                                                                            class="js-select theme-input-style w-100">
                                                                        <option value="0">-Select Status-</option>
                                                                        <option value="1" {{$arabic_bapprovalst == "1" ? "selected" : ""}}>
                                                                            Approved
                                                                        </option>
                                                                        <option value="2" {{$arabic_bapprovalst == "2" ? "selected" : ""}}>
                                                                            Not Approved
                                                                        </option>
                                                                        <option value="3" {{$arabic_bapprovalst == "3" ? "selected" : ""}}>
                                                                            Cancel
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('mark_as_new')}}</strong> :</label><br>
                                                                    <input type="checkbox" id="arabic_mark_as_new_status_button"
                                                                           class="js-switch"
                                                                        {{$arabic_mark_as_new_status == 1 ? 'checked' : ''}}>
                                                                    <input type="hidden" id="arabic_mark_as_new_status"
                                                                           name="arabic_mark_as_new_status"
                                                                           value="{{$arabic_mark_as_new_status == 1 ? 1 : 0}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('approval_date')}} :</strong></label><br>
                                                                    <input type="hidden" name="arabic_approvalDt" readonly="readonly"
                                                                           value="{{date('d-m-Y')}}"/>
                                                                    <label class="badge badge-info">{{date('d-m-Y')}}</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5 mb-lg-0">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('block_product')}}</strong> :</label><br>
                                                                    <span class="text-danger text-sm"> Note : Once this product is block than it is unlisted from website and application. Block product note is readable to vendor.</span><br>
                                                                    <input type="checkbox" id="arabic_block_product_status_button"
                                                                           class="js-switch" {{$arabic_block_product_status == 1 ? 'checked' : ''}}>
                                                                    <input type="hidden" id="arabic_block_product_status"
                                                                           name="arabic_block_product_status"
                                                                           value="{{$arabic_block_product_status == 1 ? 1 : 0}}">
                                                                </div>
                                                            </div>
                                                            @php
                                                                $style = $arabic_block_product_status == 1 ? "" : "display:none;";
                                                            @endphp
                                                            <div class="col-md-6 mb-5 mb-lg-0" id="arabic_block_comment_area" style="{{$style}}">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('block_product_cmnt')}}</strong>: </label><br>
                                                                    <textarea name="arabic_block_comment" id="arabic_block_comment" class="form-control"
                                                                              rows="3" placeholder="Enter Block Product Comment">{{$arabic_block_comment != '' ? $arabic_block_comment : ''}}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="mb-30">
                                                                    <label for=""><strong>{{translate('admin_comment')}}</strong> :</label><br>
                                                                    <textarea name="arabic_adminComment" id="arabic_adminComment" class="form-control"
                                                                              rows="3">{{$arabic_adminComment != '' ? $arabic_adminComment : ''}}</textarea>
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
                                                    </div>
                                            </div>
                                        </div>
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
                    // console.log(response.template_for_variant);
                    console.log(response.template)
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
        $(document).ready(function () {
            $('.product-image-thumb').on('click', function () {
                var $image_element = $(this).find('img')
                $('.product-image').prop('src', $image_element.attr('src'))
                $('.product-image-thumb.active').removeClass('active')
                $(this).addClass('active')
            })
        })
    </script>

    <script>
        //Published Switch
        var changeCheckbox = document.querySelector('#published_status_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#published_status').val(1);
            } else {
                $('#published_status').val(0);
            }
        };

        //Show in Home page switch
        var changeCheckbox = document.querySelector('#show_home_page_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#show_home_page_status').val(1);
            } else {
                $('#show_home_page_status').val(0);
            }
        };

        //Allow Review switch
        var changeCheckbox = document.querySelector('#review_status_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#review_status').val(1);
            } else {
                $('#review_status').val(0);
            }
        };

        //Allow Review switch
        var changeCheckbox = document.querySelector('#mark_as_new_status_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#mark_as_new_status').val(1);
            } else {
                $('#mark_as_new_status').val(0);
            }
        };

        //Top Seller Switch
        var changeCheckbox = document.querySelector('#topseller_status_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#topseller_status').val(1);
            } else {
                $('#topseller_status').val(0);
            }
        };

        //In Demand Switch
        var changeCheckbox = document.querySelector('#indemand_status_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#indemand_status').val(1);
            } else {
                $('#indemand_status').val(0);
            }
        };

        // Block Product Switch
        var changeCheckbox = document.querySelector('#block_product_status_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#block_product_status').val(1);
                $('#block_comment_area').show();
            } else {
                $('#block_product_status').val(0);
                $('#block_comment_area').hide();
                $('#block_comment_area').val('');
            }
        };
    </script>

    {{--Arabic Script--}}
    <script>
        //Published Switch
        var changeCheckbox = document.querySelector('#arabic_published_status_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#arabic_published_status').val(1);
            } else {
                $('#arabic_published_status').val(0);
            }
        };

        //Show in Home page switch
        var changeCheckbox = document.querySelector('#arabic_show_home_page_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#arabic_show_home_page_status').val(1);
            } else {
                $('#arabic_show_home_page_status').val(0);
            }
        };

        //Allow Review switch
        var changeCheckbox = document.querySelector('#arabic_review_status_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#arabic_review_status').val(1);
            } else {
                $('#arabic_review_status').val(0);
            }
        };

        //Allow Review switch
        var changeCheckbox = document.querySelector('#arabic_mark_as_new_status_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#arabic_mark_as_new_status').val(1);
            } else {
                $('#arabic_mark_as_new_status').val(0);
            }
        };

        //Top Seller Switch
        var changeCheckbox = document.querySelector('#arabic_topseller_status_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#arabic_topseller_status').val(1);
            } else {
                $('#arabic_topseller_status').val(0);
            }
        };

        //In Demand Switch
        var changeCheckbox = document.querySelector('#arabic_indemand_status_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#arabic_indemand_status').val(1);
            } else {
                $('#arabic_indemand_status').val(0);
            }
        };

        // Block Product Switch
        var changeCheckbox = document.querySelector('#arabic_block_product_status_button');
        var init = new Switchery(changeCheckbox);
        changeCheckbox.onchange = function () {
            if ($(this).is(':checked')) {
                $('#arabic_block_product_status').val(1);
                $('#arabic_block_comment_area').show();
            } else {
                $('#arabic_block_product_status').val(0);
                $('#arabic_block_comment_area').hide();
                $('#arabic_block_comment_area').val('');
            }
        };
    </script>
@endpush
