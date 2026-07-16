<article class="treasure-card">
    <span>{{ $item['category'] }}</span>
    <h3>{{ $item['name'] }}</h3>
    <div>
        @if ($item['type'])
            <b>{{ $item['type'] }}</b>
        @endif
        @if ($item['rarity'])
            <b>{{ $item['rarity'] }}</b>
        @endif
        @if ($item['cost'])
            <b>{{ $item['cost'] }}</b>
        @endif
        @if (($item['quantity'] ?? 1) > 1)
            <b>{{ $item['quantity'] }} шт.</b>
        @endif
    </div>
    @if ($item['description'])
        <p>{{ $item['description'] }}</p>
    @endif
    @if ($item['url'])
        <a href="{{ $item['url'] }}">Открыть карточку</a>
    @endif
</article>
