<?php
/**
 * Archivo: /includes/dte/dte_schema_validator.php
 * Descripción:
 *  Valida un DTE contra el schema oficial del MH (JSON Schema)
 *  para evitar errores silenciosos en firma o envío.
 *
 * Fecha: 2026-01-08
 * Team: MYTS Cloud Computing
 */
require_once __DIR__ . '/../../../../../vendor/autoload.php';

use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;

function validarDTEContraSchema(array $dte, string $schemaPath): void
{
    if (!file_exists($schemaPath)) {
        throw new Exception("Schema MH no encontrado: $schemaPath");
    }

    $validator = new Validator();

    $validator->validate(
        json_decode(json_encode($dte)),
        json_decode(file_get_contents($schemaPath)),
        Constraint::CHECK_MODE_APPLY_DEFAULTS
    );

    if (!$validator->isValid()) {
        $errores = [];

        foreach ($validator->getErrors() as $error) {
            $errores[] = "[{$error['property']}] {$error['message']}";
        }

        throw new Exception(
            "DTE NO cumple schema MH:\n" . implode("\n", $errores)
        );
    }
}
