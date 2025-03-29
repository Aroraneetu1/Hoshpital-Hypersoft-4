<section id="unseen">

    <?php include('rooms_div.php'); ?>

    <div class="form">
        <form action="" class="cmxform form-horizontal form-example" method="post">
            <div class="col-lg-4 col-md-6 col-sm-6 col-xm-12">
                <div class="form-group">
                    <label>Room Category Name</label>
                    <input type="text" class="form-control" name="name" value="<?= old("name"); ?>"> 
                    <span class="error"><?= isset($validation) ? $validation->getError('name') : ''; ?></span>
                </div>

                <div class="form-group">
                    <label>&nbsp;</label>
                    <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">
                        Add New Room Category
                    </button>
                </div>
            </div>
        </form>
    </div>

</section>