<?php 

$permissions = unserialize(get_session_data('permissions'));

$providers = get_key_value_array('users', 'id', array('first_name', 'last_name')); ?>


<section id="unseen">
    <table id="table-db-js" class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <th>Doctor Name</th>
                <th>Schedule From</th>
                <th>Schedule To</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($rows): ?>
                <?php foreach($rows as $row): ?>
                    <tr>
                        <td><?php echo @$providers[$row->provider_id]; ?></td>
                        <td><?php echo $row->s_date; ?></td>
                        <td><?php echo $row->e_date; ?></td>
                        <td><?php echo $row->s_start; ?></td>
                        <td><?php echo $row->s_end; ?></td>
                        <td>
                            <?php if($permissions['doctors'] == 1){?>
                            <a class="btn btn-info btn-sm loader-activate" href="<?php echo get_site_url('providers/edit_schedule/'.$row->id); ?>">
                                Edit
                            </a>
                            <a class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');" href="<?php echo get_site_url('providers/delete_schedule/'.$row->id); ?>">
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



<script type="text/javascript">
$(document).ready(function(){
    $("#table-db-js thead th").each(function(){
        var title = $(this).text();
        if(title != "Actions"){
        	$(this).append('<input class="fz-col-filter">');
        }
    });
    var table = $("#table-db-js").DataTable({
        paging: false,
        // info: false,
        sort: false,
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
</style>