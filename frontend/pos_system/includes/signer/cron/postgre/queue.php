<?php

class Queue {

    public static function take(PDO $pdo) {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            SELECT *
            FROM job_queue
            WHERE estado = 'pendiente'
              AND disponible_en <= now()
            ORDER BY prioridad ASC, created_at ASC
            FOR UPDATE SKIP LOCKED
            LIMIT 1
        ");

        $stmt->execute();
        $job = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$job) {
            $pdo->commit();
            return null;
        }

        $pdo->commit();
        return $job;
    }

    public static function markProcessing(PDO $pdo, int $id) {
        $pdo->prepare("
            UPDATE job_queue
            SET estado='procesando', updated_at=now()
            WHERE id=:id
        ")->execute([':id'=>$id]);
    }

    public static function markDone(PDO $pdo, int $id) {
        $pdo->prepare("
            UPDATE job_queue
            SET estado='completado', updated_at=now()
            WHERE id=:id
        ")->execute([':id'=>$id]);
    }

    public static function markFailed(PDO $pdo, int $id, string $error) {
        $pdo->prepare("
            UPDATE job_queue
            SET
              estado = CASE WHEN intentos >= 3 THEN 'fallido' ELSE 'pendiente' END,
              intentos = intentos + 1,
              error = :error,
              disponible_en = now() + interval '2 minutes',
              updated_at = now()
            WHERE id=:id
        ")->execute([
            ':id'=>$id,
            ':error'=>$error
        ]);
    }
}
