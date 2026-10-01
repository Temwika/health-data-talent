@props(['name', 'label', 'required' => false, 'hint' => null, 'value' => null])

<div class="field">
    <label for="f-{{ $name }}">
        {{ $label }}
        @if ($required)<span class="req" aria-hidden="true">*</span>@endif
        @if ($hint)<span class="hint">{{ $hint }}</span>@endif
    </label>
    <textarea id="f-{{ $name }}" name="{{ $name }}"
        @required($required)
        @error($name) aria-invalid="true" aria-describedby="f-{{ $name }}-err" @enderror
        {{ $attributes }}>{{ old($name, $value) }}</textarea>
    @error($name)<span class="err" id="f-{{ $name }}-err">{{ $message }}</span>@enderror
</div>
