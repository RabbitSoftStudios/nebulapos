<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const API_URL = '/controllers/product_api.php';

/* ===============================
   MODAL
================================ */
function openNewProduct() {
  document.getElementById('modalTitle').innerText = 'Nuevo Producto';
  document.getElementById('productForm').reset();
  document.querySelector('[name=action]').value = 'create';
  document.getElementById('modalProduct').style.display = 'block';
}

function closeProductModal() {
  document.getElementById('modalProduct').style.display = 'none';
}

/* ===============================
   SUBMIT FORM
================================ */
document.getElementById('productForm').addEventListener('submit', function(e) {
  e.preventDefault();

  Swal.fire({
    title: 'Guardando...',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  fetch(API_URL, {
    method: 'POST',
    body: new FormData(this)
  })
  .then(r => r.json())
  .then(res => {
    Swal.close();
    if (res.success) {
      Swal.fire('Éxito', 'Producto guardado correctamente', 'success');
      closeProductModal();
      loadProducts(currentPage);
    } else {
      Swal.fire('Error', res.error || 'No se pudo guardar', 'error');
    }
  });
});

/* ===============================
   DELETE
================================ */
function deleteProduct(id, nombre) {
  Swal.fire({
    title: '¿Eliminar producto?',
    text: nombre,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar'
  }).then(result => {
    if (!result.isConfirmed) return;

    fetch(API_URL, {
      method: 'POST',
      body: new URLSearchParams({ action:'delete', id })
    })
    .then(r => r.json())
    .then(() => {
      Swal.fire('Eliminado', 'Producto eliminado', 'success');
      loadProducts(currentPage);
    });
  });
}
</script>
