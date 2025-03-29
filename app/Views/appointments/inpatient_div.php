<?php
$request = \Config\Services::request();
$uri = $request->getUri(); // Correct way to get URI in CI4
$segment2 = $uri->getSegment(2);
$segment3 = $uri->getSegment(3);
?>

<div class="row" style="margin-bottom: 15px;">
    <div class="col-md-10">

        <a href="<?= get_site_url('appointments/allinpatient'); ?>" 
           class="btn loader-activate <?= ($segment2 == 'allinpatient') ? 'btn-success' : 'btn-default'; ?>">
            All In-Patient
        </a>

        <a href="<?= get_site_url('appointments/addinpatient'); ?>" 
           class="btn loader-activate <?= ($segment2 == 'addinpatient') ? 'btn-success' : 'btn-default'; ?>">
            Add New In-Patient
        </a>

        <?php if ($segment2 == 'viewinpatient'): ?>
            <a href="<?= get_site_url('appointments/viewinpatient/' . $segment3); ?>" 
               class="btn loader-activate <?= ($segment2 == 'viewinpatient') ? 'btn-success' : 'btn-default'; ?>">
                View In-Patient
            </a>
        <?php endif; ?>

        <?php if ($segment2 == 'payinpatient'): ?>
            <a href="<?= get_site_url('appointments/payinpatient/' . $segment3); ?>" 
               class="btn loader-activate <?= ($segment2 == 'payinpatient') ? 'btn-success' : 'btn-default'; ?>">
                Pay In-Patient
            </a>
        <?php endif; ?>

        <?php if ($segment2 == 'editinpatient'): ?>
            <a href="<?= get_site_url('appointments/editinpatient/' . $segment3); ?>" 
               class="btn loader-activate <?= ($segment2 == 'editinpatient') ? 'btn-success' : 'btn-default'; ?>">
                Edit In-Patient
            </a>
        <?php endif; ?>

        <?php if ($segment2 == 'discharge'): ?>
            <a href="<?= get_site_url('appointments/discharge/' . $segment3); ?>" 
               class="btn loader-activate <?= ($segment2 == 'discharge') ? 'btn-success' : 'btn-default'; ?>">
                Discharge
            </a>
        <?php endif; ?>

    </div>

    <div class="col-md-2">
        <?php if ($segment2 == 'allinpatient'): ?>
            <a href="<?= get_site_url('Cronjob/manual_update_discharge_date'); ?>" 
               class="btn btn-danger pull-right">
                Extend Room Date
            </a>
        <?php endif; ?>
    </div>
</div>
