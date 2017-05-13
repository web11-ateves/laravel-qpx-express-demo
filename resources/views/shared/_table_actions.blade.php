@if (isset($showUrl))
    <div class="btn-group btn-group-xs">
        <a title="Detalhes" class="btn btn-primary" href="{{ $showUrl }}">
            <i class="fa fa-eye"></i>
        </a>
    </div>
@endif

@if (isset($editUrl))
    <div class="btn-group btn-group-xs">
        <a title="Editar" class="btn btn-primary" href="{{ $editUrl }}">
            <i class="fa fa-edit"></i>
        </a>
    </div>
@endif

@if (isset($deleteUrl))
<div class="btn-group btn-group-xs">
    {{ csrf_field() }}
    <a title="Remover" class="btn btn-danger btn-block" href="javascript:void(0)" data-form-link data-confirm-title="Confirmação de exclusão" data-confirm-text="Deseja realmente excluir esse registro?" data-method="DELETE" data-token="{{ csrf_token() }}" data-action="{{ $deleteUrl }}">
        <i class="fa fa-trash"></i>
    </a>
</div>
@endif