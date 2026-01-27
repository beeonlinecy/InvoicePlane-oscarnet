<h1>Dashboard</h1>
<p>Welcome, <?= html_escape($username) ?> (ID: <?= $user_id ?>)</p>
<p><a href="<?= site_url('login/logout') ?>">Logout</a></p>

<hr>

<h3>My companies</h3>

<?php if (empty($companies)): ?>
    <div class="alert alert-info">You have no companies yet.</div>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($companies as $company): ?>
            <tr>
                <td><?= html_escape($company->title) ?></td>
                <td><?= html_escape($company->role) ?></td>
                <td>
                    <a href="<?= site_url('dashboard/sso/' . $company->slug) ?>" class="btn btn-primary btn-sm" target="_blank">
                        Log in <i class="fa fa-external-link"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
