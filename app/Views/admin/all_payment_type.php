<section id="unseen">

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">
            <a href="<?php echo get_site_url('admin/payment_types'); ?>" class="btn loader-activate btn-success">Payment Types</a>
            <a href="<?php echo get_site_url('admin/add_payment_types'); ?>" class="btn loader-activate btn-default">Add New Payment Types</a>
        </div>
    </div>

    <table class="table table-bordered table-striped table-condensed table-db-js">
        <thead>
            <tr>
                <th>S. No.</th>
                <th>Payment Type Name</th>
                <th>Action</th>
                
            </tr>
        </thead>
        <tbody>
            <?php if($payment_types): ?>
                <?php foreach($payment_types as $row): ?>
                    <tr>
                        <td><?php echo $row->id; ?></td>
                        <td><?php echo $row->name; ?></td>
                        <td>
                             <a class="btn btn-info btn-sm loader-activate" href="<?php echo get_site_url('admin/edit_payment_type/'.$row->id); ?>">
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
                        paging: true,
                        info: false,
                        sorting: false,
                        pageLength: 8,
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
                    <td colspan="2">
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
          text: "You won't be able to revert this payment type!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type : 'POST',
                    url: '<?php echo get_site_url('admin/delete_payment_type'); ?>',
                    data: {id: appid},
                    success: function(result){

                        Swal.fire({
                          title: "Deleted!",
                          text: "Payment type deleted.",
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