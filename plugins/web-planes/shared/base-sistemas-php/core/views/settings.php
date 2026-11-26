<?php $val = static fn(string $k) => old($k, $s[$k] ?? ''); ?>
<div class="page-head"><h1>Configuración del negocio</h1></div>

<form class="card form-grid" method="post" action="<?= e(url('settings/save')) ?>" enctype="multipart/form-data" novalidate data-validate>
  <?= Csrf::field() ?>
  <fieldset>
    <legend>Datos del negocio</legend>
    <div class="field">
      <label for="business_name">Nombre del negocio <span class="req" aria-hidden="true">*</span></label>
      <input id="business_name" name="business_name" required maxlength="120" value="<?= e($val('business_name')) ?>"<?= aria_error('business_name') ?>>
      <?= field_error('business_name') ?>
    </div>
    <div class="field">
      <label for="logo">Logo (JPG, PNG o WEBP)</label>
      <?php if (!empty($s['logo_media_id'])): ?>
        <img class="thumb" src="<?= e(media_url((int) $s['logo_media_id'])) ?>" alt="Logo actual" width="64" height="64">
        <label class="check"><input type="checkbox" name="remove_logo" value="1"> Quitar logo</label>
      <?php endif; ?>
      <input id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp"<?= aria_error('logo') ?>>
      <?= field_error('logo') ?>
    </div>
    <div class="field-row">
      <div class="field">
        <label for="phone">Teléfono</label>
        <input id="phone" name="phone" type="tel" maxlength="30" value="<?= e($val('phone')) ?>" placeholder="0212-1234567"<?= aria_error('phone') ?>>
        <?= field_error('phone') ?>
      </div>
      <div class="field">
        <label for="whatsapp">WhatsApp</label>
        <input id="whatsapp" name="whatsapp" type="tel" maxlength="30" value="<?= e($val('whatsapp')) ?>" placeholder="0414-1234567"<?= aria_error('whatsapp') ?>>
        <?= field_error('whatsapp') ?>
      </div>
    </div>
    <div class="field">
      <label for="email">Correo de contacto</label>
      <input id="email" name="email" type="email" maxlength="190" value="<?= e($val('email')) ?>"<?= aria_error('email') ?>>
      <?= field_error('email') ?>
    </div>
    <div class="field">
      <label for="address">Dirección</label>
      <input id="address" name="address" maxlength="255" value="<?= e($val('address')) ?>"<?= aria_error('address') ?>>
      <?= field_error('address') ?>
    </div>
    <div class="field">
      <label for="timezone">Zona horaria</label>
      <select id="timezone" name="timezone" required<?= aria_error('timezone') ?>>
        <?php foreach ($zones as $z): ?><option value="<?= e($z) ?>"<?= selected($z, $val('timezone')) ?>><?= e($z) ?></option><?php endforeach; ?>
      </select>
      <?= field_error('timezone') ?>
    </div>
  </fieldset>

  <fieldset>
    <legend>Moneda y tasa</legend>
    <div class="field-row">
      <div class="field">
        <label for="base_currency">Moneda principal</label>
        <select id="base_currency" name="base_currency" required>
          <option value="USD"<?= selected('USD', $val('base_currency')) ?>>Dólares (USD)</option>
          <option value="VES"<?= selected('VES', $val('base_currency')) ?>>Bolívares (Bs.)</option>
        </select>
      </div>
      <div class="field">
        <label for="exchange_rate">Tasa: Bs. por 1 US$ <span class="req" aria-hidden="true">*</span></label>
        <input id="exchange_rate" name="exchange_rate" inputmode="decimal" required value="<?= e($val('exchange_rate')) ?>"<?= aria_error('exchange_rate') ?> aria-describedby="rate-help">
        <p class="help" id="rate-help">Se usa para mostrar montos en ambas monedas. Cada cambio queda en el historial.</p>
        <?= field_error('exchange_rate') ?>
      </div>
    </div>
    <?php if ($rates): ?>
      <details>
        <summary>Historial de tasas</summary>
        <table class="table table-compact">
          <caption class="sr-only">Últimas tasas registradas</caption>
          <thead><tr><th scope="col">Fecha</th><th scope="col">Tasa</th><th scope="col">Usuario</th></tr></thead>
          <tbody>
          <?php foreach ($rates as $r): ?>
            <tr><td><?= e(datetime_es($r['created_at'])) ?></td><td><?= e(number_format((float) $r['rate'], 4, ',', '.')) ?></td><td><?= e($r['name'] ?? '—') ?></td></tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </details>
    <?php endif; ?>
  </fieldset>

  <?php if (is_file(BASE_PATH . '/app/views/_settings_extra.php')): ?>
    <?= View::partial('app/_settings_extra', ['val' => $val]) ?>
  <?php endif; ?>

  <div class="form-actions"><button class="btn btn-primary" type="submit">Guardar configuración</button></div>
</form>
