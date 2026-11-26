<div class="page-head">
  <h1>Usuarios</h1>
  <a class="btn btn-primary" href="<?= e(url('users/form')) ?>">Nuevo usuario</a>
</div>
<div class="card table-wrap">
  <table class="table">
    <caption class="sr-only">Usuarios del sistema</caption>
    <thead><tr><th scope="col">Nombre</th><th scope="col">Correo</th><th scope="col">Rol</th><th scope="col">Estado</th><th scope="col">Último acceso</th><th scope="col"><span class="sr-only">Acciones</span></th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td data-label="Nombre"><?= e($u['name']) ?></td>
        <td data-label="Correo"><?= e($u['email']) ?></td>
        <td data-label="Rol"><?= $u['role'] === 'admin' ? 'Administrador' : 'Solo lectura' ?></td>
        <td data-label="Estado"><?= $u['is_active'] ? 'Activo' : 'Inactivo' ?></td>
        <td data-label="Último acceso"><?= $u['last_login_at'] ? e(datetime_es($u['last_login_at'])) : 'Nunca' ?></td>
        <td class="actions">
          <a class="btn btn-sm" href="<?= e(url('users/form', ['id' => $u['id']])) ?>">Editar</a>
          <?php if ((int) $u['id'] !== Auth::id()): ?>
          <form method="post" action="<?= e(url('users/delete', ['id' => $u['id']])) ?>" data-confirm="¿Eliminar al usuario <?= e($u['name']) ?>? Esta acción no se puede deshacer.">
            <?= Csrf::field() ?><button class="btn btn-sm btn-danger" type="submit">Eliminar</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
