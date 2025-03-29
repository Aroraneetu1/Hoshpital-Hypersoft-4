<?php 

    $permissions = unserialize(get_session_data('permissions'));
    $settingspp = isset($permissions['settings']) ? $permissions['settings'] : 0;

    //error_reporting(0);
    //$consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name'));
    //$providers = get_key_value_array('users', 'id', array('first_name', 'last_name')); 
    //$services = get_key_value_array('service_types', 'id', array('name')); 
    $serpric = get_key_value_array('service_types', 'id', array('amount'));
    //$appointments = get_key_value_array('appointments', 'id', array('consumer_id'));
    //$products = get_key_value_array('products', 'id', array('name'));


    $tot_amount = 0;
    if($sales1):
        foreach($sales1 as $row): 

            $amt = isset($serpric[$row->service_id]) ? $serpric[$row->service_id] : 0;
            $tot_amount += $amt; 
            
        endforeach;
    endif; 
    if($sales2):
        foreach($sales2 as $row): 
        	$tot_amount += $row->lt_price; 
        endforeach;
    endif;

    /*echo '<pre>';
    print_r($expense_array);
    //print_r($sales2);
    echo '</pre>';*/

?>

<?php if($settingspp != 1){  

    echo no_access_msg(); ?>


<?php }else{ ?>

    <style type="text/css">
    	strong, h4{
    		font-weight: 600 !important;
    	}
    	th, td{
    		text-align: left !important;
            font-weight: 600 !important;
    	}
        p#plprint{
            font-size: 30px !important;
        }
        .tophead{
            float: left !important;
        }
    </style>
    <section id="unseen">
    	<div class="row" style="margin-bottom: 15px;">
    		<div class="col-lg-9">
    			<form action="<?php echo get_site_url('finance/income_statement'); ?>" method="get" >
    				<input value="<?php echo $from; ?>" type="text" name="from" placeholder="From" required style="margin-right: 10px; width: 180px; height: 35px; border: none; background-color: #eee; padding-left: 15px;" >
    				<input value="<?php echo $to; ?>" type="text" name="to" placeholder="To" required style="margin-right: 10px; width: 180px; height: 35px; border: none; background-color: #eee; padding-left: 15px;" >
    				<input type="submit" name="submit" value="Search" class="btn btn-success">
    			</form>
    		</div>
            <div class="col-lg-3">
               
                <a href="javascript:;" onclick="printpl()" class="btn btn-danger pull-right" style="margin-right: 5px;">Print</a>
            </div>
    	</div>

    	<div id="customPrintButton">
            
            <div class="row">
                <?php 
                    $get_clinic_info = get_clinic_info();

                    $header = '<div class="col-md-3" style="text-align: left;">
                           <img src="'.str_replace('index.php/', '', get_site_url().''.$get_clinic_info->logo).'" alt="Hospital Logo" style="width: 150px;">
                           
                        </div>

                        <div class="col-md-9" style="text-align: left;">
                            <p style="margin: 0; " id="plprint"><strong>Profit & Loss</strong></p>
                            <p style="margin: 0;margin-top: 5px; font-size: 18px;"></p>
                            <p style="margin: 0; font-size: 18px;">'.$get_clinic_info->name.'</p>
                            <p style="margin: 0; font-size: 16px;">'.$get_clinic_info->address.'</p>
                            <p style="margin: 0; font-size: 14px;">'.$get_clinic_info->contact.'</p>
                            
                        </div>';

                    echo $header;
                ?>
            </div>
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-3">
                    <h4>Date: <?php echo date('d-M-Y');?></h4>
                </div>
                <div class="col-md-9">
                    <h4>From: <?php echo $from; ?> &nbsp;&nbsp;&nbsp;&nbsp; To:<?php echo $to; ?></h4>
                </div>
            </div>
           
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td style="width: 15%;"><strong>Income</strong></td>
                        <td colspan="10"></td>
                        <td style="width: 15%;"></td>
                    </tr>
                    <tr>
                    	<td></td>
                        <td colspan="10">Total Sales</td>
                        <td><?php echo number_format($tot_amount,2);?></td>
                    </tr>
                    <tr>
                        <td></td>
                    	<td colspan="10"><strong>Gross Sales</strong></td>
                       
                        <td><strong><?php echo number_format($tot_amount,2);?></strong></td>
                    </tr>
                    <tr>
                        <td></td>
                    	<td colspan="10">Total Cost of Good Sold</td>
                        
                        <td>0.00</td>
                    </tr>
                    <tr>
                    	<td><strong>Gross Profit</strong></td>
                        
                        <td colspan="10"></td>
                        <td><strong><?php echo number_format($tot_amount,2);?></strong></td>
                    </tr>
                    <tr>
                    	<td><strong>Expenses</strong></td>
                        
                        <td colspan="10"></td>
                        <td></td>
                    </tr>

                    <?php 
                    $totexp = 0;
                    if(!empty($expense_array)){
                    foreach ($expense_array as $kk => $vv) { 

                        $totexp += $vv['amount']; ?>
                        
                        <tr>
                            <td></td>
                            <td colspan="10"><?php echo $vv['name']?></td>
                            
                            <td><?php echo number_format($vv['amount'],2)?></td>
                        </tr>
                    <?php } } ?>
                    
                    <tr>
                    	<td><strong>Total</strong></td>
                        
                        <td colspan="10"></td>
                        <td><strong><?php echo number_format($totexp,2)?></strong></td>
                    </tr>
                    <tr>
                    	<td><strong>Net Profit</strong></td>
                       
                        <td colspan="10"></td>
                        <td><strong><?php echo number_format($tot_amount - $totexp,2)?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    <script type="text/javascript">
    function printpl(){
        setTimeout(() => {
            fzPrint('customPrintButton', 'Report')
        }, 1000);
    }

    $(document).ready(function(){
        $(".table-db-js thead th").each(function(){
            var title = $(this).text();
            if(title != "Actions"){
            	$(this).append('<input class="fz-col-filter">');
            }
        });
        var table = $(".table-db-js").DataTable({
            paging: true,
            info: false,
            sorting: false,
            pageLength: 10,
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

<?php } ?>