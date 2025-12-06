<?php
/**
 * Vista de Configuración del Chatbot
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('configuraciones') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Configuraciones
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center mb-6">
        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
            <i class="fab fa-whatsapp text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
            <p class="text-gray-600">Configura el chatbot de WhatsApp y los mensajes predeterminados</p>
        </div>
    </div>
    
    <?php if (!empty($success)): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p><?= htmlspecialchars($success) ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="<?= url('configuraciones/chatbot') ?>">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        
        <div class="space-y-6">
            <!-- Configuración de WhatsApp -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fab fa-whatsapp text-green-500 mr-2"></i>Configuración de WhatsApp
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="chatbot_whatsapp_numero" class="block text-sm font-medium text-gray-700 mb-1">
                            Número de WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="chatbot_whatsapp_numero" name="chatbot_whatsapp_numero" 
                               value="<?= htmlspecialchars($config['chatbot_whatsapp_numero'] ?? '') ?>"
                               placeholder="521234567890 (sin + ni espacios)"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 px-4 py-2 border">
                        <p class="text-xs text-gray-500 mt-1">Número con código de país sin + ni espacios (ej: 521234567890)</p>
                    </div>
                    <div>
                        <label for="chatbot_url_publica" class="block text-sm font-medium text-gray-700 mb-1">
                            URL Pública del Chatbot
                        </label>
                        <input type="url" id="chatbot_url_publica" name="chatbot_url_publica" 
                               value="<?= htmlspecialchars($config['chatbot_url_publica'] ?? '') ?>"
                               placeholder="https://..."
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 px-4 py-2 border">
                        <p class="text-xs text-gray-500 mt-1">URL del chatbot web si está disponible</p>
                    </div>
                </div>
            </div>
            
            <!-- Mensajes Predeterminados -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-comments text-blue-500 mr-2"></i>Mensajes Predeterminados
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="chatbot_mensaje_bienvenida" class="block text-sm font-medium text-gray-700 mb-1">
                            Mensaje de Bienvenida
                        </label>
                        <textarea id="chatbot_mensaje_bienvenida" name="chatbot_mensaje_bienvenida" rows="3"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 px-4 py-2 border"
                                  placeholder="Mensaje que se mostrará al iniciar conversación..."><?= htmlspecialchars($config['chatbot_mensaje_bienvenida'] ?? 'Hola, bienvenido a nuestro soporte técnico. ¿En qué podemos ayudarte?') ?></textarea>
                    </div>
                    
                    <div>
                        <label for="chatbot_mensaje_horario" class="block text-sm font-medium text-gray-700 mb-1">
                            Mensaje de Horario de Atención
                        </label>
                        <textarea id="chatbot_mensaje_horario" name="chatbot_mensaje_horario" rows="2"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 px-4 py-2 border"
                                  placeholder="Información sobre horario de atención..."><?= htmlspecialchars($config['chatbot_mensaje_horario'] ?? 'Nuestro horario de atención es de Lunes a Viernes de 9:00 a 18:00 hrs.') ?></textarea>
                    </div>
                    
                    <div>
                        <label for="chatbot_mensaje_fuera_horario" class="block text-sm font-medium text-gray-700 mb-1">
                            Mensaje Fuera de Horario
                        </label>
                        <textarea id="chatbot_mensaje_fuera_horario" name="chatbot_mensaje_fuera_horario" rows="2"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 px-4 py-2 border"
                                  placeholder="Mensaje cuando está fuera del horario de atención..."><?= htmlspecialchars($config['chatbot_mensaje_fuera_horario'] ?? 'En este momento estamos fuera de horario. Por favor déjanos tu mensaje y te contactaremos a la brevedad.') ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8 flex justify-end space-x-4">
            <a href="<?= url('configuraciones') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <i class="fas fa-save mr-2"></i>Guardar Cambios
            </button>
        </div>
    </form>
</div>

<!-- Preview Section -->
<div class="mt-6 bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">
        <i class="fas fa-eye text-purple-500 mr-2"></i>Vista Previa del Botón de WhatsApp
    </h3>
    <p class="text-sm text-gray-600 mb-4">Así se verá el botón de WhatsApp en la página pública de soporte:</p>
    
    <div class="relative bg-gray-100 rounded-lg p-8 min-h-[200px]">
        <!-- Simulated WhatsApp button -->
        <div class="fixed-preview absolute bottom-4 right-4">
            <a href="#" class="flex items-center justify-center w-14 h-14 bg-green-500 rounded-full shadow-lg hover:bg-green-600 transition-colors">
                <i class="fab fa-whatsapp text-white text-2xl"></i>
            </a>
        </div>
        <p class="text-center text-gray-500">Contenido de la página de soporte</p>
    </div>
</div>
