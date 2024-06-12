@extends('adminmodule::layouts.master')

@section('title',translate('attribute_update'))

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('update_attribute')}}</h2>
                    </div>

                    @php
                        $id = explode(',',$attributes[0]->id)[0];
                        $arabic_id = explode(',',$attributes[0]->id)[1];
                        $lang_id = explode(',',$attributes[0]->lang_id);
                        $name = !empty(explode(',',$attributes[0]->attribute_name)[0]) ? explode(',',$attributes[0]->attribute_name)[0] : '';
                        $arabic_name = !empty(explode(',',$attributes[0]->attribute_name)[1]) ? explode(',',$attributes[0]->attribute_name)[1] : '';
                        $display_name = !empty(explode(',',$attributes[0]->display_name)[0]) ? explode(',',$attributes[0]->display_name)[0] : '';
                        $arabic_display_name = !empty(explode(',',$attributes[0]->display_name)[1]) ? explode(',',$attributes[0]->display_name)[1] : '';
                    @endphp

                    <div class="card mb-30">
                        <div class="card-body p-30">
                            <form action="{{route('admin.attribute.update',[$attributes[0]->group_id])}}" method="POST">
                                @csrf
                                @method('put')
{{--                                <input type="text" name="group_id" value="{{$attribute_grp_id}}">--}}
                                <!-- Nav Tabs -->
                                <div class="row">
                                    <div class="col-12 col-md-6">
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
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="float-end">
                                            <a class="btn btn--secondary btn-sm"
                                               href="{{route('admin.attribute.index')}}"><span class="material-icons"
                                                                                               title="{{translate('service_zones')}}">chevron_left</span> {{translate('back')}}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <!-- End Nav Tabs -->
                                <!-- Tab Content -->
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="english">
                                        <div class="mb-30">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="attribute_name"
                                                       placeholder="{{translate('attribute_name')}} *"
                                                       required="" value="{{$name}}">
                                                <label>{{translate('attribute_name')}} *</label>
                                            </div>
                                        </div>

                                        <div class="mb-30">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="display_name"
                                                       placeholder="{{translate('display_name')}} *"
                                                       required="" value="{{$display_name}}">
                                                <label>{{translate('display_name')}} *</label>
                                            </div>
                                        </div>

                                        <div class="mb-30">
                                            <select class="js-select theme-input-style w-100"
                                                    name="attribute_field_type_id">
                                                <option value="" selected
                                                        disabled>{{translate('Select_field_type')}}</option>
                                                {{--                                                        @foreach($main_categories as $item)--}}
                                                {{--                                                            <option value="{{$item['id']}}">{{$item->name}}</option>--}}
                                                {{--                                                        @endforeach--}}
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div class="tab-content">
                                    <div class="tab-pane fade" id="arabic">
                                        <div class="mb-30">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="arabic_attribute_name"
                                                       placeholder="{{translate('arabic_attribute_name')}} *"
                                                       required="" value="{{$arabic_name}}">
                                                <label>{{translate('arabic_attribute_name')}} *</label>
                                            </div>
                                        </div>

                                        <div class="mb-30">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="arabic_display_name"
                                                       placeholder="{{translate('arabic_display_name')}} *"
                                                       required="" value="{{$arabic_display_name}}">
                                                <label>{{translate('arabic_display_name')}} *</label>
                                            </div>
                                        </div>

                                        <div class="mb-30">
                                            <select class="js-select theme-input-style w-100"
                                                    name="arabic_attribute_field_type_id">
                                                <option value="" selected
                                                        disabled>{{translate('arabic_Select_field_type')}}</option>
                                                {{--                                                        @foreach($main_categories as $item)--}}
                                                {{--                                                            <option value="{{$item['id']}}">{{$item->name}}</option>--}}
                                                {{--                                                        @endforeach--}}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button class="btn btn--primary" type="submit">{{translate('update')}}
                                    </button>
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
@endpush
