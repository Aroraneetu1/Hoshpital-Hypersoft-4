<div class="row" style="margin-bottom: 15px;">
    <div class="col-md-12">
        <a href="<?= get_site_url('appointments/alloperation'); ?>" 
           class="btn loader-activate <?= (service('uri')->getSegment(2) == 'alloperation') ? 'btn-success' : 'btn-default'; ?>">
           All Operation
        </a>

        <a href="<?= get_site_url('appointments/addoperation'); ?>" 
           class="btn loader-activate <?= (service('uri')->getSegment(2) == 'addoperation') ? 'btn-success' : 'btn-default'; ?>">
           Add New Operation
        </a>

        <?php if (service('uri')->getSegment(2) == 'viewoperation') { ?>
            <a href="<?= get_site_url('appointments/viewoperation/' . service('uri')->getSegment(3)); ?>" 
               class="btn loader-activate <?= (service('uri')->getSegment(2) == 'viewoperation') ? 'btn-success' : 'btn-default'; ?>">
               View Operation
            </a>
        <?php } ?>

        <?php if (service('uri')->getSegment(2) == 'payoperation') { ?>
            <a href="<?= get_site_url('appointments/payoperation/' . service('uri')->getSegment(3)); ?>" 
               class="btn loader-activate <?= (service('uri')->getSegment(2) == 'payoperation') ? 'btn-success' : 'btn-default'; ?>">
               Pay Operation
            </a>
        <?php } ?>

        <?php if (service('uri')->getSegment(2) == 'editoperation') { ?>
            <a href="<?= get_site_url('appointments/editoperation/' . service('uri')->getSegment(3)); ?>" 
               class="btn loader-activate <?= (service('uri')->getSegment(2) == 'editoperation') ? 'btn-success' : 'btn-default'; ?>">
               Edit Operation
            </a>
        <?php } ?>

        <?php if (service('uri')->getSegment(2) == 'discharge') { ?>
            <a href="<?= get_site_url('appointments/discharge/' . service('uri')->getSegment(3)); ?>" 
               class="btn loader-activate <?= (service('uri')->getSegment(2) == 'discharge') ? 'btn-success' : 'btn-default'; ?>">
               Discharge
            </a>
        <?php } ?>
    </div>
</div>
