<?php



namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DB;
use Auth;

use Session;

use App\Models\Customer;

use App\Models\CustomDetails;

use App\Models\Order;

use App\Models\OrderHead;

use App\Models\Size;

use App\Models\Coupon;

use App\Models\CustomCartDetails;

use App\Models\ProductDetails;

use App\Models\CustomOrders;

use App\Models\CustomPrices;

use App\Models\TempCart;

use App\Models\StockHistory;

use App\Models\Item;

use App\Models\MultiplePictures;

use Illuminate\Support\Facades\Hash;

class AjaxController extends Controller

{
    public function sizeprice(Request $request)
    {
        $item=Item::where("id",$request->itemcode)->first();
           //DB::select(" SELECT * FROM `item` WHERE `itemcode`='".$id."' ");
           
        //   according to client requirement sort in this order
        $type = strtolower($item->type);

        $colorsInOrder = ["White", "Orange", "Yellow", "Camo", "Green", "Purple", "Blue", "Brown", "Red", "Red/Black"];

        $option="<option value='' selected disabled>Select color</option>";
        //$s_i=$request->s_id;
        $size_id=$request->size_id;
        $itemcode=$request->itemcode;
         $productDetailsRows=ProductDetails::where("size_id",$size_id)
         ->where("itemcode",$itemcode)
         ->when($type === 'belt', function ($query) use ($colorsInOrder) {
                // If $type is 'belt', order by color_name in the specified order
                $query->orderByRaw("FIELD(color_name, '" . implode("','", $colorsInOrder) . "')");
            })
         ->get();
    if($productDetailsRows->count()==0)
{
     return response()->json([
                    "errors"=>[
                        "email"=>[" This size does not have any colors registered. "]
                        ]
                ],422);
}
        //$e=DB::select(" SELECT * FROM `product_details` WHERE `size_id`='".$size_id."' AND `itemcode`='".$itemcode."' ");
        foreach($productDetailsRows as $gg)
        {
            //$p_d_id=$gg->p_d_id;
            //$price=number_format(floor(($gg->price)*100)/100,2, '.', '');
            $option.="<option value=$gg->id>$gg->color_name</option>";
        }
        //$arr=array($price,$option);
        return response()->json([
            "message"=>" Colors registered against this size. ",
            "options"=>$option
            ],200);
        //return json_encode($arr);
    }



    public function pricecalculate(Request $request)
    {

        $option="";
        $p_d_id=$request->p_d_id;
        $productDetails=ProductDetails::where("id",$p_d_id)
        ->first();
    //    return $productDetails;
        // $e=DB::select(" SELECT * FROM `product_details` WHERE `id`='".$p_d_id."'  ");
        // foreach($e as $gg)
        // {
            $price=$productDetails->price;
            $qty=$productDetails->qty;
            //$price=number_format(floor(($gg->price)*100)/100,2, '.', '');
       // }
       // return $price;
        $arr=array($price,$qty);
        return json_encode($arr);
    }



    public function pricecalculatecustom(Request $request)
    {

        $option="";
        $p_d_id=$request->colorId;
        $size_id=$request->size_id;
        $productDetails=ProductDetails::select([
            "price",
            "id"
        ])
        ->where("id",$p_d_id)
        ->first();
        if(empty($productDetails))
    {
         return response()->json([
                    "errors"=>[
                        "email"=>[" This id do not have price. "]
                        ]
                ],422);
    }
        // $e=DB::select(" SELECT * FROM `product_details` WHERE `id`='".$p_d_id."'  ");
        // foreach($e as $gg)
        // {
            $price=$productDetails->price;
            $db_p_d_id=$productDetails->id;

       // }
       return response()->json([
           "message"=>" Price against the id ",
           "price"=>sprintf("%0.2f",$price),
           "id"=>$db_p_d_id,
           "qty"=>$productDetails->qty
           ],200);
       // $arr=array(sprintf("%0.2f",$price),$db_p_d_id,$productDetails->qty);
        //return json_encode($arr);
    }




public function sizeoption(Request $request)
{
        $option="";
    $itemcode=$request->itemcode;
   // $si=DB::select(" SELECT * FROM `product_details` WHERE `itemcode`='".$itemcode."' ");
   $si=ProductDetails::where("itemcode",$itemcode)->get();
    foreach($si as $sizessee)
    {
                                $option.="<option  value=$sizessee->id>$sizessee->size_name</option>";
    }
    return json_encode($option);

}

public function sizepricecustom(Request $request)
{
    $option="<option value='' selected disabled>Select color</option>";
    $size=$request->size;
    $itemcode=$request->itemcode;
    $productDetailsRows=ProductDetails::where("size_id",$size)
    ->where("itemcode",$itemcode)
    ->get();
    if($productDetailsRows->count()==0)
{
     return response()->json([
                    "errors"=>[
                        "email"=>[" This size does not have any colors registered. "]
                        ]
                ],422);
}
   // $fgtreew=DB::select(" SELECT * FROM `product_details` WHERE `size_id`='".$size."' AND `itemcode`='".$itemcode."' ");
            foreach($productDetailsRows as $gw)
            {
         //   $p_d_id=$gw->id;
           $option.="<option value=$gw->id>$gw->color_name</option>";
            }
           // $arr=array($p_d_id,$option);
           return response()->json([
               "message"=>"Colors registered against this size",
               "options"=>$option
               ],200);
           // return json_encode($arr);
}




public function addtocart2(Request $request)

{

    session_set_cookie_params(3600 * 24 * 7);

        session_start();

        $details_total=0;

        $itemcode=$request->itemcode;

        $my=$request->MyApp;

    foreach ($my as $custome_name => $custome_value)

{

if($custome_name=="itemname" || $custome_name=="name" || $custome_name=="year" || $custome_name=="qty" || $custome_name=="schoolname" || $custome_name=="embroidery_color"  || $custome_name=="rightlanguage1"  || $custome_name=="rightlanguage2" || $custome_name=="leftlanguage1" || $custome_name=="leftlanguage2"    || $custome_name=="city" || $custome_name=="state" || $custome_name=="color" || $custome_name=="size" || $custome_value==null || $custome_value=="No" || $custome_value=="None")

{

    continue;

}

else

{

    if($custome_name=="silkscreen" || $custome_name=="lettering_type")

    {

        $custome_name=$custome_value;

    }



    $customPrices=CustomPrices::select([

        "price"

        ])

        ->where("webcustomname",$custome_name)

        ->first();

//return $customPrices;

if(empty($customPrices))

{

   continue;

   // return $custome_name;

}

        $details_total+=$customPrices->price;

    //}

}

//return json_encode("Student one got ".$custome_value." in ".$custome_name."\n");

}

// return $details_total;

        $item_name=$my["itemname"];

        $silkscreen=$my["silkscreen"];

        $lettering_type=$my["lettering_type"];

        $name=$my["name"];

        $year=$my["year"];







        $schoolname=$my["schoolname"];

        $qty=$my["qty"];



        $collar=$my["collar"];

        $embroidery_color=$my["embroidery_color"];



        $rush=$my["rush"];



        $rightpersonnalline1=$my["rightpersonnalline1"];

        $rightlanguage1=$my["rightlanguage1"];

        $rightlanguageEng1="";





        $rightpersonnalline2=$my["rightpersonnalline2"];

        $rightlanguage2=$my["rightlanguage2"];

        $rightlanguageEng2="";













        $leftpersonnalline1=$my["leftpersonnalline1"];

        $leftlanguage1=$my["leftlanguage1"];

        $leftlanguageEng1="";





        $leftpersonnalline2=$my["leftpersonnalline2"];

        $leftlanguage2=$my["leftlanguage2"];

        $leftlanguageEng2="";







        $left_chest=$my["left_chest"];

        $leftt=$my["leftt"];
        $leftSleeve=$my["leftSleeve"];

        $nameses=$my["name"];

        $pant_stripe=$my["pant_stripe"];

        $right_chest=$my["right_chest"];

        $city=$my["city"];

        $state=$my["state"];









          $creativeweapons=$my["creativeweapons"];;

$traditioalweapons=$my["traditioalweapons"];

$teamweaponsparring=$my["teamweaponsparring"];

$creativeforms=$my["creativeforms"];



$extremeweapons=$my["extremeweapons"];



$extremeforms=$my["extremeforms"];



$pointsparring=$my["pointsparring"];



$swordsparring=$my["swordsparring"];



$escrimasparring=$my["escrimasparring"];



$teamsparring=$my["teamsparring"];

$traditionalform=$my["traditionalform"];



$ranktype=$my["ranktype"];



$degree1=$my["degree1"];

$degree2=$my["degree2"];

$degree3=$my["degree3"];

$degree4=$my["degree4"];

$degree5=$my["degree5"];

$degree6=$my["degree6"];

$degree7=$my["degree7"];

$degree8=$my["degree8"];



      $color=$my["color"];

        $size=$my["size"];

        $six_digit_random_number = random_int(100000, 999999);

        $cusname="custom".$itemcode.$item_name.$color.$size.$six_digit_random_number;



        $productDetails=ProductDetails::select([

            "price",

            "color_name",

            "size_name",
            "qty"

            ])

            ->where("id",$size)

            ->first();

          //  return $productDetails;

        $price=floatval($productDetails->price);

        //$price+=floatval($details_total);

      //  return $price;



    //    }

          $cu=$_COOKIE["user"];

$id=$cu;



$orderHead=OrderHead::select([

    "id",

    "amount"

    ])
    ->where("customer_id",$id)
->where("isOrder",0)
->first();

if(empty($orderHead))

{

    $orderHead=new OrderHead;

    $orderHead->customer_id=$id;

    $orderHead->save();

   $orderId =$orderHead->id;

   //$orderAmount=0;

}

else

{

       $orderId =$orderHead->id;

    //   $orderAmount=$orderHead->amount;

}



$orders =new Order;

$orders->orderid=$orderId;

$orders->itemname=$item_name;

$orders->rate=$price;

$orders->custom_total=$details_total;

$orders->quantity=$qty;

$orders->color_id=$productDetails->color_name;

$orders->size_id=$productDetails->size_name;

$orders->status="pending";

$orders->retail_price=$qty*($price+$details_total);

$orders->order_type="Custom";

$orders->itemcode=$itemcode;

$orders->random=$six_digit_random_number;

$orders->save();



$customOrders=new CustomOrders;

$customOrders->orderid=$orderId;

$customOrders->orders_id=$orders->id;

$customOrders->unique_name=$cusname;

$customOrders->city=$city;

$customOrders->state=$state;

$customOrders->flname=$name;

$customOrders->schoolname=$schoolname;

$customOrders->year=$year;

$customOrders->lettering_type=$lettering_type;

$customOrders->embroidery_color=$embroidery_color;

$customOrders->rightlanguageEng1="";

$customOrders->rightlanguageEng2="";

$customOrders->rightlanguagekor1=$rightlanguage1;

$customOrders->rightlanguagekor2=$rightlanguage2;

$customOrders->rightpersonnalline1=$rightpersonnalline1;

$customOrders->rightpersonnalline2=$rightpersonnalline2;

$customOrders->leftlanguagekor1=$leftlanguage1;

$customOrders->leftlanguageEng1="";

$customOrders->leftlanguagekor2=$leftlanguage2;

$customOrders->leftlanguageEng2="";

$customOrders->leftpersonnalline1=$leftpersonnalline1;

$customOrders->leftpersonnalline2=$leftpersonnalline2;

$customOrders->left_chest=$left_chest;

$customOrders->leftt=$leftt;

$customOrders->left_sleeve=$leftSleeve;

$customOrders->pant_stripe=$pant_stripe;

$customOrders->right_chest=$right_chest;

$customOrders->rush=$rush;

$customOrders->collar=$collar;

$customOrders->silkscreen=$silkscreen;

$customOrders->creativeweapons=$creativeweapons;

$customOrders->traditioalweapons=$traditioalweapons;

$customOrders->teamweaponsparring=$teamweaponsparring;

$customOrders->creativeforms=$creativeforms;

$customOrders->extremeweapons=$extremeweapons;

$customOrders->extremeforms=$extremeforms;

$customOrders->pointsparring=$pointsparring;

$customOrders->swordsparring=$swordsparring;

$customOrders->escrimasparring=$escrimasparring;

 $customOrders->teamsparring=$teamsparring;

  $customOrders->traditionalform=$traditionalform;

  $customOrders->ranktype=$ranktype;

    $customOrders->degree1=$degree1;

     $customOrders->degree2=$degree2;

      $customOrders->degree3=$degree3;

      $customOrders->degree4=$degree4;

      $customOrders->degree5=$degree5;

      $customOrders->degree6=$degree6;

      $customOrders->degree7=$degree7;

      $customOrders->degree8=$degree8;

      $customOrders->save();


$msg = "Added to the cart.";
if($productDetails->qty<$qty){
    // will send out of stock only for NMAA warehouse items
    if($this->isNmaaProduct($itemcode)) $msg = "out_of_stock";
}



return response()->json([
    "message"=>$msg
    ],200);
   // return json_encode(array("status"=>1,"message"=>"Added to the cart successfully."));

}











public function addtocart(Request $request)

{

    session_set_cookie_params(3600 * 24 * 7);

        session_start();

        $price=$request->price;

        $itemcode=$request->itemcode;

        $name=$request->name;

        $qty=$request->qty;

        $color=$request->color;

        //return $color;

        $size=$request->size;

        $type="Normal";

               $cu=$_COOKIE["user"];

$id=$cu;

        $six_digit_random_number = random_int(100000, 999999);

        $nn=$name."".$color."".$size."".$itemcode."".$six_digit_random_number;



           // $id=$cu;



$orderHead=OrderHead::select([

    "id",

    "amount"

    ])->where("customer_id",$id)

->where("isOrder",0)

->first();

if(empty($orderHead))

{

    $orderHead=new OrderHead;

    $orderHead->customer_id=$id;
    $serialNo=OrderHead::max('serial_no');
    $orderHead->serial_no=$serialNo+1;
    $orderHead->save();

   $orderId =$orderHead->id;

   $orderAmount=0;

}

else

{

       $orderId =$orderHead->id;

      // $orderAmount=$orderHead->amount;

}



$productDetails=ProductDetails::where("id",$color)

->first();





    $orders =new Order;

$orders->orderid=$orderId;

$orders->itemname=$name;

$orders->rate=$price;
//$orders->rate=$price;
$orders->quantity=$qty;

$orders->color_id=$productDetails->color_name;

$orders->size_id=$productDetails->size_name;

$orders->status="pending";

$orders->retail_price=$qty*$price;

$orders->order_type=$type;

$orders->itemcode=$itemcode;

$orders->random=$six_digit_random_number;

$orders->save();

$msg = "Added to the cart.";
if($productDetails->qty<$qty){
    // will send out of stock only for NMAA warehouse items
    if($this->isNmaaProduct($itemcode)) $msg = "out_of_stock";
}


      return json_encode(array("status"=>1,"message"=>$msg));

}

public function isNmaaProduct($itemcode):bool
{
    $warehouse = DB::table('warehouse')->where('name', 'like', '%NMAA%')->first();
    if(!$warehouse) {
        return false;
    }
    return DB::table('item')->where('id', $itemcode)->where('warehouse_id', $warehouse->id)->exists();
}

public function itemcount(Request $request)

{

    session_set_cookie_params(3600 * 24 * 7);

    session_start();

    $count=0;

    if(isset($_COOKIE["user"]))

{

        $cu=$_COOKIE["user"];

    $orderHead=OrderHead::join("orders","orders.orderid","=","order_head.id")

->where("order_head.customer_id",$cu)

->where("order_head.isOrder",0)
->whereNull("orders.deleted_at")
->get();

$count=$orderHead->count();

}

return $count;

}







public function totalcart(Request $request)

{

    session_set_cookie_params(3600 * 24 * 7);

    session_start();

    $total=0;

    if(isset($_COOKIE["user"]))

{

    $id=$_COOKIE["user"];

$orderHead=OrderHead::select([
    DB::raw(" COALESCE(sum(orders.retail_price),0) as amount ")
])
->join("orders","orders.orderid","=","order_head.id")
->where("order_head.customer_id",$id)
->where("order_head.isOrder",0)
->whereNull("orders.deleted_at")
->first();
//return $orderHead;
if(!empty($orderHead))

{
    // foreach($orderHead as $or)
    // {
        $total=$orderHead->amount;
//    }



}



}

//     if(isset($_SESSION["coupan"]))

// {

//     $cou=$_SESSION["coupan"];

// }

// else

// {

//     $cou=0;

// }
$couponSessionDiscount=session()->get('discount');
if(empty($couponSessionDiscount))
{
    $couponDiscount=0;
}
else
{
    $couponDiscount=$couponSessionDiscount;
}


    //$dis=$total($total*0.1)-$request->discount;

    $arr=array($total,$couponDiscount);

    return $arr;

}





public function cartrecord(Request $request)

{

    session_set_cookie_params(3600 * 24 * 7);

    session_start();

    $data='';

    $total=0;



if(isset($_COOKIE["user"]))

{

         $cu=$_COOKIE["user"];

$id=$cu;


$cartDetails =OrderHead::select([
    "orders.quantity",
    "orders.color_id",
    "orders.rate",
    "orders.size_id",
    "orders.itemname",
    "item.img_dir",
    "orders.itemcode",
    "orders.custom_total"
])
->join("orders","orders.orderid","=","order_head.id")
->join("item","item.id","=","orders.itemcode")
->where("order_head.customer_id",$id)
->where("order_head.isOrder",0)
->whereNull("orders.deleted_at")
->get();

    if($cartDetails->count() == 0)

    {

        return $data;

    }

    foreach ($cartDetails as  $cartDetailsRow)

    {

        $image=asset($cartDetailsRow->img_dir);

     $data.='

    <li class="single-product-cart">

    <div class="cart-img">

        <a href="#"><img src='.$image.' alt=""></a>

    </div>

    <div class="cart-title">

        <h4><a href="#">'.$cartDetailsRow->itemname.'</a></h4>

        <span>Color: '.$cartDetailsRow->color_id.' -- Size: '.$cartDetailsRow->size_id.'	</span><br>

        <span> '.$cartDetailsRow->quantity.' × $'.number_format(floor(floatval(($cartDetailsRow->rate+$cartDetailsRow->custom_total))*100)/100,2, '.', '').'	</span>

    </div>







</li>

';



     //}

}

}

return $data;

}







public function updateqty(Request $request)

{

$id=$_COOKIE["user"];
 $qty=$request->qty;
$order=Order::where("id",$request->orderId)->first();
$order->quantity=$qty;
$order->retail_price=$qty*($order->rate+$order->custom_total);
$order->save();
$orderHead=OrderHead::select([
    DB::raw(" COALESCE(sum(orders.retail_price),0) as amount ")
])
->join("orders","orders.orderid","=","order_head.id")
->where("order_head.customer_id",$id)
->where("order_head.isOrder",0)
->whereNull("orders.deleted_at")
->first();
$total=$orderHead->amount;
return json_encode(array("status"=>1,"message"=>"","data"=>$total));



}




public function detailcustombtn(Request $request)

{
    $orderId=$request->orderId;
    $session_data=CustomOrders::where("orders_id",$orderId)->first();
 //   return $session_data;
    
    // if(empty($session_data))
    // {
    //     return response()->json([
    //         "error"=>
    //         ],422);
    // }
    
    
    
    if($session_data->leftlanguageEng1!="")
    {
        //return "Yes";
        $leftlanguage1=$session_data->leftlanguageEng1;
    }
    else
    {
        //return "No";
        $leftlanguage1=$session_data->leftlanguagekor1; 
       // return $leftlanguage1;
    }
    
     if($session_data->rightlanguageEng1!="")
    {
        $rightlanguage1=$session_data->rightlanguageEng1;
    }
    else
    {
        $rightlanguage1=$session_data->rightlanguagekor1; 
    }
    
      if($session_data->leftlanguageEng2!="")
    {
        $leftlanguage2=$session_data->leftlanguageEng2;
    }
    else
    {
        $leftlanguage2=$session_data->leftlanguagekor2; 
    }
        if($session_data->rightlanguageEng2!="")
    {
        $rightlanguage2=$session_data->rightlanguageEng2;
    }
    else
    {
        $rightlanguage2=$session_data->rightlanguagekor2; 
    }
    
    $option="
    
    <table id='tab1' style='visibility:collapse'>
            <thead>
            <td>Lettering Type</td>
            <td>Traditional Form</td>
            <td>Team Sparring</td>
            <td>Escrima Sparring</td>
            <td>Sword Sparring</td>
            <td>Point Sparring</td>
            <td>Extreme Forms</td>
            <td>Extreme Weapons</td>
            <td>Creative Forms</td>
            <td>Team Weapon Sparring</td>
            <td>Traditional Weapons</td>
            <td>Creative Weapons</td>
            
            
            <td>City</td>
            <td>State</td>
            <td>School Name</td>
            <td>First Name & Last Name</td>
            <td>Year</td>
            <td>Left Chest</td>
            <td>Right Chest</td>
            <td>Right Sleeve</td>
            <td>Left Sleeve</td>
            <td>Collar</td>
            <td>Silk Screen</td>
            <td>Uniform Pant Stripe</td>
            <td>Rush Processing</td>
            <td>Rank Type</td>
            <td>Embroidery thread color</td>
            <td>1st Degree</td>
            <td>2nd Degree</td>
            <td>3rd Degree</td>
            <td>4th Degree</td>
            <td>5th Degree</td>
            <td>6th Degree</td>
            <td>7th Degree</td>
            <td>8th Degree</td>
            <td>Size</td>
            <td>Color</td>
            <td>Price</td>
            <td>Left side, line 1</td>
            <td>Language</td>
            <td>Left side, line 2</td>
            <td>Language</td>
            <td>Right side, line 1</td>
            <td>Language</td>
            <td>Right side, line 2</td>
            <td>Language</td>
            </thead>
            <tbody>
                <td id='table_lettering_type'> $session_data->lettering_type</td>
                
                <td id='table_traditional'>$session_data->traditionalform</td>
                <td id='table_teamsparring'>$session_data->teamsparring</td>
                <td id='table_escrimasparring'>$session_data->escrimasparring</td>
                <td id='table_swordparring'>$session_data->swordsparring</td>
                <td id='table_pointsparring'>$session_data->pointsparring</td>
                <td id='table_extremeforms'>$session_data->extremeforms</td>
                <td id='table_extremewepons'>$session_data->extremeweapons</td>
                <td id='table_creativeforms'>$session_data->creativeforms</td>
                <td id='table_teamweponsparring'>$session_data->extremeforms</td>
                <td id='table_traditionalweapons'>$session_data->traditioalweapons</td>
                <td id='table_creativwepons'>$session_data->creativeweapons</td>
                
                <td id='table_city'>$session_data->city</td>
                <td id='table_state'>$session_data->state</td>
                <td id='table_school'>$session_data->schoolname</td>
                <td id='table_flname'>$session_data->flname</td>
                <td id='table_year'>$session_data->year</td>
                <td id='table_leftchest'>$session_data->left_chest</td>
                <td id='table_rightchest'>$session_data->right_chest</td>
                <td id='table_rightsleeve'>$session_data->leftt</td>
                <td id='table_leftsleeve'>$session_data->left_sleeves</td>
                <td id='table_collar'>$session_data->collar</td>
                <td id='table_silkscreen'>$session_data->silkscreen</td>
                <td id='table_uniform'>$session_data->pant_stripe</td>
                <td id='table_rush'>$session_data->rush</td>
                <td id='table_ranktype'>$session_data->ranktype</td>
                <td id='table_em_thread_color'>$session_data->embroidery_color</td>
                <td id='table_degree1'>$session_data->degree1</td>
                <td id='table_degree2'>$session_data->degree1</td>
                <td id='table_degree3'>$session_data->degree1</td>
                <td id='table_degree4'>$session_data->degree1</td>
                <td id='table_degree5'>$session_data->degree1</td>
                <td id='table_degree6'>$session_data->degree1</td>
                <td id='table_degree7'>$session_data->degree1</td>
                <td id='table_degree8'>$session_data->degree1</td>
                <td id='table_size'>$session_data->degree1</td>
                <td id='table_colour'>$session_data->degree1</td>
                <td id='table_price'>$session_data->degree1</td>
                <td id='table_left_side_line1'>$session_data->leftpersonnalline1</td>
                <td id='table_left_language'>$leftlanguage1</td>
                <td id='table_left_side_line2'>$session_data->leftpersonnalline2</td>
                <td id='table_left_language'>$leftlanguage2</td>
                <td id='table_right_side_line1'>$session_data->rightpersonnalline1</td>
                <td id='table_right_language'>$rightlanguage1</td>
                <td id='table_right_side_line1'>$session_data->rightpersonnalline2</td>
                <td id='table_right_language'>$rightlanguage2</td>
                
            </tbody>
        </table>    
    
    ";
    $option.="

    <form action='#' method='post' enctype='multipart/form-data'>

    <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>City</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p id='custom_city'>$session_data->city </p>

      </div>
       <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>State</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p id='custom_state'>$session_data->state </p>

      </div>
    </div>
    <hr>

    <p class='cen'>OR</p>
<br>
<br>
<div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>First Name & Last Name</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p id='custom_name'>$session_data->flname </p>

      </div>

     <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Year</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p id='custom_year'> $session_data->year </p>

      </div>

    </div>

      <p class='cen'>OR</p>
<br>
<br>
<div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>School Name</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p id='custom_schoolname'> $session_data->schoolname  </p>

      </div>


    </div>

    <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Silk Screen Logo on back.</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p id='custom_logoonback'>  $session_data->silkscreen  </p>

      </div>


    </div>

<hr>



  <p class='cen'>Lettering Type</p>
<br>
<br>
<div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Lettering</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_lettering_type'> $session_data->lettering_type </p>

      </div>



    </div>


    <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Traditional Forms</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_traditional'> $session_data->traditionalform </p>

      </div>


     <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Team Sparring</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_teamsparring'> $session_data->teamsparring </p>

      </div>


    </div>




     <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Escrima Sparring</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_escrimasparring'> $session_data->escrimasparring </p>

      </div>


     <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Sword Sparring</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_swordparring'> $session_data->swordsparring </p>

      </div>


    </div>




     <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Point Sparring</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_pointsparring'> $session_data->pointsparring </p>

      </div>


     <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Extreme Forms</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_extremeforms'> $session_data->extremeforms </p>

      </div>


    </div>




      <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Extreme Weapons</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_extremewepons'> $session_data->extremeweapons </p>

      </div>


     <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Creative Forms</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_creativeforms'> $session_data->creativeforms </p>

      </div>


    </div>




      <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Team Weapon Sparring</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_teamweponsparring'> $session_data->teamweaponsparring </p>

      </div>


     <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Traditional Weapons</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_traditionalweapons'> $session_data->traditioalweapons </p>

      </div>


    </div>


         <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Creative Weapons</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_creativwepons'> $session_data->creativeweapons </p>

      </div>




    </div>




  <hr>


  <p class='cen'>Personnalized Text</p>
<br>
<br>
<div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>left side,line 1</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p id='custom_left_side_line1'> $session_data->leftpersonnalline1 </p>

      </div>

     <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Language</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p id='custom_leftlanguageline1'> $leftlanguage1 </p>

      </div>



    </div>

    <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>left side,line 2</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p id='custom_left_side_line2'> $session_data->leftpersonnalline2 </p>

      </div>

     <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Language</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p id='custom_leftlanguageline2'> $leftlanguage2 </p>

      </div>



    </div>










<div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>right side,line 1</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p id='custom_right_side_line1'> $session_data->rightpersonnalline1 </p>

      </div>

     <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Language</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p id='custom_rightlanguageline1'> $rightlanguage1 </p>

      </div>



    </div>

    <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>right side,line 2</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p id='custom_right_side_line2'> $session_data->rightpersonnalline2 </p>

      </div>

     <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Language</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p id='custom_rightlanguageline2'> $rightlanguage2 </p>

      </div>



    </div>






        <hr>



  <p class='cen'>Patches</p>
<br>
<br>
<div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Left Chest</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_leftchest'> $session_data->left_chest </p>

      </div>

     <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Right Chest</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_rightchest'> $session_data->right_chest </p>

      </div>



    </div>


    <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Right Sleeves</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_sleeveleft'> $session_data->leftt  </p>

      </div>
     
         <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Left Sleeves</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_sleeveleft'> $session_data->left_sleeve  </p>

      </div>
 <br>
     <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Collar </label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_collar'> $session_data->collar  </p>

      </div>



    </div>




        <hr>



  <p class='cen'>HEM Options</p>
<br>
<br>
<div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Pant Stripe</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_pantstripe'>  $session_data->pant_stripe  </p>

      </div>





    </div>

    <hr>

       <p class='cen'>Rush Processing</p>

<div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Rush Option</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_rush'> $session_data->rush </p>

      </div>





    </div>


      <hr>

       <p class='cen'>Rank</p>
<br>
<br>
<div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Rank type</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_ranktype'> $session_data->ranktype </p>

      </div>





    </div>



<div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>Embroidery thread color</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_em_thread_color'> $session_data->embroidery_color </p>

      </div>





    </div>



    <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>1st Degree</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_degree1'> $session_data->degree1 </p>

      </div>





    </div>

    <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>2nd Degree</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_degree2'> $session_data->degree2 </p>

      </div>





    </div>

    <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>3rd Degree</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_degree3'> $session_data->degree3 </p>

      </div>





    </div>

    <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>4th Degree</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_degree4'> $session_data->degree4 </p>

      </div>





    </div>

    <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>5th Degree</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_degree5'> $session_data->degree5 </p>

      </div>





    </div>

    <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>6th Degree</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_degree6'> $session_data->degree6 </p>

      </div>





    </div>

    <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>7th Degree</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_degree7'> $session_data->degree7 </p>

      </div>





    </div>

    <div class='form-group row'>
      <label class='control-label col-md-3 col-sm-3 col-xs-3 ac'>8th Degree</label>
      <div class='col-md-3 col-sm-9 col-xs-9'>
        <p  id='custom_degree8'> $session_data->degree8 </p>

      </div>





    </div>





</div>
<div class='modal-footer'>
  <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
  <!--<button type='submit' class='btn btn-success'>Update</button>-->
</div>
</form>

    ";



    return $option;
}






public function getSearchResults(Request $request)
{
    $searchKey = $request->search;
    $option="";
    $searchResults= Item::select([
        "id",
        "item_name",
        "img_dir"
    ])
    ->where("item_name","LIKE","%{$searchKey}%")
     ->where("isActive",1)
    ->skip(0)
    ->take(15)
    ->get();
    if($searchResults->count()==0)
    {
        $option.="
        <li class='list-group-item product-list-item'>
     Sorry, no products exists with this product name.
      </li>
        ";
        return json_encode(array("status"=>1,"message"=>"Product does not exist.","data"=>$option));
    }
    else
    {
        foreach($searchResults as $searchResultsRow)
        {
            $route=route("front.product_details",$searchResultsRow->id);
            $option.="
            <a href=$route>
            <li class='list-group-item product-list-item'>
            <img src=".asset($searchResultsRow->img_dir)." alt=$searchResultsRow->img_dir>
            $searchResultsRow->item_name
          </li>
          </a>
            ";
        }
    }
    return json_encode(array("status"=>1,"message"=>"Product list.","data"=>$option));

}

public function getOrderHistory(Request $request)
{
    $id=$_COOKIE["user"];
    $orderList=OrderHead::select([
        "order_head.id",
        "order_head.created_at",
        "order_head.status",
        "order_head.amount"
    ])
    ->where("order_head.customer_id",$id)
    ->where("isOrder",1)
    ->get();
    $orderBody="";
    if(empty($orderList))
    {
        return json_encode(array("status" => 0,"message" => " You have no orders."));
    }
    else
    {

        foreach($orderList as $key=>$orderListRow)
        {
            $route=route("front.order.details",$orderListRow->id);
$orderBody.="
<tr>
<td>".($key+1)."</td>
<td>$orderListRow->id</td>
<td>$orderListRow->created_at</td>
<td>$orderListRow->status</td>
<td>$".number_format(floor(floatval($orderListRow->amount)*100)/100,2, '.', '')."</td>
<td><a href=$route class='check-btn sqr-btn '>View</a></td>
</tr>

";
        }

    }
    return json_encode(array("status" => 1,"message" => " Order list.","data"=>$orderBody));
}


public function validateCoupon(Request $request)
{
    //session_start();
    $date=date("Y-m-d");
    $coupon=Coupon::where("coupon_code",$request->coupon_code)->whereDate("expiry",">=",$date)->first();
    if(empty($coupon))
    {
        return json_encode(array("status"=>0,"message"=>"Coupon does not exists"));
    }
    else
    {
        session()->put('discount', $coupon->discount);
        return json_encode(array("status"=>1,"message"=>"Coupon validated","data"=>$coupon));
    }
}

}
