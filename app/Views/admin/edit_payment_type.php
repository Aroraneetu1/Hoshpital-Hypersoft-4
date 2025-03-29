<section id="unseen">

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">
            <a href="<?= get_site_url('admin/payment_types') ?>" class="btn loader-activate btn-default">Payment Types</a>
            <a href="<?= get_site_url('admin/add_payment_types') ?>" class="btn loader-activate btn-default">Add New Payment Type</a>
            <a href="<?= get_site_url('admin/edit_payment_type/' . service('uri')->getSegment(3)) ?>" class="btn loader-activate btn-success">Edit Payment Type</a>
        </div>
    </div>

    <hr>

    <div class="form">
        <form action="<?= get_site_url('admin/edit_payment_type/' . service('uri')->getSegment(3)) ?>" method="post" class="cmxform form-horizontal form-example">
            <?= csrf_field() ?> 

            <div class="col-lg-4 col-md-6 col-sm-6 col-xm-12">
                <div class="form-group">
                    <label>Payment Type Name</label>
                    <input type="text" class="form-control" name="name" value="<?= isset($row) ? esc($row->name) : '' ?>"> 
                    <span class="error"><?= session('validation') ? session('validation')->getError('name') : '' ?></span>
                </div>

                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary form-control loader-activate">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

</section>
