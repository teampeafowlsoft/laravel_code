@extends('adminmodule::layouts.master')

@section('title',translate('product_list'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/select.dataTables.min.css"/>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div
                        class="page-title-wrap d-flex justify-content-between flex-wrap align-items-center gap-3 mb-3">
                        <h2 class="page-title">{{translate('product_list')}}</h2>
                        <div>
                            <a href="{{route('admin.product.create')}}" class="btn btn--primary">
                                <span class="material-icons">add</span>
                                {{translate('add_product')}}
                            </a>
                        </div>
                    </div>

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
                            <span class="opacity-75">{{translate('Total_Products')}}:</span>
                            <span class="title-color"></span>
                            {{$products->total()}}
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
                                    </div>

                                    <div class="table-responsive">
                                        <table id="example" class="table align-middle">
                                            <thead>
                                            <tr>
                                                <th>{{translate('SL')}}</th>
                                                <th>{{translate('image')}}</th>
                                                <th>{{translate('name') . ' (' . ('english') . ')'}}</th>
                                                <th>{{translate('name') . ' (' . ('arabic') . ')'}}</th>
                                                <th>{{translate('vendor_name')}}</th>
                                                <th>{{translate('overall_stock')}}</th>
                                                <th>{{translate('reference_code/sku')}}</th>
                                                {{--                                                <th>{{translate('category')}}</th>--}}
                                                {{--                                                <th>--}}
                                                {{--                                                    {{translate('zones')}}--}}
                                                {{--                                                </th>--}}
                                                <th>{{translate('status')}}</th>
                                                <th>{{translate('action')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php $i=1;
                                            $overall_qty = 0;
                                            @endphp
                                            @foreach($products as $key=>$product)
{{--                                                @php print_r(explode(',',$product->name)[0]); @endphp--}}
                                                @foreach($products_qty as $key=>$qty)
                                                    @if($qty->product_id == explode(',',$product->id)[0])
                                                        @php
                                                          $overall_qty = $qty->packate_measurement_qty;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <tr>
                                                        <td>{{$i++}}</td>
                                                        <td>
                                                            <img
                                                                width="70" height="70"
                                                                onerror="this.src='{{asset('public/assets/admin-module')}}/img/media/upload-file.png'"
                                                                src="{{asset('storage/app/public/product')}}/{{explode(',',$product->image)[0]}}"
                                                                alt="">
                                                        </td>
                                                        <td>
{{--                                                            {{!empty($product->name) ? explode(',',$product->name)[0] : ''}}--}}
{{--                                                            <?php exit(); ?>--}}
                                                            {{!empty($product->name) ? explode(',',$product->name)[0] : ''}}
                                                        </td>
                                                        <td>
{{--                                                            {{!empty($product->name) ? explode(',',$product->name)[1] : ''}}--}}
                                                            {{!empty(explode(',',$product->name)[1]) ? explode(',',$product->name)[1] : ''}}
                                                        </td>
                                                    <td>
                                                        {{!empty($product->company_name) ? $product->company_name : ''}}
                                                    </td>

                                                        <td>{{$overall_qty}}</td>
                                                        <td>{{!empty($product->sku) ? explode(',',$product->sku)[0] : ''}}</td>
                                                        <td>
                                                            <label class="switcher" data-bs-toggle="modal"
                                                                   data-bs-target="#deactivateAlertModal">
                                                                <input class="switcher_input"
                                                                       onclick="route_alert('{{route('admin.product.status-update',[$product->group_id])}}','{{translate('want_to_update_status')}}')"
                                                                       type="checkbox" {{$product->is_active?'checked':''}}>
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="table-actions">
                                                                <a href="{{route('admin.product.edit',[$product->id,$product->group_id])}}"
                                                                   class="table-actions_edit demo_check">
                                                                    <span class="material-icons">edit</span>
                                                                </a>
                                                                <button type="button"
                                                                        @if(env('APP_ENV')!='demo')
                                                                        onclick="form_alert('delete-{{$product->group_id}}','{{translate('want_to_delete_this_product')}}?')"
                                                                        @endif
                                                                        class="table-actions_delete bg-transparent border-0 p-0 demo_check">
                                                                    <span class="material-icons">delete</span>
                                                                </button>
                                                                <form
                                                                    action="{{route('admin.product.delete',[$product->group_id])}}"
                                                                    method="post" id="delete-{{$product->group_id}}"
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
                                        {!! $products->links() !!}
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
        });
    </script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/dataTables.select.min.js"></script>
@endpush
