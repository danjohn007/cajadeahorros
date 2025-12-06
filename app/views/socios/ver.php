<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/socios" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Socios
    </a>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <?= htmlspecialchars($socio['nombre'] . ' ' . $socio['apellido_paterno'] . ' ' . ($socio['apellido_materno'] ?? '')) ?>
            </h2>
            <p class="text-gray-600"><?= htmlspecialchars($socio['numero_socio']) ?></p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <?php if (in_array($_SESSION['user_role'], ['administrador', 'operativo'])): ?>
            <a href="<?= BASE_URL ?>/socios/editar/<?= $socio['id'] ?>" 
               class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                <i class="fas fa-edit mr-2"></i> Editar
            </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/socios/historial/<?= $socio['id'] ?>" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-history mr-2"></i> Historial
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Datos Personales -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user text-blue-500 mr-2"></i> Datos Personales
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-500">RFC</label>
                    <p class="font-medium"><?= htmlspecialchars($socio['rfc'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">CURP</label>
                    <p class="font-medium"><?= htmlspecialchars($socio['curp'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Fecha de Nacimiento</label>
                    <p class="font-medium"><?= $socio['fecha_nacimiento'] ? date('d/m/Y', strtotime($socio['fecha_nacimiento'])) : '-' ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Género</label>
                    <p class="font-medium">
                        <?php
                        $generos = ['M' => 'Masculino', 'F' => 'Femenino', 'O' => 'Otro'];
                        echo $generos[$socio['genero']] ?? '-';
                        ?>
                    </p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Estado Civil</label>
                    <p class="font-medium"><?= ucfirst($socio['estado_civil'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Estatus</label>
                    <?php
                    $statusColors = [
                        'activo' => 'bg-green-100 text-green-800',
                        'inactivo' => 'bg-gray-100 text-gray-800',
                        'suspendido' => 'bg-yellow-100 text-yellow-800',
                        'baja' => 'bg-red-100 text-red-800'
                    ];
                    $color = $statusColors[$socio['estatus']] ?? 'bg-gray-100 text-gray-800';
                    ?>
                    <p><span class="px-2 py-1 text-xs font-medium rounded-full <?= $color ?>"><?= ucfirst($socio['estatus']) ?></span></p>
                </div>
                <?php if (!empty($socio['asesor_nombre'])): ?>
                <div>
                    <label class="text-sm text-gray-500">Asesor</label>
                    <p class="font-medium"><?= htmlspecialchars($socio['asesor_nombre']) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Contacto -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-phone text-green-500 mr-2"></i> Información de Contacto
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-500">Teléfono</label>
                    <p class="font-medium"><?= htmlspecialchars($socio['telefono'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Celular</label>
                    <p class="font-medium"><?= htmlspecialchars($socio['celular'] ?? '-') ?></p>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-500">Correo Electrónico</label>
                    <p class="font-medium"><?= htmlspecialchars($socio['email'] ?? '-') ?></p>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-500">Dirección</label>
                    <p class="font-medium">
                        <?= htmlspecialchars($socio['direccion'] ?? '') ?>
                        <?php if ($socio['colonia']): ?>, <?= htmlspecialchars($socio['colonia']) ?><?php endif; ?>
                        <?php if ($socio['municipio']): ?>, <?= htmlspecialchars($socio['municipio']) ?><?php endif; ?>
                        <?php if ($socio['estado']): ?>, <?= htmlspecialchars($socio['estado']) ?><?php endif; ?>
                        <?php if ($socio['codigo_postal']): ?> C.P. <?= htmlspecialchars($socio['codigo_postal']) ?><?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Información Laboral -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-briefcase text-purple-500 mr-2"></i> Información Laboral
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-500">Unidad de Trabajo</label>
                    <p class="font-medium"><?= htmlspecialchars($socio['unidad_trabajo'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Puesto</label>
                    <p class="font-medium"><?= htmlspecialchars($socio['puesto'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Número de Empleado</label>
                    <p class="font-medium"><?= htmlspecialchars($socio['numero_empleado'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Fecha de Ingreso</label>
                    <p class="font-medium"><?= $socio['fecha_ingreso_trabajo'] ? date('d/m/Y', strtotime($socio['fecha_ingreso_trabajo'])) : '-' ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Salario Mensual</label>
                    <p class="font-medium">$<?= number_format($socio['salario_mensual'] ?? 0, 2) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Beneficiario -->
        <?php if ($socio['beneficiario_nombre']): ?>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-users text-orange-500 mr-2"></i> Beneficiario
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm text-gray-500">Nombre</label>
                    <p class="font-medium"><?= htmlspecialchars($socio['beneficiario_nombre']) ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Parentesco</label>
                    <p class="font-medium"><?= htmlspecialchars($socio['beneficiario_parentesco'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Teléfono</label>
                    <p class="font-medium"><?= htmlspecialchars($socio['beneficiario_telefono'] ?? '-') ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Cuenta de Ahorro -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-wallet text-green-500 mr-2"></i> Cuenta de Ahorro
            </h3>
            
            <?php if ($cuentaAhorro): ?>
            <div class="text-center mb-4">
                <p class="text-sm text-gray-500">Saldo Actual</p>
                <p class="text-3xl font-bold text-green-600">$<?= number_format($cuentaAhorro['saldo'], 2) ?></p>
                <p class="text-sm text-gray-500 mt-1"><?= $cuentaAhorro['numero_cuenta'] ?></p>
            </div>
            
            <div class="border-t pt-4">
                <a href="<?= BASE_URL ?>/ahorro/socio/<?= $socio['id'] ?>" 
                   class="block text-center text-blue-600 hover:text-blue-800">
                    Ver movimientos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-center">Sin cuenta de ahorro</p>
            <?php endif; ?>
        </div>
        
        <!-- Créditos -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-hand-holding-usd text-purple-500 mr-2"></i> Créditos
            </h3>
            
            <?php if (empty($creditos)): ?>
            <p class="text-gray-500 text-center">Sin créditos registrados</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach (array_slice($creditos, 0, 5) as $credito): ?>
                <a href="<?= BASE_URL ?>/creditos/ver/<?= $credito['id'] ?>" 
                   class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-800"><?= htmlspecialchars($credito['tipo_credito']) ?></p>
                            <p class="text-sm text-gray-500"><?= $credito['numero_credito'] ?></p>
                        </div>
                        <?php
                        $creditoColors = [
                            'activo' => 'bg-green-100 text-green-800',
                            'liquidado' => 'bg-blue-100 text-blue-800',
                            'solicitud' => 'bg-yellow-100 text-yellow-800',
                            'rechazado' => 'bg-red-100 text-red-800'
                        ];
                        $cColor = $creditoColors[$credito['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $cColor ?>">
                            <?= ucfirst($credito['estatus']) ?>
                        </span>
                    </div>
                    <p class="text-sm font-medium text-purple-600 mt-2">
                        Saldo: $<?= number_format($credito['saldo_actual'] ?? 0, 2) ?>
                    </p>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div class="border-t pt-4 mt-4">
                <a href="<?= BASE_URL ?>/creditos/solicitud/<?= $socio['id'] ?>" 
                   class="block text-center px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition">
                    <i class="fas fa-plus mr-2"></i> Nueva Solicitud
                </a>
            </div>
        </div>
        
        <!-- Fechas -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-calendar text-blue-500 mr-2"></i> Fechas
            </h3>
            
            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-500">Fecha de Alta</label>
                    <p class="font-medium"><?= $socio['fecha_alta'] ? date('d/m/Y', strtotime($socio['fecha_alta'])) : '-' ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Fecha de Registro</label>
                    <p class="font-medium"><?= date('d/m/Y H:i', strtotime($socio['created_at'])) ?></p>
                </div>
                <?php if ($socio['fecha_baja']): ?>
                <div>
                    <label class="text-sm text-gray-500">Fecha de Baja</label>
                    <p class="font-medium text-red-600"><?= date('d/m/Y', strtotime($socio['fecha_baja'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Últimos Movimientos de Ahorro -->
<?php if (!empty($movimientosAhorro)): ?>
<div class="mt-6 bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-history text-gray-500 mr-2"></i> Últimos Movimientos de Ahorro
        </h3>
        <a href="<?= BASE_URL ?>/ahorro/historial/<?= $cuentaAhorro['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm">
            Ver todos
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach ($movimientosAhorro as $mov): ?>
                <tr>
                    <td class="px-4 py-2 text-sm"><?= date('d/m/Y H:i', strtotime($mov['fecha'])) ?></td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            <?= $mov['tipo'] === 'aportacion' ? 'bg-green-100 text-green-800' : 
                                ($mov['tipo'] === 'retiro' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') ?>">
                            <?= ucfirst($mov['tipo']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-2 text-sm"><?= htmlspecialchars($mov['concepto'] ?? '-') ?></td>
                    <td class="px-4 py-2 text-sm text-right font-medium 
                        <?= $mov['tipo'] === 'aportacion' ? 'text-green-600' : 'text-red-600' ?>">
                        <?= $mov['tipo'] === 'retiro' ? '-' : '+' ?>$<?= number_format($mov['monto'], 2) ?>
                    </td>
                    <td class="px-4 py-2 text-sm text-right">$<?= number_format($mov['saldo_nuevo'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
