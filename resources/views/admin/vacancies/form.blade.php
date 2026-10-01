@extends('layouts.admin')

@section('title', $vacancy->exists ? 'Edit vacancy' : 'Add a vacancy')
@section('handles-errors', '1')

@section('content')
<p><a class="textlink back" href="{{ route('admin.vacancies.index') }}">All jobs</a></p>
<h1>{{ $vacancy->exists ? 'Edit vacancy' : 'Add a vacancy' }}</h1>

<form class="card narrow-form" method="post" action="{{ $vacancy->exists ? route('admin.vacancies.update', $vacancy) : route('admin.vacancies.store') }}">
    @csrf
    @if ($vacancy->exists) @method('put') @endif

    <div class="row">
        <x-field name="title" label="Job title" :value="$vacancy->title" required maxlength="120" />
        <x-field name="organisation_name" label="Organisation (as shown publicly)" :value="$vacancy->organisation_name" required maxlength="120" />
        <x-field name="location" label="Location" :value="$vacancy->location" required maxlength="80" />
        <x-select name="pattern" label="Working pattern" :options="config('hdt.patterns')" :value="$vacancy->pattern" required />
        <x-field name="salary" label="Salary range" :value="$vacancy->salary" required maxlength="60" />
        <x-select name="contract_type" label="Contract type" :options="config('hdt.contract_types')" :value="$vacancy->contract_type" :placeholder="false" required />
        <x-select name="area" label="Area" :options="config('hdt.areas')" :value="$vacancy->area" required />
    </div>
    <x-field name="skills" label="Essential skills" hint="Separate with commas" :value="$vacancy->skills" required maxlength="300" />
    <x-textarea name="description" label="Description" :value="$vacancy->description" required maxlength="4000" rows="10" />
    <div class="row">
        <x-field name="contact_name" label="Employer contact name" :value="$vacancy->contact_name" maxlength="100" />
        <x-field name="contact_email" label="Employer contact email" type="email" :value="$vacancy->contact_email" maxlength="160" />
    </div>

    <div class="form-actions">
        <button class="btn primary" type="submit">Save vacancy</button>
    </div>
</form>
@endsection
