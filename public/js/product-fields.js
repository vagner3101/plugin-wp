(function($) {
    'use strict';

    $(document).ready(function() {
        var $form = $('form.cart');
        var $priceElement = $('.price .woocommerce-Price-amount bdi');
        var originalPrice = parseFloat($priceElement.text().replace(/[^0-9.,]/g, '').replace(',', '.'));
        var currencySymbol = $priceElement.text().replace(/[0-9.,]/g, '').trim();

        console.log('Preço original:', originalPrice);
        console.log('Símbolo da moeda:', currencySymbol);

        function updatePrice() {
            var totalPrice = originalPrice;
            console.log('Iniciando cálculo. Preço base:', totalPrice);

            // Cálculo para Metro Quadrado
            if ($('.grafica-rapida-metro-quadrado').length) {
                var largura = parseFloat($('#largura_m2').val()) || 0;
                var altura = parseFloat($('#altura_m2').val()) || 0;
                var valorMinimo = parseFloat($('.grafica-rapida-metro-quadrado input[name="valor_minimo"]').val()) || 0;

                if (largura > 0 && altura > 0) {
                    var calculatedPrice = (largura * altura * originalPrice) / 10000;
                    totalPrice = Math.max(calculatedPrice, valorMinimo);
                    console.log('Metro Quadrado:', largura, 'x', altura, '=', totalPrice);
                }
            }

            // Cálculo para Metro Linear
            if ($('.grafica-rapida-metro-linear').length) {
                var largura = parseFloat($('#largura_ml').val()) || 0;
                var altura = parseFloat($('#altura_ml').val()) || 0;
                var valorMinimo = parseFloat($('.grafica-rapida-metro-linear input[name="valor_minimo"]').val()) || 0;

                if (largura > 0 && altura > 0) {
                    var calculatedPrice = (largura * altura * originalPrice) / 10000;
                    totalPrice = Math.max(calculatedPrice, valorMinimo);
                    console.log('Metro Linear:', largura, 'x', altura, '=', totalPrice);
                }
            }

            // Cálculo para Quantidade
            if ($('.grafica-rapida-quantidade').length) {
                var selectedPrice = parseFloat($('#quantidade option:selected').data('preco'));
                if (!isNaN(selectedPrice)) {
                    totalPrice = selectedPrice;
                    console.log('Quantidade selecionada. Novo preço:', totalPrice);
                }
            }

            // Adicionar valor dos acabamentos selecionados
            $('.grafica-rapida-acabamentos input:checked').each(function() {
                var acabamentoPreco = parseFloat($(this).data('preco')) || 0;
                totalPrice += acabamentoPreco;
                console.log('Acabamento adicionado:', $(this).val(), 'Preço:', acabamentoPreco, 'Total:', totalPrice);
            });

            // Adicionar valor da criação de arte, se selecionado
            if ($('input[name="criacao_arte"]:checked').val() === 'sim') {
                var artePreco = parseFloat($('input[name="criacao_arte"]:checked').data('preco')) || 0;
                totalPrice += artePreco;
                console.log('Criação de arte adicionada. Preço:', artePreco, 'Total:', totalPrice);
            }

            console.log('Preço final calculado:', totalPrice);

            // Atualizar o preço exibido
            if (!isNaN(totalPrice)) {
                $priceElement.text(currencySymbol + ' ' + totalPrice.toFixed(2).replace('.', ','));
            } else {
                console.error('Preço final inválido:', totalPrice);
                $priceElement.text(currencySymbol + ' ' + originalPrice.toFixed(2).replace('.', ','));
            }
        }

        // Função para atualizar o preço com debounce
        var updatePriceDebounced = (function() {
            var timer;
            return function() {
                clearTimeout(timer);
                timer = setTimeout(updatePrice, 100);
            };
        })();

        // Eventos para atualização de preço em tempo real
        $form.on('input', '.grafica-rapida-metro-quadrado input, .grafica-rapida-metro-linear input', updatePriceDebounced);
        $form.on('change', '.grafica-rapida-metro-linear select, .grafica-rapida-quantidade select, input[name="criacao_arte"]', updatePrice);

        // Evento específico para checkboxes de acabamentos
        $('.grafica-rapida-acabamentos input[type="checkbox"]').on('change', updatePrice);

        // Evento específico para o select de quantidade
        $('#quantidade').on('change', updatePrice);

        // Exibição condicional do campo de upload de arte
        $('input[name="criacao_arte"]').on('change', function() {
            $('#upload-arte').toggle($(this).val() === 'nao');
            updatePrice();
        });

        // Validação e exibição de mensagens para Metro Quadrado e Metro Linear
        $('#largura_m2, #altura_m2, #largura_ml, #altura_ml').on('input', function() {
            var $this = $(this);
            var min = parseFloat($this.attr('min'));
            var max = parseFloat($this.attr('max'));
            var val = parseFloat($this.val());

            $this.siblings('.info').toggle(val < min || val > max || isNaN(val));
            updatePriceDebounced();
        });

        // Upload de arte
        $('#confirmar-upload').on('click', function() {
            var fileInput = $('input[name="arte_upload"]')[0];
            if (fileInput.files.length > 0) {
                var file = fileInput.files[0];
                var timestamp = new Date().getTime();
                var randomString = Math.random().toString(36).substring(2, 15);
                var fileExtension = file.name.split('.').pop();
                var newFileName = 'arte_' + timestamp + '_' + randomString + '.' + fileExtension;

                var formData = new FormData();
                formData.append('arte', file, newFileName);
                formData.append('action', 'upload_arte');

                $.ajax({
                    url: grafica_rapida_vars.ajax_url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            alert('Arquivo enviado com sucesso! Nome do arquivo: ' + newFileName);
                        } else {
                            alert('Erro ao enviar arquivo: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Erro ao enviar arquivo. Por favor, tente novamente.');
                    }
                });
            } else {
                alert('Por favor, selecione um arquivo para enviar.');
            }
        });

        // Inicializar o preço
        updatePrice();
        console.log('Preço inicial calculado');
    });

})(jQuery);
