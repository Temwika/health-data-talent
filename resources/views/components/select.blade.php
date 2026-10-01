@props(['name', 'label', 'options' => [], 'required' => false, 'placeholder' => 'Choose', 'value' => null])

@php
    $current = (string) old($name, $value);
    $isList = array_is_list($options);
@endphp
<div class="field">
    <label for="f-{{ $name }}">
        {{ $label }}
        @if ($required)<span class="req" aria-hidden="true">*</span>@endif
    </label>
    <select id="f-{{ $name }}" name="{{ $name }}"
        @required($required)
        @error($name) aria-invalid="true" aria-describedby="f-{{ $name }}-err" @enderror
        {{ $attributes }}>
        @if ($placeholder !== false)<option value="">{{ $placeholder }}</option>@endif
        @foreach ($options as $key => $text)
            <option value="{{ $isList ? $text : $key }}" @selected($current === (string) ($isList ? $text : $key))>{{ $text }}</option>
        @endforeach
    </select>
    @error($name)<span class="err" id="f-{{ $name }}-err">{{ $message }}</span>@enderror
</div>
