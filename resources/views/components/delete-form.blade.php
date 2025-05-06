@props(['route', 'message' => 'Êtes-vous sûr de vouloir supprimer cet élément ?', 'buttonClass' => 'btn btn-sm btn-danger', 'icon' => 'bi bi-trash', 'buttonText' => 'Supprimer'])

<form method="POST" action="{{ $route }}" class="d-inline" onsubmit="return false;">
    @csrf
    @method('DELETE')
    <button type="button" class="{{ $buttonClass }}" onclick="confirmAction('{{ $message }}', () => this.closest('form').submit())">
        <i class="{{ $icon }}"></i> {{ $buttonText }}
    </button>
</form>
