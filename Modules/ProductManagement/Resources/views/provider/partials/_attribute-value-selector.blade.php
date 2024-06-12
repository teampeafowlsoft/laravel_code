<label
    for="unit"><strong>{{translate('attribute_value')}}</strong></label>
<select class="js-select theme-input-style w-100" name="packate_measurement_attribute_value[]">
    @foreach($attribute_val as $att_val)
        <option value="{{$att_val->id}}">{{$att_val->attribute_value}}</option>
    @endforeach
</select>

<script>
    $(document).ready(function () {
        $('.js-select').select2();
    });
</script>
