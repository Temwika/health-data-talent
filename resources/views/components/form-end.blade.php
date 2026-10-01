@props(['submit', 'terms' => false])

{{-- Honeypot, consent tick and submit button shared by the public forms. --}}
<div class="hp" aria-hidden="true">
    <label for="f-website">Leave this empty</label>
    <input id="f-website" name="website" type="text" tabindex="-1" autocomplete="off">
</div>

<label class="check">
    <input type="checkbox" name="privacy" value="1" required @checked(old('privacy')) @error('privacy') aria-invalid="true" @enderror>
    <span>
        I have read the <a href="{{ route('privacy') }}">privacy notice</a>@if ($terms) and the <a href="{{ route('terms') }}">terms of business</a>@endif
        and understand how my information will be used. <span class="req" aria-hidden="true">*</span>
    </span>
</label>
@error('privacy')<span class="err">{{ $message }}</span>@enderror

{{ $slot }}

<div class="form-actions">
    <button class="btn primary" type="submit">{{ $submit }}</button>
</div>
