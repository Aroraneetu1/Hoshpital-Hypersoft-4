<div class="container">
	<div class="row social_networks">
		<div class="col-md-2" style="margin-bottom: 30px;"></div>
        <div class="col-md-4" style="margin-bottom: 30px;">
	        <ul>
	            <li>
	                <a href="<?php echo get_site_url('providers/all_schedules'); ?>" class="facebook loader-activate">
	                    <i class="fa fa-clock-o" style="font-size: 33px; color: #fff;"></i>
	                    <span>Manage Schedules</span>
	                    <div class="clear"></div>
	                </a>
	            </li>
	            <li>
	                <a href="<?php echo get_site_url('service_types/all'); ?>" class="facebook loader-activate">
	                    <i class="fa fa-heartbeat" style="font-size: 33px; color: #fff;"></i>
	                    <span>Manage Services</span>
	                    <div class="clear"></div>
	                </a>
	            </li>

	            <?php if(get_session_data('role') == 1){ ?>
	            	
		            <li>
		                <a href="<?php echo get_site_url('admin/clinic_info'); ?>" class="facebook loader-activate">
		                    <i class="fa fa-hospital-o" style="font-size: 33px; color: #fff;"></i>
		                    <span>Hospital Info</span>
		                    <div class="clear"></div>
		                </a>
		            </li>
		        <?php } ?>
	            
	            <li>
	                <a href="<?php echo get_site_url('consumers/export_excel'); ?>" class="facebook">
	                    <i class="fa fa-download" style="font-size: 33px; color: #fff;"></i>
	                    <span>Export Excel - Patients</span>
	                    <div class="clear"></div>
	                </a>
	            </li>

	            <li>
	                <a href="<?php echo get_site_url('admin/rooms'); ?>" class="facebook">
	                    <i class="fa fa-hotel" style="font-size: 33px; color: #fff;"></i>
	                    <span>Rooms</span>
	                    <div class="clear"></div>
	                </a>
	            </li>

	            <li>
	                <a href="javascript: void();" class="facebook">
	                    <i class="fa fa-clock-o" style="font-size: 33px; color: #fff;"></i>
	                    <span style="padding: 13px !important;">
							<?php
							$server_timezone = get_option('timezone', 'Etc/Greenwich');
							$timezone_array = array(
								'Pacific/Midway' => '(GMT-11:00) Midway Island','Pacific/Samoa' => '(GMT-11:00) Samoa','Pacific/Honolulu' => '(GMT-10:00) Hawaii','US/Alaska' => '(GMT-09:00) Alaska','America/Los_Angeles' => '(GMT-08:00) Pacific Time (US & Canada)','America/Tijuana' => '(GMT-08:00) Tijuana','US/Arizona' => '(GMT-07:00) Arizona','America/Chihuahua' => '(GMT-07:00) Chihuahua','America/Chihuahua' => '(GMT-07:00) La Paz','America/Mazatlan' => '(GMT-07:00) Mazatlan','US/Mountain' => '(GMT-07:00) Mountain Time (US & Canada)','America/Managua' => '(GMT-06:00) Central America','US/Central' => '(GMT-06:00) Central Time (US & Canada)','America/Mexico_City' => '(GMT-06:00) Guadalajara','America/Mexico_City' => '(GMT-06:00) Mexico City','America/Monterrey' => '(GMT-06:00) Monterrey','Canada/Saskatchewan' => '(GMT-06:00) Saskatchewan','America/Bogota' => '(GMT-05:00) Bogota','US/Eastern' => '(GMT-05:00) Eastern Time (US & Canada)','US/East-Indiana' => '(GMT-05:00) Indiana (East)','America/Lima' => '(GMT-05:00) Lima','America/Bogota' => '(GMT-05:00) Quito','Canada/Atlantic' => '(GMT-04:00) Atlantic Time (Canada)','America/Caracas' => '(GMT-04:30) Caracas','America/La_Paz' => '(GMT-04:00) La Paz','America/Santiago' => '(GMT-04:00) Santiago','Canada/Newfoundland' => '(GMT-03:30) Newfoundland','America/Sao_Paulo' => '(GMT-03:00) Brasilia','America/Argentina/Buenos_Aires' => '(GMT-03:00) Buenos Aires','America/Argentina/Buenos_Aires' => '(GMT-03:00) Georgetown','America/Godthab' => '(GMT-03:00) Greenland','America/Noronha' => '(GMT-02:00) Mid-Atlantic','Atlantic/Azores' => '(GMT-01:00) Azores','Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.','Africa/Casablanca' => '(GMT+00:00) Casablanca','Europe/London' => '(GMT+00:00) Edinburgh','Etc/Greenwich' => '(GMT+00:00) Greenwich Mean Time : Dublin','Europe/Lisbon' => '(GMT+00:00) Lisbon','Europe/London' => '(GMT+00:00) London','Africa/Monrovia' => '(GMT+00:00) Monrovia','GMT' => '(GMT+00:00) GMT','Europe/Amsterdam' => '(GMT+01:00) Amsterdam','Europe/Belgrade' => '(GMT+01:00) Belgrade','Europe/Berlin' => '(GMT+01:00) Berlin','Europe/Berlin' => '(GMT+01:00) Bern','Europe/Bratislava' => '(GMT+01:00) Bratislava','Europe/Brussels' => '(GMT+01:00) Brussels','Europe/Budapest' => '(GMT+01:00) Budapest','Europe/Copenhagen' => '(GMT+01:00) Copenhagen','Europe/Ljubljana' => '(GMT+01:00) Ljubljana','Europe/Madrid' => '(GMT+01:00) Madrid','Europe/Paris' => '(GMT+01:00) Paris','Europe/Prague' => '(GMT+01:00) Prague','Europe/Rome' => '(GMT+01:00) Rome','Europe/Sarajevo' => '(GMT+01:00) Sarajevo','Europe/Skopje' => '(GMT+01:00) Skopje','Europe/Stockholm' => '(GMT+01:00) Stockholm','Europe/Vienna' => '(GMT+01:00) Vienna','Europe/Warsaw' => '(GMT+01:00) Warsaw','Africa/Lagos' => '(GMT+01:00) West Central Africa','Europe/Zagreb' => '(GMT+01:00) Zagreb','Europe/Athens' => '(GMT+02:00) Athens','Europe/Bucharest' => '(GMT+02:00) Bucharest','Africa/Cairo' => '(GMT+02:00) Cairo','Africa/Harare' => '(GMT+02:00) Harare','Europe/Helsinki' => '(GMT+02:00) Helsinki','Europe/Istanbul' => '(GMT+02:00) Istanbul','Asia/Jerusalem' => '(GMT+02:00) Jerusalem','Europe/Helsinki' => '(GMT+02:00) Kyiv','Africa/Johannesburg' => '(GMT+02:00) Pretoria','Europe/Riga' => '(GMT+02:00) Riga','Europe/Sofia' => '(GMT+02:00) Sofia','Europe/Tallinn' => '(GMT+02:00) Tallinn','Europe/Vilnius' => '(GMT+02:00) Vilnius','Asia/Baghdad' => '(GMT+03:00) Baghdad','Asia/Kuwait' => '(GMT+03:00) Kuwait','Europe/Minsk' => '(GMT+03:00) Minsk','Africa/Nairobi' => '(GMT+03:00) Nairobi','Asia/Riyadh' => '(GMT+03:00) Riyadh','Europe/Volgograd' => '(GMT+03:00) Volgograd','Asia/Tehran' => '(GMT+03:30) Tehran','Asia/Muscat' => '(GMT+04:00) Abu Dhabi','Asia/Baku' => '(GMT+04:00) Baku','Europe/Moscow' => '(GMT+04:00) Moscow','Asia/Muscat' => '(GMT+04:00) Muscat','Europe/Moscow' => '(GMT+04:00) St. Petersburg','Asia/Tbilisi' => '(GMT+04:00) Tbilisi','Asia/Yerevan' => '(GMT+04:00) Yerevan','Asia/Kabul' => '(GMT+04:30) Kabul','Asia/Karachi' => '(GMT+05:00) Islamabad','Asia/Karachi' => '(GMT+05:00) Karachi','Asia/Tashkent' => '(GMT+05:00) Tashkent','Asia/Calcutta' => '(GMT+05:30) Chennai','Asia/Kolkata' => '(GMT+05:30) Kolkata','Asia/Calcutta' => '(GMT+05:30) Mumbai','Asia/Calcutta' => '(GMT+05:30) New Delhi','Asia/Calcutta' => '(GMT+05:30) Sri Jayawardenepura','Asia/Katmandu' => '(GMT+05:45) Kathmandu','Asia/Almaty' => '(GMT+06:00) Almaty','Asia/Dhaka' => '(GMT+06:00) Astana','Asia/Dhaka' => '(GMT+06:00) Dhaka','Asia/Yekaterinburg' => '(GMT+06:00) Ekaterinburg','Asia/Rangoon' => '(GMT+06:30) Rangoon','Asia/Bangkok' => '(GMT+07:00) Bangkok','Asia/Bangkok' => '(GMT+07:00) Hanoi','Asia/Jakarta' => '(GMT+07:00) Jakarta','Asia/Novosibirsk' => '(GMT+07:00) Novosibirsk','Asia/Hong_Kong' => '(GMT+08:00) Beijing','Asia/Chongqing' => '(GMT+08:00) Chongqing','Asia/Hong_Kong' => '(GMT+08:00) Hong Kong','Asia/Krasnoyarsk' => '(GMT+08:00) Krasnoyarsk','Asia/Kuala_Lumpur' => '(GMT+08:00) Kuala Lumpur','Australia/Perth' => '(GMT+08:00) Perth','Asia/Singapore' => '(GMT+08:00) Singapore','Asia/Taipei' => '(GMT+08:00) Taipei','Asia/Ulan_Bator' => '(GMT+08:00) Ulaan Bataar','Asia/Urumqi' => '(GMT+08:00) Urumqi','Asia/Irkutsk' => '(GMT+09:00) Irkutsk','Asia/Tokyo' => '(GMT+09:00) Osaka','Asia/Tokyo' => '(GMT+09:00) Sapporo','Asia/Seoul' => '(GMT+09:00) Seoul','Asia/Tokyo' => '(GMT+09:00) Tokyo','Australia/Adelaide' => '(GMT+09:30) Adelaide','Australia/Darwin' => '(GMT+09:30) Darwin','Australia/Brisbane' => '(GMT+10:00) Brisbane','Australia/Canberra' => '(GMT+10:00) Canberra','Pacific/Guam' => '(GMT+10:00) Guam','Australia/Hobart' => '(GMT+10:00) Hobart','Australia/Melbourne' => '(GMT+10:00) Melbourne','Pacific/Port_Moresby' => '(GMT+10:00) Port Moresby','Australia/Sydney' => '(GMT+10:00) Sydney','Asia/Yakutsk' => '(GMT+10:00) Yakutsk','Asia/Vladivostok' => '(GMT+11:00) Vladivostok','Pacific/Auckland' => '(GMT+12:00) Auckland','Pacific/Fiji' => '(GMT+12:00) Fiji','Pacific/Kwajalein' => '(GMT+12:00) International Date Line West','Asia/Kamchatka' => '(GMT+12:00) Kamchatka','Asia/Magadan' => '(GMT+12:00) Magadan','Pacific/Fiji' => '(GMT+12:00) Marshall Is.','Asia/Magadan' => '(GMT+12:00) New Caledonia','Asia/Magadan' => '(GMT+12:00) Solomon Is.','Pacific/Auckland' => '(GMT+12:00) Wellington','Pacific/Tongatapu' => '(GMT+13:00) Tongatapu',
							);
							$timezone_array = get_timezone_array();
							?>
							<form id="timezone-form" action="<?php echo get_site_url('admin/change_timezone'); ?>" method="post">
								<select onchange="$('#timezone-form').submit();" style="display: inline-block; width: 255px; background-color: #fff;" class="form-control" name="timezone">
									<?php foreach($timezone_array as $key => $value): ?>
										<option <?php if($server_timezone == $key){ echo 'selected'; } ?> value="<?php echo $key; ?>">
											<?php echo $value; ?>
										</option>
									<?php endforeach; ?>
								</select>
							</form>
		                </span>
	                    <div class="clear"></div>
	                </a>    
	            </li>
	            <!-- <li>
	                <a href="<?php echo get_site_url('admin/change_password'); ?>" class="twitter loader-activate">
	                    <i class="fa fa-lock" style="font-size: 33px; color: #fff;"></i>
	                    <span>Change Password</span>
	                    <div class="clear"></div>
	                </a>
	            </li> -->
	        </ul>  
        </div>
        <div class="col-md-4" style="margin-bottom: 30px;">
	        <ul>
	        	<?php if(get_session_data('role') == 1){ ?>
		            <li>
		                <a href="<?php echo get_site_url('admin/category'); ?>" class="googleplus loader-activate">
		                    <i class="fa fa-list" style="font-size: 33px; color: #fff;"></i>
		                    <span>Category</span>
		                    <div class="clear"></div>
		                </a>
		            </li>
		            
		        <?php } ?>

		        <li>
	                <a href="<?php echo get_site_url('admin/products'); ?>" class="googleplus loader-activate">
	                    <i class="fa fa-product-hunt" style="font-size: 33px; color: #fff;"></i>
	                    <span>Products</span>
	                    <div class="clear"></div>
	                </a>
	            </li>

	            <li>
	                <a href="<?php echo get_site_url(); ?>admin/user_list" class="googleplus loader-activate">
	                    <i class="fa fa-users" style="font-size: 33px; color: #fff;"></i>
	                    <span>Users</span>
	                    <div class="clear"></div>
	                </a>
	            </li>
	            <li>
	                <a href="<?php echo get_site_url(); ?>supplier/all" class="googleplus loader-activate">
	                    <i class="fa fa-users" style="font-size: 33px; color: #fff;"></i>
	                    <span>Supplier</span>
	                    <div class="clear"></div>
	                </a>
	            </li>

	            <?php if(get_session_data('role') == 1){ ?>
	            <li>
	                <a href="<?php echo get_site_url(); ?>admin/payment_types" class="googleplus loader-activate">
	                    <i class="fa fa-credit-card" style="font-size: 33px; color: #fff;"></i>
	                    <span>Payment Types</span>
	                    <div class="clear"></div>
	                </a>
	            </li>
	            <?php } ?>

	            <li>
	                <a href="<?php echo get_site_url('admin/report'); ?>" class="googleplus loader-activate">
	                    <i class="fa fa-file" style="font-size: 33px; color: #fff;"></i>
	                    <span>Reports</span>
	                    <div class="clear"></div>
	                </a>
	            </li>
	        </ul>
	    </div>
	    <div class="col-md-2" style="margin-bottom: 30px;"></div>
    </div>
</div>