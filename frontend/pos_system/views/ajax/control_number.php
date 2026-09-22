<?php
class DocumentControlNumber {
    const CONTROL_FILE = 'storage/control_numbers.json';
    
    const DOCUMENT_CODES = [
        "Factura" => "01",
        "Comprobante de crédito fiscal" => "03",
        "Nota de remisión" => "04",
        "Nota de crédito" => "05",
        "Nota de débito" => "06",
        "Comprobante de retención" => "07",
        "Comprobante de liquidación" => "08",
        "Documento contable de liquidación" => "09",
        "Facturas de exportación" => "11",
        "Factura de sujeto excluido" => "14",
        "Comprobante de donación" => "15"
    ];
    
    const ESTABLISHMENT_CODES = [
        "Sucursal" => "12345678",
        "Casa matriz" => "12345678",
        "Bodega" => "12345678",
        "Predio y/o patio" => "12345678",
        "Otro" => "12345678"
    ];
    
    public static function generate(string $documentType, string $establishment): string {
        // Validar parámetros
        if (!isset(self::DOCUMENT_CODES[$documentType])) {
            throw new InvalidArgumentException("Tipo de documento no válido");
        }
        if (!isset(self::ESTABLISHMENT_CODES[$establishment])) {
            throw new InvalidArgumentException("Establecimiento no válido");
        }
        
        $docCode = self::DOCUMENT_CODES[$documentType];
        $estabCode = self::ESTABLISHMENT_CODES[$establishment];
        
        // Crear directorio si no existe
        if (!file_exists('storage')) {
            mkdir('storage', 0755, true);
        }
        
        // Cargar o inicializar datos
        $data = file_exists(self::CONTROL_FILE) ? 
                json_decode(file_get_contents(self::CONTROL_FILE), true) : [];
        
        // Generar clave única con año
        $year = date('Y');
        $key = "{$docCode}-{$estabCode}-{$year}";
        
        // Obtener y actualizar número
        $lastNumber = $data[$key] ?? 0;
        $newNumber = $lastNumber + 1;
        $data[$key] = $newNumber;
        
        // Guardar datos
        file_put_contents(self::CONTROL_FILE, json_encode($data, JSON_PRETTY_PRINT));
        
        // Formatear número de control (sin año, con 8 dígitos)
        return "DTE-{$docCode}-{$estabCode}-" . str_pad($newNumber, 15, '0', STR_PAD_LEFT);
    }
}

// Generar el número de control (ejemplo con Factura y Casa matriz)
$controlnumber = DocumentControlNumber::generate("Factura", "Casa matriz");
?>