@extends('adminmodule::layouts.master')

@section('title',translate('update_coupon'))

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('update_coupon')}}</h2>
                    </div>

                    <div class="card mb-30">
                        <div class="card-body p-30">
                            <form action="{{route('admin.productcoupon.update',[$coupon->id])}}" method="POST">
                                @method('PUT')
                                @csrf
                                <div class="coupon-type">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-30">
                                                <select class="js-select theme-input-style w-100" name="coupon_type">
                                                    <option>{{translate('select_coupon_type')}}</option>
                                                    @foreach(COUPON_TYPES as $index=>$coupon_type)
                                                        <option value="{{$index}}" {{$coupon->coupon_type==$index?'selected':''}}>
                                                            {{$coupon_type}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" name="coupon_code" value="{{$coupon->coupon_code}}"
                                                           placeholder="{{translate('coupon_code')}}">
                                                    <label>{{translate('coupon_code')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">{{translate('discount_type')}}</div>
                                    <div class="d-flex flex-wrap align-items-center gap-4 mb-30">
                                        <div class="custom-radio">
                                            <input type="radio" id="category" name="discount_type"
                                                   value="category" {{$coupon->discount->discount_type=='category'?'checked':''}}>
                                            <label for="category">{{translate('category_wise')}}</label>
                                        </div>
                                        <div class="custom-radio">
                                            <input type="radio" id="product" name="discount_type"
                                                   value="product" {{$coupon->discount->discount_type=='product'?'checked':''}}>
                                            <label for="product">{{translate('product_wise')}}</label>
                                        </div>
                                        <div class="custom-radio">
                                            <input type="radio" id="mixed" name="discount_type"
                                                   value="mixed" {{$coupon->discount->discount_type=='mixed'?'checked':''}}>
                                            <label for="mixed">{{translate('mixed')}}</label>
                                        </div>
                                    </div>

                                    <div class="mb-30">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="discount_title"
                                                   placeholder="{{translate('discount_title')}} *"
                                                   value="{{$coupon->discount->discount_title}}"
                                                   required="">
                                            <label>{{translate('discount_title')}} *</label>
                                        </div>
                                    </div>
                                    <div class="mb-30" id="category_selector"
                                         style="display: {{($coupon->discount->discount_type=='category' || $coupon->discount->discount_type=='mixed')?'block':'none'}}">
                                        <select class="category-select theme-input-style w-100" name="category_ids[]"
                                                multiple="multiple" id="category_selector__select"  {{($coupon->discount->discount_type=='category' || $coupon->discount->discount_type=='mixed')?'required':''}}>
                                            @foreach($categories as $category)
                                                <option
                                                    value="{{$category->id}}" {{in_array($category->id,$coupon->discount->category_types->pluck('type_wise_id')->toArray())?'selected':''}}>
                                                    {{$category->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-30" id="product_selector"
                                         style="display: {{($coupon->discount->discount_type=='product' || $coupon->discount->discount_type=='mixed')?'block':'none'}}">
                                        <select class="product-select theme-input-style w-100" name="product_ids[]"
                                                multiple="multiple" id="product_selector__select">
                                            @foreach($products as $product)
                                                <option
                                                    value="{{$product->id}}" {{in_array($product->id,$coupon->discount->service_types->pluck('type_wise_id')->toArray())?'selected':''}}>
                                                    {{$product->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-30">
                                        <select class="zone-select theme-input-style w-100" name="zone_ids[]"
                                                multiple="multiple" required>
                                            @foreach($zones as $zone)
                                                <option
                                                    value="{{$zone->id}}" {{in_array($zone->id,$coupon->discount->zone_types->pluck('type_wise_id')->toArray())?'selected':''}}>
                                                    {{$zone->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="coupon-amount-type">
                                    <div class="mb-3">{{translate('discount_amount_type')}}</div>
                                    <div class="d-flex flex-wrap align-items-center gap-4 mb-30">
                                        <div class="custom-radio">
                                            <input type="radio" id="percentage" name="discount_amount_type"
                                                   value="percent" {{$coupon->discount->discount_amount_type=='percent'?'checked':''}}>
                                            <label for="percentage">{{translate('percentage')}}</label>
                                        </div>
                                        <div class="custom-radio">
                                            <input type="radio" id="fixed_amount" name="discount_amount_type"
                                                   value="amount" {{$coupon->discount->discount_amount_type=='amount'?'checked':''}}>
                                            <label for="fixed_amount">{{translate('fixed_amount')}}</label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="number" class="form-control" name="discount_amount"
                                                           value="{{$coupon->discount->discount_amount}}" id="discount_amount"
                                                           placeholder="{{translate('amount')}}" step="any"
                                                           min="0" {{$coupon->discount->discount_amount_type == 'percent'? 'max=100' : ''}}>
                                                    <label id="discount_amount__label">{{translate('amount')}} ({{$coupon->discount->discount_amount_type == 'amount' ? currency_symbol() : '%'}})</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="date" class="form-control" name="start_date"
                                                           value="{{$coupon->discount->start_date}}">
                                                    <label>{{translate('start_date')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="date" class="form-control" name="end_date"
                                                           value="{{$coupon->discount->end_date}}">
                                                    <label>{{translate('end_date')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="number" class="form-control" step="any"
                                                           name="min_purchase"
                                                           placeholder="{{translate('min_purchase')}} ({{currency_symbol()}}) *"
                                                           value="{{$coupon->discount->min_purchase}}"
                                                           min="0">
                                                    <label>{{translate('min_purchase')}} ({{currency_symbol()}})
                                                        *</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4" id="max_discount_amount"
                                             style="display: {{$coupon->discount->discount_amount_type=='amount'?'none':'block'}}">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="number" class="form-control" step="any"
                                                           name="max_discount_amount"
                                                           placeholder="{{translate('max_discount_amount')}} ({{currency_symbol()}}) *"
                                                           value="{{$coupon->discount->max_discount_amount}}"
                                                           min="0">
                                                    <label>{{translate('max_discount')}} ({{currency_symbol()}}) *</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-3">
                                    <button type="reset" class="btn btn--secondary">{{translate('reset')}}</button>
                                    <button type="submit" class="btn btn--primary">{{translate('update')}}</button>
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
    <script>
        "use Strict"
        $('#category').on('click', function () {
            $('#category_selector').show();
            $('#product_selector').hide();

            $('#category_selector__select').prop('required',true);
            $('#product_selector__select').prop('required',false);
        });

        $('#product').on('click', function () {
            $('#category_selector').hide();
            $('#product_selector').show();

            $('#product_selector__select').prop('required',true);
            $('#category_selector__select').prop('required',false);
        });

        $('#mixed').on('click', function () {
            $('#category_selector').show();
            $('#product_selector').show();

            $('#product_selector__select').prop('required',true);
            $('#category_selector__select').prop('required',true);
        });

        $('#percentage').on('click', function () {
            $('#max_discount_amount').show();

            //Attribute Update
            $('#discount_amount').attr({"max" : 100});
            $('#discount_amount__label').html('{{translate('amount')}} (%)');
        });

        $('#fixed_amount').on('click', function () {
            $('#max_discount_amount').hide();

            //Attribute Update
            $('#discount_amount').removeAttr('max');
            $('#discount_amount__label').html('{{translate('amount')}} ({{currency_symbol()}})');
        });

        //Select 2
        $(".category-select").select2({
            placeholder: "Select Category",
        });
        $(".product-select").select2({
            placeholder: "Select Product",
        });
        $(".zone-select").select2({
            placeholder: "Select Zone",
        });

    </script>
@endpush
