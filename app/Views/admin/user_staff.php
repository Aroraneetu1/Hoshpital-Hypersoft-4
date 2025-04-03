<?php 

$validation = $validation ?? \Config\Services::validation();
?>

<div class="form">

    <form action="" class="cmxform form-horizontal form-example" method="post">

        <div class="col-md-4">

            <div class="form-group">

                <label>First Name</label>

                <input type="text" class="form-control" name="first_name" value=""> 

                <span class="error">
    <?= isset($validation) ? $validation->getError('first_name') : '' ?>
</span>


            </div>

            

            <div class="form-group">

                <label>Username</label>

                <input type="text" class="form-control" name="username" value=""> 

                <span class="error"><?= isset($validation) ? $validation->getError('username') : '' ?></span>


            </div>

            <div class="form-group">

                <label>User Role</label>

                <select class="form-control" name="role">
                    <option value="Receptionist">Receptionist</option>
                    <option value="Doctor-User">Doctor</option>
                    <option value="Lab">Lab Staff</option>
                    <option value="Finance">Finance</option>
                    <option value="Cashier">Cashier</option>
                </select> 

                <span class="error"><?= isset($validation) ? $validation->getError('role') : '' ?></span>


            </div>

            

            <div id="user-permissions" class="form-group">

                <label style="margin-bottom: 8px;">User Permissions</label><br>

                <div class="row">
                    <div class="col-md-6">

                        <label class="fz-checkbox">
                            <input type="hidden" name="permissions[doctors]" value="0"> 
                            <input type="checkbox" name="permissions[doctors]" value="1"> 
                            <i class="material-icons" style="margin-left: 5px;"></i> Doctors
                        </label>
                        <br>
                        <label class="fz-checkbox">
                            <input type="hidden" name="permissions[patients]" value="0"> 
                            <input type="checkbox" name="permissions[patients]" value="1"> 
                            <i class="material-icons" style="margin-left: 5px;"></i> Patients
                        </label>
                        
                        <br>
                        <label class="fz-checkbox">
                            <input type="hidden" name="permissions[appointments]" value="0"> 
                            <input type="checkbox" name="permissions[appointments]" value="1"> 
                            <i class="material-icons" style="margin-left: 5px;"></i> Appointments
                        </label>
                        <br>
                        <label class="fz-checkbox">
                            <input type="hidden" name="permissions[visit]" value="0"> 
                            <input type="checkbox" name="permissions[visit]" value="1"> 
                            <i class="material-icons" style="margin-left: 5px;"></i> Visit
                        </label>
                        <br>
                        <label class="fz-checkbox">
                            <input type="hidden" name="permissions[lab]" value="0"> 
                            <input type="checkbox" name="permissions[lab]" value="1"> 
                            <i class="material-icons" style="margin-left: 5px;"></i> Lab
                        </label>
                        
                        <br>
                        <label class="fz-checkbox">
                            <input type="hidden" name="permissions[expense]" value="0"> 
                            <input type="checkbox" name="permissions[expense]" value="1"> 
                            <i class="material-icons" style="margin-left: 5px;"></i> Expense
                        </label>

                        <br>
                        <label class="fz-checkbox">
                            <input type="hidden" name="permissions[reports]" value="0"> 
                            <input type="checkbox" name="permissions[reports]" value="1"> 
                            <i class="material-icons" style="margin-left: 5px;"></i> Reports
                        </label>

                        <br>
                        <label class="fz-checkbox">
                            <input type="hidden" name="permissions[settings]" value="0"> 
                            <input type="checkbox" name="permissions[settings]" value="1"> 
                            <i class="material-icons" style="margin-left: 5px;"></i> Settings
                        </label>
                        

                    </div>

                    <div class="col-md-6">

                        <label class="fz-checkbox">
                            <input type="hidden" name="permissions[debitpay]" value="0"> 
                            <input type="checkbox" name="permissions[debitpay]" value="1"> 
                            <i class="material-icons" style="margin-left: 5px;"></i> Debit pay 
                        </label>
                        <br>
                        <label class="fz-checkbox">
                            <input type="hidden" name="permissions[inpatient]" value="0"> 
                            <input type="checkbox" name="permissions[inpatient]" value="1"> 
                            <i class="material-icons" style="margin-left: 5px;"></i> In-Patient 
                        </label>

                        <br>
                        <label class="fz-checkbox">
                            <input type="hidden" name="permissions[operation]" value="0"> 
                            <input type="checkbox" name="permissions[operation]" value="1"> 
                            <i class="material-icons" style="margin-left: 5px;"></i> Operation 
                        </label>

                        <br>
                        <label class="fz-checkbox">
                            <input type="hidden" name="permissions[finance]" value="0"> 
                            <input type="checkbox" name="permissions[finance]" value="1"> 
                            <i class="material-icons" style="margin-left: 5px;"></i> Finance 
                        </label>

                        <br>
                        <label class="fz-checkbox">
                            <input type="hidden" name="permissions[salespay]" value="0"> 
                            <input type="checkbox" name="permissions[salespay]" value="1"> 
                            <i class="material-icons" style="margin-left: 5px;"></i> Sales Pay 
                        </label>

                        <br>
                        <label class="fz-checkbox">
                            <input type="hidden" name="permissions[expensepay]" value="0"> 
                            <input type="checkbox" name="permissions[expensepay]" value="1"> 
                            <i class="material-icons" style="margin-left: 5px;"></i> Expense Pay 
                        </label>

                    </div>

                </div>
                
            </div>


            <div class="form-group">

                <label>&nbsp;</label>

                <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">

                    Save Changes

                </button>

            </div>

        </div>

        <div class="col-md-1"></div>

        <div class="col-md-4">
            
            <div class="form-group">

                <label>Last Name</label>

                <input type="text" class="form-control" name="last_name" value=""> 

                <span class="error"><?= isset($validation) ? $validation->getError('last_name') : '' ?></span>


            </div>

            <div class="form-group">

                <label>Password</label>

                <input type="password" class="form-control" name="password" value=""> 

                <span class="error"><?= isset($validation) ? $validation->getError('password') : '' ?></span>


            </div>

        </div>

        
    </form>

</div>