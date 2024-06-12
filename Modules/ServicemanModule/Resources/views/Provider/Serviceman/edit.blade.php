@extends('providermanagement::layouts.master')

@section('title',translate('Update_Serviceman'))

@push('css_or_js')

@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('update_serviceman')}}</h2>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form action="{{route('provider.serviceman.update',[$serviceman->id])}}" method="post"
                                  enctype="multipart/form-data">
                                @method('put')
                                @csrf

                                <h4 class="c1 mb-20">{{translate('General_Information')}}</h4>
                                <div class="row gx-xl-5">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-30">
                                            <input type="text" class="form-control" name="first_name"
                                                    placeholder="{{translate('First_name')}}"
                                                    value="{{$serviceman->user->first_name}}" required>
                                            <label>{{translate('First_name')}}</label>
                                        </div>
                                        <div class="row mb-30">
                                            <div class="col-1">
                                                <div class="form-floating">
                                                    <input type="text" readonly class="form-control disabled p-0  text-center" name="sign"
                                                           placeholder="+"
                                                           required="" value="+">
                                                </div>
                                            </div>
                                            <div class="col-3 px-0">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" name="country_code" id="country_code"
                                                           placeholder="{{translate('country_code')}} *"
                                                           required="" value="{{substr($serviceman->user->country_code,1)}}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');">
                                                    <label>
                                                        {{translate('country_code')}}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="mobile" name="mobile"
                                                           placeholder="{{translate('Phone_number')}}"
                                                           value="{{$serviceman->user->mobile}}" required>
                                                    <input type="hidden" id="phoneNumber" name="phone" value="{{$serviceman->user->phone}}">
                                                    <label>
                                                        {{translate('Phone')}}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-floating mb-30">
                                            <input type="text" class="form-control" name="identity_number"
                                                    placeholder="Identity Number"
                                                    value="{{$serviceman->user->identification_number}}" required>
                                            <label>{{translate('Identity_Number')}}</label>
                                        </div>
                                        <div class="d-flex flex-column align-items-center gap-3 mb-30">
                                            <h3 class="mb-0">{{translate('Serviceman_image')}}</h3>
                                            <div>
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input"
                                                            name="profile_image">
                                                    <div class="upload-file__img">
                                                        <img
                                                            src="{{asset('storage/app/public/serviceman/profile')}}/{{$serviceman->user->profile_image}}"
                                                            onerror="this.src='{{asset('public/assets/admin-module/img/media/upload-file.png')}}'"
                                                            alt="">
                                                    </div>
                                                    <span class="upload-file__edit">
                                                        <span class="material-icons">edit</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <p class="opacity-75 max-w220 mx-auto">{{translate('Image format - jpg, png,jpeg,gif')}}</p>
                                        </div>

                                        <h4 class="c1 mb-30">{{translate('Account_Information')}}</h4>
                                        <div class="form-floating mb-30">
                                            <input type="email" class="form-control" name="email"
                                                    placeholder="{{translate('Email_*')}}"
                                                    value="{{$serviceman->user->email}}" required>
                                            <label>{{translate('Email_*')}}</label>
                                        </div>
                                        <div class="form-floating mb-30">
                                            <input type="password" class="form-control" name="password"
                                                    placeholder="{{translate('Password')}}">
                                            <label>{{translate('Password')}}</label>
                                            <span class="material-icons togglePassword">visibility_off</span>
                                        </div>
                                        <div class="form-floating mb-30">
                                            <input type="password" class="form-control"
                                                    name="confirm_password"
                                                    placeholder="{{translate('Confirm_Password')}}">
                                            <label>{{translate('Confirm_Password')}}</label>
                                            <span class="material-icons togglePassword">visibility_off</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-30">
                                            <input type="text" class="form-control" name="last_name"
                                                    placeholder="{{translate('Last_name')}}"
                                                    value="{{$serviceman->user->last_name}}" required>
                                            <label>{{translate('Last_name')}}</label>
                                        </div>

                                        @php($id_types=['passport','driving_license','company_id','nid','trade_license'])
                                        <select class="select-identity theme-input-style w-100 mb-30"
                                                name="identity_type" required>
                                            <option selected disabled>{{translate('Select_Identity_Type')}}</option>
                                            @foreach($id_types as $type)
                                                <option value="{{$type}}" {{$type==$serviceman->user->identification_type?'selected':''}}>{{translate($type)}}</option>
                                            @endforeach
                                        </select>

                                        <div class="d-flex flex-column align-items-center gap-3 mb-30">
                                            <h3 class="mb-0">{{translate('Identification_Image')}}</h3>
                                            <div id="multi_image_picker">
                                                @foreach($serviceman->user->identification_image as $img)
                                                    <img src="{{asset('storage/app/public/serviceman/identity').'/'.$img}}">
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-4 flex-wrap justify-content-end">
                                    <button type="reset" class="btn btn--secondary">{{translate('Reset')}}</button>
                                    <button type="submit" class="btn btn--primary">{{translate('update')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')
    <script src="{{asset('public/assets/provider-module')}}/js/spartan-multi-image-picker.js"></script>
    <script>
        $("#multi_image_picker").spartanMultiImagePicker({
                fieldName: 'identity_image[]',
                maxCount: 2,
                rowHeight: 'auto',
                groupClassName: 'multi_image_picker_item',
                //maxFileSize: '',
                dropFileLabel: "{{translate('Drop_here')}}",
                placeholderImage: {
                    image: '{{asset('public/assets/provider-module')}}/img/media/identity-img.png',
                    width: '100%',
                },

                onRenderedPreview: function (index) {
                    toastr.success('{{translate('Image_added')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onRemoveRow: function (index) {
                    console.log(index);
                },
                onExtensionErr: function (index, file) {
                    toastr.error('{{translate('Please_only_input_png_or_jpg_type_file')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{translate('File_size_too_big')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }
        );
    </script>
    <script>
        $(document).ready(function (){
            $("#mobile,#country_code").keyup(function (){
                var countryCode = $("#country_code").val();
                var mobileNumber = $("#mobile").val();
                $("#phoneNumber").val("+"+countryCode+mobileNumber);
            });
        });
    </script>
@endpush
