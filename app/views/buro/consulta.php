<?php
/**
 * Vista pública de consulta al Buró de Crédito
 * Sistema de Gestión Integral de Caja de Ahorros
 */
$siteName = getSiteName();
$logoUrl = getLogo();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= htmlspecialchars($siteName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center">
                <?php if ($logoUrl): ?>
                    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-10 mr-3">
                <?php else: ?>
                    <i class="fas fa-piggy-bank text-3xl text-blue-600 mr-3"></i>
                <?php endif; ?>
                <span class="font-bold text-xl text-gray-800"><?= htmlspecialchars($siteName) ?></span>
            </div>
            <a href="<?= url('') ?>" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-home mr-1"></i> Inicio
            </a>
        </div>
    </header>
    
    <main class="max-w-4xl mx-auto px-4 py-12">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-600 rounded-full mb-6">
                <i class="fas fa-search-dollar text-4xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Consulta tu Buró de Crédito</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Conoce tu historial crediticio de forma rápida y segura. Obtén tu score crediticio
                y un reporte detallado de tu situación financiera.
            </p>
        </div>
        
        <?php if (!$buroEnabled): ?>
        <!-- Servicio No Disponible -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
            <i class="fas fa-exclamation-triangle text-5xl text-yellow-500 mb-4"></i>
            <h2 class="text-xl font-semibold text-yellow-800 mb-2">Servicio Temporalmente No Disponible</h2>
            <p class="text-yellow-700">El servicio de consulta al Buró de Crédito no está disponible en este momento. Por favor intente más tarde.</p>
        </div>
        <?php else: ?>
        
        <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-medium text-red-800">Por favor corrija los siguientes errores:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Formulario -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">
                        <i class="fas fa-user-check mr-2 text-blue-600"></i>Datos para Consulta
                    </h2>
                    
                    <form method="POST" action="<?= url('buro/consulta') ?>">
                        <div class="space-y-6">
                            <!-- Tipo de Consulta -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Tipo de Identificador</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-blue-500">
                                        <input type="radio" name="tipo_consulta" value="rfc" checked class="sr-only">
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium text-gray-900">RFC</span>
                                                <span class="mt-1 flex items-center text-xs text-gray-500">Registro Federal de Contribuyentes</span>
                                            </span>
                                        </span>
                                        <i class="fas fa-check-circle text-blue-600 hidden check-icon"></i>
                                    </label>
                                    
                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-blue-500">
                                        <input type="radio" name="tipo_consulta" value="curp" class="sr-only">
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium text-gray-900">CURP</span>
                                                <span class="mt-1 flex items-center text-xs text-gray-500">Clave Única de Registro</span>
                                            </span>
                                        </span>
                                        <i class="fas fa-check-circle text-blue-600 hidden check-icon"></i>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Identificador -->
                            <div>
                                <label for="identificador" class="block text-sm font-medium text-gray-700 mb-1">
                                    RFC o CURP <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="identificador" name="identificador" required
                                       value="<?= htmlspecialchars($_POST['identificador'] ?? '') ?>"
                                       placeholder="Ingrese su RFC o CURP"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-3 border text-lg uppercase"
                                       maxlength="18">
                                <p class="mt-1 text-xs text-gray-500">RFC: 12-13 caracteres | CURP: 18 caracteres</p>
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Correo Electrónico <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" required
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                       placeholder="correo@ejemplo.com"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-3 border">
                                <p class="mt-1 text-xs text-gray-500">Recibirás una copia de tu reporte en este correo</p>
                            </div>
                            
                            <!-- Términos -->
                            <div class="flex items-start">
                                <input type="checkbox" id="terminos" name="terminos" required
                                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 mt-1">
                                <label for="terminos" class="ml-2 text-sm text-gray-600">
                                    Acepto los <a href="#" class="text-blue-600 hover:underline">términos y condiciones</a> 
                                    y autorizo la consulta de mi historial crediticio ante el Buró de Crédito.
                                </label>
                            </div>
                            
                            <!-- Submit -->
                            <button type="submit" 
                                    class="w-full bg-blue-600 text-white py-4 rounded-lg font-semibold text-lg hover:bg-blue-700 transition flex items-center justify-center">
                                <i class="fas fa-credit-card mr-2"></i>
                                Pagar y Consultar - $<?= number_format($costoConsulta, 2) ?> MXN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Información Lateral -->
            <div class="space-y-6">
                <!-- Precio -->
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <p class="text-gray-500 text-sm mb-1">Costo por Consulta</p>
                    <p class="text-4xl font-bold text-blue-600">$<?= number_format($costoConsulta, 2) ?></p>
                    <p class="text-gray-500 text-sm mt-1">MXN (IVA incluido)</p>
                    <div class="mt-4 pt-4 border-t">
                        <div class="flex items-center justify-center text-sm text-gray-600">
                            <i class="fab fa-paypal text-blue-600 mr-2 text-xl"></i>
                            Pago seguro con PayPal
                        </div>
                    </div>
                </div>
                
                <!-- Beneficios -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">¿Qué obtienes?</h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                            <span>Score crediticio actualizado</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                            <span>Número de cuentas activas</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                            <span>Historial de pagos</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                            <span>Nivel de riesgo crediticio</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                            <span>Resultado inmediato</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Seguridad -->
                <div class="bg-green-50 rounded-xl p-6">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-shield-alt text-green-600 text-xl mr-2"></i>
                        <h3 class="font-semibold text-green-800">100% Seguro</h3>
                    </div>
                    <p class="text-sm text-green-700">
                        Tu información está protegida. Usamos conexiones encriptadas y no almacenamos 
                        datos sensibles.
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- FAQ -->
        <div class="mt-12 bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6 text-center">Preguntas Frecuentes</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-gray-800 mb-2">¿Qué es el Buró de Crédito?</h3>
                    <p class="text-sm text-gray-600">
                        Es una empresa que recopila y proporciona información sobre el comportamiento 
                        crediticio de personas y empresas en México.
                    </p>
                </div>
                <div>
                    <h3 class="font-medium text-gray-800 mb-2">¿Para qué sirve el score?</h3>
                    <p class="text-sm text-gray-600">
                        El score crediticio es una calificación numérica que indica la probabilidad 
                        de que una persona cumpla con sus compromisos de pago.
                    </p>
                </div>
                <div>
                    <h3 class="font-medium text-gray-800 mb-2">¿Afecta mi score consultarlo?</h3>
                    <p class="text-sm text-gray-600">
                        No, las consultas personales no afectan tu score crediticio. Solo las consultas 
                        de instituciones financieras pueden tener impacto.
                    </p>
                </div>
                <div>
                    <h3 class="font-medium text-gray-800 mb-2">¿Cuánto tiempo tarda?</h3>
                    <p class="text-sm text-gray-600">
                        El resultado se obtiene de forma inmediata después de confirmar el pago. 
                        También recibirás una copia por correo electrónico.
                    </p>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-white border-t mt-12 py-6">
        <div class="max-w-4xl mx-auto px-4 text-center text-sm text-gray-500">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. Todos los derechos reservados.</p>
            <p class="mt-2">
                Servicio proporcionado a través de 
                <a href="https://apif.burodecredito.com.mx" target="_blank" class="text-blue-600 hover:underline">API Buró de Crédito</a>
            </p>
        </div>
    </footer>
    
    <script>
        // Manejar selección de tipo de consulta
        document.querySelectorAll('input[name="tipo_consulta"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('input[name="tipo_consulta"]').forEach(r => {
                    const label = r.closest('label');
                    const icon = label.querySelector('.check-icon');
                    if (r.checked) {
                        label.classList.add('border-blue-500', 'ring-2', 'ring-blue-500');
                        icon.classList.remove('hidden');
                    } else {
                        label.classList.remove('border-blue-500', 'ring-2', 'ring-blue-500');
                        icon.classList.add('hidden');
                    }
                });
            });
            // Trigger initial state
            if (radio.checked) radio.dispatchEvent(new Event('change'));
        });
        
        // Convertir identificador a mayúsculas
        document.getElementById('identificador').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>
