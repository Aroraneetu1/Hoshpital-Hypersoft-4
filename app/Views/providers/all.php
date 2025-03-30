<?php 

$permissions = unserialize(get_session_data('permissions'));

$services = get_key_value_array('service_types', 'id', array('name')); ?>


<section id="unseen">
    <table class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
            	<th>REF. ID</th>
                <th>Doctor Name</th>
                <th>Phone Number</th>
                <th>Education</th>
                <th>Services</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($rows): ?>
                <?php foreach($rows as $row): ?>
                    <tr>
                    	<td><?php echo get_formated_id('D', $row->id); ?></td>
                        <td><?php echo $row->first_name; ?> <?php echo $row->last_name; ?></td>
                        <td><?php echo $row->phone_number; ?></td>
                        <td><?php echo $row->provider_education; ?></td>
                        <td>
                            <?php
                                $provider_services = json_decode($row->provider_services, TRUE);
                                if(is_array($provider_services) && count($provider_services) > 0){
                                    foreach($provider_services as $provider_service){
                                        echo '<p>- '. @$services[$provider_service] .'</p>';
                                    }
                                }
                            ?>
                        </td>
                        <td>
                            <?php if($permissions['doctors'] == 1){?>
                                <a class="btn btn-info loader-activate" href="<?php echo get_site_url('providers/edit/'.$row->id); ?>">
                                    Edit
                                </a>
                                <a class="btn btn-danger" onclick="return confirm('Are you sure?');" href="<?php echo get_site_url('providers/delete/'.$row->id); ?>">
                                    Delete
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">
                        No record found.
                    </td>
                </tr>    
            <?php endif; ?>
        </tbody>
    </table>
</section>
<?= $pagination ?>