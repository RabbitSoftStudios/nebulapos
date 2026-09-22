$(document).ready(function () {
    const API_URL = (typeof BASE_URL !== 'undefined' ? BASE_URL : '..') + '/api/v1/products.php';
    
    console.log('🔧 POS Products JS Initialized');
    console.log('📡 API URL:', API_URL);

    // Reset form
    $('#btn-add-product').on('click', function () {
        console.log('➕ Opening Add Product Modal');
        $('#productForm')[0].reset();
        $('#product-id').val('');
        $('#productModalLabel').text('Agregar Nuevo Producto');
    });

    // Populate Edit
    $(document).on('click', '.btn-edit', function () {
        const btn = $(this);
        console.log('✏️ Opening Edit Product Modal', {
            id: btn.data('id'),
            nombre: btn.data('nombre')
        });

        $('#productModalLabel').text('Editar Producto');
        $('#product-id').val(btn.data('id'));
        $('#codigo_producto').val(btn.data('codigo'));
        $('#codigo_barras').val(btn.data('barras'));
        $('#nombre_producto').val(btn.data('nombre'));
        $('#precio_venta').val(btn.data('precio'));
        $('#stock_actual').val(btn.data('stock'));
        $('#stock_minimo').val(btn.data('stockmin'));

        const isVentaLibre = btn.data('venta-libre') == 1;
        $('#es_venta_libre').prop('checked', isVentaLibre);
    });

    // Submit Form
    $('#productForm').on('submit', function (e) {
        e.preventDefault();

        const productId = $('#product-id').val();
        const isEdit = productId ? true : false;
        const method = isEdit ? 'PUT' : 'POST';

        const formData = {
            id: productId,
            codigo_producto: $('#codigo_producto').val(),
            codigo_barras: $('#codigo_barras').val(),
            nombre_producto: $('#nombre_producto').val(),
            precio_venta: $('#precio_venta').val(),
            stock_actual: $('#stock_actual').val() || 0,
            stock_minimo: $('#stock_minimo').val() || 5,
            es_venta_libre: $('#es_venta_libre').is(':checked')
        };

        console.log('💾 Submitting Product Form', {
            method: method,
            isEdit: isEdit,
            data: formData
        });

        const submitBtn = $('#save-product-btn');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Guardando...');

        $.ajax({
            url: API_URL + (isEdit ? '?id=' + productId : ''),
            method: method,
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function (response) {
                console.log('✅ Success Response:', response);
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: isEdit ? 'Producto actualizado exitosamente' : 'Producto creado exitosamente',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#productModal').modal('hide');
                        location.reload();
                    });
                } else {
                    console.error('❌ Server returned error:', response.error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.error || 'No se pudo guardar el producto',
                        confirmButtonText: 'Entendido'
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('❌ AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });

                let errorMessage = 'Error de conexión o servidor';
                
                try {
                    const res = JSON.parse(xhr.responseText);
                    errorMessage = res.error || res.message || errorMessage;
                    console.error('📋 Parsed Error:', res);
                } catch (e) {
                    console.error('⚠️ Could not parse error response');
                    if (xhr.responseText) {
                        errorMessage = xhr.responseText.substring(0, 200);
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error al Guardar',
                    html: `<p><strong>Detalles:</strong></p><p>${errorMessage}</p><p class="text-muted small">Código: ${xhr.status}</p>`,
                    confirmButtonText: 'Entendido',
                    footer: 'Revise los logs del servidor para más información'
                });
            },
            complete: function () {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Guardar Producto');
            }
        });
    });

    // Delete
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');
        const productName = $(this).closest('tr').find('td:eq(2)').text();
        
        console.log('🗑️ Delete Request for Product:', { id, productName });

        Swal.fire({
            title: '¿Está seguro?',
            html: `¿Desea eliminar el producto <strong>${productName}</strong>?<br><small class="text-muted">Esta acción no se puede deshacer</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('🗑️ Deleting product ID:', id);
                
                $.ajax({
                    url: API_URL + '?id=' + id,
                    method: 'DELETE',
                    success: function (response) {
                        console.log('✅ Delete Response:', response);
                        
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: 'Producto eliminado exitosamente',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            console.error('❌ Delete Error:', response.error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error || 'No se pudo eliminar el producto',
                                confirmButtonText: 'Entendido'
                            });
                        }
                    },
                    error: function (xhr) {
                        console.error('❌ Delete AJAX Error:', xhr);
                        
                        let errorMessage = 'Error al procesar la solicitud';
                        try {
                            const res = JSON.parse(xhr.responseText);
                            errorMessage = res.error || errorMessage;
                        } catch (e) {
                            // Ignore parse error
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                            confirmButtonText: 'Entendido'
                        });
                    }
                });
            }
        });
    });
    
    console.log('✅ All event handlers registered');
});
