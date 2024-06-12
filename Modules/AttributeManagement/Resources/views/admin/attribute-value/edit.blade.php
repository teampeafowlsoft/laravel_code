@extends('adminmodule::layouts.master')

@section('title',translate('attribute_value_update'))

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('attribute_value_update')}}</h2>
                    </div>

                    @php
                            $id = explode(',',$attributes[0]->id)[0];
                            $arabic_id = explode(',',$attributes[0]->id)[1];
                            $lang_id = explode(',',$attributes[0]->lang_id);
                            $attribute_value = !empty(explode(',',$attributes[0]->attribute_value)[0]) ? explode(',',$attributes[0]->attribute_value)[0] : '';
                            $arabic_attribute_value = !empty(explode(',',$attributes[0]->attribute_value)[1]) ? explode(',',$attributes[0]->attribute_value)[1] : '';
                    @endphp

                    <div class="card mb-30">
                        <div class="card-body p-30">
                            <form action="{{route('admin.attributeval.update',[$attributes[0]->group_id])}}" method="POST">
                                @csrf
                                @method('put')
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
                                               href="{{route('admin.attributeval.index',[$attributes[0]->group_id])}}"><span class="material-icons"
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
                                                <input type="text" class="form-control" name="attribute_value"
                                                       placeholder="{{translate('attribute_value')}} *"
                                                       required="" value="{{$attribute_value}}">
                                                <label>{{translate('attribute_value')}} *</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-content">
                                    <div class="tab-pane fade" id="arabic">
                                        <div class="mb-30">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="arabic_attribute_value"
                                                       placeholder="{{translate('arabic_attribute_value')}} *"
                                                       required="" value="{{$arabic_attribute_value}}">
                                                <label>{{translate('arabic_attribute_value')}} *</label>
                                            </div>
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
