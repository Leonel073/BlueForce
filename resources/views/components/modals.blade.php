{{-- Modales Reutilizables del Sistema --}}

{{-- MODAL DE CONFIRMACIÓN GENÉRICA --}}
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-gradient" style="background: linear-gradient(135deg, #0B2D59, #2E608C); color: white; border: none;">
        <h5 class="modal-title fw-bold" id="confirmModalLabel">Confirmar acción</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p id="confirmModalMessage">¿Está seguro?</p>
      </div>
      <div class="modal-footer border-top-0">
        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">
          <i class="bi bi-x-lg me-1"></i> Cancelar
        </button>
        <button type="button" class="btn btn-primary rounded-3" id="confirmModalBtn">
          <i class="bi bi-check-lg me-1"></i> Continuar
        </button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL DE CONFIRMACIÓN DE LOGOUT --}}
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-danger text-white border-0">
        <h5 class="modal-title fw-bold" id="logoutModalLabel">
          <i class="bi bi-box-arrow-left me-2"></i>Cerrar sesión
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p class="mb-0">¿Desea finalizar la sesión actual?</p>
      </div>
      <div class="modal-footer border-top-0">
        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">
          <i class="bi bi-x-lg me-1"></i> Cancelar
        </button>
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
          @csrf
          <button type="submit" class="btn btn-danger rounded-3">
            <i class="bi bi-box-arrow-left me-1"></i> Cerrar sesión
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- MODAL DE NOTIFICACIÓN --}}
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-info text-white border-0">
        <h5 class="modal-title fw-bold" id="notificationModalLabel">
          <i class="bi bi-info-circle me-2"></i>Notificación
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p id="notificationModalMessage">Mensaje de notificación</p>
      </div>
      <div class="modal-footer border-top-0">
        <button type="button" class="btn btn-primary rounded-3" data-bs-dismiss="modal">
          <i class="bi bi-check-lg me-1"></i> Entendido
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  .modal-content {
    border-radius: 12px;
  }

  .modal-header {
    border-radius: 12px 12px 0 0;
  }

  .btn-close {
    font-size: 0.8rem;
  }

  @media (max-width: 576px) {
    .modal-dialog {
      margin: 10px !important;
    }
  }
</style>

<script>
  // Funciones globales para modales

  function showConfirmModal(message, callback) {
    document.getElementById('confirmModalMessage').textContent = message;
    const confirmBtn = document.getElementById('confirmModalBtn');
    
    // Limpiar listeners anteriores
    confirmBtn.onclick = null;
    confirmBtn.addEventListener('click', function() {
      bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
      if (typeof callback === 'function') {
        callback();
      }
    });

    new bootstrap.Modal(document.getElementById('confirmModal')).show();
  }

  function showNotificationModal(title, message) {
    document.getElementById('notificationModalLabel').innerHTML = '<i class="bi bi-info-circle me-2"></i>' + title;
    document.getElementById('notificationModalMessage').textContent = message;
    new bootstrap.Modal(document.getElementById('notificationModal')).show();
  }

  function showLogoutConfirm() {
    new bootstrap.Modal(document.getElementById('logoutModal')).show();
  }
</script>
