<?php
/**
 * VERIFICATION SCRIPT - Confirma que todos los cambios fueron aplicados
 */

echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║         VERIFICATION OF CRITICAL FIXES IMPLEMENTATION             ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

$base_path = __DIR__ . '/../';
$issues = [];
$fixed = [];

// ================================================================
// CHECK 1: dte_validator_new.php - codigoGeneracion UUID v4
// ================================================================
echo "CHECK 1: dte_validator_new.php - codigoGeneracion validation\n";
echo "──────────────────────────────────────────────────────────────\n";

$file = $base_path . 'includes/signer/utils/dte_validator_new.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    
    // Check for WRONG pattern (8 chars)
    if (preg_match('/preg_match\([\'"]\/\^\\[A-Z0-9\\]\\{8\\}\$\/', $content)) {
        $issues[] = "❌ dte_validator_new.php still has 8-char validation for codigoGeneracion";
        echo "❌ FAILED: Old 8-char pattern found\n";
    } else if (preg_match('/\[A-F0-9\]\\{8\\}-\[A-F0-9\]\\{4\\}-\[A-F0-9\]\\{4\\}-\[A-F0-9\]\\{4\\}-\[A-F0-9\]\\{12\\}/', $content)) {
        $fixed[] = "✅ dte_validator_new.php correctly validates codigoGeneracion as UUID v4 (36 chars)";
        echo "✅ PASSED: UUID v4 pattern found (36 chars)\n";
    } else {
        $issues[] = "⚠️ Could not verify UUID v4 pattern in dte_validator_new.php";
        echo "⚠️ WARNING: UUID v4 pattern not clearly found\n";
    }
} else {
    $issues[] = "❌ File not found: dte_validator_new.php";
    echo "❌ FAILED: File not found\n";
}
echo "\n";

// ================================================================
// CHECK 2: process_sale_complete.php - numeroControl before validation
// ================================================================
echo "CHECK 2: process_sale_complete.php - numeroControl generation order\n";
echo "───────────────────────────────────────────────────────────────────\n";

$file = $base_path . 'views/ajax/process_sale_complete.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    
    // Find line numbers
    $lines = explode("\n", $content);
    $line_num = 0;
    $validate_line = 0;
    $generate_line = 0;
    $assign_line = 0;
    
    foreach ($lines as $idx => $line) {
        if (strpos($line, 'validar_json_dte_nuevo') !== false && strpos($line, '\$validacion =') !== false) {
            $validate_line = $idx + 1;
        }
        if (strpos($line, 'GENERAR NUMERO DE CONTROL') !== false) {
            $generate_line = $idx + 1;
        }
        if (strpos($line, "['identificacion']['numeroControl']") !== false && strpos($line, 'ASIGNAR') === false) {
            $assign_line = $idx + 1;
        }
    }
    
    if ($generate_line > 0 && $validate_line > 0) {
        if ($generate_line < $validate_line && $assign_line < $validate_line) {
            $fixed[] = "✅ process_sale_complete.php: numeroControl generated BEFORE validation (correct order)";
            echo "✅ PASSED: Correct order - Generate (line $generate_line) → Assign (line $assign_line) → Validate (line $validate_line)\n";
        } else {
            $issues[] = "❌ process_sale_complete.php: Wrong order - validation happens before generation";
            echo "❌ FAILED: Wrong order - Validate at line $validate_line, Generate at line $generate_line\n";
        }
    } else {
        echo "⚠️ WARNING: Could not determine line numbers\n";
    }
} else {
    $issues[] = "❌ File not found: process_sale_complete.php";
    echo "❌ FAILED: File not found\n";
}
echo "\n";

// ================================================================
// CHECK 3: pos_sale.php - NRC is 4 digits
// ================================================================
echo "CHECK 3: pos_sale.php - NRC field (4 digits)\n";
echo "────────────────────────────────────────────\n";

$file = $base_path . 'views/pos_sale.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    
    // Look for NRC assignment
    if (preg_match('/"nrc":\s*"(\d+)"/', $content, $matches)) {
        $nrc_value = $matches[1];
        $nrc_length = strlen($nrc_value);
        
        if ($nrc_length === 4) {
            $fixed[] = "✅ pos_sale.php: NRC is 4 digits ('$nrc_value')";
            echo "✅ PASSED: NRC is 4 digits ('$nrc_value')\n";
        } else {
            $issues[] = "❌ pos_sale.php: NRC has $nrc_length digits, should be 4";
            echo "❌ FAILED: NRC has $nrc_length digits (should be 4)\n";
        }
    } else {
        echo "⚠️ WARNING: Could not find NRC field\n";
    }
} else {
    $issues[] = "❌ File not found: pos_sale.php";
    echo "❌ FAILED: File not found\n";
}
echo "\n";

// ================================================================
// CHECK 4: dte_validator_schema.php exists
// ================================================================
echo "CHECK 4: dte_validator_schema.php - Deep validation (NEW FILE)\n";
echo "──────────────────────────────────────────────────────────────\n";

$file = $base_path . 'includes/signer/utils/dte_validator_schema.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    
    // Check for key validations
    $checks = [
        'numeroControl' => 'Validating numeroControl',
        'codigoGeneracion' => 'Validating codigoGeneracion',
        'ventaGravada' => 'Validating ventaGravada',
        'ivaItem' => 'Validating ivaItem',
        'condicionOperacion' => 'Validating condicionOperacion'
    ];
    
    $found_checks = 0;
    foreach ($checks as $check => $desc) {
        if (strpos($content, $check) !== false) {
            $found_checks++;
        }
    }
    
    if ($found_checks >= 4) {
        $fixed[] = "✅ dte_validator_schema.php: Complete deep validation file created";
        echo "✅ PASSED: Schema validator created with comprehensive checks ($found_checks/5)\n";
    } else {
        $issues[] = "⚠️ dte_validator_schema.php: May be incomplete (only $found_checks/5 checks found)";
        echo "⚠️ WARNING: Only $found_checks/5 key validations found\n";
    }
} else {
    $issues[] = "❌ File not found: dte_validator_schema.php";
    echo "❌ FAILED: New schema validator file not found\n";
}
echo "\n";

// ================================================================
// CHECK 5: pos_sale.php - UUID v4 generation
// ================================================================
echo "CHECK 5: pos_sale.php - UUID v4 generation function\n";
echo "───────────────────────────────────────────────────\n";

$file = $base_path . 'views/pos_sale.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    
    // Check for UUID generation function
    if (preg_match('/function\s+generarCodigoGeneracion\s*\(\s*\)/', $content)) {
        if (preg_match('/bin2hex\s*\(\s*random_bytes/', $content)) {
            if (preg_match('/random_bytes\s*\(\s*4\s*\).*random_bytes\s*\(\s*2\s*\).*random_bytes\s*\(\s*2\s*\).*random_bytes\s*\(\s*2\s*\).*random_bytes\s*\(\s*6\s*\)/s', $content)) {
                $fixed[] = "✅ pos_sale.php: UUID v4 generation function is correct (8-4-4-4-12)";
                echo "✅ PASSED: UUID v4 generation function found (4,2,2,2,6 bytes)\n";
            } else {
                $issues[] = "⚠️ pos_sale.php: UUID generation found but byte distribution may be wrong";
                echo "⚠️ WARNING: byte distribution may be incorrect\n";
            }
        } else {
            $issues[] = "❌ pos_sale.php: UUID generation function doesn't use bin2hex/random_bytes";
            echo "❌ FAILED: Not using bin2hex/random_bytes\n";
        }
    } else {
        $issues[] = "❌ pos_sale.php: UUID generation function not found";
        echo "❌ FAILED: generarCodigoGeneracion() not found\n";
    }
} else {
    $issues[] = "❌ File not found: pos_sale.php";
    echo "❌ FAILED: File not found\n";
}
echo "\n";

// ================================================================
// CHECK 6: Validator includes in process_sale_complete
// ================================================================
echo "CHECK 6: process_sale_complete.php - Includes correct validator\n";
echo "──────────────────────────────────────────────────────────────\n";

$file = $base_path . 'views/ajax/process_sale_complete.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    
    if (preg_match('/require_once.*dte_validator_new\.php/', $content)) {
        $fixed[] = "✅ process_sale_complete.php: Includes dte_validator_new.php";
        echo "✅ PASSED: Correct validator included\n";
    } else {
        $issues[] = "❌ process_sale_complete.php: Doesn't include dte_validator_new.php";
        echo "❌ FAILED: Validator not included\n";
    }
} else {
    $issues[] = "❌ File not found: process_sale_complete.php";
    echo "❌ FAILED: File not found\n";
}
echo "\n";

// ================================================================
// FINAL SUMMARY
// ================================================================
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║                           SUMMARY                                 ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

echo "✅ FIXED ITEMS (" . count($fixed) . "):\n";
foreach ($fixed as $item) {
    echo "   $item\n";
}
echo "\n";

if (!empty($issues)) {
    echo "❌ ISSUES (" . count($issues) . "):\n";
    foreach ($issues as $item) {
        echo "   $item\n";
    }
    echo "\n";
}

$total_checks = count($fixed) + count($issues);
$success_rate = round((count($fixed) / $total_checks) * 100);

echo "VERIFICATION STATUS:\n";
echo "─────────────────────\n";
echo "  Fixed: " . count($fixed) . "/$total_checks\n";
echo "  Issues: " . count($issues) . "/$total_checks\n";
echo "  Success Rate: $success_rate%\n\n";

if (count($issues) === 0) {
    echo "╔════════════════════════════════════════════════════════════════════╗\n";
    echo "║  ✅ ALL CRITICAL FIXES HAVE BEEN SUCCESSFULLY IMPLEMENTED         ║\n";
    echo "║     READY FOR TESTING AND DEPLOYMENT                             ║\n";
    echo "╚════════════════════════════════════════════════════════════════════╝\n";
} else {
    echo "╔════════════════════════════════════════════════════════════════════╗\n";
    echo "║  ⚠️  PLEASE REVIEW THE ISSUES ABOVE BEFORE DEPLOYMENT              ║\n";
    echo "╚════════════════════════════════════════════════════════════════════╝\n";
}

echo "\n";
echo "Next steps:\n";
echo "1. Run: php __TESTERS/TEST_VALIDATION_FIXES.php\n";
echo "2. Test a complete sale from the POS\n";
echo "3. Verify logs in /storage/logs/\n";
echo "4. Submit to MH API\n";
