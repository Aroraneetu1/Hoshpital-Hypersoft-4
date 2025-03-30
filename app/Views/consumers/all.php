<?php 
$permissions = unserialize(get_session_data('permissions'));
?>
<style type="text/css">
    .paginate_button{
        padding: 0.2em 0.3em !important;
    }

    .next{
        background: transparent;
    }
    .dataTables_paginate {
        width: 20%;
    }
    .dataTables_length {
       display: none;
    }
</style>
<section id="unseen">
    <table class="table table-bordered table-striped table-condensed table-db-js">
        <thead>
            <tr>
                <th>REF. ID</th>
                <th>Full Name</th>
                <th>DOB</th>
                <th>Gender</th>
                <th>Phone Number</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($rows): ?>
                <?php foreach($rows as $row): 

                    $get_debit_payment = get_debit_payment($row->id); ?>
                    <tr>
                    	<td><?php echo get_formated_id('P', $row->id); ?></td>
                        <td><?php echo $row->first_name; ?> <?php echo $row->last_name; ?></td>
                        <td><?php echo $row->dob; ?></td>
                        <td><?php echo $row->gender; ?></td>
                        <td><?php echo $row->phone_number; ?></td>
                        <td><?php echo number_format($get_debit_payment,2);?></td>
                        <td>
                            <?php if($permissions['patients'] == 1){?> 

                                <a class="btn btn-primary btn-sm loader-activate" href="<?php echo get_site_url('consumers/history/'.$row->id); ?>">
                                    History
                                </a>
                                <a class="btn btn-info btn-sm loader-activate" href="<?php echo get_site_url('consumers/edit/'.$row->id); ?>">
                                    Edit
                                </a>
                                <?php if($row->status == 1): ?>
    	                            <a class="btn btn-warning btn-sm" href="<?php echo get_site_url('consumers/change_status/0/'.$row->id); ?>">
    	                                Deactivate Now
    	                            </a>
                                <?php else: ?>
    	                            <a class="btn btn-success btn-sm" href="<?php echo get_site_url('consumers/change_status/1/'.$row->id); ?>">
    	                                Activate Now
    	                            </a>
                                <?php endif; ?>

                                <?php if($get_debit_payment > 0){?>

                                    <?php if($permissions['debitpay'] == 1){?>

                                    <a class="btn btn-danger btn-sm loader-activate" href="<?php echo get_site_url('consumers/pay/'.$row->id); ?>">
                                        Pay
                                    </a>
                                    
                                    <?php } ?>
                                <?php } ?>

                            <?php } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <script type="text/javascript">
                $(document).ready(function(){
                    $(".table-db-js").DataTable({
                        paging: true,
                        info: false,
                        pageLength: 10,
                        sorting: false,
                        columnDefs: [
                            { orderable: false, targets: '_all' }
                        ],
                    });

                    $(".dataTables_wrapper .dataTables_filter input").css("border", "none");
                    $(".dataTables_wrapper .dataTables_filter input").css("background-color", "#eee");
                    $(".dataTables_wrapper .dataTables_filter input").css("height", "35px");
                    $(".dataTables_wrapper .dataTables_filter input").css("margin-bottom", "10px");
                });
                </script>
            <?php else: ?>
                <tr>
                    <td colspan="6">
                        No record found.
                    </td>
                </tr>    
            <?php endif; ?>
        </tbody>
    </table>
</section>