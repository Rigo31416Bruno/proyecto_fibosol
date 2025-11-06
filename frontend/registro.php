<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro</title>
  <link rel="stylesheet" href="../styles/registro.css">
  <style>
    #message{margin-top:12px;font-size:0.95rem}
    #message.error{color:#c00}
    #message.success{color:#080}
    .modal-backdrop{position:fixed;inset:0;background:rgba(2,6,23,0.6);display:none;align-items:center;justify-content:center;z-index:9999}
    .modal{background:#fff;padding:20px;border-radius:10px;max-width:420px;width:90%}
    .modal h3{margin:0 0 8px}
    .modal .actions{display:flex;gap:8px;justify-content:flex-end;margin-top:12px}
    .modal input{width:100%;padding:10px;border-radius:8px;border:1px solid #ccc}
    .btn{padding:10px 14px;border-radius:8px;border:none;cursor:pointer}
    .btn.primary{background:#2563eb;color:#fff}
    .btn.ghost{background:#f3f4f6}
  </style>
</head>
<body>

  <div class="cuadro">
      <h2 style="text-align: center;">Registrese</h2>
      <form id="registro-form" autocomplete="off">
          <label for="usuario">Usuario</label>
          <input type="text" name="usuario" id="usuario">

          <label for="correo">Correo</label>
          <input type="email" name="correo" id="correo">

          <label for="password">Contraseña</label>
          <input type="password" name="password" id="password">

          <div class="enlace-registro">
            <a href="inicio_de_sesion.html">Ya tienes una cuenta? Inicia Sesion</a>
          </div>

          <button type="submit">Registrar</button>
          <div id="message" role="status" aria-live="polite"></div>
      </form>
  </div>

  <div class="modal-backdrop" id="modal-backdrop" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
      <h3 id="modal-title">Ingresa el codigo</h3>
      <p id="modal-info" style="margin:0 0 8px;font-size:0.95rem;color:#333">Se envió un código a tu correo.</p>
      <input type="text" id="verify-code" maxlength="6" inputmode="numeric" placeholder="000000">
      <div class="actions">
        <button class="btn ghost" id="cancel-verify">Cancelar</button>
        <button class="btn primary" id="submit-verify">Verificar</button>
      </div>
      <div id="modal-msg" style="margin-top:8px;font-size:0.95rem"></div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  $(function(){
    var endpoint = '../backend/registro.php';
    var verificationId = null;

    function openModal() {
      $('#verify-code').val('');
      $('#modal-msg').text('');
      $('#modal-backdrop').css('display','flex').attr('aria-hidden','false');
      $('#verify-code').focus();
    }
    function closeModal() {
      $('#modal-backdrop').hide().attr('aria-hidden','true');
      verificationId = null;
    }

    $('#registro-form').on('submit', function(e){
      e.preventDefault();
      var $form = $(this);
      var $msg = $('#message').removeClass('error success').text('');
      var $btn = $form.find('button[type="submit"]');

      var usuario = $.trim($('#usuario').val());
      var correo = $.trim($('#correo').val());
      var password = $('#password').val();

      if(!usuario || !correo || !password){
        $msg.addClass('error').text('Completa todos los campos');
        return;
      }
      if(!/^\S+@\S+\.\S+$/.test(correo)){
        $msg.addClass('error').text('Correo invalido.');
        return;
      }

      $btn.prop('disabled', true).text('Registrando...');

      $.ajax({
        url: endpoint,
        method: 'POST',
        data: $form.serialize(),
        dataType: 'json',
        timeout: 99999
      }).done(function(response){
        if(response && response.success && response.action === 'verify'){
          verificationId = response.verification_id;
          $('#modal-info').text(response.message || 'Se envio un codigo a tu correo.');
          openModal();
        } else if (response && response.success) {
          $msg.addClass('success').text(response.message || 'Registro exitoso');
          $form[0].reset();
        } else {
          $msg.addClass('error').text((response && response.message) || 'Error en el registro');
        }
      }).fail(function(jqXHR, textStatus){
        console.log(textStatus, jqXHR.responseText);
        var texto = 'Error de red.';
        if(textStatus === 'timeout') texto = 'Tiempo de espera agotado';
        else if(jqXHR.responseJSON && jqXHR.responseJSON.message) texto = jqXHR.responseJSON.message;
        $('#message').addClass('error').text(texto);
      }).always(function(){
        $btn.prop('disabled', false).text('Registrar');
      });
    });

    $('#submit-verify').on('click', function(){
      var code = $('#verify-code').val().trim();
      $('#modal-msg').text('');
      if(!verificationId || code.length !== 6){
        $('#modal-msg').css('color','crimson').text('Codigo invalido.');
        return;
      }
      $(this).prop('disabled', true).text('Verificando...');
      $.ajax({
        url: '../backend/confirmar_codigo.php',
        method: 'POST',
        data: { verification_id: verificationId, code: code },
        dataType: 'json',
        timeout: 10000
      }).done(function(resp){
        if (resp && resp.success){
          $('#message').removeClass('error').addClass('success').text(resp.message || 'Cuenta verificada');
          closeModal();
          $('#registro-form')[0].reset();
        } else {
          $('#modal-msg').css('color','crimson').text((resp && resp.message) || 'Codigo incorrecto');
        }
      }).fail(function(jqXHR, textStatus){
        console.log(textStatus, jqXHR.responseText);
        $('#modal-msg').css('color','crimson').text('Error de red al verificar');
      }).always(function(){
        $('#submit-verify').prop('disabled', false).text('Verificar');
      });
    });

    $('#cancel-verify').on('click', function(){
      if(!verificationId){
        closeModal(); return;
      }
      $.post('../backend/cancelar_verificacion.php', { verification_id: verificationId }, function(resp){
        closeModal();
        if(resp && resp.success){
          $('#message').removeClass('success').addClass('error').text('Verificacion cancelada. Registro no completado.');
        } else {
          $('#message').removeClass('success').addClass('error').text((resp && resp.message) || 'Operacion cancelada.');
        }
      }, 'json').fail(function(){ closeModal(); $('#message').removeClass('success').addClass('error').text('Cancelado.'); });
    });

    // cerrar modal con ESC
    $(document).on('keydown', function(e){ if(e.key === 'Escape'){ $('#cancel-verify').click(); } });
  });
  </script>
</body>
</html>