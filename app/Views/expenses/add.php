<style type="text/css">
    
    .table td, th {
        padding: 3px 8px !important;
    }
</style>
<div class="form">
    <form action="" class="cmxform form-horizontal form-example" method="post">

        <div class="row">
            <div class="col-md-5">

                <div class="row">
                    <div class="col-md-6">
                        <label>Expense Date</label>
                        <input name="expense_date" class="form-control" value="<?php echo date('Y-m-d');?>">
                    </div>
                    <div class="col-md-6">
                        <label>Supplier</label>
                        <select name="supplier_id" class="form-control" required>
                            <option value="">Please Select</option>
                            <?php if(count($suppliers) > 0): ?>
                                <?php foreach($suppliers as $supplier): ?>
                                    <option value="<?php echo $supplier->id; ?>">
                                        <?php echo $supplier->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?> 
                        </select>
                    </div>
                </div>
                <div class="row" style="margin-top: 12px;">
                    <div class="col-md-12">
                        <label>Remark</label>
                        <input class="form-control" name="remark">
                    </div>
                </div>
                <div class="row" style="margin-top: 12px;">
                    <div class="col-md-12">
                        <label>Filter by Product Name</label>
                        <input class="form-control" onkeyup="getproduct(this.value)">
                    </div>
                </div>
                <table class="table table-bordered table-sm table-responsive" style="margin-top: 5px;">
                    <thead class="text-primary">
                        <th>Product</th>
                        <th>Price</th>
                        <th style="width:1%;"><a href="javascript:;"><i class="fa fa-plus-circle fa-lg"></i></a></th>
                    </thead>
                    <tbody id="resdata">
                         
                    </tbody>
                </table>

            </div>
            <div class="col-md-7">

                <table class="table table-bordered table-sm my-table" style="margin-top: 20px;">
                    <thead class="panel-footer">
                        <th><b>Product</b></th>
                        <th style="width: 90px;"><b>Price</b></th>
                        <th style="width: 90px;"><b>Quantity</b></th>
                        <th style="width: 90px;"><b>Subtotal</b></th>
                        <th style="width: 10px;"><b><a href="javascript:;" class="text-danger"><i class="fa fa-trash fa-lg"></i></a></b></th>
                    </thead>
                    <tbody id="addproducts">
                        
                    </tbody>
                    <tfoot class="panel-footer">
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="font-weight: bold;">Total</td>
                            <td style="font-weight: bold;" id="gtotal"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <div class="pos-checkout">
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="javascript: void(0);" onclick="clearAll()" class="btn btn-danger pull-left">
                                Clear all
                            </a>
                            
                            <button name="submit" type="submit" class="btn btn-primary pull-right">
                                Create Expense
                            </button>
                        </div>  
                    </div>  
                </div>

            </div>
        </div>
    </form>
</div>
<script type="text/javascript">

    $(document).ready(function(){
        $("input[name='expense_date']").datepicker({
            dateFormat: "yy-mm-dd",
            //changeMonth: true,
            //changeYear: true,
            //yearRange: "1950:c",
        });
    });
    
    function additem(key){

        var product_id = key;
        var product_name = $('#pname'+key).text();
        var product_price = $('#pprice'+key).text() || 0;
        var product_qty = '1.00';
        var product_subtots = parseFloat(product_price) * parseFloat(product_qty);
        var product_subtot = product_subtots.toFixed(2);

        /*alert(product_id);
        alert(product_name);
        alert(product_price);
        alert(product_qty);
        alert(product_subtot);*/

        var className = 'rem'+product_id+'';
        if (!$("tr").hasClass(className)) {
            
            var tr = '<tr class="remall rem'+product_id+'">';
                tr +='<td><input name="product_id[]" value="'+product_id+'" type="hidden"> '+product_name+'</td>';
                tr +='<td><input name="price[]" id="p'+product_id+'" onkeyup="recalc('+product_id+')" type="text" value="'+product_price+'" class="form-control" required></td>';
                tr +='<td><input name="qty[]" id="q'+product_id+'" onkeyup="recalc('+product_id+')" type="text" value="'+product_qty+'" class="form-control" required></td>';
                tr +='<td><input name="sub_amount[]" id="subt'+product_id+'" type="text" value="'+product_subtot+'" class="form-control subtot" required></td>';
                tr +='<td class="text-primary plus-parent">';
                    tr +='<a href="javascript: void(0);" class="text-danger" onclick="removeitem('+product_id+')">';
                    tr +='<i class="fa fa-trash fa-lg"></i></a>';
                tr +='</td>';
            tr +='</tr>';

            $('#addproducts').append(tr);
            recalctotal();

        }else{

            var id = product_id;
            var q = $('#q'+id).val() || 0;
            var nqs = parseFloat(q) + parseInt(1);
            var nq = nqs.toFixed(2);
            $('#q'+id).val(nq);

            var p = $('#p'+id).val() || 0;
            var product_subtots = parseFloat(p) * parseFloat(nq);
            var product_subtot = product_subtots.toFixed(2);
            $('#subt'+id).val(product_subtot);
            recalctotal();
        }
    }

    function recalc(id){
        var p = $('#p'+id).val() || 0;
        var q = $('#q'+id).val() || 0;
        var product_subtots = parseFloat(p) * parseFloat(q);
        var product_subtot = product_subtots.toFixed(2);
        $('#subt'+id).val(product_subtot);
        recalctotal();
    }

    function clearAll(){
        $('.remall').remove();
        recalctotal();
    }

    function removeitem(id){
        $('.rem'+id).remove();
        recalctotal();
    }

    function recalctotal(){
        let total = 0;
        $('.my-table .subtot').each(function () {
            total += parseFloat($(this).val()) || 0;
        });
        $('#gtotal').text(total.toFixed(2));
    }

    getproduct('');
    function getproduct(pro){

        $.ajax({
            type : 'POST',
            url: '<?php echo get_site_url('expenses/get_expense_peoducts'); ?>',
            data: {keyword: pro},
            success: function(result){

                $('#resdata tr').remove();
                $('#resdata').append(result);
            }
        })
    }
</script>