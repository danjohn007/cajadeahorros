<?php
/**
 * Vista para clientes sin vínculo con socio
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <div class="text-yellow-500 mb-4">
            <i class="fas fa-user-slash text-6xl"></i>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Cuenta No Vinculada</h1>
        
        <p class="text-gray-600 mb-6">
            Tu cuenta de usuario aún no está vinculada a un registro de socio en nuestro sistema.
        </p>
        
        <?php if (!empty($solicitudPendiente)): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <h3 class="font-medium text-yellow-800 mb-2">
                <i class="fas fa-clock mr-2"></i>Solicitud de Vinculación Pendiente
            </h3>
            <p class="text-sm text-yellow-700">
                Tu solicitud de vinculación está siendo revisada. Te notificaremos cuando sea procesada.
            </p>
            <p class="text-xs text-yellow-600 mt-2">
                Enviada: <?= date('d/m/Y H:i', strtotime($solicitudPendiente['created_at'])) ?>
            </p>
        </div>
        <?php else: ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <h3 class="font-medium text-green-800 mb-2">
                <i class="fas fa-hand-point-up mr-2"></i>Solicitar Vinculación
            </h3>
            <p class="text-sm text-green-700 mb-4">
                Si ya eres socio de la Caja de Ahorros, puedes solicitar vincular tu cuenta de usuario con tu registro de socio.
            </p>
            <button onclick="mostrarFormularioVinculacion()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-link mr-2"></i>Solicitar Vinculación
            </button>
        </div>
        <?php endif; ?>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-medium text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-2"></i>¿Qué puedes hacer?
            </h3>
            <ul class="text-sm text-blue-700 text-left list-disc list-inside space-y-1">
                <li>Solicita la vinculación con el botón de arriba</li>
                <li>Contacta a la oficina de la Caja de Ahorros para más información</li>
                <li>Si eres nuevo, acude a nuestras oficinas para registrarte como socio</li>
            </ul>
        </div>
        
        <div class="flex flex-col space-y-3">
            <a href="<?= url('usuarios/perfil') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-user-cog mr-2"></i>Ver Mi Perfil
            </a>
            <a href="<?= url('auth/logout') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
            </a>
        </div>
    </div>
    
    <div class="mt-6 text-center text-gray-500 text-sm">
        <p>
            <i class="fas fa-phone mr-1"></i>
            Teléfono de Contacto: <?= htmlspecialchars(getConfig('telefono_contacto', 'No disponible')) ?>
        </p>
        <p class="mt-1">
            <i class="fas fa-envelope mr-1"></i>
            Email: <?= htmlspecialchars(getConfig('email_contacto', getConfig('correo_sistema', 'No disponible'))) ?>
        </p>
    </div>
</div>

<!-- Modal de Solicitud de Vinculación -->
<div id="modalVinculacion" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-link mr-2 text-green-600"></i>Solicitar Vinculación de Cuenta
            </h3>
        </div>
        <form method="POST" action="<?= url('cliente/solicitarVinculacion') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">
                    Proporciona tus datos de contacto para que podamos verificar tu identidad como socio.
                </p>
                
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="tel" name="telefono" id="telefono" maxlength="10"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                           placeholder="10 dígitos">
                </div>
                
                <div>
                    <label for="celular" class="block text-sm font-medium text-gray-700 mb-1">Celular / WhatsApp *</label>
                    <input type="tel" name="celular" id="celular" maxlength="10" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                           placeholder="10 dígitos">
                </div>
                
                <div>
                    <label for="mensaje" class="block text-sm font-medium text-gray-700 mb-1">Mensaje adicional</label>
                    <textarea name="mensaje" id="mensaje" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                              placeholder="Información adicional que nos ayude a identificarte (número de socio, RFC, etc.)"></textarea>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="cerrarModalVinculacion()" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    <i class="fas fa-paper-plane mr-2"></i>Enviar Solicitud
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function mostrarFormularioVinculacion() {
    document.getElementById('modalVinculacion').classList.remove('hidden');
}

function cerrarModalVinculacion() {
    document.getElementById('modalVinculacion').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalVinculacion').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalVinculacion();
    }
});
</script>
