<li class="job">
    <div>
        <h3>
            <a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a>
            @if ($job->is_example)<span class="tag ex">Example listing</span>@endif
        </h3>
        <div class="meta">
            <span>{{ $job->organisation_name }}</span>
            <span>{{ $job->location }}</span>
            <span>{{ $job->pattern }}</span>
            <span>{{ $job->salary }}</span>
        </div>
    </div>
    <a class="btn ghost small" href="{{ route('jobs.show', $job) }}" aria-label="View role: {{ $job->title }}">View role</a>
</li>
