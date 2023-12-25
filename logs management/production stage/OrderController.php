<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\CustomOrders;
use App\Models\OrderHead;
class OrderController extends Controller
{

    public function orderDetails($id)
    {

       $e=Order::select([
        "orders.itemname",
        "item.img_dir",
        "orders.rate",
       // "orders.r",
        "orders.quantity",
        "orders.color_id as color_name",
        "orders.size_id as size_name",
        "orders.id"
       ])
       ->leftJoin("item","item.id","=","orders.itemcode")
       ->where("orders.orderid",$id)
       ->get();
       //DB::select(" SELECT * FROM `orders` WHERE `orderid`='".$id."' ");
        return view("pages/front/orderDetails")->with("order",$e);


    }

    public function orderSave(Request $request)
    {




    $fname=$request->fname;

    $lname=$request->lname;

    $phone=$request->phone;

    $email=$request->email;

    $postcode=$request->postcode;

    $city=$request->city;

    $address=$request->address;

    $address1=$request->address1;

    $country=$request->country;

    $county=$request->county;

    $note=$request->note;

    $town=$request->city;

    $postcode=$request->postcode;

    $city=$request->city;

    $account_no=$request->account_no;

 $id=$_COOKIE['user'];
 $customer = DB::table('customer')->where('id', $id)->first();

    $orderHead=OrderHead::where("customer_id",$id)

    ->where("isOrder",0)

    ->first();
   // dd($orderHead->fname);
    $orderHead->fname=$fname;

    $orderHead->lname=$lname;

    $orderHead->phone=$phone;

    $orderHead->orderdate=date("Y-m-d H:i:s");

    $orderHead->postcode=$postcode;

    $orderHead->city=$city;

    $orderHead->address=$address;

    $orderHead->address2=$address1;

    $orderHead->country=$country;

    $orderHead->county=$county;

    $orderHead->note=$note;

    $orderHead->town=$town;

    $orderHead->account_no=$account_no;

    $orderHead->amount=$request->total_amount;

    $couponSessionDiscount=session()->get('discount');
    if(empty($couponSessionDiscount))
    {
        $couponDiscount=0;
    }
    else
    {
        $couponDiscount=$couponSessionDiscount;
    }


    $orderHead->discount=$couponDiscount;

    $orderHead->isOrder=1;

    $orderHead->last_four=$request->last_four;
    $orderHead->dispatcher_id=$customer->dispatcher_id;

    $orderHead->save();



$to = "Orders@nmaalliance.com";

$subject = "Order placed.";

$message=" An order is placed . Please check your admin panel. ";



$headers = 'From: <orders@shimsanmerchandise.com/>' . "\r\n";



mail($to,$subject,$message,$headers);



    return redirect()->route("front.success");

}
}
