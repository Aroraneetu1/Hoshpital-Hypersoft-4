<?php 
    error_reporting(0);
    $permissions = unserialize(get_session_data('permissions'));
    $settingspp = isset($permissions['settings']) ? $permissions['settings'] : 0;
    $salespay = isset($permissions['salespay']) ? $permissions['salespay'] : 0;

    $consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name')); 
    $rooms = get_key_value_array('rooms', 'id', array('room_number')); 
    //$providers = get_key_value_array('users', 'id', array('first_name', 'last_name')); 
    //$services = get_key_value_array('service_types', 'id', array('name'));

?>

<?php if($permissions['operation'] == 0){  

    echo no_access_msg(); ?>


<?php }else{ ?>

<section id="unseen">

    <?php include('operation_div.php'); ?>

    <table class="table table-bordered table-striped table-condensed table-sm table-db-js">
        <thead>
            <tr>
                <th>Operation Id</th>
                <th>Operation Date</th>
                <th>Patient Name</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
                
            </tr>
        </thead>
        <tbody>
            <?php if($operationall): ?>
                <?php foreach($operationall as $row): 

                    $total_amt = get_operation_total_amt($row->id); ?>
                    <tr>
                        <td><?php echo $row->unique_id; ?></td>
                        <td><?php echo $row->operation_date; ?></td>
                        <td><?php echo @$consumers[$row->patient_id]; ?></td>
                        <td><?php echo $total_amt; ?></td>
                        <td style="font-weight: 600" class="<?php if($row->pay_status != 'Pending'){echo 'text-success';}else{echo 'text-danger';}?>"><?php echo $row->pay_status; ?></td>
                        
                        <td>
                            <a class="btn btn-warning btn-sm" href="<?php echo get_site_url('appointments/viewoperation/'.$row->id); ?>">
                                View
                            </a>

                            <a class="btn btn-info btn-sm" href="<?php echo get_site_url('appointments/editoperation/'.$row->id); ?>">
                                Edit
                            </a>

                            <?php if($salespay == 1){?>
                                <a class="btn btn-success btn-sm" href="<?php echo get_site_url('appointments/payoperation/'.$row->id); ?>">
                                    Pay
                                </a>
                            <?php } ?>

                            <?php if($settingspp == 1){?>
                                <a class="btn btn-danger btn-sm" onclick="cancelex(<?php echo $row->id?>)" href="javascript:;">
                                    Delete
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
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

table.dataTable tbody td {
    padding: 5px 18px;
}
</style>
<script type="text/javascript">

    $(document).ready(function(){
        $(".table-db-js").DataTable({
            paging: true,
            info: false,
            sorting: false,
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: '_all' }
            ],
        });

        $(".dataTables_wrapper .dataTables_filter input").css("border", "none");
        $(".dataTables_wrapper .dataTables_filter input").css("background-color", "#eee");
        $(".dataTables_wrapper .dataTables_filter input").css("height", "35px");
        $(".dataTables_wrapper .dataTables_filter input").css("margin-bottom", "10px");
    });

    function cancelex(opsid) {

        Swal.fire({
          title: "Are you sure?",
          text: "You won't be able to revert this operation!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type : 'POST',
                    url: '<?php echo get_site_url('appointments/delete_operation'); ?>',
                    data: {id: opsid},
                    success: function(result){

                        Swal.fire({
                          title: "Deleted!",
                          text: "Operation deleted.",
                          icon: "success"
                        });

                        setTimeout(function(){
                           window.location.reload();
                        }, 500);
                    }
                })
            }
        });
    }
</script>

<?php } ?>