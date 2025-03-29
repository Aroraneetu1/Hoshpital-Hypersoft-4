<h2>Admin Profile</h2>

<?php if (session()->getFlashdata('success_msg')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success_msg') ?></div>
<?php endif; ?>

<form method="post" action="">
    <div class="form-group">
        <label for="firstname">First Name</label>
        <input type="text" class="form-control" name="first_name" value="<?= esc($admin->first_name) ?>" required>
    </div>

    <div class="form-group">
        <label for="lastname">Last Name</label>
        <input type="text" class="form-control" name="last_name" value="<?= esc($admin->last_name) ?>" required>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" name="email" value="<?= esc($admin->email) ?>" required>
    </div>

    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" class="form-control" name="phone" value="<?= esc($admin->phone) ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>