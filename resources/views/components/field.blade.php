@props(['name', 'label', 'type' => 'text', 'required' => false, 'hint' => null, 'value' => null])

<div class="field">
    <label for="f-{{ $name }}">
        {{ $label }}
        @if ($required)<span class="req" aria-hidden="true">*</span>@endif
        @if ($hint)<span class="hint">{{ $hint }}</span>@endif
    </label>
    <input id="f-{{ $name }}" name="{{ $name }}" type="{{ $type }}"
        @unless ($type === 'password') value="{{ old($name, $value) }}" @endunless
        @required($required)
        @error($name) aria-invalid="true" aria-describedby="f-{{ $name }}-err" @enderror
        {{ $attributes }}>
    @error($name)<span class="err" id="f-{{ $name }}-err">{{ $message }}</span>@enderror
</div>
