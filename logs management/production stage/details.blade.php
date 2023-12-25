@extends('layout/app')

@section("page-css")
            <link href="{{asset("vendors/toastr/toatr.css")}}" rel="stylesheet" />
@endsection
@section("page-js")
        <script src="{{asset("vendors/toastr/toastr.js")}}"></script>
        <script src="{{asset("vendors/toastr/toastr.min.js")}}"></script>
@endsection

@section('content')

<style>
  p{
    color: black;
  }
</style>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Order Details</h3>

            </div>
 
        </div>
        <div class="clearfix"></div>

<div class="row">


<!-- form input mask -->
<div class="col-md-12 col-sm-12  ">
  <div class="x_panel">
    <div class="x_title">

      <h2 style="font-weight:bold;">Customer Details</h2>
      <div class="clearfix"></div>
    </div>
    <div class="x_content">
      <br />
         <?php
          $name=$orderHead->fname." ".$orderHead->lname;
          $address=$orderHead->address;
          $address1=$orderHead->address2;
          $phone=$orderHead->phone;
          $city=$orderHead->city;
          $town=$orderHead->town;
          $county=$orderHead->county;
          $account_no=$orderHead->account_no;
          $postcode=$orderHead->postcode;
          $country=$orderHead->country;
          $cc=$orderHead->last_four;
              $total=0;
         ?>
      
              <table id="customer_details" style='visibility:collapse'>
            <thead>
            <td>Customer name</td>
            <td>Address</td>
            <td>Other address</td>
            <td>Country</td>
            <td>City</td>
            <td>Town</td>
            <td>Post code</td>
            <td>Phone</td>
            <td>Account number</td>
            <td>last 4 digits of CC</td>
            </thead>
            <tbody>
               
                <td >{{$name}}</td>
                <td >{{$address}}</td>
                <td >{{$address1}}</td>
                <td >{{$country}}</td>
                <td >{{$city}}</td>
                <td >{{$town}}</td>
                <td >{{$postcode}}</td>
                <td >{{$phone}}</td>
                <td >{{$account_no}}</td>
                <td>{{$cc}} </td>
            </tbody>
        </table>    
            
      
      
      
      
      
      
      
      <form method="post" id="submit_for_back" action="includes/ajax.php" class="form-horizontal form-label-left">

          <div class="form-group row">
              <label class="control-label col-md-2 col-sm-3 col-xs-3 ac">Customer Name</label>
              <div class="col-md-2 col-sm-3 col-xs-3">
                <p> {{$name}}</p>
                {{-- <textarea required type="text" class="form-control" name="batch_name"></textarea>
                 --}}
              </div>
              <label class="control-label col-md-2 col-sm-3 col-xs-3 ac">Account No:</label>
              <div class="col-md-2 col-sm-3 col-xs-3">
                <p> {{$account_no}}</p>
                {{-- <textarea required type="text" class="form-control" name="account_no"></textarea>
                 --}}
              </div>
              
              <label class="control-label col-md-2 col-sm-3 col-xs-3 ac">Last 4 digits of CC:</label>
              <div class="col-md-2 col-sm-3 col-xs-3">
                <p> {{$cc}}</p>
                {{-- <textarea required type="text" class="form-control" name="account_no"></textarea>
                 --}}
              </div>
              
            </div>

        <div class="form-group row">
          <label class="control-label col-md-3 col-sm-3 col-xs-3 ac">Address</label>
          <div class="col-md-3 col-sm-9 col-xs-9">
            <p>{{$address}}</p>
            {{-- <textarea required type="text" class="form-control" name="batch_name">{{$address}}</textarea>
             --}}
          </div>
          <label class="control-label col-md-4 col-sm-3 col-xs-3 ac" style="">Other Address</label>
          <div class="col-md-2 col-sm-9 col-xs-9">
          <p>{{$address1}}</p>
            {{-- <textarea required type="text" class="form-control" name="batch_name">{{$address}}</textarea>
             --}}
          </div>
        </div>

        <div class="form-group row">
          <label class="control-label col-md-2 col-sm-3 col-xs-3 ac">Country</label>
          <div class="col-md-2 col-sm-9 col-xs-9">
            <p>{{$country}} </p>

          </div>
          <label class="control-label col-md-2 col-sm-3 col-xs-3 ac" style="">County</label>
          <div class="col-md-2 col-sm-9 col-xs-9">
           <p>{{$county}}</p>

          </div>
          <label class="control-label col-md-2 col-sm-3 col-xs-3 ac" style="">City</label>
          <div class="col-md-2 col-sm-9 col-xs-9">
           <p>{{$county}}</p>

          </div>
        </div>

        <div class="form-group row">
          <label class="control-label col-md-2 col-sm-3 col-xs-3 ac">Town</label>
          <div class="col-md-2 col-sm-9 col-xs-9">

           <p>{{$town}}</p>

          </div>
          <label class="control-label col-md-2 col-sm-3 col-xs-3 ac" style="">Post Code</label>
          <div class="col-md-2 col-sm-9 col-xs-9">
           <p>{{$postcode}}</p>

          </div>
          <label class="control-label col-md-2 col-sm-3 col-xs-3 ac" style="">Phone</label>
          <div class="col-md-2 col-sm-9 col-xs-9">
           <p>{{$phone}}</p>

          </div>
        </div>





      </form>
    </div>
  </div>
</div>
<!-- /form input mask -->



  <!-- form color picker -->
  <div class="col-md-12 col-sm-12 ">
    <div class="x_panel">
      <div class="x_title">


      <?php
if(isset($_GET["str1"]))
{
?>

<div class="alert alert-success" role="alert">
Course Updated successfully.
</div>
<?php
}
?>

<?php
if(isset($_GET["str2"]))
{
?>

<div class="alert alert-danger" role="alert">
Course Deleted successfully.
</div>
<?php
}
?>


        <h2 style="font-weight:bold;"> <div>

        </div>Order Detail<small></small>

    </h2>
    <button  style="float:right" class="btn btn-primary" id="execute" onclick="executeCode()">CSV</button>


        <div class="clearfix"></div>
      </div>
      <div class="x_content">


          <div class="row">
              <div class="col-sm-12">
                <div class="card-box table-responsive">

   <table id="tab" class="table table-striped table-bordered orderDetailsTable" style="width:100%">
          <thead>

            <tr>
                
              <th >Item Name</th>
              <th >Size</th>
              <th >Color</th>
              <th>Quantity</th>
              <th>Rate</th>
              <th>Amount</th>
              <th></th>
             </tr>
          </thead>


          <tbody>
            			@foreach($orderDetails as $orderDetailsRow)

                        <?php
                            $total+=number_format(floor((floatval($orderDetailsRow->retail_price))*100)/100,2, '.', '');
                            $back=$orderDetailsRow->orderid.",".$orderDetailsRow->random;
                        ?>
            <tr>
               
              <td>{{$orderDetailsRow->itemname}}
              
              &nbsp;&nbsp;&nbsp;&nbsp;
              <?php
              if($orderDetailsRow->order_type=="Custom")
              {
      
                  $custt="custom".$orderDetailsRow->itemcode.$orderDetailsRow->itemname.$orderDetailsRow->p_d_id.$orderDetailsRow->p_d_id.$orderDetailsRow->random;
              ?>
              <input type="hidden" value="{{$orderDetailsRow->id}}">
              <button type="button" class="btn btn-primary detailcustombtn">Details</button>
              <?php
              //&nbsp;&nbsp;<a style="color:white;" class="btn btn-secondary csvdownloadbtn" >CSV</a>
                  
              }
              
              ?>
              </td>
              <td>
                  <input type="hidden" value="{{$orderDetailsRow->id}}"> 
                  {{".$orderDetailsRow->size_id."}}</td>
              <td>{{$orderDetailsRow->color_id}}</td>
              <td>{{floatval( $orderDetailsRow->quantity)-floatval( $orderDetailsRow->back_qty)}}</td>
              
              <td>${{sprintf('%0.2f',($orderDetailsRow->rate+$orderDetailsRow->custom_total))}}</td>
              
              <td>${{sprintf('%0.2f',$orderDetailsRow->retail_price)}}</td>
                <td>
                     <input type="hidden" value="{{$back}}">
                     @if($orderDetailsRow->split_status==0)
                     <a class="btn btn-primary setquantityback noExl" data-orderid="{{$orderDetailsRow->id}}" data-qty="{{$orderDetailsRow->quantity}}" type="button" href="#">Add quantity</a>
                     &nbsp;&nbsp;&nbsp;
                     <a class="btn btn-danger noExl" onclick="return confirm('Are you sure?');" type="button" href="/set-to-back/{{$back}}/{{$orderDetailsRow->quantity}}">Remove</a>
                     @else
                     <button class="btn btn-info" disabled type="button">Picked</button>
                     @endif
            </td>

            </tr>
            <?php
                       // }
            ?>
            @endforeach
            <?php
              $es=$total*0.1;
              ?>
                <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="font-weight: bold;">Sub Total</td>
                <td style="font-weight:bold;">${{sprintf('%0.2f',$total)}}</td>

              </tr>
                <tr>
                <td></td>    
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="font-weight: bold;">Estimated Shipping</td>
                             <td style="font-weight:bold;">${{sprintf('%0.2f',$es)}}</td>

              </tr>
               
              
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="font-weight: bold;">Total</td>
      <td style="font-weight:bold;">${{sprintf('%0.2f',($total+$es))}}</td>
          
              </tr>
              
            
              
              


          </tbody>

        </table>

<?php
// only processing and pending order dispatcher can be change
if(in_array($orderHead->status,["pending", "confirmed"]) && auth()->user()->role_id==1)
{
?>

        <form style="position: static; overflow-y:auto;" id="formsubmit" action="/confirm-order" method="POST">
            {{ csrf_field() }}

            <input form="formsubmit" type="hidden" name="orderid" value="{{$orderHead->id}}">
        </form>
        <form action="/assign-dispatcher-to-order/{{$orderHead->id}}" method="POST" class="d-flex">
            {{ csrf_field() }}
          <select required style="width: 250px;" class="form-control select2" name="dispatcher_id" id="">
            <option value="" selected disabled>Select Dispatcher</option>
           <?php
            $dispatcher=DB::table('admin')->where("role_id",3)->get();
            
            //DB::select(" SELECT * FROM `dispatcher` WHERE `status`='working' ");
             foreach($dispatcher as $dd)
            {
                 if($dd->id==$orderHead->dispatcher_id)
                {
           ?>
              <option value="{{$dd->id}}" selected>{{$dd->name}} </option>
        <?php
                }
                else
                {
                    ?>
            <option value="{{$dd->id}}">{{$dd->name}} </option>
        <?php
        }
            }
            ?>
          </select>
          <button type="submit" class="btn btn-success ml-2" >Assign</button>
          </form>

                    <br>
                    <br>
                    <br>
<?php
// cancel button for only pending order
if($orderHead->status=="pending") {

?>

                <a onclick="return confirm('Are you sure?');" style="float: left;" class="btn btn-danger" href="/cancell-order/{{$orderHead->id}}">Cancel</a>
<?php
}
?>
                    <button form="formsubmit" type="submit" style="float: right;" class="btn btn-success d-none" >Confirm</button>
<?php
}
// order status can be change for processing and canceled ordered
if(in_array($orderHead->status,["cancelled", "confirmed"])) {

?>


<form style="position: static; overflow-y:auto;" id="statusupdateform" action="{{route("update.status.order")}}" method="POST">
    {{ csrf_field() }}

    <input form="statusupdateform" type="hidden" name="orderid" value="{{$orderHead->id}}">
</form>
  <select form="statusupdateform" required style="width: 250px;" class="form-control select2" name="order_status">
    <option value="" selected disabled>Select status</option>
   <?php

    //$dispatcher=\App\Models\Dispatcher::where("status","working")->get();
    $status = array("cancelled"=>"Cancel", "delivered"=>"Shipped", "confirmed"=>"Processing");
    //DB::select(" SELECT * FROM `dispatcher` WHERE `status`='working' ");
     foreach($status as $key=>$statusRow)
    {
        if($orderHead->status==$key)
        {
   ?>
    <option value="{{$key}}" selected>{{$statusRow}} </option>
    <?php
}
else
{
?>
<option value="{{$key}}">{{$statusRow}} </option>
<?php
}
}
    ?>
  </select>

            <br>
            <br>
            <br>

        {{-- <a onclick="return confirm('Are you sure?');" style="float: left;" class="btn btn-danger" href="/cancell-order/{{$orderHead->id}}">Cancel</a> --}}

            <button form="statusupdateform" type="submit" style="float: right;" class="btn btn-success" >Update</button>


<?php
}
?>







      </div>
      </div>
  </div>
</div>
    </div>
  </div>
    </div>
  </div>
  <!-- /form color picker -->



</div>
</div>
</div>
<!-- /page content -->

@endsection

@section("page-script")

@if (Session::has('error'))
        @if(Session::get('error') == 'pending_orders')
            <script>
                toastr.error("Some orders still not picked.", "Error", {
                            showDuration: 1000,
                            rtl: "rtl" === $("html").attr("data-textdirection"),
                        });
            </script>
        @endif
@endif

<script>
    $(document).ready(function(){
$(".orderDetailsTable").on('click','.setquantityback',function(){
     var currentRow=$(this).closest("tr");
     var qty=$(this).data('qty')
     var id = $(this).data('orderid')
     console.log(qty, id);
     $('#setquantitybackmodal').modal('show');
     $('#actual_qty').val(qty);
     $('#orderid').val(id);
});
});

        $(document).ready(function(){
        $(".orderDetailsTable").on('click','.detailcustombtn',function(){
        // get the current row
        var keyupdate="11";
        var currentRow=$(this).closest("tr");
        var col1=currentRow.find("td:eq(0) input[type='hidden']"). val();
        var orderId=currentRow.find("td:eq(1) input[type='hidden']"). val();
        var size_table=currentRow.find("td:eq(1) "). text();
        var colour_table=currentRow.find("td:eq(2) "). text();
        var qty_table=currentRow.find("td:eq(3) "). text();
        var rate_table=currentRow.find("td:eq(4) "). text();
        var amount_table=currentRow.find("td:eq(5) "). text();
     
 //alert(col1+"\n"+col2);
 
 //alert("Yes");
 //$('#customorderdetail').modal('show');
   document.getElementById("loader").style.display="flex";
 $.ajax({
     url:"{{route("get.custom.details.by.id")}}",
   //  dataType:'json',
     type:'POST',
     data:
     {
         _token:"{{csrf_token()}}",
         orderId:orderId
     },
     success:function(response)
     {
         //if(response.)
         $(".itemdetailbody").html(response);
         $('#customorderdetail').modal('show');
   document.getElementById("loader").style.display="none";

    }
});
});
});







let options = {
    "filename": "Order Details.csv"
};
	let action;

function executeCode() 
{
    executeCode3();
        const retVal = $('#tab').table2csv(action, options);
        if (action === "return") {
            window.alert(retVal);
        }
    }
    
    let options3 = {
        "filename": "Customer Details.csv"
    };
	let action3;

function executeCode3() {
        const retVal = $('#customer_details').table2csv(action3, options3);
        if (action3 === "return") {
            window.alert(retVal);
        }
    }
    
    
    let options1 = {
        "filename": "Custom Order Details.csv"
    };
	let action1;

function executeCode1() {
        const retVal = $('#tab1').table2csv(action1, options1);
        if (action1 === "return") {
            window.alert(retVal);
        }
    }

let orderByDate = {
    "filename": "Order Details.csv"
};
	let action123;

function convertToCsv() 
{
        const retVal = $('#order-details').table2csv(action123, orderByDate);
        if (action123 === "return") {
            window.alert(retVal);
        }
    }
    function convertToExcel()
    {
        $("#order-details").table2excel({
  exclude: ".noExl",
  name: "Order details",
  filename: "Order details" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls",
  fileext: ".xls",
  preserveColors: true
}); 
    }




</script>

@endsection


@section("page-modal")

<!-- Modal -->
<div class="modal fade" id="setquantitybackmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Update back qty</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;
          </button>
        </div>
        <div class="modal-body">
          <form action="{{route('order.add.qty')}}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
          <input type="hidden" name="orderid" id="orderid">
         <div class="form-group row">
            <label class="control-label col-md-3 col-sm-3 col-xs-3 ac">Qty</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
          <input type="text" name="actual_qty" id="actual_qty" disabled>

            </div>
          </div>
           <div class="form-group row">
            <label class="control-label col-md-3 col-sm-3 col-xs-3 ac">Add qty</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
          <input type="text" name="qty"  required>

            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Update</button>
        </div>
        </form>
      </div>
    </div>
  </div>
  
  
  
  
<!-- Modal -->
<div class="modal fade" id="customorderdetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Custom Order Details</h5>
           
           <button  style="float:right" class="btn btn-primary" id="execute1" onclick="executeCode1()">CSV</button>
        </div>
        <div class="modal-body itemdetailbody">
            
        
        

        
        
        </div>
    
    
      </div>
    </div>
  </div>


@endsection

