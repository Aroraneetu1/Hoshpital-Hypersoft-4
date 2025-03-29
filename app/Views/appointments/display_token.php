<?php $consumers = get_key_value_array('consumers', 'id', array('first_name', 'last_name')); ?>
<?php $providers = get_key_value_array('users', 'id', array('first_name', 'last_name')); ?>
<?php $room_number = get_key_value_array('users', 'id', array('room_number')); ?>
<?php $services = get_key_value_array('service_types', 'id', array('name')); ?>

<section id="unseen">
    <table id="table-db-js" class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <th>Service Name</th>
                <th>Token Number</th>
                <th>Doctor Name - Room No.</th>
                <th class="hidden">Appointment Time</th>
                <th>Status</th>
                <th>Print</th>
            </tr>
        </thead>
        <tbody>
            <?php if($appointments): ?>
                <?php foreach($appointments as $row): ?>
                    <tr>
                        <td><?php echo @$services[$row->service_id]; ?></td>
                        <td><?php echo @$services[$row->service_id].' - '.$row->token_number; ?></td>
                        <td><?php echo @$providers[$row->provider_id]; ?> - <?php echo @$room_number[$row->provider_id]; ?></td>
                        <td class="hidden"><?php echo $row->start_time; ?> </td>
                        <td class="text-success" style="font-weight: 600;">
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
                        <td>
                            <a class="btn btn-warning btn-sm print-btn" href="javascript: void(0);">Print</a>
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


<!--============== For Print prescription only ===============-->
<!-- <style type="text/css">
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
.receipt {
  text-align: center;
}
.stars {
  font-size: 14px;
  margin: 10px 0;
}
.details {
  font-size: 12px;
  margin: 10px 0;
}
.token {
  font-size: 24px;
  font-weight: bold;
  margin: 20px 0;
}
.thank-you {
  font-size: 14px;
  margin: 20px 0;
}
</style>
<div class="hidden">

    <div class="receipt" id="printToken">
        <div class="stars">***************</div>
        <div class="details">DATE: 16/06/23</div>
        <div class="details">TIME: 17:38</div>
        <div class="token">TOKEN NO</div>
        <div class="token">004</div>
        <div class="thank-you">THANKS FOR VISIT</div>
    </div>

</div> -->

<script>
    document.addEventListener("DOMContentLoaded", function () {
      // Attach event listeners to all print buttons
      const printButtons = document.querySelectorAll(".print-btn");

      printButtons.forEach(button => {
        button.addEventListener("click", function () {
          // Fetch the row data
          const row = this.closest("tr");
          const department = row.cells[0].textContent;
          const token = row.cells[1].textContent;
          const name = row.cells[2].textContent;
          const date = row.cells[3].textContent;
          //const status = row.cells[4].textContent;

          const tokenNumber = token.match(/\d+$/)[0];

          // Generate the receipt layout
          const receiptContent = `
            <div class="receipt">
              <div>***************</div>
              <div style="font-weight: bold;">${date}</div>
              <div style="font-size: 30px; font-weight: bold;margin-top:10px;">TOKEN NO</div>
              <div style="font-size: 30px; font-weight: bold;margin-bottom:10px;">${tokenNumber}</div>
              <div style="font-weight: bold;">Thanks for Visiting</div>
              <div>***************</div>
            </div>
          `;

          // Open a new window for printing
          const printWindow = window.open("", "_blank");
          printWindow.document.write(`
            <html>
              <head>
                <style>
                  body { font-family: 'Courier New', Courier, monospace; text-align: center; margin: 0; }
                  /*body { text-align: center; margin: 0; }*/
                  .receipt { width: 80mm; margin: auto; }
                </style>
              </head>
              <body>
                ${receiptContent}
              </body>
            </html>
          `);
          printWindow.document.close();
          printWindow.focus();
          printWindow.print();
          printWindow.close();
        });
      });
    });
</script>

<script type="text/javascript">
$(document).ready(function(){
    /*$("#table-db-js thead th").each(function(){
        var title = $(this).text();
        if(title == "Appointment Time" || title == "Status" || title == "Doctor"){
            $(this).append('<input class="fz-col-filter">');
        }
    });*/
    var table = $("#table-db-js").DataTable({
        paging: false,
        info: false,
        sorting: false,
        //pageLength: 6,
        columnDefs: [
            { orderable: false, targets: '_all' }
        ],
    });
    /*table.columns().every(function(){
        var that = this;
        $("input.fz-col-filter", this.header()).on("keyup change", function(){
            if(that.search() !== this.value){
                that.search( this.value ).draw();
            }
        });
    });*/
    $(".dataTables_wrapper .dataTables_filter").hide();

    setInterval(function() {
        window.location.reload();
    }, 5000);
    
});
</script>

<style type="text/css">

    table.dataTable tbody td {
        padding: 5px 18px;
    }
</style>

