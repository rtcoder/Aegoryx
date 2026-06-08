@props([
    'title' => null,
    'subtitle' => null,
    'padded' => true,
])

<section {{ $attributes->class('ui-card') }}>
    @if ($title || $subtitle)
        <div class="ui-card-header">
            @if ($title)
                <h2 class="ui-heading-2">{{ $title }}</h2>
            @endif

            @if ($subtitle)
                <p class="ui-body mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div @class(['ui-card-body' => $padded])>
        {{ $slot }}
    </div>
</section>
