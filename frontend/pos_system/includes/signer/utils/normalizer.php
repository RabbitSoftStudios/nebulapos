<?php
/**
 * Archivo: /includes/dte/dte_normalizer.php
 * Sistema: POS + DTE El Salvador
 * Descripción:
 *  Normaliza y corrige automáticamente un DTE armado por el POS
 *  para que cumpla el schema oficial del MH antes de firmarse.
 *
 * Fecha: 2026-01-08
 * Team: MYTS Cloud Computing
 */

function normalizarDTE(array $dte): array
{
    /* ===============================
     * IDENTIFICACION
     * =============================== */

    if (!isset($dte['identificacion'])) {
        $dte['identificacion'] = [];
    }

    // numeroControl OBLIGATORIO
    if (empty($dte['identificacion']['numeroControl'])) {
        throw new Exception("numeroControl no presente. Debe generarse antes de firmar.");
    }

    // tipoDte default
    $dte['identificacion']['tipoDte'] ??= '01';

    /* ===============================
     * RECEPTOR - CONSUMIDOR FINAL
     * =============================== */
    if (
        ($dte['identificacion']['tipoDte'] === '01') &&
        (!isset($dte['receptor']['numDocumento']))
    ) {
        $dte['receptor']['tipoDocumento'] = null;
        $dte['receptor']['numDocumento']  = null;
        $dte['receptor']['nrc']            = null;
        $dte['receptor']['nombre']        ??= 'Consumidor Final';
    }

    /* ===============================
     * CUERPO DOCUMENTO
     * =============================== */
    foreach ($dte['cuerpoDocumento'] as &$item) {

        // tributos cuando hay venta gravada
        if (
            ($item['ventaGravada'] ?? 0) > 0 &&
            empty($item['tributos'])
        ) {
            $item['tributos'] = ['20']; // IVA
        }

        // tributos no puede ser []
        if (isset($item['tributos']) && is_array($item['tributos']) && count($item['tributos']) === 0) {
            $item['tributos'] = null;
        }
    }

    /* ===============================
     * RESUMEN
     * =============================== */

    // tributos NO puede ser []
    if (isset($dte['resumen']['tributos']) && empty($dte['resumen']['tributos'])) {
        $dte['resumen']['tributos'] = null;
    }

    return $dte;
}
