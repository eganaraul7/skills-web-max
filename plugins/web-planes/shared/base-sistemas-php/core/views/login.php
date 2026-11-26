<section class="card auth-card" aria-labelledby="login-title">
  <h1 id="login-title">Iniciar sesión</h1>
  <form method="post" action="<?= e(url('login')) ?>" novalidate data-validate>
    <?= Csrf::field() ?>
    <div class="field">
      <label for="email">Correo electrónico</label>
      <input id="email" name="email" type="email" autocomplete="username" required maxlength="190" value="<?= e(old('email')) ?>"<?= aria_error('email') ?>>
      <?= field_error('email') ?>
    </div>
    <div class="field">
      <label for="password">Contraseña</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required maxlength="200"<?= aria_error('password') ?>>
      <?= field_error('password') ?>
    </div>
    <button class="btn btn-primary btn-block" type="submit">Entrar</button>
  </form>
</section>
