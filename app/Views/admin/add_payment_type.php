<section id="unseen">

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">
            <a href="<?= get_site_url('admin/payment_types') ?>" class="btn loader-activate btn-default">Payment Types</a>
            <a href="<?= get_site_url('admin/add_payment_types') ?>" class="btn loader-activate btn-success">Add New Payment Type</a>
        </div>
    </div>

    <hr>

    <div class="form">
        <form action="<?= get_site_url('admin/add_payment_types') ?>" method="post" class="cmxform form-horizontal form-example">
            <?= csrf_field() ?>  <!-- CSRF protection for security -->

            <div class="col-lg-4 col-md-6 col-sm-6 col-xm-12">
                <div class="form-group">
                    <label>Payment Type Name</label>
                    <input type="text" class="form-control" name="name" value="<?= old('name') ?>"> 
                    <span class="error"><?= session('validation') ? session('validation')->getError('name') : '' ?></span>
                </div>

                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary form-control loader-activate">
                        Add New Payment Type
                    </button>
                </div>
            </div>
        </form>
    </div>

</section>
