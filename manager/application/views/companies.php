<?php foreach ($companies as $company): ?>
  <div>
    <a href="/manager/sso/login/<?= $company['id'] ?>">Login to <?= $company['name'] ?></a>
  </div>
<?php endforeach; ?>