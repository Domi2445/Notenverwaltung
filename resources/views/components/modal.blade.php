@props(['id', 'title', 'action', 'submit' => 'Speichern'])
<dialog id="{{ $id }}" class="modal" aria-labelledby="{{ $id }}-title" onclick="if (event.target === this) this.close()">
    <form method="POST" action="{{ $action }}" class="modal-body">
        @csrf
        <div class="modal-header">
            <h2 id="{{ $id }}-title">{{ $title }}</h2>
            <button type="button" class="btn-icon" onclick="this.closest('dialog').close()" aria-label="Schließen">
                <x-icon name="x" />
            </button>
        </div>

        <div class="form-grid">
            {{ $slot }}
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="this.closest('dialog').close()">Abbrechen</button>
            <button type="submit" class="btn btn-primary">{{ $submit }}</button>
        </div>
    </form>
</dialog>
