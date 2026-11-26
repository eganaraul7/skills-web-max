<?php $listFields = array_filter($d['fields'], static fn($f) => !empty($f['list'])); ?>
<div class="page-head">
  <h1><?= e($d['title']) ?></h1>
  <div class="head-actions">
    <?php if (!empty($d['parent'])): ?><a class="btn btn-ghost" href="<?= e(url($d['parent']['route'])) ?>"><?= e($d['parent']['label']) ?></a><?php endif; ?>
    <?php if (Auth::canWrite()): ?><a class="btn btn-primary" href="<?= e(url($d['route'] . '/form')) ?>">Nuevo <?= e($d['singular']) ?></a><?php endif; ?>
  </div>
</div>

<?php if (!empty($d['search'])): ?>
<form class="toolbar" method="get" action="<?= e(url()) ?>" role="search">
  <input type="hidden" name="r" value="<?= e($d['route']) ?>">
  <label class="sr-only" for="q">Buscar</label>
  <input id="q" name="q" type="search" value="<?= e($q) ?>" placeholder="Buscar…">
  <button class="btn" type="submit">Buscar</button>
</form>
<?php endif; ?>

<?php if (!$rows): ?>
  <div class="card empty-state">
    <p><?= e($q !== '' ? 'No hay resultados para «' . $q . '».' : $d['empty']) ?></p>
    <?php if ($q === '' && Auth::canWrite()): ?><a class="btn btn-primary" href="<?= e(url($d['route'] . '/form')) ?>">Crear <?= e($d['singular']) ?></a><?php endif; ?>
  </div>
<?php else: ?>
<div class="card table-wrap">
  <table class="table">
    <caption class="sr-only"><?= e($d['title']) ?> (<?= (int) $total ?>)</caption>
    <thead><tr>
      <?php foreach ($listFields as $f): ?><th scope="col"><?= e($f['label']) ?></th><?php endforeach; ?>
      <?php if (Auth::canWrite()): ?><th scope="col"><span class="sr-only">Acciones</span></th><?php endif; ?>
    </tr></thead>
    <tbody>
    <?php foreach ($rows as $row): ?>
      <tr>
        <?php foreach ($listFields as $name => $f): ?><td data-label="<?= e($f['label']) ?>"><?= e(Crud::display($f, $row[$name] ?? null)) ?></td><?php endforeach; ?>
        <?php if (Auth::canWrite()): ?>
        <td class="actions">
          <a class="btn btn-sm" href="<?= e(url($d['route'] . '/form', ['id' => $row['id']])) ?>">Editar</a>
          <form method="post" action="<?= e(url($d['route'] . '/delete', ['id' => $row['id']])) ?>" data-confirm="¿Eliminar este <?= e($d['singular']) ?>? Esta acción no se puede deshacer.">
            <?= Csrf::field() ?><button class="btn btn-sm btn-danger" type="submit">Eliminar</button>
          </form>
        </td>
        <?php endif; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php if ($pages > 1): ?>
  <nav class="pagination" aria-label="Paginación">
    <?php for ($p = 1; $p <= $pages; $p++): ?>
      <a href="<?= e(url($d['route'], ['q' => $q, 'page' => $p])) ?>"<?= $p === $page ? ' aria-current="page"' : '' ?>><?= $p ?></a>
    <?php endfor; ?>
  </nav>
<?php endif; ?>
<?php endif; ?>
