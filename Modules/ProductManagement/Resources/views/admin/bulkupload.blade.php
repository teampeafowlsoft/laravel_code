@extends('adminmodule::layouts.master')

@section('title',translate('bulk_upload'))

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('bulk_upload')}}</h2>
                    </div>

                    <div class="card">
                        <div class="card-body p-30">
                            <form action="{{route('admin.product.import')}}" method="POST"
                                  enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="group_id" value="{{$product_grp_id}}">
                                <div class="discount-type">

                                    <div class="row">
                                        <div class="col-lg-6 mb-30">
                                            <div class="mb-30">
                                                <label for="image"
                                                       class="mb-3"><strong>{{translate('select_type')}}</strong></label>
                                                <div class="form-floating">
                                                    <select name="type" id="type"
                                                            class="js-select theme-input-style w-100">
                                                        <option value="">Select</option>
                                                        <option value="products">Products</option>
{{--                                                        <option value="subcategory">Subcategory</option>--}}
                                                        <option value="variants">Variants</option>
                                                        <option value="features">Features</option>
                                                        <option value="specifications">Specifications</option>
                                                        <option value="media">Products Media</option>
                                                        <option value="shipping">Products Shipping</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-30">
                                                <label for="image"
                                                       class="mb-3"><strong>{{translate('bulk_upload')}}</strong></label>
                                                <div class="form-floating">
                                                    <div class="field">
                                                        <input type="file" name="products"
                                                               class="form-control"
                                                               required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 d-none">
                                            <div class="card card-primary">
                                                <div class="card-header with-border">
                                                    <h3 class="card-title">Read and follow instructions carefully before proceed.</h3>
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="card-footer">
                                                    <a class='btn btn-info' href="{{route('admin.product.download')}}"><span class="material-icons">file_download</span> Download Sample File</a>
                                                    <p><span class="text-danger"><strong>Note:</strong></span> Before upload products in bulk, follow the <span class="text-warning"><strong>Sample File</strong></span> for all types.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="d-flex justify-content-end gap-3">
                                    {{--                                    <input type="submit" name="submit" value="Import Users">--}}
                                    <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

