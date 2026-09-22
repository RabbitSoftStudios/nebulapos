/**
 * Sistema POS - Configuración
 * ===========================
 * Maneja la interacción de la interfaz de configuración con la API.
 * Incluye gestión de empresa y usuarios con soporte multiempresa.
 */

$(document).ready(function () {
    const API_SETTINGS_URL = (typeof BASE_URL !== 'undefined' ? BASE_URL : '..') + '/api/v1/settings.php';
    const API_USERS_URL = (typeof BASE_URL !== 'undefined' ? BASE_URL : '..') + '/api/v1/users.php';

    let usersTable = null;

    // ========================================
    // SECCIÓN: INFORMACIÓN DE LA EMPRESA
    // ========================================

    // Cargar información de la empresa al iniciar
    loadCompanySettings();

    function loadCompanySettings() {
        $.ajax({
            url: API_SETTINGS_URL,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success && response.data) {
                    const data = response.data;

                    // Llenar el formulario de información de la empresa
                    $('#company_user_id').val(data.user_id || '');
                    $('#company-nombre').val(data.nombre || '');
                    $('#company-razon_social').val(data.razon_social || '');
                    $('#company-nit').val(data.nit || '');
                    $('#company-nrc').val(data.nrc || '');
                    $('#company-actividad_economica').val(data.actividad_economica || '');
                    $('#company-direccion').val(data.direccion || '');
                    $('#company-municipio').val(data.municipio || '');
                    $('#company-departamento').val(data.departamento || '');
                    $('#company-pais').val(data.pais || 'El Salvador');

                    // Guardar el ID de la empresa para actualizaciones futuras
                    if (data.id) {
                        $('#generalSettingsForm').data('empresa-id', data.id);
                    }

                    // Guardar UUID de la empresa para usuarios
                    if (data.uuid) {
                        $('#generalSettingsForm').data('empresa-uuid', data.uuid);
                    }
                } else {
                    console.warn('No se encontró información de la empresa');
                }
            },
            error: function (xhr) {
                console.error('Error cargando configuración:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cargar la información de la empresa'
                });
            }
        });
    }

    // Manejar el envío del formulario de información de la empresa
    $('#generalSettingsForm').on('submit', function (e) {
        e.preventDefault();

        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.html();

        // Mostrar confirmación con SweetAlert2
        Swal.fire({
            title: '¿Está seguro?',
            text: '¿Desea actualizar la información de la empresa?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Obtener datos del formulario
                const formData = {
                    id: $(this).data('empresa-id'),
                    nombre: $('#company-nombre').val(),
                    razon_social: $('#company-razon_social').val(),
                    nit: $('#company-nit').val(),
                    nrc: $('#company-nrc').val(),
                    actividad_economica: $('#company-actividad_economica').val(),
                    direccion: $('#company-direccion').val(),
                    municipio: $('#company-municipio').val(),
                    departamento: $('#company-departamento').val(),
                    pais: $('#company-pais').val(),
                    user_id: $('#company_user_id').val()
                };

                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Guardando...');

                $.ajax({
                    url: API_SETTINGS_URL,
                    method: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Información de la empresa guardada exitosamente',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            loadCompanySettings(); // Recargar para confirmar
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error || 'No se pudo guardar la información'
                            });
                        }
                    },
                    error: function (xhr) {
                        try {
                            const res = JSON.parse(xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.error || 'Error en el servidor'
                            });
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error de conexión con el servidor'
                            });
                        }
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            }
        });
    });

    // ========================================
    // SECCIÓN: GESTIÓN DE USUARIOS
    // ========================================

    // Cargar usuarios al iniciar
    loadUsers();

    function loadUsers() {
        $.ajax({
            url: API_USERS_URL,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success && response.data) {
                    renderUsersTable(response.data);
                } else {
                    $('#usersTableBody').html('<tr><td colspan="4" class="text-center text-muted">No hay usuarios registrados</td></tr>');
                }
            },
            error: function (xhr) {
                console.error('Error cargando usuarios:', xhr.responseText);
                $('#usersTableBody').html('<tr><td colspan="4" class="text-center text-danger">Error al cargar usuarios</td></tr>');
            }
        });
    }

    function renderUsersTable(users) {
        const tbody = $('#usersTableBody');
        tbody.empty();

        if (!users || users.length === 0) {
            tbody.html('<tr><td colspan="4" class="text-center text-muted">No hay usuarios registrados</td></tr>');
            return;
        }

        users.forEach(user => {
            const row = `
                <tr>
                    <td>${escapeHtml(user.nombre || 'N/A')}</td>
                    <td>${escapeHtml(user.email || 'N/A')}</td>
                    <td><span class="badge bg-primary">${escapeHtml(user.rol || 'N/A')}</span></td>
                    <td>
                        <button class="btn btn-sm btn-warning me-1" onclick="editUser(${user.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        // Inicializar DataTable si no está inicializado
        if (!usersTable) {
            usersTable = $('#usersTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [[0, 'asc']]
            });
        } else {
            usersTable.destroy();
            usersTable = $('#usersTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [[0, 'asc']]
            });
        }
    }

    // Abrir modal para nuevo usuario
    $('button[data-bs-target="#userModal"]').on('click', function () {
        resetUserForm();
        $('#userModalTitle').text('Nuevo Usuario');
        $('#password-required').show();
        $('#user-password').prop('required', true);
    });

    // Resetear formulario de usuario
    function resetUserForm() {
        $('#userForm')[0].reset();
        $('#user-id').val('');
        $('#user-activo').prop('checked', true);
    }

    // Editar usuario (función global)
    window.editUser = function (userId) {
        $.ajax({
            url: API_USERS_URL + '?id=' + userId,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success && response.data) {
                    const user = response.data;

                    // Llenar formulario
                    $('#user-id').val(user.id);
                    $('#user-nombre').val(user.nombre);
                    $('#user-email').val(user.email);
                    $('#user-rol').val(user.rol);
                    $('#user-telefono').val(user.telefono || '');
                    $('#user-activo').prop('checked', user.activo !== false);

                    // Cambiar título y hacer contraseña opcional
                    $('#userModalTitle').text('Editar Usuario');
                    $('#password-required').hide();
                    $('#user-password').prop('required', false).val('');

                    // Abrir modal
                    const modal = new bootstrap.Modal(document.getElementById('userModal'));
                    modal.show();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar el usuario'
                    });
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar el usuario'
                });
            }
        });
    };

    // Eliminar usuario (función global)
    window.deleteUser = function (userId) {
        Swal.fire({
            title: '¿Está seguro?',
            text: '¿Desea eliminar este usuario? Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: API_USERS_URL,
                    method: 'DELETE',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: userId }),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: 'Usuario eliminado exitosamente',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            loadUsers();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error || 'No se pudo eliminar el usuario'
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al eliminar el usuario'
                        });
                    }
                });
            }
        });
    };

    // Manejar envío del formulario de usuario
    $('#userForm').on('submit', function (e) {
        e.preventDefault();

        const userId = $('#user-id').val();
        const isEdit = userId !== '';

        const userData = {
            nombre: $('#user-nombre').val(),
            email: $('#user-email').val(),
            rol: $('#user-rol').val(),
            telefono: $('#user-telefono').val(),
            activo: $('#user-activo').is(':checked')
        };

        // Solo incluir contraseña si se proporcionó
        const password = $('#user-password').val();
        if (password) {
            userData.password = password;
        }

        // Si es edición, incluir ID
        if (isEdit) {
            userData.id = userId;
        }

        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Guardando...');

        $.ajax({
            url: API_USERS_URL,
            method: isEdit ? 'PUT' : 'POST',
            contentType: 'application/json',
            data: JSON.stringify(userData),
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: isEdit ? 'Usuario actualizado exitosamente' : 'Usuario creado exitosamente',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Cerrar modal y recargar tabla
                    const modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
                    modal.hide();
                    loadUsers();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.error || 'No se pudo guardar el usuario'
                    });
                }
            },
            error: function (xhr) {
                try {
                    const res = JSON.parse(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.error || 'Error en el servidor'
                    });
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión con el servidor'
                    });
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // ========================================
    // UTILIDADES
    // ========================================

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }

    // Configuración financiera (desactivada por ahora)
    $('#financialSettingsForm').on('submit', function (e) {
        e.preventDefault();
        Swal.fire({
            icon: 'info',
            title: 'Próximamente',
            text: 'Configuración financiera (Impuestos y Moneda) se conectará en la siguiente versión.'
        });
    });
});
