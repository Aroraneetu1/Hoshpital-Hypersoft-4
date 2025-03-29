<?php 
    
    $roomcategories = get_key_value_array('room_categories', 'id', array('name'));

 ?>

<section id="unseen">

    <?php include('rooms_div.php'); ?>

    <table class="table table-bordered table-striped table-condensed table-sm table-db-js">
        <thead>
            <tr>
                <th>Room Name</th>
                <th>Room Number</th>
                <th>Category</th>
                <th>Description</th>
                <th>Rate</th>
                <th>Status</th>
                <th>Action</th>
                
            </tr>
        </thead>
        <tbody>
            <?php if($rooms): ?>
                <?php foreach($rooms as $row): ?>
                    <tr>
                        <td><?php echo $row->room_name; ?></td>
                        <td><?php echo $row->room_number; ?></td>
                        <td><?php echo @$roomcategories[$row->room_category]; ?></td>
                        <td><?php echo $row->description; ?></td>
                        <td><?php echo $row->room_rate; ?></td>
                        <td style="font-weight: 600" class="<?php if($row->status == 'Available'){echo 'text-success';}else{echo 'text-info';}?>"><?php echo $row->status; ?></td>
                        
                        <td>
                            <a class="btn btn-info btn-sm" href="<?php echo get_site_url('admin/erooms/'.$row->id); ?>">
                                Edit
                            </a>
                            <a class="btn btn-danger btn-sm" onclick="cancelex(<?php echo $row->id?>)" href="javascript:;">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <script type="text/javascript">
                $(document).ready(function(){
                    $(".table-db-js").DataTable({
                        paging: false,
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
                </script>
            <?php else: ?>
                <tr>
                    <td colspan="7">
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
    function cancelex(appid) {

        Swal.fire({
          title: "Are you sure?",
          text: "You won't be able to revert this room!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type : 'POST',
                    url: '<?php echo get_site_url('admin/delete_room'); ?>',
                    data: {id: appid},
                    success: function(result){

                        Swal.fire({
                          title: "Deleted!",
                          text: "Room category deleted.",
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