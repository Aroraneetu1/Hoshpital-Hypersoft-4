<?php $validation = \Config\Services::validation(); ?>
<?php $permission = !empty($users->permissions) ? unserialize($users->permissions) : []; ?>

<div class="form">
    <form action="" class="cmxform form-horizontal form-example" method="post">
        <div class="col-md-4">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" class="form-control" name="first_name" value="<?= esc($users->first_name) ?>">
                <span class="error"><?= $validation->getError('first_name') ?></span>
            </div>

            <div class="form-group">
                <label>User Role</label>
                <select class="form-control" name="role">
                    <option value="Receptionist" <?= $users->role == 'Receptionist' ? 'selected' : '' ?>>Receptionist</option>
                    <option value="Doctor-User" <?= $users->role == 'Doctor-User' ? 'selected' : '' ?>>Doctor</option>
                    <option value="Lab" <?= $users->role == 'Lab' ? 'selected' : '' ?>>Lab Staff</option>
                    <option value="Finance" <?= $users->role == 'Finance' ? 'selected' : '' ?>>Finance</option>
                    <option value="Cashier" <?= $users->role == 'Cashier' ? 'selected' : '' ?>>Cashier</option>
                </select>
                <span class="error"><?= $validation->getError('role') ?></span>
            </div>

            <div id="user-permissions" class="form-group">
                <label style="margin-bottom: 8px;">User Permissions</label><br>
                <div class="row">
                    <div class="col-md-6">
                        <?php 
                        $permissions_list = [
                            'doctors' => 'Doctors', 'patients' => 'Patients', 'appointments' => 'Appointments',
                            'visit' => 'Visit', 'lab' => 'Lab', 'expense' => 'Expense',
                            'reports' => 'Reports', 'settings' => 'Settings'
                        ];
                        foreach ($permissions_list as $key => $label) : ?>
                            <label class="fz-checkbox">
                                <input type="hidden" name="permissions[<?= $key ?>]" value="0">
                                <input type="checkbox" name="permissions[<?= $key ?>]" <?= isset($permission[$key]) && $permission[$key] == 1 ? 'checked' : '' ?> value="1">
                                <i class="material-icons" style="margin-left: 5px;"></i> <?= $label ?>
                            </label><br>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-md-6">
                        <?php 
                        $permissions_list2 = [
                            'debitpay' => 'Debit Pay', 'inpatient' => 'In-Patient', 'operation' => 'Operation',
                            'finance' => 'Finance', 'salespay' => 'Sales Pay', 'expensepay' => 'Expense Pay'
                        ];
                        foreach ($permissions_list2 as $key => $label) : ?>
                            <label class="fz-checkbox">
                                <input type="hidden" name="permissions[<?= $key ?>]" value="0">
                                <input type="checkbox" name="permissions[<?= $key ?>]" <?= isset($permission[$key]) && $permission[$key] == 1 ? 'checked' : '' ?> value="1">
                                <i class="material-icons" style="margin-left: 5px;"></i> <?= $label ?>
                            </label><br>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>&nbsp;</label>
                <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">Save Changes</button>
            </div>
        </div>

        <div class="col-md-1"></div>

        <div class="col-md-4">
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" class="form-control" name="last_name" value="<?= esc($users->last_name) ?>">
                <span class="error"><?= $validation->getError('last_name') ?></span>
            </div>
        </div>
    </form>
</div>
