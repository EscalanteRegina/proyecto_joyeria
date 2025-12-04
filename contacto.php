<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'includes/header.php';
?>

<h1 class="mb-4 text-center">Contacto</h1>

<div class="contacto-wrapper mb-5">

  <div class="row g-4">
    <!-- Datos de la tienda -->
    <div class="col-md-6">
      <div class="card contacto-card">
        <div class="card-body">
          <h5 class="card-title mb-3">PEONIA STORE</h5>
          <p class="card-text contacto-texto">
            Tienda en línea especializada en bolsos y joyería contemporánea.
          </p>

          <ul class="list-unstyled contacto-lista">
            <li>
              <strong>Correo:</strong>
              <a href="mailto:contacto@joyasbolsas.com">peonia.store@hotmail.com</a>
            </li>
            <li>
              <strong>Teléfono / WhatsApp:</strong>
              +52 55 4099 3202
            </li>
            <li>
              <strong>Horario de atención:</strong>
              Lunes a viernes de 10:00 a 19:00 hrs.
            </li>
            <li>
              <strong>Envíos:</strong>
              A toda la República Mexicana.
            </li>
          </ul>

          <h6 class="mt-4">Redes sociales</h6>
          <p class="contacto-texto">
            Instagram: <a href="#">@PEONIASTORE</a><br>
            TikTok: <a href="#">@PEONIASTORE</a>
          </p>
        </div>
      </div>
    </div>

    <!-- Formulario de contacto (simulado) -->
    <div class="col-md-6">
      <div class="card contacto-card">
        <div class="card-body">
          <h5 class="card-title mb-3">Envíanos un mensaje</h5>

          <form>
            <div class="mb-3">
              <label class="form-label">Nombre</label>
              <input type="text" class="form-control" placeholder="Tu nombre completo">
            </div>

            <div class="mb-3">
              <label class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" placeholder="peonia.store@hotmail.com">
            </div>

            <div class="mb-3">
              <label class="form-label">Mensaje</label>
              <textarea class="form-control" rows="4" placeholder="Escribe tu mensaje aquí"></textarea>
            </div>

            <button type="button" class="btn btn-primary w-100">
              Enviar mensaje
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>

<?php include 'includes/footer.php'; ?>
