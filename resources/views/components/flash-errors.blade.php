@props(['message' => session('error')])

@if ($message || $errors->any())
    <div {{ $attributes->merge(['class' => 'flash flash-errors']) }} role="alert" aria-live="assertive">
        @if ($message)
            <p class="flash-title">{{ $message }}</p>
        @endif

        @if ($errors->any())
            <p class="flash-title">Lūdzu, izlabojiet šādas kļūdas:</p>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
