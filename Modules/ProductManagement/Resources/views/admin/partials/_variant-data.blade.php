{{--for add variants--}}
@if(session()->has('variations'))
    @foreach(session('variations') as $key=>$item)
        <tr>
            <th scope="row">
                {{$item['variant']}}
                <input name="variants[]" value="{{\Illuminate\Support\Str::slug($item['variant'])}}" class="hide-div">
            </th>
            <td>
                <input type="number" value="{{$item['price']}}" class="theme-input-style" id="default-set-{{$key}}" onkeyup="set_values('{{$key}}')">
            </td>
            @foreach($zones as $zone)
                <td>
                    <input type="number" name="{{$item['variant_key']}}_{{$zone->id}}_price" value="{{$item['price']}}" class="theme-input-style default-get-{{$key}}">
                </td>
            @endforeach
            <td>
                <a class="btn btn--danger" onclick="ajax_remove_variant('{{route('admin.service.ajax-remove-variant',[$item['variant_key']])}}','variation-table')">
                    <span class="material-icons">delete</span>
                </a>
            </td>
        </tr>
    @endforeach
@endif

<script>
    "use strict"
    function set_values(key){
        $('.default-get-'+key).val($('#default-set-'+key).val())
    }
</script>
