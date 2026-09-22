<?php
function render_template(string $template, array $data): string
{
    extract($data);
    ob_start();
    include __DIR__ . "/templates/{$template}.php";
    return ob_get_clean();
}

$template = match ($config['formato_factura']) {
    'CARTA_CLASICA' => 'factura_carta_clasica.php',
    'CARTA_MODERNA' => 'factura_carta_moderna.php',
    default => 'factura_carta_simple.php'
};

$html = render_template(
    "factura/{$template}",
    compact('dte', 'config', 'qrPath')
);
