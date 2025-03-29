<?php  
$categoryConfig = config('Category');
$yes_no_option = $categoryConfig->yes_no_option;
?>

<section id="unseen">
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">
            <a href="<?php echo get_site_url('admin/products'); ?>" class="btn loader-activate btn-default">Products List</a>
            <a href="<?php echo get_site_url('admin/new_product'); ?>" class="btn loader-activate btn-success">Add New Product</a>
        </div>
    </div>
    <hr>
    <div class="form">
        <form action="" class="cmxform form-horizontal form-example" method="post">
            <div class="col-lg-4 col-md-6 col-sm-6 col-xm-12">
                <div class="form-group">
                    <label>Select Category</label>
                    <select class="form-control" id="cdropdown" name="category_id" onchange="hidefileds(this)">
                        <?php foreach ($category as $k => $v){ ?>
                            <option value="<?php echo $v->id;?>" data-key="<?php echo $v->type;?>"><?php echo $v->name;?></option>
                        <?php } ?>
                    </select>
                    <span class="error"><?php echo isset($validation) ? $validation->getError("category_id") : ''; ?></span>
                </div>
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" class="form-control" name="name" value="<?php echo set_value("name"); ?>"> 
                    <span class="error"><?php echo isset($validation) ? $validation->getError("name") : ''; ?></span>
                </div>
                <div class="form-group">
                    <label>Product Price</label>
                    <input type="text" class="form-control" name="price" value="<?php echo set_value("price"); ?>"> 
                    <span class="error"><?php echo isset($validation) ? $validation->getError("price") : ''; ?></span>
                </div>
                <div class="form-group hidd">
                    <label>Normal Range</label>
                    <input type="text" class="form-control" name="normal_range" value="<?php echo set_value("normal_range"); ?>"> 
                    <span class="error"><?php echo isset($validation) ? $validation->getError("normal_range") : ''; ?></span>
                </div>
                <div class="form-group hidd">
                    <label>Product Description</label>
                    <textarea rows="4" class="form-control" name="description"><?php echo set_value("description"); ?></textarea>
                    <span class="error"><?php echo isset($validation) ? $validation->getError("description") : ''; ?></span>
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">
                        Add New Product
                    </button>
                </div>
            </div>

            <div class="col-md-1"></div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-xm-12">
                <div class="form-group">
                    <label>Has Sub-Product?</label><br>
                    <select class="form-control" name="has_subproduct">
                        <?php foreach ($yes_no_option as $k => $v){ ?>
                            <option value="<?php echo $k;?>"><?php echo $v;?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Sub-Product Of</label>
                    <select class="form-control" name="subproduct_of">
                        <option value="0">Select if have?</option>
                        <?php foreach ($products as $k => $v){ ?>
                            <option value="<?php echo $v->id;?>"><?php echo $v->name;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
</section>

<script type="text/javascript">
    function hidefileds(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const dataKey = selectedOption.getAttribute('data-key'); 

        if(dataKey == 2){
            $('.hidd').addClass('hidden');
        } else {
            $('.hidd').removeClass('hidden');
        }
    }

    $(document).ready(function(){
        const dropdown = document.getElementById("cdropdown");
        const selectedOption = dropdown.options[dropdown.selectedIndex];
        const dataKey = selectedOption.getAttribute("data-key"); 

        if(dataKey == 2){
            $('.hidd').addClass('hidden');
        } else {
            $('.hidd').removeClass('hidden');
        }
    });
</script>
