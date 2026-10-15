<x-filament-widgets::widget>
    <x-filament::section>
        <div class="fa-hero">
            <div>
                <h2>Welcome back, {{ $name }}</h2>
                <p>{{ now()->format('l, j F Y') }} · Keep the site up to date from here.</p>
            </div>
        </div>

        <div class="fa-actions">
            @foreach ($actions as $action)
                <a class="fa-action" href="{{ $action['url'] }}" @if ($action['external'] ?? false) target="_blank" rel="noopener noreferrer" @endif>
                    <x-filament::icon :icon="$action['icon']" />
                    <div>
                        <strong>{{ $action['label'] }}</strong>
                        <span>{{ $action['hint'] }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
