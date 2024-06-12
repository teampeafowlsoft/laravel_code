@extends('adminmodule::layouts.master')

@section('title',translate('country_setup'))

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
                        <h2 class="page-title">{{translate('country_setup')}}</h2>
                    </div>

                    <!-- Country Setup -->
                    <div class="card mb-30">
                        <div class="card-body p-30">
                            <form action="{{route('admin.country.store')}}" method="POST"
                                  enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6 mb-4 mb-lg-0">
                                        <input type="hidden" name="group_id" value="{{$country_grp_id}}">

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
                                        <hr class="pb-1">
                                        <!-- End Nav Tabs -->

                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="english">
                                                <div class="form-floating mb-30">
                                                    <select class="form-select form-control" id="floatingSelect" aria-label="Select appropriate country" name="country_type" required>
                                                        <option value="">{{translate('Select_any_one')}}</option>
                                                        <option value="1">GCC</option>
                                                        <option value="2">Other</option>
                                                    </select>
                                                    <label for="floatingSelect">{{translate('Select_country_type')}}</label>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="" name="name"
                                                           placeholder="Country Name" required="">
                                                    <label for="floatingInput">{{translate('Name')}}</label>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="floatingInput"
                                                           name="country_code"
                                                           placeholder="Country Code" required="">
                                                    <label for="floatingInput">{{translate('country_code')}}</label>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="floatingInput"
                                                           name="currency_name"
                                                           placeholder="Currency Name" required="">
                                                    <label for="floatingInput">{{translate('currency_name')}}</label>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="floatingInput"
                                                           name="currency_code"
                                                           placeholder="Currency Code" required="">
                                                    <label for="floatingInput">{{translate('currency_code')}}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic">
                                                <div class="form-floating mb-30">
                                                    <select class="form-select form-control" id="floatingSelect" aria-label="Select appropriate country" name="arabic_country_type" required>
                                                        <option value="">{{translate('Select_any_one_arabic')}}</option>
                                                        <option value="1">GCC</option>
                                                        <option value="2">Other</option>
                                                    </select>
                                                    <label for="floatingSelect">{{translate('Select_country_type_arabic')}}</label>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="" name="arabic_name"
                                                           placeholder="Country Name" required="">
                                                    <label for="floatingInput">{{translate('arabic_Name')}}</label>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="floatingInput"
                                                           name="arabic_country_code"
                                                           placeholder="Country Code" required="">
                                                    <label for="floatingInput">{{translate('arabic_country_code')}}</label>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="floatingInput"
                                                           name="arabic_currency_name"
                                                           placeholder="Currency Name" required="">
                                                    <label for="floatingInput">{{translate('arabic_currency_name')}}</label>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="text" class="form-control" id="floatingInput"
                                                           name="arabic_currency_code"
                                                           placeholder="Currency Code" required="">
                                                    <label for="floatingInput">{{translate('arabic_currency_code')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="d-flex  gap-3 gap-xl-5">
                                            <p class="opacity-75 max-w220">{{translate('image_format_-_jpg,_png,_jpeg,_gif_image
                                                size_-_
                                                maximum_size_2_MB_Image_Ratio_-_1:1')}}</p>
                                            <div>
                                                <p class="opacity-75 max-w220">{{translate('country_flag')}}</p>
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input" name="country_flag"
                                                           required>
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
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-20 mt-30">
                                            <button class="btn btn--secondary"
                                                    type="reset">{{translate('reset')}}</button>
                                            <button class="btn btn--primary demo_check"
                                                    type="submit">{{translate('submit')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End Country Setup -->

                    {{--                    <div class="d-flex justify-content-end border-bottom mx-lg-4 mb-10">--}}
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
                            <span class="opacity-75">{{translate('Total_Countries')}}:</span>
                            <span class="title-color">{{ $country->total() }}</span>
                        </div>
                    </div>

                    <div class="card mb-30">
                        <div class="card-body">
                            <div class="data-table-top d-flex flex-wrap gap-10 justify-content-between">
                                <form action="{{url()->current()}}" class="search-form search-form_style-two"
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
                                    <button type="submit" class="btn btn--primary">{{translate('search')}}</button>
                                </form>

                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    <div class="dropdown">
                                        <button type="button"
                                                class="btn btn--secondary text-capitalize dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                            <span class="material-icons">file_download</span> {{translate('download')}}
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                                            <li><a class="dropdown-item"
                                                   href="{{route('admin.country.download')}}?search={{$search}}">{{translate('excel')}}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="example" class="table align-middle">
                                    <thead>
                                    <tr>
                                        <th>{{translate('SL')}}</th>
                                        <th>{{translate('country_flag')}}</th>
                                        <th>{{translate('type')}}</th>
                                        <th>{{translate('country_name') . ' (' . ('english') . ')'}}</th>
                                        <th>{{translate('country_name') . ' (' . ('arabic') . ')'}}</th>
                                        <th>{{translate('country_code')}}</th>
                                        <th>{{translate('currency_name')}}</th>
                                        <th>{{translate('currency_code')}}</th>
                                        <th>{{translate('status')}}</th>
                                        <th>{{translate('action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($country as $key=>$cntry)
                                        <tr>
                                            <td>{{$country->firstitem()+$key}}</td>
                                            <td><img
                                                    width="70" height="70"
                                                    onerror="this.src='{{asset('public/assets/admin-module')}}/img/media/upload-file.png'"
                                                    src="{{asset('storage/app/public/country')}}/{{!empty(explode(',',$cntry->country_flag)[0]) ? explode(',',$cntry->country_flag)[0] : ''}}"
                                                    alt=""></td>
                                            <td>
                                                @if($cntry->country_type == 1)
                                                    {{'GCC'}}
                                                @elseif($cntry->country_type == 2)
                                                    {{'Other'}}
                                                @else
                                                    {{''}}
                                                @endif
                                            </td>
                                            <td>{{!empty(explode(',',$cntry->name)[0]) ? explode(',',$cntry->name)[0] : ''}}</td>
                                            <td>{{!empty(explode(',',$cntry->name)[1]) ? explode(',',$cntry->name)[1] : ''}}</td>
                                            <td>{{!empty(explode(',',$cntry->country_code)[0]) ? explode(',',$cntry->country_code)[0] : ''}}</td>
                                            <td>{{!empty(explode(',',$cntry->currency_name)[0]) ? explode(',',$cntry->currency_name)[0] : ''}}</td>
                                            <td>{{!empty(explode(',',$cntry->currency_code)[0]) ? explode(',',$cntry->currency_code)[0] : ''}}</td>
                                            <td>
                                                <label class="switcher" data-bs-toggle="modal"
                                                       data-bs-target="#deactivateAlertModal">
                                                    <input class="switcher_input"
                                                           onclick="route_alert('{{route('admin.country.status-update',[$cntry->group_id])}}','{{translate('want_to_update_status')}}')"
                                                           type="checkbox" {{$cntry->is_active?'checked':''}}>
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <div class="table-actions">
                                                    <a href="{{route('admin.country.edit',[$cntry->id,$cntry->group_id])}}"
                                                       class="table-actions_edit demo_check">
                                                        <span class="material-icons">edit</span>
                                                    </a>
                                                    <button type="button"
                                                            @if(env('APP_ENV')!='demo')
                                                            onclick="form_alert('delete-{{$cntry->group_id}}','{{translate('want_to_delete_this_country')}}?')"
                                                            @endif
                                                            class="table-actions_delete bg-transparent border-0 p-0 demo_check">
                                                        <span class="material-icons">delete</span>
                                                    </button>
                                                    <form action="{{route('admin.country.delete',[$cntry->group_id])}}"
                                                          method="post" id="delete-{{$cntry->group_id}}" class="hidden">
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
                                {!! $country->links() !!}
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

        $(document).ready(function () {
            $('.js-select').select2({
                placeholder: "{{translate('select_items')}}",
            });
            $('.select-zone').select2({
                placeholder: "{{translate('select_zones')}}",
            });
            $('.select-user').select2({
                placeholder: "{{translate('select_users')}}",
            });
        });

    </script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/dataTables.select.min.js"></script>
@endpush
