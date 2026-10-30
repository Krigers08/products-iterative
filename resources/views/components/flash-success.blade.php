@props(['message' => session('success')])

@if ($message)
    <div {{ $attributes->merge(['class' => 'flash flash-success']) }} role="status" aria-live="polite">
        {{ $message }}
    </div>
@endif
