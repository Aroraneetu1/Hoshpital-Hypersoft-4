<?php 
    $permissions = unserialize(get_session_data('permissions'));
    /*$consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name'));
    $providers = get_key_value_array('users', 'id', array('first_name', 'last_name'));
    $services = get_key_value_array('service_types', 'id', array('name'));*/ 

?>

<section id="unseen">
    <table id="table-db-js" class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <th>Supplier Name</th>
                <th>Address</th>
                <th>Contact</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($supplier): ?>
                <?php foreach($supplier as $row): ?>
                    <tr>
                        <td><?php echo $row->name; ?></td>
                        <td><?php echo $row->address; ?></td>
                        <td><?php echo $row->contact; ?></td>
                       
                        <td>
                            <a class="btn btn-info loader-activate" href="<?php echo get_site_url('supplier/edit/'.$row->id); ?>">
                                Edit
                            </a>
                            <a class="btn btn-danger" href="javascript:;" onclick="cancelApp(<?php echo $row->id;?>)">
                                Cancel
                            </a>
                            
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">
                        No record found.
                    </td>
                </tr>    
            <?php endif; ?>
        </tbody>
    </table>
</section>


<script type="text/javascript">
$(document).ready(function(){
    $("#table-db-js thead th").each(function(){
        var title = $(this).text();
        if(title == "Appointment Time" || title == "Status" || title == "Doctor" || title == "Patient"){
        	$(this).append('<input class="fz-col-filter">');
        }
    });
    var table = $("#table-db-js").DataTable({
        paging: true,
        info: false,
        sorting: false,
        pageLength: 6,
        columnDefs: [
            { orderable: false, targets: '_all' }
        ],
    });
    table.columns().every(function(){
        var that = this;
        $("input.fz-col-filter", this.header()).on("keyup change", function(){
            if(that.search() !== this.value){
                that.search( this.value ).draw();
            }
        });
    });
    $(".dataTables_wrapper .dataTables_filter").hide();


    
});

function prescriptionPrint(appid) {

    $.ajax({
        type : 'POST',
        url: '<?php echo get_site_url('appointments/ajax_print_prescription'); ?>',
        data: {appointment_id: appid},
        success: function(result){

            $('#prescptnPrint div').remove();
            $('#prescptnPrint').html(result);

            setTimeout(function(){
               fzPrint('pp', 'Report');
            }, 500);

            
        }
    })

}

function cancelApp(appid) {

    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this supplier!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                type : 'POST',
                url: '<?php echo get_site_url('supplier/cancel'); ?>',
                data: {id: appid},
                success: function(result){

                    Swal.fire({
                      title: "Deleted!",
                      text: "Supplier deleted.",
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

<style type="text/css">
.fz-col-filter{
	font-weight: normal; 
	width: 100%; 
	border: none; 
	background-color: #eee; 
	height: 35px;
	display: block;
}

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


<div class="hidden" id="prescptnPrint">
    
</div>