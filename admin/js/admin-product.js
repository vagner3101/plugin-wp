jQuery(document).ready(function($) {
    // Função para mostrar/ocultar campos baseado no tipo de venda
    function toggleFields() {
        var tipoVenda = $('#_tipo_venda').val();
        $('.metro_quadrado_fields, .metro_linear_fields, .quantidade_fields, .acabamentos_fields').hide();
        $('.' + tipoVenda + '_fields').show();
    }

    // Inicializar campos
    toggleFields();

    // Atualizar campos quando o tipo de venda mudar
    $('#_tipo_venda').change(toggleFields);

    // Função para mostrar/ocultar o campo de valor da criação de arte
    function toggleValorCriacaoArte() {
        if ($('#_tem_criacao_arte').is(':checked')) {
            $('#_valor_criacao_arte').closest('.form-field').show();
        } else {
            $('#_valor_criacao_arte').closest('.form-field').hide();
        }
    }

    // Inicializar o estado do campo de valor da criação de arte
    toggleValorCriacaoArte();

    // Atualizar o estado quando o checkbox for alterado
    $('#_tem_criacao_arte').change(toggleValorCriacaoArte);

    // Função para atualizar a miniatura do ícone
    function updateIconPreview(select) {
        var selectedOption = select.options[select.selectedIndex];
        var imageUrl = selectedOption.getAttribute('data-imagem');
        var previewImg = select.parentNode.querySelector('.gabarito-icone-preview');
        previewImg.src = imageUrl;
    }

    // Atualizar miniaturas existentes
    $('.gabarito-icone-select').each(function() {
        updateIconPreview(this);
    });

    // Atualizar miniatura quando uma nova opção é selecionada
    $(document).on('change', '.gabarito-icone-select', function() {
        updateIconPreview(this);
    });

    // Adicionar novo gabarito
    $('.add_gabarito').click(function() {
        var count = $('.gabarito_field').length;
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_gabarito_field',
                count: count,
                security: grafica_rapida_ajax.nonce
            },
            success: function(response) {
                $('.gabaritos_fields').append(response);
                // Atualizar miniatura para o novo campo
                $('.gabarito-icone-select').last().each(function() {
                    updateIconPreview(this);
                });
            }
        });
    });

    // Remover gabarito
    $(document).on('click', '.remove_gabarito', function() {
        $(this).closest('.gabarito_field').remove();
    });

    // Inicializar select2 para acabamentos
    $('.select2').select2();
});
