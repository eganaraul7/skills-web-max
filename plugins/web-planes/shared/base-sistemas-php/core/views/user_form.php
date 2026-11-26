<div class="page-head"><h1><?= e($title) ?></h1><a class="btn btn-ghost" href="<?= e(url('users')) ?>">Volver</a></div>
<form class="card form-narrow" method="post" action="<?= e(url('users/save', $id ? ['id' => $id] : [])) ?>" novalidate data-validate>
  <?= Csrf::field() ?>
  <div class="field">
    <label for="name">Nombre <span class="req" aria-hidden="true">*</span></label>
    <input id="name" name="name" required maxlength="120" value="<?= e(old('name', $user['name'] ?? '')) ?>"<?= aria_error('name') ?>>
    <?= field_error('name') ?>
  </div>
  <div class="field">
    <label for="email">Correo <span class="req" aria-hidden="true">*</span></label>
    <input id="email" name="email" type="email" required maxlength="190" value="<?= e(old('email', $user['email'] ?? '')) ?>"<?= aria_error('email') ?>>
    <?= field_error('email') ?>
  </div>
  <div class="field">
    <label for="role">Rol</label>
    <select id="role" name="role"<?= aria_error('role') ?>>
      <option value="viewer"<?= selected('viewer', old('role', $user['role'] ?? 'viewer')) ?>>Solo lectura</option>
      <option value="admin"<?= selected('admin', old('role', $user['role'] ?? '')) ?>>Administrador</option>
    </select>
    <?= field_error('role') ?>
  </div>
  <div class="field">
    <label for="password">Contraseña <?= $id ? '(déjala vacía para no cambiarla)' : '<span class="req" aria-hidden="true">*</span>' ?></label>
    <input id="password" name="password" type="password" minlength="10" maxlength="200" autocomplete="new-password"<?= $id ? '' : ' required' ?><?= aria_error('password') ?> aria-describedby="pw-help">
    <p class="help" id="pw-help">Mínimo 10 caracteres.</p>
    <?= field_error('password') ?>
  </div>
  <label class="check"><input type="checkbox" name="is_active" value="1"<?= checked(old('is_active', $user['is_active'] ?? 1)) ?>> Usuario activo</label>
  <div class="form-actions"><button class="btn btn-primary" type="submit">Guardar</button></div>
</form>
