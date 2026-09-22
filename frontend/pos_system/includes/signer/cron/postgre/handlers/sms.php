<?php

require_once __DIR__ . '/../../../../utils/sms_notifier.php';

function handle_sms(array $payload) {

    if (empty($payload['telefono'])) {
        throw new Exception('Teléfono vacío');
    }

    $ok = enviar_sms_notificacion(
        $payload['telefono'],
        $payload['mensaje']
    );

    if (!$ok) {
        throw new Exception('Error enviando SMS');
    }
}
