<?php 
    $categoryConfig = config('Category'); 
    $category_type = $categoryConfig->category_type;
?>

<section id="unseen">
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">
            <a href="<?php echo get_site_url('admin/category'); ?>" class="btn btn-default">Category List</a>
            <a href="<?php echo get_site_url('admin/new_category'); ?>" class="btn btn-default">Add New Category</a>
            <a href="<?php echo get_site_url('admin/edit_category/'.$row->id); ?>" class="btn btn-success">Edit Category</a>
        </div>
    </div>
    <hr>
    <div class="form">
        <form action="" class="cmxform form-horizontal form-example" method="post">
            <div class="col-lg-4 col-md-6 col-sm-6 col-xm-12">
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" class="form-control" name="name" value="<?php echo old('name', $row->name); ?>"> 
                    <span class="error"><?php echo isset($validation) ? $validation->getError("name") : ''; ?></span>
                </div>
                <div class="form-group">
                    <label>Category Type</label>
                    <select class="form-control" name="type">
                        <?php foreach ($category_type as $k => $v){ ?>
                            <option value="<?php echo $k;?>" <?php echo ($k == $row->type) ? 'selected' : ''; ?>>
                                <?php echo $v;?>
                            </option>
                        <?php } ?>
                    </select>
                    <span class="error"><?php echo isset($validation) ? $validation->getError("type") : ''; ?></span>
                </div>

                <div class="form-group">
                    <label>&nbsp;</label>
                    <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
