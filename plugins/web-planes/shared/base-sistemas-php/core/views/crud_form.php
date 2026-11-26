<div class="page-head"><h1><?= e($title) ?></h1><a class="btn btn-ghost" href="<?= e(url($d['route'])) ?>">Volver</a></div>
<form class="card form-narrow" method="post" action="<?= e(url($d['route'] . '/save', $id ? ['id' => $id] : [])) ?>" novalidate data-validate>
  <?= Csrf::field() ?>
  <?php foreach ($d['fields'] as $name => $f):
      $type = $f['type'] ?? 'text';
      $rules = $f['rules'] ?? '';
      $required = str_contains($rules, 'required');
      $value = old($name, $row[$name] ?? ($f['default'] ?? ''));
      preg_match('/max:(\d+)/', $rules, $mx);
      $maxAttr = $mx ? ' maxlength="' . (int) $mx[1] . '"' : '';
      $label = e($f['label']) . ($required ? ' <span class="req" aria-hidden="true">*</span>' : '');
      $help = isset($f['help']) ? '<p class="help" id="help-' . e($name) . '">' . e($f['help']) . '</p>' : '';
      $req = $required ? ' required' : '';
  ?>
    <?php if ($type === 'checkbox'): ?>
      <label class="check"><input type="checkbox" name="<?= e($name) ?>" value="1"<?= checked($value) ?>> <?= e($f['label']) ?></label>
    <?php elseif ($type === 'pivot'): $values = (array) $value; ?>
      <fieldset class="field">
        <legend><?= e($f['label']) ?></legend>
        <?= $help ?>
        <?php foreach (Crud::options($f) as $optVal => $optLabel): ?>
          <label class="check"><input type="checkbox" name="<?= e($name) ?>[]" value="<?= e($optVal) ?>"<?= checked(in_array((string) $optVal, array_map('strval', $values), true)) ?>> <?= e($optLabel) ?></label>
        <?php endforeach; ?>
      </fieldset>
    <?php else: ?>
      <div class="field">
        <label for="f-<?= e($name) ?>"><?= $label ?></label>
        <?php if ($type === 'textarea'): ?>
          <textarea id="f-<?= e($name) ?>" name="<?= e($name) ?>" rows="4"<?= $req . $maxAttr . aria_error($name) ?>><?= e($value) ?></textarea>
        <?php elseif ($type === 'select'): ?>
          <select id="f-<?= e($name) ?>" name="<?= e($name) ?>"<?= $req . aria_error($name) ?>>
            <?php if (!$required): ?><option value="">— Ninguno —</option><?php endif; ?>
            <?php foreach (Crud::options($f) as $optVal => $optLabel): ?>
              <option value="<?= e($optVal) ?>"<?= selected($optVal, $value) ?>><?= e($optLabel) ?></option>
            <?php endforeach; ?>
          </select>
        <?php else:
            $htmlType = ['email' => 'email', 'tel' => 'tel', 'integer' => 'number', 'time' => 'time'][$type] ?? 'text';
            $extra = $type === 'decimal' ? ' inputmode="decimal"' : '';
            if ($type === 'integer' && preg_match('/min_value:(-?\d+)/', $rules, $mn)) { $extra .= ' min="' . (int) $mn[1] . '"'; }
            if ($type === 'integer' && preg_match('/max_value:(-?\d+)/', $rules, $mv)) { $extra .= ' max="' . (int) $mv[1] . '"'; }
            if ($type === 'time') { $value = substr((string) $value, 0, 5); }
        ?>
          <input id="f-<?= e($name) ?>" name="<?= e($name) ?>" type="<?= $htmlType ?>" value="<?= e($value) ?>"<?= $req . $maxAttr . $extra . aria_error($name) ?>>
        <?php endif; ?>
        <?= $help ?>
        <?= field_error($name) ?>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
  <div class="form-actions"><button class="btn btn-primary" type="submit">Guardar</button></div>
</form>
