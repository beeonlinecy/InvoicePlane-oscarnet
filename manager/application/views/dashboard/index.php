<h1>Dashboard</h1>
<p>Welcome, <?= html_escape($username) ?> (ID: <?= $user_id ?>)</p>
<p><a href="<?= site_url('login/logout') ?>">Logout</a></p>

<hr>

<h3>Мои компании</h3>

<?php if (empty($companies)): ?>
    <div class="alert alert-info">У вас нет доступных компаний.</div>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Название</th>
                <th>Роль</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($companies as $company): ?>
            <tr>
                <td><?= html_escape($company->title) ?></td>
                <td><?= html_escape($company->role) ?></td>
                <td>
                    <a href="<?= site_url('dashboard/sso/' . $company->slug) ?>" class="btn btn-primary btn-sm" target="_blank">
                        Войти <i class="fa fa-external-link"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
