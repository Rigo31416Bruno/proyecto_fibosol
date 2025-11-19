<?php
include '../backend/validarSesion.php';

if(!validarSesion())
{
    header('Location: inicio_de_sesion.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Dirección</title>

    <link rel="stylesheet" href="../styles/direccion.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!-- Added header navigation -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="inicio.php" class="navbar-brand">Tienda</a>
            <a href="carrito.php" class="navbar-cart">
                <span class="cart-icon">🛒</span>
            </a>
        </div>
    </nav>

    <form id="formularioDireccion">
        <div class="container-form">
            <!-- Updated header styling -->
            <div class="header-section">
                <h1>Cambiar Dirección</h1>
                <p class="instructions">Selecciona tu ubicación en el mapa o ingresa manualmente tu dirección.</p>
            </div>

            <div class="form-section">
                <div class="input-group">
                    <label for="direccion" class="label">Dirección</label>
                    <input type="text" name="direccion" id="direccion" placeholder="Ingrese su dirección" class="direccion-input" required>
                </div>

                <!-- Updated map container styling -->
                <div class="map-wrapper">
                    <div id="map"></div>
                </div>

                <button type="submit" class="btn-guardar">Guardar Dirección</button>
            </div>
        </div>
    </form>

    <!-- Updated modal styling -->
    <div class="modal fade" id="modalResultado" tabindex="-1" aria-labelledby="modalResultadoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="modalResultadoLabel">Resultado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-custom" id="modalMensaje">
                    <!-- Mensaje dinámico -->
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<script>
    const culiacanCoordinates = { lat: 24.8074, lng: -107.3940 };

    // Configuración del mapa
    const map = L.map('map').setView([culiacanCoordinates.lat, culiacanCoordinates.lng], 13);
    map.scrollWheelZoom.disable();
    map.on('focus', function () { map.scrollWheelZoom.enable(); });
    map.on('blur', function () { map.scrollWheelZoom.disable(); });

    // Cargar tiles de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Agregar marcador inicial
    const marker = L.marker([culiacanCoordinates.lat, culiacanCoordinates.lng], { draggable: true }).addTo(map)
        .bindPopup('Ubicación inicial: Culiacán, Sinaloa')
        .openPopup();

    // Función para realizar geocodificación inversa
    function reverseGeocode(lat, lng) {
        const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    document.getElementById('direccion').value = data.display_name;
                } else {
                    document.getElementById('direccion').value = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
                }
            })
            .catch(error => {
                console.error('Error al realizar geocodificación inversa:', error);
                document.getElementById('direccion').value = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
            });
    }

    // Actualizar dirección al mover el marcador
    marker.on('dragend', function (event) {
        const position = marker.getLatLng();
        map.setView(position);
        reverseGeocode(position.lat, position.lng);
    });

    // Actualizar dirección al hacer clic en el mapa
    map.on('click', function (event) {
        const { lat, lng } = event.latlng;
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng]);
        reverseGeocode(lat, lng);
    });

    // Manejar el envío del formulario
    $('#formularioDireccion').on('submit', function (event) {
        event.preventDefault(); // Evitar el envío predeterminado del formulario

        const direccion = $('#direccion').val();
        if (!direccion) {
            alert('Por favor, ingresa una dirección válida.');
            return;
        }

        // Enviar la dirección al servidor con AJAX
        $.ajax({
            url: '../backend/actualizarDireccion.php',
            type: 'POST',
            data: { direccion: direccion },
            success: function (response) {
                // Mostrar mensaje en el modal
                $('#modalMensaje').text(response);
                $('#modalResultado').modal('show');
            },
            error: function (response) {
                // Mostrar mensaje de error en el modal
                console.log(response);
                $('#modalMensaje').text('Hubo un error al guardar la dirección. Inténtalo nuevamente.', response);
                $('#modalResultado').modal('show');
            }
        });
    });

    // Redirigir al carrito cuando se cierre el modal
    $('#modalResultado').on('hidden.bs.modal', function () {
        window.location.href = 'inicio.php';
    });
</script>
