@extends('adminmodule::layouts.master')

@section('title',translate('zone_setup'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/select.dataTables.min.css"/>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('zone_setup')}}</h2>
                    </div>

                    <!-- Instructions -->
                    <div class="card zone-setup-instructions mb-30">
                        <div class="card-body p-30">
                            <form action="{{route('admin.zone.store')}}" enctype="multipart/form-data" method="POST">
                                @csrf
                                <div class="row justify-content-between">
                                    <div class="col-lg-5 col-xl-4 mb-5 mb-lg-0">
                                        <h4 class="mb-3 c1">{{translate('instructions')}}</h4>
                                        <div class="d-flex flex-column">
                                            <p>{{translate('create_zone_by_click_on_map_and_connect_the_dots_together')}}</p>

                                            <div class="media mb-2 gap-3 align-items-center">
                                                <img
                                                    src="{{asset('public/assets/admin-module')}}/img/icons/map-drag.png"
                                                    alt="">
                                                <div class="media-body ">
                                                    <p>{{translate('use_this_to_drag_map_to_find_proper_area')}}</p>
                                                </div>
                                            </div>

                                            <div class="media gap-3 align-items-center">
                                                <img
                                                    src="{{asset('public/assets/admin-module')}}/img/icons/map-draw.png"
                                                    alt="">
                                                <div class="media-body ">
                                                    <p>{{translate('click_this_icon_to_start_pin_points_in_the_map_and_connect_them_
                                                        to_draw_a_
                                                        zone_._Minimum_3_points_required')}}</p>
                                                </div>
                                            </div>
                                            <div class="map-img mt-4">
                                                <img src="{{asset('public/assets/admin-module')}}/img/instructions.gif"
                                                     alt="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <input type="hidden" name="group_id" value="{{$zone_grp_id}}">
                                        <!-- Nav Tabs -->
                                        <div class="mb-3">
                                            <ul class="nav nav--tabs nav--tabs__style2">
                                                <input type="hidden" name="eng_lang_id" id="eng_lang_id"
                                                       value="{{$languages[0]->language_master_id}}">
                                                <li class="nav-item">
                                                    <label data-bs-toggle="tab" data-bs-target="#english"
                                                           class="nav-link active" id="english_lang">
                                                        {{$languages[0]->language_name}}
                                                        {{--                                                        {{translate('english')}}--}}
                                                    </label>
                                                </li>
                                                <li class="nav-item">
                                                    <input type="hidden" name="arabic_lang_id" id="arabic_lang_id"
                                                           value="{{$languages[1]->language_master_id}}">
                                                    <label data-bs-toggle="tab" data-bs-target="#arabic"
                                                           class="nav-link" id="arabic_lang">
                                                        {{$languages[1]->language_name}}
                                                        {{--                                                        {{translate('arabic')}}--}}
                                                    </label>
                                                </li>
                                            </ul>
                                        </div>
                                        <hr>
                                        <!-- End Nav Tabs -->

                                        <!-- Tab Content -->
                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="english">
                                                <div class="card">
                                                    <div class="card-body p-30">
                                                        <div class="form-floating mb-30">
                                                            <select class="form-select form-control" id="floatingSelect"
                                                                    aria-label="Select appropriate country"
                                                                    name="country_id" required>
                                                                <option value="">{{translate('Select')}}</option>
                                                                @foreach($countries as $country)
                                                                    <option value="{{$country->id}}"
                                                                        {{old('identity_type') == $country->id ? 'selected': ''}}>
                                                                        {{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            <label
                                                                for="floatingSelect">{{translate('Select_Country')}}</label>
                                                        </div>

                                                        <div class="form-floating mb-30">
                                                            <input type="text" class="form-control area_name"
                                                                   id="floatingInput" name="name" required
                                                                   placeholder="{{translate('zone_area_name')}}"
                                                                   value="{{old('name')}}">
                                                            <label
                                                                for="floatingInput">{{translate('zone_area_name')}}</label>
                                                        </div>

                                                        <div class="form-floating mb-30">
                                                            <input type="text" class="form-control area_charge"
                                                                   id="floatingInput" name="shipping_charge" required
                                                                   placeholder="{{translate('shipping_charge')}}"
                                                                   value="{{old('shipping_charge')}}" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="7">
                                                            <label
                                                                for="floatingInput">{{translate('shipping_charge')}}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="arabic">
                                                <div class="card">
                                                    <div class="card-body p-30">
                                                        <div class="discount-type">
                                                            <div class="row">
                                                                <div class="col-md-12 col-12">
                                                                    <div class="form-floating mb-30">
                                                                        <select class="form-select form-control"
                                                                                id="floatingSelect"
                                                                                aria-label="Select appropriate country"
                                                                                name="arabic_country_id" required>
                                                                            <option
                                                                                value="">{{translate('Select_arabic')}}</option>
                                                                            @foreach($arabic_countries as $country)
                                                                                <option value="{{$country->id}}"
                                                                                    {{old('identity_type') == $country->id ? 'selected': ''}}>
                                                                                    {{$country->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <label
                                                                            for="floatingSelect">{{translate('Select_Country_arabic')}}</label>
                                                                    </div>

                                                                    <div class="form-floating mb-30">
                                                                        <input type="text"
                                                                               class="form-control arabic_area_name"
                                                                               name="arabic_name" required
                                                                               placeholder="{{translate('zone_area_name_arabic')}}"
                                                                               value="{{old('name')}}">
                                                                        <label
                                                                            for="">{{translate('zone_area_name_arabic')}}</label>
                                                                    </div>

                                                                    <div class="form-floating mb-30">
                                                                        <input type="text" class="form-control arabic_area_charge"
                                                                               id="floatingInput" name="shipping_charge_arabic" required
                                                                               placeholder="{{translate('shipping_charge_arabic')}}"
                                                                               value="{{old('shipping_charge')}}" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="7">
                                                                        <label
                                                                            for="floatingInput">{{translate('shipping_charge_arabic')}}</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Tab Content -->

                                        <div class="form-group mb-3" style="display: none">
                                            <label class="input-label"
                                                   for="exampleFormControlInput1">{{translate('coordinates')}}
                                                <span
                                                    class="input-label-secondary">{{translate('draw_your_zone_on_the_map')}}</span>
                                            </label>
                                            <textarea type="text" rows="8" name="coordinates" id="coordinates"
                                                      class="form-control" readonly></textarea>
                                        </div>

                                        <!-- Start Map -->
                                        <div class="map-warper overflow-hidden" style="border-radius: 5px;">
                                            <input id="pac-input" class="controls rounded"
                                                   style="height: 3em;width:fit-content;"
                                                   title="{{translate('search_your_location_here')}}" type="text"
                                                   placeholder="{{translate('search_here')}}"/>
                                            <div id="map-canvas" style="height: 310px"></div>
                                        </div>
                                        <!-- End Map -->
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-20 mt-30">
                                            <button class="btn btn--secondary" type="reset"
                                                    id="reset_btn">{{translate('reset')}}</button>
                                            <button class="btn btn--primary"
                                                    type="submit">{{translate('submit')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End Instructions -->

                    <div class="d-flex justify-content-end border-bottom mx-lg-4 mb-10">
                        <div class="d-flex gap-2 fw-medium">
                            <span class="opacity-75">{{translate('Total_Zones')}}:</span>
                            <span class="title-color">{{ $zones->total() }}</span>
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
                                                   href="{{route('admin.zone.download')}}?search={{$search}}">{{translate('excel')}}</a>
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
                                        <th>{{translate('country_name')}}</th>
                                        <th>{{translate('zone_area_name') . '(' . ('english') . ')'}}</th>
                                        <th>{{translate('zone_area_name') . '(' . ('arabic') . ')'}}</th>
                                        <th>{{translate('providers')}}</th>
                                        <th>{{translate('Category')}}</th>
                                        <th>{{translate('status')}}</th>
                                        <th>{{translate('action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $i=1;@endphp
                                    @foreach($zones as $key=>$zone)
                                        @php
                                            $prov = '';
                                            $customer = '';
                                            $cntry_nm = '';
                                        @endphp
                                        @foreach($countries as $key=>$cntr)
                                            @php
                                                if ($cntr->id == (explode(',',$zone->country_id)[0])) {
                                                $cntry_nm = $cntr->name;
                                    }
                                            @endphp
                                        @endforeach
                                        @foreach($providers as $key=>$provider)
                                            @php
                                                if ($provider->id == (explode(',',$zone->id)[0])) {
                                                      $prov .= $provider->providers_count;
                                                      $customer .= $provider->categories_count;
                                                      }
                                            @endphp
                                        @endforeach
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$cntry_nm}}</td>
                                            <td>{{explode(',',$zone->name)[0]}}</td>
                                            <td>{{explode(',',$zone->name)[1]}}</td>
                                            <td>{{$prov}}</td>
                                            <td>{{$customer}}</td>
                                            <td>
                                                <label class="switcher" data-bs-toggle="modal"
                                                       data-bs-target="#deactivateAlertModal">
                                                    <input class="switcher_input"
                                                           onclick="route_alert('{{route('admin.zone.status-update',[$zone->id])}}','{{translate('want_to_update_status')}}')"
                                                           type="checkbox" {{$zone->is_active?'checked':''}}>
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <div class="table-actions">
                                                    <a href="{{route('admin.zone.edit',[$zone->id,$zone->group_id])}}"
                                                       class="table-actions_edit demo_check">
                                                        <span class="material-icons">edit</span>
                                                    </a>
                                                    <button type="button"
                                                            @if(env('APP_ENV')!='demo')
                                                            onclick="form_alert('delete-{{$zone->group_id}}','{{translate('want_to_delete_this_zone')}}?')"
                                                            @endif
                                                            class="table-actions_delete bg-transparent border-0 p-0 demo_check"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteAlertModal">
                                                        <span class="material-icons">delete</span>
                                                    </button>
                                                    <form action="{{route('admin.zone.delete',[$zone->group_id])}}"
                                                          method="post" id="delete-{{$zone->group_id}}" class="hidden">
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
                                {!! $zones->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/dataTables.select.min.js"></script>

    <script>
        auto_grow();

        function auto_grow() {
            let element = document.getElementById("coordinates");
            element.style.height = "5px";
            element.style.height = (element.scrollHeight) + "px";
        }
    </script>

    @php($api_key=(business_config('google_map', 'third_party'))->live_values)
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{$api_key['map_api_key_client']}}&libraries=drawing,places&v=3.45.8"></script>

    <script>
        var map; // Global declaration of the map
        var drawingManager;
        var lastpolygon = null;
        var polygons = [];

        function resetMap(controlDiv) {
            // Set CSS for the control border.
            const controlUI = document.createElement("div");
            controlUI.style.backgroundColor = "#fff";
            controlUI.style.border = "2px solid #fff";
            controlUI.style.borderRadius = "3px";
            controlUI.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
            controlUI.style.cursor = "pointer";
            controlUI.style.marginTop = "8px";
            controlUI.style.marginBottom = "22px";
            controlUI.style.textAlign = "center";
            controlUI.title = "Reset map";
            controlDiv.appendChild(controlUI);
            // Set CSS for the control interior.
            const controlText = document.createElement("div");
            controlText.style.color = "rgb(25,25,25)";
            controlText.style.fontFamily = "Roboto,Arial,sans-serif";
            controlText.style.fontSize = "10px";
            controlText.style.lineHeight = "16px";
            controlText.style.paddingLeft = "2px";
            controlText.style.paddingRight = "2px";
            controlText.innerHTML = "X";
            controlUI.appendChild(controlText);
            // Setup the click event listeners: simply set the map to Chicago.
            controlUI.addEventListener("click", () => {
                lastpolygon.setMap(null);
                $('#coordinates').val('');
            });
        }

        @php($location = session('location'))

        function initialize() {
            var myLatlng = {
                lat: '{{$location['lat']}}',
                lng: '{{$location['lng']}}'
            };

            var myOptions = {
                zoom: 13,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
            }
            map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
            drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                },
                polygonOptions: {
                    editable: true
                }
            });
            drawingManager.setMap(map);

            //get current location block
            // infoWindow = new google.maps.InfoWindow();
            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        map.setCenter(pos);
                    });
            }

            google.maps.event.addListener(drawingManager, "overlaycomplete", function (event) {
                if (lastpolygon) {
                    lastpolygon.setMap(null);
                }
                $('#coordinates').val(event.overlay.getPath().getArray());
                lastpolygon = event.overlay;
                auto_grow();
            });

            const resetDiv = document.createElement("div");
            resetMap(resetDiv, lastpolygon);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(resetDiv);

            // Create the search box and link it to the UI element.
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
            // Bias the SearchBox results towards current map's viewport.
            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });
            let markers = [];

            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

                if (places.length === 0) {
                    return;
                }
                // Clear out the old markers.
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
                // For each place, get the icon, name and location.
                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    const icon = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25),
                    };
                    // Create a marker for each place.
                    markers.push(
                        new google.maps.Marker({
                            map,
                            icon,
                            title: place.name,
                            position: place.geometry.location,
                        })
                    );

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });
        }

        google.maps.event.addDomListener(window, 'load', initialize);

    </script>

    <script>
        $('#reset_btn').click(function () {
            $('#name').val(null);

            lastpolygon.setMap(null);
            $('#coordinates').val(null);
        })
    </script>

    <script>
        // $('#lang_id').val(1);
        // var id = $('#lang_id').val();
        // if (id == '1') {
        //     $(".area_name").addClass("required");
        // }
        // if (id == '2') {
        //     $(".area_name").removeClass("required");
        //     $(".arabic_area_name").addClass("required");
        // }
        // $('#arabic_lang').click(function () {
        //     $('#lang_id').val(2);
        //     $(".area_name").removeClass("required");
        //     $(".arabic_area_name").addClass("required");
        // });
        // $('#english_lang').click(function () {
        //     $('#lang_id').val(1);
        //     $(".area_name").addClass("required");
        //     $(".arabic_area_name").removeClass("required");
        // });
    </script>
@endpush
