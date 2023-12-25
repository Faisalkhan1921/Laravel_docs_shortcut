<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderHead;
use App\Models\StockHistory;
use Auth;
use App\Models\Role;
class OrderController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth');
    }
    // @auther faisal 
     public function orderByDate()
    {

        $orders_by_date = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
          ['orders_by_date',1]
           ])
          ->first();
        if ($orders_by_date ) {
            $role=Role::where("isDelete",0)->get();
            $customer=Customer::get();
            return view("pages.orders.orders_by_date",compact("customer","role"));
            // return view("pages.user_management.index",compact("role"));

        } else {
            return abort(403, 'Unauthorized action.'); // or redirect to some other page
        }


    }
       public function pendingOrderView()
    {
        $role=Role::where("isDelete",0)->get();
        $pending_orders = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
          ['pending_orders',1]
           ])
          ->first();
        if ($pending_orders) {
        return view("pages.orders.pending",compact('role'));

        } else {
            return abort(403, 'Unauthorized action.'); // or redirect to some other page
        }
    }
  public function shipOrderView()
    {
         $shipped_orders = DB::table('admin_accesses')
                    
         ->where([
           ['admin_id',Auth::user()->id] ,
           ['shipped_orders',1]
            ])
           ->first();
        if ($shipped_orders ) {
            $role=Role::where("isDelete",0)->get();
             return view("pages.orders.ship",compact("role"));

            // return view("pages.user_management.index",compact("role"));

        } else {
            return abort(403, 'Unauthorized action.'); // or redirect to some other page
        }

    }
    public function processingOrderView()
    {
         $processing_orders = DB::table('admin_accesses')
                    
         ->where([
           ['admin_id',Auth::user()->id] ,
           ['processing_orders',1]
            ])
           ->first();
        if ($processing_orders) {
            $role=Role::where("isDelete",0)->get();
            // return view("pages.user_management.index",compact("role"));
             return view("pages.orders.processing",compact("role"));

        } else {
            return abort(403, 'Unauthorized action.'); // or redirect to some other page
        }

    }
  public function cancelOrderView()
    {
         $canceled_orders = DB::table('admin_accesses')
                    
         ->where([
           ['admin_id',Auth::user()->id] ,
           ['canceled_orders',1]
            ])
           ->first();
        //  $canceled_orders = auth()->guard('web')->user()->canceled_orders;

        if ($canceled_orders ) {
            $role=Role::where("isDelete",0)->get();
             return view("pages.orders.cancel",compact("role"));

            // return view("pages.user_management.index",compact("role"));

        } else {
            return abort(403, 'Unauthorized action.'); // or redirect to some other page
        }

    }
    public function backOrderView()
    {
         return view("pages.orders.back");
    }

    public function orderByDateDetails(Request $request)
    {
       // return $request;
        $start=$request->start_date;
        $end=$request->end_date;
        $customerId=$request->user_id;
        $status=$request->status;
        $startDate1=date("Y-m-d", strtotime($start));
        $endDate1=date("Y-m-d", strtotime($end));
        $startDate=$startDate1." 00:00:00";
        $endDate=$endDate1." 23:59:59";
        if($customerId!="")
        {
        $orderHead=OrderHead::where("status",$status)
        ->where("customer_id",$customerId)
        ->whereBetween('orderdate', [$startDate, $endDate])
        ->get();
    }
    else
    {
          $orderHead=OrderHead::where("status",$status)
      //  ->where("customer_id",$customerId)
        ->whereBetween('orderdate', [$startDate, $endDate])
        ->get();
    }
        if($orderHead->count()==0)
        {
            return array(0);
        }
         $customerTable="";
             $ordersTable="<table id='order-details'>";

        foreach($orderHead as $orderHeadRow){
            // $ordersTable.="
            // <th>Order id</td>
            // <th>Customer name</td>
            // <th>Address</td>
            // <th>Post code</td>
            // <th>Phone</td>
            // <th>Account number</td>
            // <th>last 4 digits of CC</td>
            // <tr>
            // <td>$orderHeadRow->orderid</td>
            // <td>$orderHeadRow->fname $orderHeadRow->lname</td>
            // <td>$orderHeadRow->address</td>
            // <td>$orderHeadRow->postcode</td>
            // <td>$orderHeadRow->phone</td>
            // <td>$orderHeadRow->account_no</td>
            // <td>$orderHeadRow->last_four</td>
            // </tr>
            // ";

            $ordersTable.="
            <tr>
                <th style='color:black;'><b>Order id</b></th>
                <th style='color:black;'><b>Customer Name</b></th>
              <th style='color:black;'><b>Item Name</b></th>
              <th style='color:black;'><b>Size</b></th>
              <th style='color:black;'><b>Color</b></th>
              <th style='color:black;'><b>Quantity</b></th>
              <th style='color:black;'><b>Rate</b></th>
              <th style='color:black;'><b>Amount</b></th>
             </tr>
            ";

            $orders=Order::where("orderid",$orderHeadRow->id)
            ->get();
            $total=0;
            foreach($orders as $ordersRow)
            {
                //$amount=($ordersRow->quantity*$ordersRow->rate);
                $quantity=floatval($ordersRow->quantity)-floatval($ordersRow->back_qty);
                $rate=number_format(floor(floatval($ordersRow->rate)*100)/100,2, '.', '');
                $amount=number_format(floor((floatval( $ordersRow->rate)*floatval( ($ordersRow->quantity-$ordersRow->back_qty)))*100)/100,2, '.', '');
                $total+=$amount;
                    $ordersTable.="

                    <tr>
                    <td style='color:black;'>$orderHeadRow->id</td>
                    <td style='color:black;'>$orderHeadRow->fname $orderHeadRow->lname</td>
            <td style='color:black;'>$ordersRow->itemname</td>
            <td style='color:black;'>.$ordersRow->size_id.</td>
            <td style='color:black;'>$ordersRow->color_id</td>
            <td style='color:black;'>$quantity</td>
            <td style='color:black;'>$$rate</td>
            <td style='color:black;'>$$amount</td>
            </tr>";
            }
            $es=$total*0.1;
            $estimatedShipping=number_format(floor(floatval($es)*100)/100,2, '.', '');
            $subTotal=(number_format(floor(floatval($total)*100)/100,2, '.', ''));
            $grandTotal=(number_format(floor(floatval( ($total+$es))*100)/100,2, '.', ''));
              $ordersTable.="<tr>
             <td></td>
              <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style='font-weight: bold;color:black;'><b>Sub total</b></td>
            <td style='font-weight: bold;color:black;'>$$subTotal</td>
            </tr>
            <tr>
             <td></td>
              <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style='font-weight: bold;color:black;'><b>Estimated shipping</b></td>
            <td style='font-weight: bold;color:black;'>$$estimatedShipping</td>
            </tr>
            <tr>
             <td></td>
              <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style='font-weight: bold;color:black;'><b>Total</b></td>
            <td style='font-weight: bold;color:black;'>$$grandTotal</td>
            </tr>
            ";

        }
        $ordersTable.="</table>";
        return array($ordersTable);
    }
       public function orderData($status)
    {
        //$status=$request->status;
        
        $id1 = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
          ['role_id',1]
           ])
          ->get();
        $id2 = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
          ['role_id',2]
           ])
          ->get();
        $id3 = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
          ['role_id',5]
           ])
          ->get();
        $id4 = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
          ['role_id',6]
           ])
          ->get();
           if($id1)
     {
          $order=OrderHead::select([
              DB::raw("CONCAT(order_head.fname,' ',order_head.lname) as name"),
              "order_head.id",
              DB::raw(" COALESCE(amount,0) as amount "),
              "orderdate",
              "isPaid",
              "isComplete",
              "admin.name as dispatcher_name"
              ])
               ->where("order_head.isOrder",1)
               ->leftJoin("customer","customer.id","=","order_head.customer_id")
               ->leftJoin("admin","admin.id","=","customer.dispatcher_id")
              ->where("order_head.status",$status)
              ->get();
    }
    else if($id2)
    {
         $order=OrderHead::select([
             DB::raw("CONCAT(order_head.fname,' ',order_head.lname) as name"),
             "order_head.id",
             DB::raw(" COALESCE(amount,0) as amount "),
             "orderdate",
             "isPaid",
             "isComplete",
             "admin.name as dispatcher_name"
             ])
              ->where("order_head.isOrder",1)
              ->leftJoin("customer","customer.id","=","order_head.customer_id")
              ->leftJoin("admin","admin.id","=","customer.dispatcher_id")
             ->where("order_head.status",$status)
             ->get();
   }
   else if($id3)
   {
        $order=OrderHead::select([
            DB::raw("CONCAT(order_head.fname,' ',order_head.lname) as name"),
            "order_head.id",
            DB::raw(" COALESCE(amount,0) as amount "),
            "orderdate",
            "isPaid",
            "isComplete",
            "admin.name as dispatcher_name"
            ])
             ->where("order_head.isOrder",1)
             ->leftJoin("customer","customer.id","=","order_head.customer_id")
             ->leftJoin("admin","admin.id","=","customer.dispatcher_id")
            ->where("order_head.status",$status)
            ->get();
  }
  else if($id4)
  {
       $order=OrderHead::select([
           DB::raw("CONCAT(order_head.fname,' ',order_head.lname) as name"),
           "order_head.id",
           DB::raw(" COALESCE(amount,0) as amount "),
           "orderdate",
           "isPaid",
           "isComplete",
           "admin.name as dispatcher_name"
           ])
            ->where("order_head.isOrder",1)
            ->leftJoin("customer","customer.id","=","order_head.customer_id")
            ->leftJoin("admin","admin.id","=","customer.dispatcher_id")
           ->where("order_head.status",$status)
           ->get();
 }
    else
    {
       $order=OrderHead::select([
              DB::raw("CONCAT(order_head.fname,' ',order_head.lname) as name"),
              "order_head.id",
              DB::raw(" COALESCE(order_head.amount,0) as amount "),
              "order_head.orderdate",
              "order_head.isPaid",
              "order_head.isComplete",
              ])
              ->leftJoin("customer","customer.id","=","order_head.customer_id")
              ->where("order_head.status",$status)
              ->where("customer.dispatcher_id",Auth::user()->id)
               ->where("order_head.isOrder",1)
              ->get();
    }
         $orderData = array();
          foreach($order as $key=>$orderRecord)
          {
              $action="";
              $detail=route('order.detail',$orderRecord->id);
              $delete=route("order.delete",$orderRecord->id);
                 $pay=route("order.pay",$orderRecord->id);
                 $pick=route("order.pick",$orderRecord->id);
        $orderRows = array();
     $orderRows[] =$orderRecord->id;
       $orderRows[] =$orderRecord->name;
        //$orderRows[] =number_format(floor(floatval($orderRecord->amount)*100)/100,2, '.', '');
        $orderRows[] =$orderRecord->amount;
        $orderRows[] =$orderRecord->orderdate;
              if(Auth::user()->role_id==1)
     {
        $orderRows[] =$orderRecord->dispatcher_name;
          }
          else
          {
                $orderRows[] =Auth::user()->name;
          }
                   if($orderRecord->isPaid)
           {
           $orderRows[] ="Paid";
          }
          else
          {
           $orderRows[] ="Not paid";
          }
        $action.= "
           <a href=$detail >
                  <i class='fa fa-edit editorderbtn'></i></a>
                  &nbsp;&nbsp;&nbsp;
                  <a onclick='return orderDelete();' href=$delete >
                      <i class='fa fa-times'></i>
                </a>
                      &nbsp;&nbsp;&nbsp;";

           if(!$orderRecord->isPaid)
           {          $action.= "
                  <a class='btn btn-primary' href=$pay >
                    Pay
                </a>
       ";
          }
          if(!$orderRecord->isComplete)
          {          $action.= "
                 <a class='btn btn-primary' href=$pick >
                   Pick
               </a>
      ";
         }
        $orderRows[] =$action;
        $orderData[] = $orderRows;
          }

        $json["data"]=$orderData;
        return json_encode($json);

    }


    public function orderDetail($id)
    {
        $orderHead=OrderHead::select([
            "customer.dispatcher_id as customer_dispatcher_id",
            "order_head.*"
            ])
            ->leftJoin("customer","customer.id","=","order_head.customer_id")
            ->where("order_head.id",$id)
            ->first();

        $orderDetails=Order::where("orders.orderid",$id)->get();
        
        return view("pages/orders/details",compact("orderDetails"))->with("orderHead",$orderHead);


    }
    public function addOrderQty(Request $req)
    {
        $orderDetail = Order::findorfail($req->orderid);
        
        $order = OrderHead::findorfail($orderDetail->orderid);
        $newAmountToAdd = $orderDetail->rate*$req->qty;
        $shippingToAdd = $newAmountToAdd*0.1;
        $totalAmountToAdd = $shippingToAdd+$newAmountToAdd;
        $order->amount+=$totalAmountToAdd;
        $order->save();
        
        $orderDetail->quantity+=$req->qty;
        $orderDetail->retail_price+=$newAmountToAdd;
        $orderDetail->save();
        
        return redirect()->route('order.detail',[$orderDetail->orderid]);
    }
    public function delete($id)
    {
        $order=OrderHead::where("id",$id)->first();
        $order->status="Removed";
        $order->save();
        $order->delete();
        return redirect()->back();
    }
    public function updateOrderStatus(Request  $request)
    {
        $orderId=$request->orderid;
        $status=$request->order_status;
        
        if($status == 'delivered') {
            $pendingItemsCount = DB::table('orders')->where('orderid', $orderId)->where('split_status', 0)->whereNull('deleted_at')->count();
            if($pendingItemsCount) {
                return redirect()->back()->with('error', "pending_orders");
            }
        }
        $order=OrderHead::where("id",$orderId)->first();
        $order->status=$status;
        $order->save();
        //$order->delete();
        return redirect()->back();
    }

       public function orderPayment($id)
    {
        // $order=OrderHead::where("id",$id)->first();
        // $order->isPaid=1;
        // $order->save();
        OrderHead::where("id",$id)->update([
                "status"=>"confirmed",
                "isPaid" => 1
                ]);
        return redirect()->back()->with("success"," Your payment has been successfull. ");
    }


 function getOrderTracking(Request $request)
{

    //Order::where("dalilee_order_id")
$flag=0;
    $timeline="";
//  if($request->type=="order")
//     {
//     $orderTrack=OrderTrack::select([
//         "order_track.*",
//         "users.name as user_name"
//     ])
//     ->join("users","users.id","=","order_track.updated_by")
//     ->where("order_track.order_id",$request->orderId)
//   ->orderBy('order_track.id', 'desc')
//     ->get();
// }
// else
// {

//     if(strpos($request->dalileeOrderId, 'a') !== false) {
//         $dalileeOrderId = substr($request->dalileeOrderId, 1);
//         }


//         if(strpos(strtolower($request->dalileeOrderId), 'a') !== false) {
//             $dalileeOrderId = substr($request->dalileeOrderId, 1);
//         }
//         $checkDalileeId=Order::where("dalilee_order_id",$dalileeOrderId)
//         ->first();
//         if(empty($checkDalileeId))
//         {
//             return json_encode(array("status"=>0,"data"=>" Dalilee id does not exists. "));
//         }
//     $orderTrack=OrderTrack::select([
//         "order_track.*",
//         "users.name as user_name"
//     ])
//     ->join("users","users.id","=","order_track.updated_by")
//     ->join("orders","orders.id","=","order_track.order_id")
//     ->where("orders.dalilee_order_id",$dalileeOrderId)
//     ->orderBy('order_track.id', 'desc')
//     ->get();


// }


    // foreach($orderTrack as $key=>$orderTrackRow)
    // {
    //     if($status=="Unshelfed")
    //     {
    //         $color="primary";
    //     }
    //     else if($status=="Return")
    //     {
    //         $color="info";
    //     }
    //     else if($status=="Unpacked")
    //     {
    //         $color="danger";
    //     }
    //     else if ($status=="Created")
    //     {
    //         $color="warning";
    //     }
    //     else
    //     {
    //         $color="success";
    //     }
    //     if($key==0){
    //         $active="active";
    //     }
    //     else{
    //         $active="";
    //     }
    //     $timeline.="<div class='tl-item $active'>";
    //     $timeline.="<div class='tl-dot b-$color'></div>";
    //     $timeline.="
    //     <div class='tl-content'>
    //         <div class=''>$status by $orderTrackRow->user_name</div>
    //         <div class='tl-date text-muted mt-1'>".date('d-m-Y H:i:s',strtotime($orderTrackRow->created_at))."</div>
    //     </div>
    // </div>";

    // }
$orderHead=OrderHead::select([
    "status"
    ])
    ->where("id",$request->orderId)
    ->first();
    if(empty($orderHead))
    {
        return json_encode(array("status"=>0,"data"=>"Order id does not exists"));
    }
$status=$orderHead->status;
// if($status=="pending")
//         {
//             $color="primary";
//         }
//         else if($status=="confirmed")
//         {
//             $color="info";
//         }
//         else if($status=="cancelled")
//         {
//             $color="danger";
//         }
//         else if ($status=="Removed")
//         {
//             $color="warning";
//         }
//         else
//         {
//             $color="success";
//         }
//         // if($key==0){
//         //     $active="active";
//         // }
//         // else{
//             $active="active";
//         //}
//         $timeline.="<div class='tl-item $active'>";
//         $timeline.="<div class='tl-dot b-$color'></div>";
//         $timeline.="
//         <div class='tl-content'>
//             <div class=''>$status </div>
//             <div class='tl-date text-muted mt-1'></div>
//         </div>
//     </div>";
    return json_encode(array("status"=>1,"data"=>$status));

}

public function orderPick($id)
     {
         $orderHead = DB::table('order_head')->where('id', $id)->select('id', 'status')->first();
         $nmaaWarehouse = DB::table('warehouse')->where('name', 'like', '%nmaa%')->first(['id']);
            $orders=Order::select([
                "orders.id",
                "orders.itemname",
                "quantity",
                "color_id as color_name",
                "size_id as size_name",
                "retail_price",
                "split_status",
                "itemcode",
                "item.warehouse_id as isNmaawarehouse"
                ])
            ->where("orderid",$id)
            ->leftJoin('item', function ($join) use ($nmaaWarehouse) {
                $join->on('orders.itemcode', 'item.id')
                     ->on('item.warehouse_id', '=', DB::raw($nmaaWarehouse->id));
            })
            ->get();
        return view("pages.orders.pick_list",compact("orders","id","orderHead"));
     }
     
    //  for making two orders from one order
    public function orderPickEdit(Request $request, $id)
    {
        //dd($id, $request->all());
        if($request->quantity == 0){
            return response()->json(['msg'=> 'Can not set 0'], 400);
        }
        $orderHead = DB::table('order_head')->where('id', $id)->select('id', 'status')->first();
        $order=Order::find($request->order_id);
        if($order->split_status=="0"){
            $quantityDiff = $order->quantity - $request->quantity;
            if($quantityDiff<0){
                return response()->json(['msg'=> 'Can not increase quantity'], 400);
            } elseif($quantityDiff==0) {
                return response()->json(['msg'=> 'The quantity is same'], 400);
            } else {
                $amount = $order->rate+$order->custom_total;
                $newRetailPrice = $amount*$request->quantity;
                
                // setting given qty in current order
                $order->quantity = $request->quantity;
                $order->retail_price = $newRetailPrice;
                $order->save();
                
                // creating new order with remaning qty
                $newOrder = new Order;
                $newOrder->orderid = $order->orderid;
                $newOrder->itemname = $order->itemname;
                $newOrder->rate = $order->rate;
                $newOrder->custom_total = $order->custom_total;
                $newOrder->quantity = $quantityDiff;
                $newOrder->color_id = $order->color_id;
                $newOrder->size_id = $order->size_id;
                $newOrder->status = $order->status;
                $newOrder->retail_price = $amount*$quantityDiff;
                $newOrder->order_type = $order->order_type;
                $newOrder->itemcode = $order->itemcode;
                $newOrder->random = $order->random;
                $newOrder->split_status = $order->split_status;
                $newOrder->save();
                
                return response()->json(['msg'=> 'Updated success'], 200);
                
            }
        } else {
            return response()->json(['msg'=> 'Order already picked'], 400);
        }
    }

     public function printPackingList(Request $request)
     {
         //dd($request);
         //->whereIn('id', $order)
        //  previous code
        //   $orders=Order::select([
        //         "id",
        //         "itemname",
        //         "quantity",
        //         "color_id as color_name",
        //         "size_id as size_name",
        //         "retail_price"
        //         ])
        //     //->where("orderid",$id)
        //     ->whereIn("id",$request->orders_id)
        //     //->where("split_status","!=",1)
        //     ->get();
          $orders=Order::whereIn("id",$request->orders_id)->get();

           // $count=0;
           //dd($request->orders_id);
          $count=count($request->orders_id);
         //dd($count);
          $id=$request->id;
          $orderHead = OrderHead::with('customer:id,email')->findorfail($id);
            $splitCount=Order::where("split_status",0)
            ->where("orderid",$id)
            ->count();
            //dd($splitCount);

            if($splitCount==$count)
            {
                $orderHead->isComplete = 1;
                $orderHead->save();
            }

            // old code
            // foreach($request->orders_id as $ordersIds)
            // {
            //  //   $count++;
            //         Order::where("id",$ordersIds)->update([
            //             "split_status"=>1
            //             ]);       
            // }

            foreach($orders as $order) {
                $order->split_status = 1;
                $order->save();
                
                // Find the product detail
                $productDetails = DB::table('product_details')
                    ->where('itemcode', $order->itemcode)
                    ->where('color_name', 'like', $order->color_id)
                    ->where('size_name', 'like', $order->size_id)
                    ->first();
                
                if ($productDetails) {
                    // Calculate the updated quantity
                    $updatedQty = $productDetails->qty - $order->quantity;
                    if($updatedQty<0){
                        $updatedQty = 0;
                    }
                
                    // Update the product detail with the new quantity
                    DB::table('product_details')
                        ->where('itemcode', $order->itemcode)
                        ->where('color_name', 'like', $order->color_id)
                        ->where('size_name', 'like', $order->size_id)
                        ->update(['qty' => $updatedQty]);
                
                    // Create a stock history record
                    $userEmail = $orderHead->customer ?$orderHead->customer->email: 'some user';
                    $stockHistory = new StockHistory;
                    $stockHistory->module = "Update product detail";
                    $stockHistory->description = "Stock out qty = $order->quantity for orderId = $id by $userEmail";
                    $stockHistory->previous_stock = $productDetails->qty;
                    $stockHistory->current_stock = $updatedQty;
                    $stockHistory->product_detail_id = $productDetails->id;
                    $stockHistory->save();
                }
            }


            //dd($orders);


           // dd($id);
    //     $users = DB::table('users')->whereIn('id', array(1, 2, 3))->get()
         return view("pages.orders.print_packing",compact("orders","id"));
     }

}
