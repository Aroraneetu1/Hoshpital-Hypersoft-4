<div class="row" style="margin-bottom: 15px;">
    <div class="col-md-12">

        <a href="<?= get_site_url('admin/rooms'); ?>" 
           class="btn loader-activate <?= (isset($current_segment) && $current_segment == 'rooms') ? 'btn-success' : 'btn-default'; ?>">All Rooms</a>

        <a href="<?= get_site_url('admin/arooms'); ?>" 
           class="btn loader-activate <?= (isset($current_segment) && $current_segment == 'arooms') ? 'btn-success' : 'btn-default'; ?>">Add New Room</a>

        <?php if(isset($current_segment) && $current_segment == 'erooms'): ?>
            <a href="<?= get_site_url('admin/erooms'); ?>" 
               class="btn loader-activate <?= ($current_segment == 'erooms') ? 'btn-success' : 'btn-default'; ?>">Edit Room</a>
        <?php endif; ?>

        <a href="<?= get_site_url('admin/crooms'); ?>" 
           class="btn loader-activate <?= (isset($current_segment) && $current_segment == 'crooms') ? 'btn-success' : 'btn-default'; ?>">Room Categories</a>

        <a href="<?= get_site_url('admin/croomsadd'); ?>" 
           class="btn loader-activate <?= (isset($current_segment) && $current_segment == 'croomsadd') ? 'btn-success' : 'btn-default'; ?>">Add New Room Category</a>

    </div>
</div>
