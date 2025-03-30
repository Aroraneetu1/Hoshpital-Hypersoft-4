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

<section id="unseen">
    <table class="table table-bordered table-striped table-condensed table-db-js">
        <thead>
            <tr>
                <th>Service Name</th>
                <th>Duration (minutes)</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($rows): ?>
                <?php foreach($rows as $row): ?>
                    <tr>
                        <td><?php echo $row->name; ?></td>
                        <td><?php echo $row->duration_min; ?></td>
                        <td><?php echo $row->description; ?></td>
                        <td><?php echo number_format($row->amount,2); ?></td>
                        <td>
                            <a class="btn btn-info loader-activate" href="<?php echo get_site_url('service_types/edit/'.$row->id); ?>">
                                Edit
                            </a>
                            <a class="btn btn-danger" onclick="return confirm('Are you sure?');" href="<?php echo get_site_url('service_types/delete/'.$row->id); ?>">
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
                        pageLength: 8,
                        sorting: false,
                    });

                    $(".dataTables_wrapper .dataTables_filter input").css("border", "none");
                    $(".dataTables_wrapper .dataTables_filter input").css("background-color", "#eee");
                    $(".dataTables_wrapper .dataTables_filter input").css("height", "35px");
                    $(".dataTables_wrapper .dataTables_filter input").css("margin-bottom", "10px");
                });
                </script>
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
<!-- <?php// echo $pagination; ?> -->