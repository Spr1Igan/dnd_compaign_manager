@props([
    'id',
    'title' => 'Подтвердить действие',
    'message' => 'Это действие нельзя отменить.',
    'confirmText' => 'Подтвердить',
    'cancelText' => 'Отмена',
])

<div class="confirm-overlay" data-confirm-dialog="{{ $id }}" hidden>
    <section class="confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
        <p class="eyebrow">Проверка действия</p>
        <h2 id="{{ $id }}-title">{{ $title }}</h2>
        <p>{{ $message }}</p>

        <div class="confirm-actions">
            <button class="paper-button danger" type="button" data-confirm-submit>
                {{ $confirmText }}
            </button>

            <button class="paper-button secondary" type="button" data-confirm-cancel>
                {{ $cancelText }}
            </button>
        </div>
    </section>
</div>
