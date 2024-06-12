@extends('adminmodule::layouts.master')

@section('title',translate('add_new_attribute_value'))

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('add_new_attribute_value')}}</h2>
                    </div>

                    <div class="card mb-30">
                        <div class="card-body p-30">
                            <form action="{{route('admin.attributeval.store')}}" method="POST">
                                @csrf
                                <input type="hidden" name="group_id" value="{{$attribute_grp_id}}">
                                <!-- Nav Tabs -->
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="mb-3">
                                            <input type="hidden" name="attribute_group_id" id="attribute_group_id" value="{{$last_segment}}">
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
                                            <a class="btn btn--secondary btn-sm" href="{{route('admin.attributeval.index',[Request::segment(4)])}}"><span class="material-icons" title="Service zones">chevron_left</span> Back
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <!-- End Nav Tabs -->
                                <!-- Tab Content -->
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="english">
                                        <input type="hidden" name="attribute_id" value="{{$attribute_id}}">
                                        <div class="mb-30">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="attribute_value"
                                                       placeholder="{{translate('attribute_value')}} *"
                                                       required="">
                                                <label>{{translate('attribute_value')}} *</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-content">
                                    <div class="tab-pane fade" id="arabic">
                                        <input type="hidden" name="arabic_attribute_id"
                                               value="{{$arabic_attribute_id}}">
                                        <div class="mb-30">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="arabic_attribute_value"
                                                       placeholder="{{translate('arabic_attribute_value')}} *"
                                                       required="">
                                                <label>{{translate('arabic_attribute_value')}} *</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
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

@push('script')
@endpush
