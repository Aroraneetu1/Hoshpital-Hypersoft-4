<section id="unseen">
    <table class="table table-bordered table-striped table-condensed table-db-js">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Username</th>
                <th>User Role</th>
                <th>Permissions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($users): ?>
                <?php foreach($users as $row): ?>
                    <tr>
                        <td><?php echo $row->first_name; ?> <?php echo $row->last_name; ?></td>
                        <td><?php echo $row->username; ?></td>
                        <td><?php echo $row->role; ?></td>
                        <td>
                        <?php 
                            if($row->permissions !=''){
                                $permissions = unserialize($row->permissions);

                                foreach ($permissions as $key => $value) {
                                    if($value == 1){
                                        echo '<span class="badge bg-info">'.ucfirst($key);
                                        echo '</span> ';
                                    }
                                }

                            }
                        ?></td>
                        <td>
                            <a class="btn btn-info btn-sm loader-activate" href="<?php echo get_site_url('admin/edit_user/'.$row->id); ?>">
                                Edit
                            </a>
                            <a class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');" href="<?php echo get_site_url('admin/delete/'.$row->id); ?>">
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