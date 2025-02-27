$(document).ready( () => {
     
	$('#documentacao').on('click', () => {
       // $('#pagina').load('documentacao.html')
       $.get('documentacao.html', data => {
            $('#pagina').html(data)
       })
    })

    $('#suporte').on('click', () => {
       // $('#pagina').load('suporte.html')
       $.get('suporte.html', data => {
            $('#pagina').html(data)
       })
    })

    $('#competencia').on('change', e => {    

     let competencia = $(e.target).val()  
          
     $.ajax({
          type: 'GET',
          url: 'app.php',
          data: `competencia=${competencia}`,
          dataType: 'json',
          success: dados => {
               $('#numeroVendas').html(dados.numeroVendas)
               $('#totalVendas').html(dados.totalVendas) 
               $('#clientesAtivos').html(dados.totalClientesAtivos)
               $('#clientesInativos').html(dados.totalClientesInativos)
               $('#totalDespesas').html(dados.totalDespesas)
          },
          error: erro => { console.log(erro) }
     })
    }) 

})