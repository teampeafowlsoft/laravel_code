@extends('adminmodule::layouts.master')

@section('title',translate('customer_update'))

@push('css_or_js')

@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('customer_update')}}</h2>
                    </div>

                    <div class="card">
                        <div class="card-body p-30">
                            <form action="{{route('admin.customer.update',[$customer['id']])}}" method="post" enctype="multipart/form-data"
                                  id="customer-update-form">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-30">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="first_name"
                                                       placeholder="{{translate('first_name')}} *"
                                                       required="" value="{{$customer['first_name']}}">
                                                <label>{{translate('first_name')}} *</label>
                                            </div>
                                        </div>

                                        <div class="mb-30">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="last_name"
                                                       placeholder="{{translate('last_name')}} *"
                                                       required="" value="{{$customer['last_name']}}">
                                                <label>{{translate('last_name')}} *</label>
                                            </div>
                                        </div>

                                        <div class="mb-30">
                                            <div class="form-floating">
                                                <input type="email" class="form-control" name="email"
                                                       placeholder="{{translate('ex: abc@email.com')}} *"
                                                       required="" value="{{$customer['email']}}">
                                                <label>{{translate('email')}} *</label>
                                            </div>
                                        </div>

                                        <div class="mb-30">
                                            <div class="row">
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
                                                               required="" value="{{substr($customer['country_code'],1)}}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');">
                                                        <label>
                                                            {{translate('country_code')}}
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-8">
                                                    <div class="form-floating">
                                                        <input type="tel" class="form-control" name="mobile" id="mobile"
                                                               placeholder="{{translate('mobile')}} *"
                                                               required="" value="{{$customer['mobile']}}">
                                                        <input type="hidden" id="phoneNumber" name="phone" value="{{$customer['phone']}}">
                                                        <label>
                                                            {{translate('Phone')}}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-30">
                                            <div class="form-floating">
                                                <input type="password" class="form-control" name="password"
                                                    placeholder="{{translate('ex: password')}} *">
                                                <label>{{translate('password')}} *</label>
                                                <span class="material-icons togglePassword">visibility_off</span>
                                            </div>
                                        </div>

                                        <div class="mb-30">
                                            <div class="form-floating">
                                                <input type="password" class="form-control" name="confirm_password"
                                                    placeholder="{{translate('confirm_password')}} *">
                                                <label>{{translate('confirm_password')}} *</label>
                                                <span class="material-icons togglePassword">visibility_off</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="d-flex flex-column align-items-center gap-3">
                                            <p class="mb-0">{{translate('profile_image')}}</p>
                                            <div>
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input" name="profile_image">
                                                    <div class="upload-file__img">
                                                        <img
                                                            src="{{asset('storage/app/public/user/profile_image')}}/{{$customer->profile_image}}"
                                                            onerror="this.src='{{asset('public/assets/admin-module')}}/img/media/upload-file.png'"
                                                            alt="">
                                                    </div>
                                                    <span class="upload-file__edit">
                                                        <span class="material-icons">edit</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <p class="opacity-75 max-w220 mx-auto">
                                                {{translate('Image format - jpg, png,jpeg,gif')}}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-20 mt-30">
                                            <button class="btn btn--secondary"
                                                    type="reset">{{translate('reset')}}</button>
                                            <button class="btn btn--primary" type="submit">
                                                {{translate('submit')}}
                                            </button>
                                        </div>
                                    </div>
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
        $(document).ready(function (){
            $("#mobile,#country_code").keyup(function (){
                var countryCode = $("#country_code").val();
                var mobileNumber = $("#mobile").val();
                $("#phoneNumber").val("+"+countryCode+mobileNumber);
            });
        });
    </script>
@endpush
