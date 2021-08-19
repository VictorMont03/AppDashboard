$(document).ready(() => {
	$('#documentacao').on('click', () => {
        $('#pagina').load('documentacao.html');
        /*
            $.post('.html', data => {

            })
        */
    })

    $('#suporte').on('click', () => {
        $('#pagina').load('suporte.html');
    })

    /*$('#home').on('click', () => {
        $('#pagina').load('app.html');
    })*/

    $('#competencia').on('change', e => {
        let option = $(e.target).val()
        //console.log(option)

		$.ajax({
            type: 'GET',
            url: 'app.php',
            data: `competencia=${option}`,
            dataType: 'json', //modo de retorno
            success: (data) => {
                $('#numero_de_vendas').html(data.numero_vendas)
                $('#total_de_vendas').html(data.total_vendas)
                $('#clientes_ativos').html(data.clientes_ativos)
                $('#clientes_inativos').html(data.clientes_inativos)
                $('#total_despesas').html(data.total_despesas)
                $('#reclamacoes').html(data.tipo_contato.reclamacoes)
                $('#elogios').html(data.tipo_contato.elogios)
                $('#sugestoes').html(data.tipo_contato.sugestoes)
                
                console.log(data);
            },
            error: (err) => {
                console.log('Erro na requisição: ' + err.message);
            }
        })


	})
})