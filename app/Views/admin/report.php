<?php $consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name')); ?>
<?php $providers = get_key_value_array('users', 'id', array('first_name', 'last_name')); ?>
<?php $services = get_key_value_array('service_types', 'id', array('name')); ?>

<section id="unseen">
	<div class="row" style="margin-bottom: 15px;">
		<div class="col-lg-9">
			<form action="<?php echo get_site_url('/admin/report'); ?>" method="get" >
				<input value="<?php echo $from; ?>" type="text" name="from" placeholder="From" required style="margin-right: 10px; width: 180px; height: 35px; border: none; background-color: #eee; padding-left: 15px;" >
				<input value="<?php echo $to; ?>" type="text" name="to" placeholder="To" required style="margin-right: 10px; width: 180px; height: 35px; border: none; background-color: #eee; padding-left: 15px;" >
				<input type="submit" name="submit" value="Search" class="btn btn-success">
			</form>
		</div>

        <div class="col-lg-3">

            <a href="javascript:;" id="downloadCsv" class="btn btn-info pull-right">Download Excel</a>
            <a href="javascript:;" id="customPrintButton" class="btn btn-danger pull-right" style="margin-right: 5px;">Print</a>

        </div>


	</div>
    <table id="dailyPrint" class="table table-bordered table-striped table-condensed table-db-js">
        <thead>
            <tr>
            	<th>REF. ID</th>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Service</th>
                <th>Appointment Time</th>
                <th>Status</th>
                <!-- <th>Actions</th> -->
            </tr>
        </thead>
        <tbody>
            <?php if($rows): ?>
                <?php foreach($rows as $row): ?>
                    <tr>	
                    	<td><?php echo get_formated_id('A', $row->id); ?></td>
                        <td><?php echo @$consumers[$row->consumer_id]; ?></td>
                        <td><?php echo @$providers[$row->provider_id]; ?></td>
                        <td><?php echo @$services[$row->service_id]; ?></td>
                        <td>
                            <?php echo date(get_date_format(), $row->visited_start_time); ?> 
                            <?php echo date('h:i A', $row->visited_start_time); ?>
                        </td>
                        <td>
                            <?php 
                                switch($row->status){
                                    case '0':
                                        echo 'Canceled';
                                    break;
                                    case '1':
                                        echo 'Awaiting';
                                    break;
                                    case '2':
                                        echo 'Active';
                                    break;
                                    case '3':
                                        echo 'Completed';
                                    break;
                                    default:
                                        echo 'Pending';
                                    break;
                                }
                            ?>
                        </td>
                        <!-- <td>
                            <?php /*if($row->status == 0): ?>
                                -
                            <?php elseif($row->status == 1): ?>
                                <a class="btn btn-info loader-activate" href="<?php echo get_site_url('appointments/visit/'.$row->id.'/start'); ?>">
                                    Start Visit
                                </a>
                                <a class="btn btn-danger loader-activate" href="<?php echo get_site_url('appointments/cancel_visit/'.$row->id); ?>">
                                    Cancel
                                </a>
                            <?php elseif($row->status == 2): ?>
                                <a class="btn btn-primary loader-activate" href="<?php echo get_site_url('appointments/visit/'.$row->id.'/active'); ?>">
                                    Continue Visit
                                </a>
                                <a class="btn btn-danger loader-activate" href="<?php echo get_site_url('appointments/cancel_visit/'.$row->id); ?>">
                                    Cancel
                                </a>
                            <?php else: ?>    
                                <a class="btn btn-success loader-activate" href="<?php echo get_site_url('appointments/visit/'.$row->id.'/view'); ?>">
                                    View
                                </a>
                            <?php endif; */?> 


                        </td> -->
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<script type="text/javascript">
$(document).ready(function(){
    $(".table-db-js thead th").each(function(){
        var title = $(this).text();
        if(title != "Actions"){
        	$(this).append('<input class="fz-col-filter">');
        }
    });
    var table = $(".table-db-js").DataTable({
        paging: false,
        // info: false,
        sorting: false,
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

  	var dateFormat = "d MM yy";
  	var from = $("input[name='from']").datepicker({
        dateFormat: dateFormat,
        changeMonth: true,
        changeYear: true,
    }).on("change", function(){
          to.datepicker("option", "minDate", getDate(this));
    });
    var to = $("input[name='to']").datepicker({
        dateFormat: dateFormat,
        changeMonth: true,
        changeYear: true,
  	}).on("change", function(){
        from.datepicker("option", "maxDate", getDate(this));
    });
 
    function getDate(element){
      	var date;
      	try{
        	date = $.datepicker.parseDate(dateFormat, element.value);
      	}catch(error){
        	date = null;
      	}
      	return date;
    }

    $('#customPrintButton').on('click', function () {
        //var table = $('#example').DataTable();
        
        // Switch to all rows
        table.page.len(-1).draw();

        // Trigger print
        setTimeout(() => {
           fzPrint('dailyPrint', 'Report')

            // Restore original page length after print
            table.page.len(10).draw(); // Set back to default page size
        }, 500); // Add slight delay to ensure data renders
    });

    $('#downloadCsv').on('click', function () {
        // Fetch all rows
        var allData = table.rows({ search: 'applied' }).data();

        // Generate CSV
        var csvData = [];
        csvData.push(["REF. ID", "Patient Name", "Doctor Name", "Service", "Appointment Time", "Status"].join(',')); // Header row
        
        allData.each(function (row) {
            // Extract salary and convert to numeric
            var appointmentTime = row[4].toString().replace(/\n/g, ' ').trim();

            // Add row to CSV
            csvData.push([
                row[0], // Name
                row[1], // Position
                row[2], // Office
                row[3], // Age
                appointmentTime, // Start date
                row[5], // Start date
                
            ].join(','));
        });

        // Convert array to CSV string
        var csvString = csvData.join('\n');

        // Create a blob and trigger download
        var blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'daily_report.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });


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