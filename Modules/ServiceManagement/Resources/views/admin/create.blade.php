@extends('adminmodule::layouts.master')

@section('title',translate('service_setup'))

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
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('add_new_service')}}</h2>
                    </div>

                    <div class="card">
                        <div class="card-body p-30">
                            <form action="{{route('admin.service.store')}}" method="post" enctype="multipart/form-data"
                                  id="service-add-form">
                                @csrf
                                <input type="hidden" name="group_id" value="{{$service_grp_id}}">
                                <div id="form-wizard">
                                    <h3>{{translate('service_information')}}</h3>
                                    <section>
                                        <div class="row">
                                            {{--English Fields--}}
                                            <div class="col-lg-3 mb-5 mb-lg-0">
                                                <h4 class="mb-3"><center>{{translate('english')}}</center></h4>
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="name"
                                                               placeholder="{{translate('service_name_english')}} *"
                                                               required="" value="{{old('name')}}">
                                                        <label>{{translate('service_name_english')}} *</label>
                                                    </div>
                                                </div>
                                                <div class="mb-30">
                                                    <select class="js-select theme-input-style w-100"
                                                            name="category_id"
                                                            onchange="ajax_switch_category('{{url('/')}}/admin/category/ajax-childes/'+this.value)">
                                                        <option
                                                            value="0">{{translate('choose_category_english')}}</option>
                                                        @foreach($categories as $category)
                                                            <option
                                                                value="{{$category->id}}">{{$category->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-30" id="sub-category-selector">
                                                    <select class="subcategory-select theme-input-style w-100"
                                                            name="sub_category_id">
                                                    </select>
                                                </div>
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="tax"
                                                               min="0"
                                                               max="100" step="0.01"
                                                               placeholder="{{translate('add_tax_percentage_english')}} *"
                                                               required="" value="{{old('tax')}}">
                                                        <label>{{translate('add_tax_percentage_english')}} *</label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{--Arabic Fields--}}
                                            <div class="col-lg-3 mb-5 mb-lg-0">
                                                <h4 class="mb-3"><center>{{translate('arabic')}}</center></h4>
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="arabic_name"
                                                               placeholder="{{translate('service_name_arabic')}} *"
                                                               required="" value="{{old('name')}}">
                                                        <label>{{translate('service_name_arabic')}} *</label>
                                                    </div>
                                                </div>
                                                <div class="mb-30">
                                                    <select class="js-select theme-input-style w-100"
                                                            name="arabic_category_id"
                                                            onchange="ajax_switch_category_arabic('{{url('/')}}/admin/category/ajax-childes-arabic/'+this.value)">
                                                        <option
                                                            value="0">{{translate('choose_category_arabic')}}</option>
                                                        @foreach($categories_arabic as $category)
                                                            <option
                                                                value="{{$category->id}}">{{$category->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-30" id="sub-category-selector-arabic">
                                                    <select class="subcategory-select-arabic theme-input-style w-100"
                                                            name="arabic_sub_category_id">
                                                    </select>
                                                </div>

                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="arabic_tax"
                                                               min="0"
                                                               max="100" step="0.01"
                                                               placeholder="{{translate('add_tax_percentage_arabic')}} *"
                                                               required="" value="{{old('tax')}}">
                                                        <label>{{translate('add_tax_percentage_arabic')}} *</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-sm-4">
                                                <div class="d-flex flex-column align-items-center gap-3">
                                                    <p class="mb-0">{{translate('cover_image')}}</p>
                                                    <div>
                                                        <div class="upload-file">
                                                            <input type="file" class="upload-file__input"
                                                                   name="cover_image">
                                                            <div
                                                                class="upload-file__img upload-file__img_banner">
                                                                <img
                                                                    src="{{asset('public/assets/admin-module')}}/img/media/banner-upload-file.png"
                                                                    alt="">
                                                            </div>
                                                            <span class="upload-file__edit">
                                                                <span class="material-icons">edit</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <p class="opacity-75 max-w220 mx-auto">{{translate('Image format - jpg, png,
                                                        jpeg, gif Image Size - maximum size 2 MB Image Ratio - 3:1')}}</p>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-sm-2 mb-5 mb-sm-0">
                                                <div class="d-flex flex-column align-items-center gap-3">
                                                    <p class="mb-0">{{translate('thumbnail_image')}}</p>
                                                    <div>
                                                        <div class="upload-file">
                                                            <input type="file" class="upload-file__input"
                                                                   name="thumbnail">
                                                            <div class="upload-file__img">
                                                                <img
                                                                    src="{{asset('public/assets/admin-module')}}/img/media/upload-file.png"
                                                                    alt="">
                                                            </div>
                                                            <span class="upload-file__edit">
                                                                <span class="material-icons">edit</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <p class="opacity-75 max-w220 mx-auto">{{translate('Image format - jpg, png,
                                                        jpeg,
                                                        gif Image
                                                        Size -
                                                        maximum size 2 MB Image Ratio - 1:1')}}</p>
                                                </div>
                                            </div>

                                            {{--English Field Short Descr--}}
                                            <div class="col-lg-6 mb-5 mt-5 mb-lg-0">
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <textarea type="text" class="form-control"
                                                                  name="short_description">{{old('short_description')}}</textarea>
                                                        <label>{{translate('short_description_english')}} *</label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{--Arabic Field Short Descr--}}
                                            <div class="col-lg-6 mb-5 mt-5 mb-lg-0">
                                                <div class="mb-30">
                                                    <div class="form-floating">
                                                        <textarea type="text" class="form-control"
                                                                  name="arabic_short_description">{{old('short_description')}}</textarea>
                                                        <label>{{translate('short_description_arabic_2')}} *</label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{--English Field Descr--}}
                                            <div class="col-6 mt-5">
                                                <label>{{translate('description_english')}} *</label>
                                                <section id="editor">
                                                    <textarea class="ckeditor"
                                                              name="description">{{old('description')}}</textarea>
                                                </section>
                                            </div>

                                            {{--Arabic Field Descr--}}
                                            <div class="col-6 mt-5">
                                                <label>{{translate('description_arabic')}} *</label>
                                                <section id="editor">
                                                    <textarea class="ckeditor"
                                                              name="arabic_description">{{old('description')}}</textarea>
                                                </section>
                                            </div>
                                        </div>
                                    </section>

                                    <h3>{{translate('price_variation')}}</h3>
                                    <section>
                                        <div class="d-flex flex-wrap gap-20 mb-3">
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" name="variant_name"
                                                       id="variant-name"
                                                       placeholder="{{translate('add_variant')}} *" required="">
                                                <label>{{translate('add_variant')}} *</label>
                                            </div>
                                            <div class="form-floating flex-grow-1">
                                                <input type="number" class="form-control" name="variant_price"
                                                       id="variant-price"
                                                       placeholder="{{translate('price')}} *" required=""
                                                       value="0">
                                                <label>{{translate('price')}} *</label>
                                            </div>
                                            <button type="button" class="btn btn--primary"
                                                    onclick="ajax_variation('{{route('admin.service.ajax-add-variant')}}','variation-table')">
                                                <span class="material-icons">add</span>
                                                {{translate('add')}}
                                            </button>
                                        </div>

                                        <div class="table-responsive p-01">
                                            <table class="table align-middle table-variation">
                                                <thead id="category-wise-zone">
                                                @include('servicemanagement::admin.partials._category-wise-zone',['zones'=>session()->has('category_wise_zones')?session('category_wise_zones'):[]])
                                                </thead>
                                                <tbody id="variation-table">
                                                @include('servicemanagement::admin.partials._variant-data',['zones'=>session()->has('category_wise_zones')?session('category_wise_zones'):[]])
                                                </tbody>
                                            </table>
                                        </div>
                                    </section>
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
    <script src="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.js-select').select2();
            $('.subcategory-select').select2({
                placeholder: "Choose Subcategory (English)"
            });
            $('.subcategory-select-arabic').select2({
                placeholder: "اختر التصنيف الفرعي (عربي)"
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
                    console.log(response.template);
                    // console.log(response.template_for_variant);
                    /* console.log(response.template) */
                    // 25-05-23 Pc1
                    $('.select2-results__option,.select2-search__field,.select2-search--dropdown').remove();
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

        function ajax_switch_category_arabic(route) {
            $.get({
                url: route,
                dataType: 'json',
                data: {},
                beforeSend: function () {
                    /*$('#loading').show();*/
                },
                success: function (response) {
                    console.log(response.template);
                    // 25-05-23 Pc1
                    $('.select2-results__option,.select2-search__field,.select2-search--dropdown').remove();
                    // 25-05-23 Pc1 Close
                    $('#sub-category-selector-arabic').html(response.template);
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
