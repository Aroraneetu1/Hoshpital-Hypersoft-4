
<div class="modal fade" id="cpModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 400px;">
        <div class="modal-content">
            <form action="<?php echo get_site_url('admin/change_password'); ?>" method="post">
               
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            
                            <div class="form-group">

				                <label>Old Password</label>
				                <input type="password" class="form-control" name="opwd" required> 
				                

				            </div>

				            <div class="form-group">

				                <label>New Password</label>
				                <input type="password" class="form-control" name="npwd" minlength="6" required> 
				                

				            </div>

				            <div class="form-group">

				                <label>Confirm Password</label>
				                <input type="password" class="form-control" name="cpwd" required> 
				              

				            </div>

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
    
        
<style type="text/css">
span.error{
    background-color: #fff;
    color: red;
}    
</style>

       
    
<script type="text/javascript">

function changepass(){
	$('#cpModal').modal('show');
}

$(document).on("click", ".loader-activate", function(){
    $("#site-loader").show();
});

$(window).load(function(){
    $("#site-loader").hide();
});

function fzPrint(id, title){
	$("title").html("<?php echo get_site_name(); ?> - " + title);
	$(".fz-no-print").hide();
	printJS({ 
		printable: id, 
		type: 'html', 
		// header: title,
	});
	$(".fz-no-print").show();
}
</script>

	

				   	 	</div>
					</section>
				</div>
			</div>
		</div>
	</body>
</html>