<?php

function obtener_config_empresa(string $nit): array
{
    $db = pg_pool();

    $stmt = $db->prepare("
        SELECT *
        FROM dte_config_empresa
        WHERE nit = :nit
        LIMIT 1
    ");

    $stmt->execute([':nit' => $nit]);
    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$config) {
        throw new Exception('No existe configuración para la empresa NIT ' . $nit);
    }

    return $config;
}
