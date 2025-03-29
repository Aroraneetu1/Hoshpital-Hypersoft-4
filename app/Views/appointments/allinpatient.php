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
<?php if($permissions['inpatient'] == 0){  

    echo no_access_msg(); ?>


<?php }else{ ?>

<section id="unseen">

    <?php include('inpatient_div.php'); ?>

    <table class="table table-bordered table-striped table-condensed table-sm table-db-js">
        <thead>
            <tr>
                <th>In-Patienta Id</th>
                <th>Admission Date</th>
                <th>Discharge Date</th>
                <th>Patient Name</th>
                <th>Room No. / Rate</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
                
            </tr>
        </thead>
        <tbody>
            <?php if($inpatientall): ?>
                <?php foreach($inpatientall as $row): 
                        $total_amt = (float) $row->room_rate_amt;
                        $total_amt += (float) get_inpatients_total_amt($row->id);
                     ?>
                    <tr>
                        <td><?php echo $row->uniq_id; ?></td>
                        <td><?php echo $row->check_in; ?></td>
                        <td><?php echo $row->check_out; ?></td>
                        <td><?php echo @$consumers[$row->patient_id]; ?></td>
                        <td><?php echo @$rooms[$row->room_no].' / '.$row->room_rate; ?></td>
                        <td><?php echo $total_amt; ?></td>
                        <td style="font-weight: 600" class="<?php if($row->pay_status != 'Pending'){echo 'text-success';}else{echo 'text-danger';}?>"><?php echo $row->pay_status; ?></td>
                        
                        <td>
                            <a class="btn btn-warning btn-sm" href="<?php echo get_site_url('appointments/viewinpatient/'.$row->id); ?>">
                                View
                            </a>

                            <a class="btn btn-info btn-sm" href="<?php echo get_site_url('appointments/editinpatient/'.$row->id); ?>">
                                Edit
                            </a>

                            <?php if($salespay == 1){?>
                                <a class="btn btn-success btn-sm" href="<?php echo get_site_url('appointments/payinpatient/'.$row->id); ?>">
                                    <?php if($row->pay_status != 'Pending'){echo 'Paid';}else{echo 'Pay';}?>
                                </a>
                            <?php } ?>
                            
                            

                            <?php if($settingspp == 1){?>
                                <a class="btn btn-danger btn-sm" onclick="cancelex(<?php echo $row->id?>, <?php echo $row->room_no?>)" href="javascript:;">
                                    Delete
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
            <?php else: ?>
                <tr>
                    <td colspan="8">
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

    function cancelex(appid, roomid) {

        Swal.fire({
          title: "Are you sure?",
          text: "You won't be able to revert this inpatient!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type : 'POST',
                    url: '<?php echo get_site_url('appointments/delete_inpatient'); ?>',
                    data: {id: appid, roomid: roomid},
                    success: function(result){

                        Swal.fire({
                          title: "Deleted!",
                          text: "Inpatient deleted.",
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