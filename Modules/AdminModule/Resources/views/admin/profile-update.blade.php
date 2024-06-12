@extends('adminmodule::layouts.master')

@section('title',translate('profile_update'))

@push('css_or_js')

@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('Update_Profile')}}</h2>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.profile_update') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row gx-2 mt-2">
                                    <div class="col-md-6">
                                        <div class="radius-10 h-100">
                                            <div class="card-body">
                                                <h4 class="c1 mb-20">{{translate('Information')}}</h4>
                                                <div class="row gx-2">
                                                    <div class="col-lg-6">
                                                        <div class="form-floating mb-30">
                                                            <input type="text" class="form-control" name="first_name" value="{{ auth()->user()->first_name }}"
                                                                   placeholder="{{translate('First_Name')}}">
                                                            <label>{{translate('First_Name')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-floating mb-30">
                                                            <input type="text" class="form-control" name="last_name" value="{{ auth()->user()->last_name }}"
                                                                   placeholder="{{translate('Last_Name')}}">
                                                            <label>{{translate('Last_Name')}}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-floating mb-30">
                                                    <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}"
                                                           placeholder="{{translate('Email')}}">
                                                    <label>{{translate('Email')}}</label>
                                                </div>
                                                <div class="row mb-30">
                                                    <div class="col-1">
                                                        <div class="sign form-floating">
                                                            <input type="text" readonly class="form-control disabled px-0  text-center" name="sign"
                                                                   placeholder="+"
                                                                   required="" value="+">
                                                        </div>
                                                    </div>
                                                    <div class="col-3 px-0">
                                                        <div class="countryCode form-floating">
                                                            <input type="text" class="form-control" name="country_code" id="country_code"
                                                                   placeholder="{{translate('country_code')}} *"
                                                                   required="" value="{{substr(auth()->user()->country_code,1)}}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');">
                                                            <label>{{translate('country_code')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="form-floating">
                                                            <input oninput="this.value = this.value.replace(/[^+\d]+$/g, '').replace(/(\..*)\./g, '$1');" type="tel" class="form-control" id="phone" name="phone" value="{{ auth()->user()->mobile }}"
                                                                   placeholder="{{translate('Phone')}}" required>
                                                            <label>{{translate('Phone')}}</label>
                                                            <input type="hidden" id="phoneNumber" name="signaccountphone">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row gx-2">
                                                    <div class="col-lg-6">
                                                        <div class="form-floating mb-30">
                                                            <input type="password" class="form-control" name="password"
                                                                   placeholder="{{translate('Password')}}">
                                                            <label>{{translate('Password')}}</label>
                                                            <span class="material-icons togglePassword">visibility_off</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-floating mb-30">
                                                            <input type="password" class="form-control" name="confirm_password"
                                                                   placeholder="{{translate('Confirm_Password')}}">
                                                            <label>{{translate('Confirm_Password')}}</label>
                                                            <span class="material-icons togglePassword">visibility_off</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex flex-column align-items-center gap-3">
                                            <h3 class="mb-0">{{translate('profile_image')}}</h3>
                                            <div>
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input" name="profile_image">
                                                    <div class="upload-file__img">
                                                        <img
                                                            src="{{asset('storage/app/public/user/profile_image')}}/{{ auth()->user()->profile_image }}"
                                                            onerror="this.src='{{asset('public/assets/admin-module')}}/img/media/upload-file.png'"
                                                            alt="">
                                                    </div>
                                                    <span class="upload-file__edit">
                                                        <span class="material-icons">edit</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <p class="opacity-75 max-w220 mx-auto">
                                                {{translate('Image format - jpg, png,jpeg, gif Image Size -maximum size 10 MB Image Ratio - 1:1')}}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-4 flex-wrap justify-content-end mt-20">
                                    <button type="reset" class="btn btn--secondary">{{translate('Reset')}}</button>
                                    <button type="submit" class="btn btn--primary demo_check">{{translate('update')}}</button>
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
    <script>
        $(document).ready(function (){
            $("#country_code,#phone").keyup(function (){
                var countryCode = $("#country_code").val();
                var mobileNumber = $("#phone").val();
                $("#phoneNumber").val("+"+countryCode+mobileNumber);
            });
        });
    </script>
@endpush
