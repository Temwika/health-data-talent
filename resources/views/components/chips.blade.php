@props(['name', 'label', 'options' => []])

@php
    $checked = (array) old($name, []);
@endphp
<div class="field">
    <span class="label" id="l-{{ $name }}">{{ $label }}</span>
    <div class="chips" role="group" aria-labelledby="l-{{ $name }}">
        @foreach ($options as $option)
            <label class="chip">
                <input type="checkbox" name="{{ $name }}[]" value="{{ $option }}" @checked(in_array($option, $checked, true))>
                <span>{{ $option }}</span>
            </label>
        @endforeach
    </div>
    @error($name.'.*')<span class="err">{{ $message }}</span>@enderror
</div>
