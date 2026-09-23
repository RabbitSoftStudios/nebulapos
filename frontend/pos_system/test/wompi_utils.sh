#!/bin/bash
# ============================================================
# SCRIPT DE UTILIDADES - INTEGRACIÓN WOMPI
# ============================================================
# Use este script para tareas comunes de mantenimiento
# 
# Uso:
#   bash wompi_utils.sh [comando]
#
# Comandos disponibles:
#   test-config       Probar que .env está configurado
#   test-endpoints    Probar todos los endpoints
#   check-files       Verificar que archivos existen
#   clear-logs        Limpiar logs
#   backup            Hacer backup de configuración
#   help              Mostrar esta ayuda

# ============================================================
# CONFIGURACIÓN
# ============================================================

BASE_DIR="$(cd "$(dirname "$0")" && pwd)"
ENV_FILE="$BASE_DIR/.env"
LOG_DIR="$BASE_DIR/storage/logs"

# ============================================================
# FUNCIONES AUXILIARES
# ============================================================

log_ok() {
    echo "✓ $1"
}

log_error() {
    echo "✗ $1"
}

log_info() {
    echo "ℹ $1"
}

# ============================================================
# COMANDO: test-config
# ============================================================

test_config() {
    log_info "Verificando configuración de Wompi..."
    
    if [ ! -f "$ENV_FILE" ]; then
        log_error "Archivo .env no encontrado en: $ENV_FILE"
        return 1
    fi
    
    if grep -q "WOMPI_CLIENT_ID" "$ENV_FILE"; then
        log_ok "WOMPI_CLIENT_ID encontrado"
    else
        log_error "WOMPI_CLIENT_ID no encontrado en .env"
        return 1
    fi
    
    if grep -q "WOMPI_CLIENT_SECRET" "$ENV_FILE"; then
        log_ok "WOMPI_CLIENT_SECRET encontrado"
    else
        log_error "WOMPI_CLIENT_SECRET no encontrado en .env"
        return 1
    fi
    
    log_ok "Configuración correcta"
}

# ============================================================
# COMANDO: test-endpoints
# ============================================================

test_endpoints() {
    log_info "Probando endpoints de Wompi..."
    
    BASE_URL="http://localhost:8000"
    
    # Test: get-config
    log_info "Probando: get-config"
    RESPONSE=$(curl -s "$BASE_URL/views/ajax/wompi_payment.php?action=get-config")
    if echo "$RESPONSE" | grep -q "success"; then
        log_ok "Endpoint get-config: OK"
    else
        log_error "Endpoint get-config: FALLO"
        echo "  Respuesta: $RESPONSE"
    fi
    
    # Test: create-payment
    log_info "Probando: create-payment"
    RESPONSE=$(curl -s -X POST "$BASE_URL/views/ajax/wompi_payment.php?action=create-payment" \
        -H "Content-Type: application/json" \
        -d '{"amount":1.00,"email":"test@example.com","description":"Test"}')
    
    if echo "$RESPONSE" | grep -q "success\|error"; then
        log_ok "Endpoint create-payment: OK"
    else
        log_error "Endpoint create-payment: FALLO"
        echo "  Respuesta: $RESPONSE"
    fi
    
    # Test: verify-payment
    log_info "Probando: verify-payment"
    RESPONSE=$(curl -s -X POST "$BASE_URL/views/ajax/wompi_payment.php?action=verify-payment" \
        -H "Content-Type: application/json" \
        -d '{"reference":"TEST-123"}')
    
    if echo "$RESPONSE" | grep -q "success"; then
        log_ok "Endpoint verify-payment: OK"
    else
        log_error "Endpoint verify-payment: FALLO"
        echo "  Respuesta: $RESPONSE"
    fi
}

# ============================================================
# COMANDO: check-files
# ============================================================

check_files() {
    log_info "Verificando archivos requeridos..."
    
    FILES=(
        "views/ajax/wompi_payment.php"
        "views/ajax/wompi.js"
        "config/wompi.php"
        "views/pos_sale.php"
        "WOMPI_INTEGRATION.php"
        "WOMPI_README.md"
        "CAMBIOS_REALIZADOS.md"
        "EJEMPLOS_WOMPI.js"
        "views/wompi_test.html"
        "views/wompi_checklist.html"
    )
    
    local count=0
    for file in "${FILES[@]}"; do
        if [ -f "$BASE_DIR/$file" ]; then
            log_ok "$file"
            ((count++))
        else
            log_error "$file (NO ENCONTRADO)"
        fi
    done
    
    log_info "Archivos encontrados: $count/${#FILES[@]}"
}

# ============================================================
# COMANDO: clear-logs
# ============================================================

clear_logs() {
    log_info "Limpiando logs..."
    
    if [ -d "$LOG_DIR" ]; then
        find "$LOG_DIR" -name "*.log" -type f -delete
        log_ok "Logs limpios"
    else
        log_error "Directorio de logs no existe: $LOG_DIR"
    fi
}

# ============================================================
# COMANDO: backup
# ============================================================

backup() {
    log_info "Creando backup..."
    
    BACKUP_DIR="$BASE_DIR/backups"
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    BACKUP_FILE="$BACKUP_DIR/wompi_backup_$TIMESTAMP.tar.gz"
    
    mkdir -p "$BACKUP_DIR"
    
    tar -czf "$BACKUP_FILE" \
        "$ENV_FILE" \
        "$BASE_DIR/config/wompi.php" \
        "$BASE_DIR/views/ajax/wompi_payment.php" \
        "$BASE_DIR/views/ajax/wompi.js" \
        2>/dev/null
    
    if [ -f "$BACKUP_FILE" ]; then
        log_ok "Backup creado: $BACKUP_FILE"
    else
        log_error "Error creando backup"
    fi
}

# ============================================================
# COMANDO: help
# ============================================================

show_help() {
    cat << EOF

╔════════════════════════════════════════════════════════════╗
║         UTILIDADES - INTEGRACIÓN WOMPI                    ║
╚════════════════════════════════════════════════════════════╝

USO:
  bash wompi_utils.sh [comando]

COMANDOS DISPONIBLES:

  test-config
    - Verifica que .env contiene credenciales de Wompi
    - Uso: bash wompi_utils.sh test-config

  test-endpoints
    - Prueba todos los endpoints de API
    - Uso: bash wompi_utils.sh test-endpoints
    - Nota: Requiere servidor activo en http://localhost:8000

  check-files
    - Verifica que todos los archivos existen
    - Uso: bash wompi_utils.sh check-files

  clear-logs
    - Limpia archivo de logs
    - Uso: bash wompi_utils.sh clear-logs

  backup
    - Crea backup de configuración y código Wompi
    - Uso: bash wompi_utils.sh backup
    - Ubicación: ./backups/wompi_backup_TIMESTAMP.tar.gz

  help
    - Muestra esta ayuda
    - Uso: bash wompi_utils.sh help

EJEMPLOS:

  # Verificar que todo está configurado
  bash wompi_utils.sh test-config

  # Probar que los endpoints funcionan
  bash wompi_utils.sh test-endpoints

  # Hacer backup antes de cambios
  bash wompi_utils.sh backup

  # Ver esta ayuda
  bash wompi_utils.sh help

DOCUMENTACIÓN:

  - WOMPI_INTEGRATION.php   : Documentación técnica
  - WOMPI_README.md         : Guía rápida
  - CAMBIOS_REALIZADOS.md   : Resumen de cambios
  - EJEMPLOS_WOMPI.js       : Ejemplos de código

EOF
}

# ============================================================
# MAIN
# ============================================================

COMMAND="${1:-help}"

case "$COMMAND" in
    test-config)
        test_config
        ;;
    test-endpoints)
        test_endpoints
        ;;
    check-files)
        check_files
        ;;
    clear-logs)
        clear_logs
        ;;
    backup)
        backup
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        log_error "Comando desconocido: $COMMAND"
        show_help
        exit 1
        ;;
esac

exit $?
