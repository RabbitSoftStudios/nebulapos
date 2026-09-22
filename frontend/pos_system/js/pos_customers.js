$(document).ready(function () {
    const API_URL = (typeof BASE_URL !== 'undefined' ? BASE_URL : '..') + '/api/v1/customers.php';

    // Reset form when opening "Add Customer" modal
    $('#btn-add-customer').on('click', function () {
        $('#customerForm')[0].reset();
        $('#customer-id').val('');
        $('#customerModalLabel').text('Agregar Nuevo Cliente');
    });

    // Populate form when clicking "Edit"
    $(document).on('click', '.btn-edit', function () {
        const btn = $(this);

        $('#customerModalLabel').text('Editar Cliente');
        $('#customer-id').val(btn.data('id'));
        $('#nombres').val(btn.data('nombres'));
        $('#apellidos').val(btn.data('apellidos'));
        $('#dui').val(btn.data('dui'));
        $('#nit').val(btn.data('nit'));
        $('#nrc').val(btn.data('nrc'));
        $('#telefono').val(btn.data('telefono'));
        $('#email').val(btn.data('email'));
        $('#direccion').val(btn.data('direccion'));
        $('#municipio').val(btn.data('municipio'));
        $('#departamento').val(btn.data('departamento'));

        // Handle boolean/checkbox
        const isActive = btn.data('active') == 1 || btn.data('active') === 'true' || btn.data('active') === true;
        $('#active').prop('checked', isActive);
    });

    // Handle Form Submission
    $('#customerForm').on('submit', function (e) {
        e.preventDefault();

        const customerId = $('#customer-id').val();
        const isEdit = customerId ? true : false;
        const method = isEdit ? 'PUT' : 'POST';

        // Collect form data
        const formData = {
            id: customerId, // Needed for PUT logic in PHP or URL
            nombres: $('#nombres').val(),
            apellidos: $('#apellidos').val(),
            dui: $('#dui').val(),
            nit: $('#nit').val(),
            nrc: $('#nrc').val(),
            telefono: $('#telefono').val(),
            email: $('#email').val(),
            direccion: $('#direccion').val(),
            municipio: $('#municipio').val(),
            departamento: $('#departamento').val(),
            active: $('#active').is(':checked')
        };

        // For simple handling, we append ID to URL for PUT/DELETE if needed, 
        // but our PHP API also checks body 'id' for PUT.
        let url = API_URL;
        if (isEdit) {
            url += '?id=' + customerId;
        }

        const submitBtn = $('#save-customer-btn');
        const originalBtnText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Guardando...');

        $.ajax({
            url: url,
            method: method,
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function (response) {
                if (response.success) {
                    alert('Cliente guardado exitosamente');
                    $('#customerModal').modal('hide');
                    location.reload(); // Simple reload to refresh table
                } else {
                    alert('Error: ' + (response.error || 'No se pudo guardar'));
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                try {
                    const res = JSON.parse(xhr.responseText);
                    alert('Error: ' + (res.error || error));
                } catch (e) {
                    alert('Ocurrió un error al procesar la solicitud.');
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    });

    // Handle Delete
    $(document).on('click', '.btn-delete', function () {
        if (!confirm('¿Está seguro de eliminar este cliente?')) {
            return;
        }

        const id = $(this).data('id');

        $.ajax({
            url: API_URL + '?id=' + id,
            method: 'DELETE',
            success: function (response) {
                if (response.success) {
                    alert('Cliente eliminado exitosamente');
                    location.reload();
                } else {
                    alert('Error: ' + (response.error || 'No se pudo eliminar'));
                }
            },
            error: function (xhr) {
                alert('Ocurrió un error al eliminar el cliente.');
            }
        });
    });
});
