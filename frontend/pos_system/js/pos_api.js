/**
 * Sistema POS - Módulo de API
 * =============================
 * Maneja todas las peticiones HTTP al backend PHP
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

class POS_API {
    constructor() {
        this.baseUrl = '../api/v1/'; // Base URL para las APIs PHP
    }

    /**
     * Realiza una petición genérica
     */
    async request(endpoint, method = 'GET', data = null, isJson = true) {
        const url = this.baseUrl + endpoint;
        const config = {
            method: method,
            headers: {}
        };

        if (isJson) {
            config.headers['Content-Type'] = 'application/json';
        }
        
        // Agregar token de autorización si existe (Supabase JWT)
        // const jwt = localStorage.getItem('supabase_jwt');
        // if (jwt) {
        //     config.headers['Authorization'] = 'Bearer ' + jwt;
        // }

        if (data) {
            if (method === 'GET') {
                // Para GET, codificar datos como query params
                const params = new URLSearchParams(data).toString();
                url += '?' + params;
            } else {
                // Para POST/PUT/PATCH, enviar datos en el cuerpo
                config.body = isJson ? JSON.stringify(data) : data;
            }
        }

        try {
            const response = await fetch(url, config);
            const result = await response.json();
            
            if (response.ok) {
                return { success: true, data: result };
            } else {
                return { success: false, error: result.error || 'Error de la API' };
            }
        } catch (error) {
            return { success: false, error: 'Error de red o servidor' };
        }
    }
    
    /**
     * Obtener producto por código de barras
     */
    async getProductByBarcode(barcode) {
        // Asumiendo que products.php maneja el parámetro 'barcode' en GET
        return this.request('products.php?barcode=' + encodeURIComponent(barcode), 'GET', null, false);
    }
    
    /**
     * Buscar productos por término
     */
    async searchProducts(searchTerm) {
        // Asumiendo que products.php maneja el parámetro 'search' en GET
        return this.request('products.php?search=' + encodeURIComponent(searchTerm), 'GET', null, false);
    }

    /**
     * Guardar una nueva venta
     */
    async saveSale(saleData) {
        // Asumiendo que sales.php recibe un POST con los datos de la venta
        return this.request('sales.php', 'POST', saleData);
    }

    /**
     * Actualizar stock de producto (simulado)
     */
    async updateProductStock(productId, quantity) {
        // En una API real, esto sería una ruta específica o un trigger en Supabase
        // Aquí simulamos una llamada:
        console.log(`Simulando actualización de stock: Prod ID ${productId}, Cantidad: -${quantity}`);
        return { success: true }; 
        // return this.request('products.php?id=' + productId, 'PATCH', { action: 'decrement_stock', quantity: quantity });
    }

    /**
     * Obtener el HTML del ticket para imprimir
     */
    async getTicketHTML(saleData) {
        // Llamada a la plantilla de PHP que genera el HTML del ticket
        try {
            const response = await fetch('../templates/ticket.php', {
                method: 'POST', // Usar POST para enviar los datos de la venta
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ sale: saleData })
            });
            
            if (response.ok) {
                return response.text();
            }
            return null;
        } catch (error) {
            console.error('Error obteniendo HTML del ticket:', error);
            return null;
        }
    }
    
    /**
     * Llamada para generar/firmar DTE (simulado)
     */
    async generateDTE(dteData) {
        console.log('Simulando generación y firma de DTE...');
        // Simular éxito después de un tiempo
        await new Promise(resolve => setTimeout(resolve, 1500)); 
        
        return { 
            success: true, 
            data: { 
                ...dteData, 
                estado: 'RECIBIDO',
                link_validacion: 'https://dte.test.gob.sv/' + dteData.identificacion.codigoGeneracion
            } 
        };
    }
}
