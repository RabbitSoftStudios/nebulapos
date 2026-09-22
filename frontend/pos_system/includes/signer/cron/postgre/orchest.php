<?php
// el orchestador es el que envia los jobs a la queue de sms, pdf e email hay q agreagr la funcion que haga los tres en uno solo
$db = pg_pool();

$stmt = $db->prepare("
  INSERT INTO job_queue (tipo, payload, prioridad)
  VALUES ('sms', :payload, 5)
");

$stmt->execute([
  ':payload' => json_encode([
    'telefono' => $dteData['receptor']['telefono'],
    'mensaje'  => 'Su factura electrónica fue enviada a su correo.',
    'codigoGeneracion' => $codigoGeneracion
  ])
]);
