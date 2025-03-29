<?php 

$permissions = unserialize(get_session_data('permissions'));

?>
<?php if($permissions['appointments'] == 0){  

    echo no_access_msg(); ?>


<?php }else{ ?>

<div class="form">
    <form onsubmit="$('#destination_services option').prop('selected', true);" action="" class="cmxform form-horizontal form-example" method="post">
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Patient</label>
                <select class="form-control" name="consumer_id">
                    <option value=""> - Please Select - </option>
                    <?php if($consumers): ?>
                        <?php foreach($consumers as $consumer): ?>
                            <option <?php echo set_select('consumer_id', $consumer->id); ?> value="<?php echo $consumer->id; ?>" >
                                <?php echo $consumer->phone_number; ?>
                                	 - 
                                <?php echo $consumer->first_name; ?> <?php echo $consumer->middle_name; ?> <?php echo $consumer->last_name; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>    
                </select>
                <span class="error"><?php echo form_error("consumer_id"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>Service Type</label>
                <select class="form-control" name="service_id">
                    <option data-duration="0" value=""> - Please Select - </option>
                    <?php if($services): ?>
                        <?php foreach($services as $service): ?>
                            <option data-duration="<?php echo $service->duration_sec; ?>" <?php echo set_select('service_id', $service->id); ?> value="<?php echo $service->id; ?>">
                                <?php echo $service->name; ?>
                            </option>
                        <?php endforeach; ?>    
                    <?php endif; ?>    
                </select>    
                <span class="error"><?php echo form_error("service_id"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-8 col-md-10 col-sm-12 col-xm-12">
                <label>Doctor</label>
                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <td>Doctor Name</td>
                                <td>Date</td>
                                <td>Start Time</td>
                                <td>Price</td>
                            </tr>    
                        </thead>
                        <tbody id="ajax-available-providers">
                        </tbody>
                    </table> 
                <span class="error"><?php echo form_error("new-appointment"); ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xm-12">
                <label>&nbsp;</label>
                <button name="submit" type="submit" class="btn btn-primary form-control loader-activate">
                    Add New Appointment
                </button>    
            </div>
        </div>
   </form>
</div>

<?php } ?>

<script type="text/javascript">
$(document).ready(function(){
  	$("select[name='consumer_id']").select2();
  	$("select[name='service_id']").select2();

    ajax_get_available_providers();
});

$(document).on("change", "select[name='service_id']", function(){
    ajax_get_available_providers();
});

function ajax_get_available_providers(){
    var service_id = $("select[name='service_id']").val();
    var duration = $("select[name='service_id'] option:selected").data("duration");
    if(service_id == ''){
        $("#ajax-available-providers").html("<tr><td colspan='4'>No available time slots found.</td></tr>");
    }else{
        $("#ajax-available-providers").html("<tr><td colspan='4'><img style='height: 40px;' src='<?php echo get_assets_url(); ?>images/cardiac-loader.gif'></td></tr>");
        $.ajax({
            type: "POST",
            url: "<?php echo get_site_url(); ?>appointments/ajax_get_available_providers",
            data: {
                service_id: service_id,
                duration: duration,
            },
            success: function(res){
                if(res == ''){
                    $("#ajax-available-providers").html("<tr><td colspan='4'>No available time slots found.</td></tr>");
                }else{
                    $("#ajax-available-providers").html(res);
                }
            }
        });
    }
}
</script>

<style type="text/css">
label.new-appointment-label{
    cursor: pointer;
}
label.new-appointment-label::before{
    background-color: #eee;
    border: 2px solid #ddd;
    color: #ccc;
    content: "✗";
    display: inline-table;
    font-size: 18px;
    font-weight: bold;
    margin-right: 10px;
    text-align: center;
    width: 28px;    
}
input[name='new-appointment']{
    display: none;
}
input[name='new-appointment']:checked + label.new-appointment-label::before{
    background-color: #ebf7b4;
    color: #8aa903;
    border: 2px solid #8aa903;
    content: "✔";
}    
</style>