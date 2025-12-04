<!-- Vista principal del Sistema KYC -->
<div class="space-y-6">
    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Verificaciones</p>
                    <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['totalVerificaciones']) ?></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-user-check text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pendientes</p>
                    <p class="text-2xl font-bold text-yellow-600"><?= number_format($stats['pendientes']) ?></p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Aprobados</p>
                    <p class="text-2xl font-bold text-green-600"><?= number_format($stats['aprobados']) ?></p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Rechazados</p>
                    <p class="text-2xl font-bold text-red-600"><?= number_format($stats['rechazados']) ?></p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Alto Riesgo</p>
                    <p class="text-2xl font-bold text-orange-600"><?= number_format($stats['altoRiesgo']) ?></p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-orange-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">PEP</p>
                    <p class="text-2xl font-bold text-purple-600"><?= number_format($stats['pep']) ?></p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-user-tie text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Acciones y filtros -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex gap-2">
                <?php if (in_array($_SESSION['user_role'], ['administrador', 'operativo'])): ?>
                <a href="<?= BASE_URL ?>/kyc/crear" 
                   class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Nueva Verificación
                </a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/kyc/reportes" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
            </div>
            
            <form method="GET" class="flex flex-wrap gap-2 items-center">
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Buscar socio, RFC..." 
                       class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                
                <select name="estatus" class="px-4 py-2 border rounded-lg">
                    <option value="">Todos los estatus</option>
                    <option value="pendiente" <?= $estatus === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="aprobado" <?= $estatus === 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
                    <option value="rechazado" <?= $estatus === 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
                    <option value="vencido" <?= $estatus === 'vencido' ? 'selected' : '' ?>>Vencido</option>
                </select>
                
                <select name="nivel_riesgo" class="px-4 py-2 border rounded-lg">
                    <option value="">Todos los niveles</option>
                    <option value="bajo" <?= $nivelRiesgo === 'bajo' ? 'selected' : '' ?>>Riesgo Bajo</option>
                    <option value="medio" <?= $nivelRiesgo === 'medio' ? 'selected' : '' ?>>Riesgo Medio</option>
                    <option value="alto" <?= $nivelRiesgo === 'alto' ? 'selected' : '' ?>>Riesgo Alto</option>
                </select>
                
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-search"></i>
                </button>
                
                <?php if ($search || $estatus || $nivelRiesgo): ?>
                <a href="<?= BASE_URL ?>/kyc" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-times"></i>
                </a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <!-- Tabla de verificaciones -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Socio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RFC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Documento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel Riesgo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estatus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($verificaciones)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-user-check text-4xl mb-2"></i>
                            <p>No hay verificaciones KYC registradas</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($verificaciones as $v): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-primary-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($v['nombre'] . ' ' . $v['apellido_paterno'] . ' ' . ($v['apellido_materno'] ?? '')) ?>
                                    </div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($v['numero_socio']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= htmlspecialchars($v['rfc'] ?? 'N/A') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= htmlspecialchars($v['tipo_documento']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $riesgoClases = [
                                'bajo' => 'bg-green-100 text-green-800',
                                'medio' => 'bg-yellow-100 text-yellow-800',
                                'alto' => 'bg-red-100 text-red-800'
                            ];
                            $riesgoClase = $riesgoClases[$v['nivel_riesgo']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 py-1 text-xs rounded-full <?= $riesgoClase ?>">
                                <?= ucfirst($v['nivel_riesgo']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $estatusClases = [
                                'pendiente' => 'bg-yellow-100 text-yellow-800',
                                'aprobado' => 'bg-green-100 text-green-800',
                                'rechazado' => 'bg-red-100 text-red-800',
                                'vencido' => 'bg-gray-100 text-gray-800'
                            ];
                            $estatusClase = $estatusClases[$v['estatus']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 py-1 text-xs rounded-full <?= $estatusClase ?>">
                                <?= ucfirst($v['estatus']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= $v['fecha_verificacion'] ? date('d/m/Y', strtotime($v['fecha_verificacion'])) : date('d/m/Y', strtotime($v['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?= BASE_URL ?>/kyc/ver/<?= $v['id'] ?>" 
                               class="text-primary-600 hover:text-primary-900 mr-3" title="Ver">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if (in_array($_SESSION['user_role'], ['administrador', 'operativo'])): ?>
                            <a href="<?= BASE_URL ?>/kyc/editar/<?= $v['id'] ?>" 
                               class="text-blue-600 hover:text-blue-900 mr-3" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <?php if ($totalPages > 1): ?>
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Mostrando <span class="font-medium"><?= (($page - 1) * 15) + 1 ?></span> a 
                        <span class="font-medium"><?= min($page * 15, $total) ?></span> de 
                        <span class="font-medium"><?= $total ?></span> resultados
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&q=<?= urlencode($search) ?>&estatus=<?= urlencode($estatus) ?>&nivel_riesgo=<?= urlencode($nivelRiesgo) ?>" 
                           class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?page=<?= $i ?>&q=<?= urlencode($search) ?>&estatus=<?= urlencode($estatus) ?>&nivel_riesgo=<?= urlencode($nivelRiesgo) ?>" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium <?= $i === $page ? 'bg-primary-50 text-primary-600' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>&estatus=<?= urlencode($estatus) ?>&nivel_riesgo=<?= urlencode($nivelRiesgo) ?>" 
                           class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
