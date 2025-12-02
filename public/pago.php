<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Comprobante - Membresía</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-6">

        <h1 class="text-2xl font-semibold text-gray-800 text-center mb-4">
            Confirmación de Pago
        </h1>

        <p class="text-gray-600 text-center mb-6">
            Sube el comprobante y tu número de transacción para activar tu membresía anual.
        </p>

        <!-- Panel de Datos -->
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-6">
            <p class="text-gray-700 font-semibold text-center">ID del Dispositivo:</p>
            <p id="deviceIdValue" class="text-blue-600 font-mono text-center break-all mt-1"></p>
        </div>

        <form id="uploadForm" class="space-y-5" enctype="multipart/form-data">

            <!-- Número de transacción -->
            <div>
                <label class="block text-gray-700 font-medium mb-1">Número de Transacción</label>
                <input
                    type="text"
                    id="transaccion"
                    name="transaccion"
                    class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-400 outline-none"
                    placeholder="Ej: 1248723499"
                    required>
            </div>

            <!-- Comprobante -->
            <!-- Vista previa -->
            <div id="previewContainer" class="hidden mt-3">
                <p class="text-gray-600 mb-2">Vista previa:</p>
                <img id="previewImage" class="rounded-lg shadow max-h-64 object-contain mx-auto">
            </div>

            <!-- Botón -->
            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-3 rounded-xl text-lg font-semibold hover:bg-blue-700 transition">
                Enviar Comprobante
            </button>
        </form>

        <p id="msg" class="text-center text-gray-700 font-medium mt-5"></p>

    </div>

<script>
// Obtener deviceId desde URL (?id=xxxx)
const deviceId = new URLSearchParams(window.location.search).get("id");
document.getElementById("deviceIdValue").innerText = deviceId ?? "(No recibido)";



// Enviar formulario vía POST
document.getElementById("uploadForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append("device_id", deviceId);
    formData.append("transaccion", document.getElementById("transaccion").value);
    

    const msg = document.getElementById("msg");
    msg.innerText = "Enviando...";

    try {
        const res = await fetch("/api/pago/subir_comprobante.php", {
            method: "POST",
            body: formData,
        });

        const json = await res.json();

        if (json.status === "ok") {
            msg.innerText = "Comprobante enviado correctamente. Validando pago...";
            msg.classList.add("text-green-600");
        } else {
            msg.innerText = "Error: " + json.message;
            msg.classList.add("text-red-600");
        }

    } catch (err) {
        msg.innerText = "No se pudo enviar el comprobante.";
        msg.classList.add("text-red-600");
    }

});
</script>

</body>
</html>
