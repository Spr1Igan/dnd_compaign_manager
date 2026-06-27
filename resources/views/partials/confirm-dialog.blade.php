@props([
    'id',
    'title' => null,
    'message' => null,
    'confirmText' => null,
    'cancelText' => null,
])

<div class="confirm-overlay" data-confirm-dialog="{{ $id }}" hidden>
    <section class="confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
        <p class="eyebrow">{{ __('ui.confirm_dialog.eyebrow') }}</p>
        <h2 id="{{ $id }}-title">{{ $title ?? __('ui.confirm_dialog.title') }}</h2>
        <p>{{ $message ?? __('ui.confirm_dialog.message') }}</p>

        <div class="confirm-actions">
            <button class="paper-button danger" type="button" data-confirm-submit>
                {{ $confirmText ?? __('ui.confirm') }}
            </button>

            <button class="paper-button secondary" type="button" data-confirm-cancel>
                {{ $cancelText ?? __('ui.cancel') }}
            </button>
        </div>
    </section>
</div>
