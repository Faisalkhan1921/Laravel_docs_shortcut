<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Models\Customer;
use Auth;
use Image;
use App\Models\User;
use App\Models\Color;
use App\Models\Size;
use App\Models\Item;
use App\Models\StockHistory;
use App\Models\ProductDetails;
//use Illuminate\Support\Facades\Hash;
class DataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function realPasswordStore(Request $request)
    {
        $realPassword=$request->save_password;
        $user=User::where("id",Auth::user()->id)->first();
        if (!Hash::check($realPassword, $user->password))
        {
            return array(0);
        }
        else
        {
        $user->real_password=$realPassword;
        $user->save();
        return array(1);
        }


    }

    public function cancell_order($id)
    {
        DB::select(" UPDATE `orders` SET `status`='cancelled' WHERE `orderid`='".$id."' ");
        DB::select(" UPDATE `order_head` SET `status`='cancelled' WHERE `id`='".$id."' ");
        
        return redirect('/order-detail/'.$id); // by moeez
        return redirect()->action("PagesController@pendingview");
    }

    public function updatecustomprice(Request $request)
    {
        $id=$request->id;
        $price=$request->price;
        DB::select(" UPDATE `custom_prices` SET `price`='".$price."' WHERE `id`='".$id."' ");

        return redirect()->action("PagesController@add_price_to_customview")->with("str","$id");
    }


    public function delete_product_image($id)
    {
        //dd($id);
        $fr=DB::select(" SELECT * FROM `item` WHERE `itemcode`='".$id."' ");
    foreach($fr as $ff)
    {
        $img_dir=$ff->img_dir;
    }
    $img_dir2="../../public_html/".$img_dir;

     //$folder="product_img/".$name;
       //  $folder2="../../public_html/product_img/".$name;
        // move_uploaded_file($tempname, $folder);
    // copy($folder,$folder2);




    unlink($img_dir);
    unlink($img_dir2);


    DB::select(" UPDATE `item` SET `img_dir`='product_img/no.jpg' WHERE `itemcode`='".$id."' ");


    $e=DB::select(" SELECT * FROM `product_details` WHERE `itemcode`='".$id."' ");
     $e2=DB::select(" SELECT * FROM `item` WHERE `itemcode`='".$id."' ");
        return view("pages/item/update_details")->with("itemcode",$id)->with("item",$e)->with("item2",$e2)->with("str6",$e);

    }

    public function confirm_order(Request $request)
    {
     $orderid=$request->orderid;
        $dispatcher_id=$request->dispatcher_id;
   $he=DB::select(" SELECT * FROM `order_head` JOIN customer ON customer.id=order_head.customer_id WHERE `order_head`.`id`='".$orderid."' ");
        foreach($he as $uu)
        {
            $email=$uu->email;
            $name=$uu->fname." ".$uu->lname;
        }
        DB::select(" UPDATE `order_head` SET `dispatcher_id`='".$dispatcher_id."',`status`='confirmed' WHERE `id`='".$orderid."' ");
        DB::select(" UPDATE `orders` SET `status`='confirmed' WHERE `orderid`='".$orderid."' ");
$fg=DB::select(" SELECT * FROM `orders` WHERE `orderid`='".$orderid."' AND `back`='Yes' ");
if(!empty($fg))
{
//$to = "17sw01@students.muet.edu.pk";
$subject = " Back order. Order id is $orderid ";
$to = $email;
$message = 'Dear '.$name.',<br>';
$message .= "These are items that are back ordered";
$message .= "Regards,NMAA<br>";

$message.="
        <table border='1'>
            <thead>
                <tr>
                    <td>Item name</td>
                    <td>Qty ordered</td>
                    <td>Back ordered</td>
                </tr>
            </thead>
            <tbody>
            ";


            foreach($fg as $ii)
            {
            $message.="
                <tr>
                    <td>$ii->itemname</td>
                    <td>$ii->quantity</td>
                    <td>$ii->back_qty</td>
                </tr>

";
}
$message.="
            </tbody>
        </table>
    ";
// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
mail($to,$subject,$message,$headers);
}
        return redirect('/order-detail/'.$orderid); //by moeez
        return redirect()->action("PagesController@completview");
    }

    public function update_custom_product(Request $request)
        {

        $view_number=$request->view_number;
        $itemcode=$request->itemcode;
        $warehouseId=$request->warehouse_id;
        $item_name=$request->name;
        $discription=$request->description;
        $nn=addslashes($discription);
        $checkimage=DB::select(" SELECT * FROM `item` WHERE `id`='".$itemcode."' ");

        foreach($checkimage as $ci)
        {
            $img_dir=$ci->img_dir;
        }

        if(!empty($_FILES["change_item"]['name']))
        {
            if($img_dir!="product_img/no.jpg")
            {
          //  $img_dir2="../../public_html/".$img_dir;
            if(file_exists($img_dir))
            {
            unlink($img_dir);
            }
           // unlink($img_dir2);
            }

           $six_digit_random_number = mt_rand(100000, 999999);
        $filename=$_FILES["change_item"]['name'];
         $tempname=$_FILES["change_item"]["tmp_name"];
         $file_ext=pathinfo($filename,PATHINFO_EXTENSION);
         $filename=pathinfo($filename,PATHINFO_FILENAME);
         $filename=$six_digit_random_number;
         $name=$filename.".".$file_ext;
        $folder="product_img/".$name;
      //  $folder2="../../public_html/product_img/".$name;
        move_uploaded_file($tempname, $folder);
      //  copy($folder,$folder2);
        }
        else
        {
            $folder=$img_dir;
        }
        DB::select(" UPDATE `item` SET `img_dir`='".$folder."',`item_name`='".$item_name."',`discription`='".$nn."',`view_number`='".$view_number."',`warehouse_id`='".$warehouseId."',`is_featured`='".$request->is_feature."' WHERE `id`='".$itemcode."' ");
        //   $e2=DB::select(" SELECT * FROM `item` WHERE `itemcode`='".$itemcode."' ");
        //  $e=DB::select(" SELECT * FROM `product_details` WHERE `itemcode`='".$itemcode."' ");
       return redirect()->back()->with("str4","test");

        }

        public function item_variant_upodate(Request $request)
        {
            $imgVar = DB::table('multiple_picture')->where('id', $request->variant_id)->first();
            if(!$imgVar){
                return redirect()->back();
            }
            $img_dir2=base_path().'/'.$imgVar->img_dir;
            
            if (file_exists($img_dir2)) {
                unlink($img_dir2);
            }

                 $six_digit_random_number = mt_rand(100000, 999999);
        $filename=$_FILES["item_variant_update"]['name'];
         $tempname=$_FILES["item_variant_update"]["tmp_name"];
         $file_ext=pathinfo($filename,PATHINFO_EXTENSION);
         $filename=pathinfo($filename,PATHINFO_FILENAME);
         $filename=$six_digit_random_number;
         $name=$filename.".".$file_ext;
        $folder="item_variant/".$name;
        $folder2=base_path()."/item_variant/".$name;
        move_uploaded_file($tempname, $folder);
        copy($folder,$folder2);


        DB::select(" UPDATE `multiple_picture` SET `img_dir`='".$folder."' WHERE `id`='".$request->variant_id."' ");

               return redirect()->route('item.update.view',[$imgVar->itemcode]);
        }

        public function item_variant_delete($id)
        {
            $imgVar = DB::table('multiple_picture')->where('id', $id)->first();
            if(!$imgVar){
                return redirect()->back();
            }
            $img_dir2=base_path().'/'.$imgVar->img_dir;
            
            if (file_exists($img_dir2)) {
                unlink($img_dir2);
            }

            DB::select(" DELETE FROM `multiple_picture` WHERE `id`='".$id."' ");

               return redirect()->route('item.update.view',[$imgVar->itemcode]);
        }

        public function add_image_variant(Request $request)
        {
            $itemcode=$request->itemcode;
            
            $uploadedImage = $request->file('item_variant');
            $image = Image::make($uploadedImage);
            $image->crop(270, 324, 0, 0);

             $six_digit_random_number = mt_rand(100000, 999999);
             
             $image_name = $six_digit_random_number . '.' . $uploadedImage->getClientOriginalExtension();
            $mainFolderPath = "item_variant/" . $image_name;
            $path = str_replace("public/", "", public_path('item_variant'));
            $uploadedImage->move($path,$image_name);
            

            // Delete the old image file using PHP's unlink function (if it exists)
            // if ($oldImagePath && file_exists(base_path($oldImagePath))) {
            //     unlink(base_path($oldImagePath));
            // }
            

 DB::select(" INSERT INTO `multiple_picture`( `itemcode`, `img_dir`) VALUES ('".$itemcode."','".$mainFolderPath."') ");

        return redirect()->route('item.update.view',[$itemcode]);

        }

        public function color_delete($id)
        {
            $color_id=$id;
            DB::select(" DELETE FROM `product_details` WHERE `color_id`='".$color_id."' ");
            DB::select(" DELETE FROM `color` WHERE `id`='".$color_id."' ");
            return redirect()->action("PagesController@add_colorview")->with("str2",$color_id);
        }

    public function update_details(Request $request)
    {
        //dd($request);
        $p_d_id=$request->p_d_id;
        $color_id=$request->color_id;
        $size_id=$request->size_id;
        $temcode=$request->itemcode;
        $update_qty=$request->update_qty;
        $colorDetails=Color::where("id",$color_id)->first();
        // $cc=DB::select(" SELECT * FROM `color` WHERE `id`='".$color_id."' ");
        // foreach($cc as $rt)
        // {
            $color_name=$colorDetails->name;
       // }
       $sizeDetails=Size::where("id",$size_id)->first();
        // $cc1=DB::select(" SELECT * FROM `size` WHERE `id`='".$size_id."' ");
        // foreach($cc1 as $rt1)
        // {
            $size_name=$sizeDetails->name;
        //}

        $price=$request->price;
        $productDetails=ProductDetails::where("id",$p_d_id)->first();
        $productDetails->color_id=$color_id;
        $productDetails->size_id=$size_id;
        $productDetails->color_name=$color_name;
        $productDetails->size_name=$size_name;
        $productDetails->price=$price;
    if($update_qty>0)
    {
        $productDetails->qty=($update_qty+$request->qty);
    }
        $productDetails->save();
        if($update_qty>0)
        {
        $stockHistory=new StockHistory;
        $stockHistory->module=" Update product detail ";
        $stockHistory->description=" Stock in qty =$update_qty ";
        $stockHistory->previous_stock=$request->qty;
        $stockHistory->current_stock=$update_qty;
        $stockHistory->product_detail_id=$p_d_id;
        $stockHistory->save();
    }
       // DB::select(" UPDATE `product_details` SET `color_id`='".$color_id."',`size_id`='".$size_id."',`color_name`='".$color_name."',`size_name`='".$size_name."',`price`='".$price."' WHERE `p_d_id`='".$p_d_id."' ");
    $productDetailsRows=ProductDetails::where("itemcode",$temcode)->get();
    //$e=DB::select(" SELECT * FROM `product_details` WHERE `itemcode`='".$temcode."' ");
     $e2=DB::select(" SELECT * FROM `item` WHERE `itemcode`='".$temcode."' ");
        return view("pages/item/update_details")->with("itemcode",$temcode)
        ->with("item",$productDetailsRows)
        ->with("item2",$e2)
        ->with("str",$productDetailsRows);

    }

    public function detailcustombtn(Request $request)
    {
        $unique=$request->unique;
        $id=$request->orderid;
        $ft=DB::select(" SELECT * FROM `custom_orders` WHERE `unique_name`='".$unique."' AND `orderid`='".$id."' ");
        
if (count($ft) > 0) {
 return json_encode($ft);
} else {
   return 0;
}
       
    }

    public function adminajax(Request $request)
    {
        $id=$request->id;
        $e=DB::select(" SELECT * FROM `admin` WHERE `id`='".$id."' ");
        $arr=array($e);
        return json_encode($arr);
    }
    public function admin(Request $request)
    {
        $name1=$request->name;
        $phone=$request->phone;
        $email=$request->email;
        $role_id=$request->role_id;
        $password=$request->password;
        $six_digit_random_number = mt_rand(100000, 999999);
        $filename=$_FILES["admin_img"]['name'];
         $tempname=$_FILES["admin_img"]["tmp_name"];
         $file_ext=pathinfo($filename,PATHINFO_EXTENSION);
         $filename=pathinfo($filename,PATHINFO_FILENAME);
         $filename=$six_digit_random_number;
         $name=$filename.".".$file_ext;

        $hash=Hash::make($password);

     $folder="admin_img/".$name;
     move_uploaded_file($tempname, $folder);

     DB::select(" INSERT INTO `admin`( `img_dir`,`password`, `name`, `email`, `phone`,`role_id`,`real_password`) VALUES ('".$folder."','".$hash."','".$name1."','".$email."','".$phone."','".$role_id."','".$password."') ");

     return redirect()->action("PagesController@adminview")->with("str",$password);

    }
    public function update_admin(Request $request)
    {
       // dd($request);
        $id=$request->admin_id;
        $name1=$request->name;
        $phone=$request->phone;
        $email=$request->email;
        $roleId=$request->role_id;
        $password=$request->password;
        $admin=DB::select(" SELECT * FROM `admin` WHERE `id`='".$id."' ");
            foreach($admin as $ad)
            {
                $img_dir=$ad->img_dir;
                $realPassword=$ad->password;
            }
       if(!empty($_FILES["admin_img"]['name']))
       {
           unlink($img_dir);
        $six_digit_random_number = mt_rand(100000, 999999);
        $filename=$_FILES["admin_img"]['name'];
         $tempname=$_FILES["admin_img"]["tmp_name"];
         $file_ext=pathinfo($filename,PATHINFO_EXTENSION);
         $filename=pathinfo($filename,PATHINFO_FILENAME);
         $filename=$six_digit_random_number;
         $name=$filename.".".$file_ext;
         $folder="admin_img/".$name;
     move_uploaded_file($tempname, $folder);
    }
    else
    {
        $folder=$img_dir;
    }


if(is_null($password))
{
    $dbPassword=$realPassword;
}
else
{
    $dbPassword=Hash::make($password);
}

     DB::select(" UPDATE  `admin` SET  `img_dir`='".$folder."', `name`='".$name1."', `email`='".$email."', `phone`='".$phone."',`role_id`='".$roleId."',`password`='".$dbPassword."' WHERE `id`='".$id."' ");

     return redirect()->action("PagesController@adminview")->with("str1",$id);

    }

    public function update_category(Request $request)
    {
        $id=$request->category_id;
        $name1=$request->name;

     DB::select(" UPDATE  `category` SET   `name`='".$name1."' WHERE `id`='".$id."' ");

     return redirect()->action("PagesController@categoryview")->with("str1",$id);

    }

    public function update_subcategory(Request $request)
    {
        $id=$request->subcategory_id;
        $name1=$request->name;

     DB::select(" UPDATE  `subcategory` SET   `name`='".$name1."' WHERE `id`='".$id."' ");

     return redirect()->action("PagesController@subcategoryview")->with("str1",$id);

    }

    public function admin_delete($id)
    {
        $admin=DB::select(" SELECT * FROM `admin` WHERE `id`='".$id."' ");
            foreach($admin as $ad)
            {
                $img_dir=$ad->img_dir;
            }
          unlink($img_dir);



     DB::select(" DELETE FROM  `admin`  WHERE `id`='".$id."' ");

     return redirect()->action("PagesController@adminview")->with("str2",$id);

    }
    public function category_delete($id)
    {
        $fr=DB::select(" SELECT * FROM `section` WHERE `id`='".$id."' ");
        foreach($fr as $ff)
        {
            $category_id=$ff->category_id;
             $subcategory_id=$ff->subcategory_id;
        }

        DB::select(" DELETE  FROM `section` WHERE `id`='".$id."' ");

     //DB::select(" DELETE FROM  `category`  WHERE `id`='".$id."' ");
     DB::select(" UPDATE `item` SET `categtory_id`='7',`subcategory_id`='20' WHERE `category_id`='".$category_id."' AND `subcategory_id`='".$subcategory_id."' ");
   return redirect()->action("PagesController@categoryview")->with("str2",$id);

    }
    public function subcategory_delete($id)
    {
      $fr=DB::select(" SELECT * FROM `section` WHERE `id`='".$id."' ");
        foreach($fr as $ff)
        {
            $category_id=$ff->category_id;
             $subcategory_id=$ff->subcategory_id;
        }

        DB::select(" DELETE  FROM `section` WHERE `id`='".$id."' ");

     //DB::select(" DELETE FROM  `category`  WHERE `id`='".$id."' ");
     DB::select(" UPDATE `item` SET `categtory_id`='7',`subcategory_id`='20' WHERE `category_id`='".$category_id."' AND `subcategory_id`='".$subcategory_id."' ");
     return redirect()->action("PagesController@subcategoryview")->with("str2",$id);
    }

    public function category(Request $request)
    {
        $name=$request->name;
        DB::select(" INSERT INTO `category`( `name`) VALUES ('".$name."') ");
        return redirect()->action("PagesController@categoryview")->with("str",$name);
    }
    public function subcategory(Request $request)
    {
        $name=$request->name;
        $nn=addslashes($name);
        DB::select(" INSERT INTO `subcategory`( `name`) VALUES ('".$nn."') ");
        return redirect()->action("PagesController@subcategoryview")->with("str",$name);
    }

    public function add_category_to_subcategory(Request $request)
    {
        $ca_id=$request->category_id;
        $sca_id=$request->scategory_id;
        $e=DB::select(" INSERT INTO `section`( `category_id`, `subcategory_id`) VALUES ('".$ca_id."','".$sca_id."') ");
        return redirect()->action("PagesController@category_to_subcategoryview")->with("str",$e);
    }

    function delete_section($id)
    {
        $e=DB::select(" DELETE FROM `section` WHERE `id`='".$id."' ");
        return redirect()->action("PagesController@category_to_subcategoryview")->with("str2",$e);
    }


    function update_section(Request $request)
    {
        $c_id=$request->category_id;
        $sc_id=$request->subcategory_id;
        $id=$request->id;
        $e=DB::select(" UPDATE `section` SET `category_id`='".$c_id."',`subcategory_id`='".$sc_id."' WHERE `id`='".$id."' ");
        return redirect()->action("PagesController@category_to_subcategoryview")->with("str1",$e);
    }


    public function dispatcherajax(Request $request)
    {
        $id=$request->id;
        $e=DB::select(" SELECT * FROM `dispatcher` WHERE `id`='".$id."' ");
        $arr=array($e);
        return json_encode($arr);
    }


    public function dispatcher_delete($id)
    {
        $admin=DB::select(" SELECT * FROM `dispatcher` WHERE `id`='".$id."' ");
            foreach($admin as $ad)
            {
                $img_dir=$ad->img_dir;
            }
          unlink($img_dir);



     DB::select(" DELETE FROM  `dispatcher`  WHERE `id`='".$id."' ");

     return redirect()->action("PagesController@dispatcherview")->with("str2",$id);

    }



    public function dispatcher(Request $request)
    {
        $name1=$request->name;
        $phone=$request->phone;
        $email=$request->email;

         $password=$request->password;
        if(!empty($_FILES["dispatcher_img"]['name']))
        {
        $six_digit_random_number = mt_rand(100000, 999999);
        $filename=$_FILES["dispatcher_img"]['name'];
         $tempname=$_FILES["dispatcher_img"]["tmp_name"];
         $file_ext=pathinfo($filename,PATHINFO_EXTENSION);
         $filename=pathinfo($filename,PATHINFO_FILENAME);
         $filename=$six_digit_random_number;
         $name=$filename.".".$file_ext;

        $hash=Hash::make($password);
//dd($hash);

     $folder="dispatcher_img/".$name;
     move_uploaded_file($tempname, $folder);
        }
        else
        {
            $folder="no.jpg";
        }

     DB::select(" INSERT INTO `dispatcher`( `img_dir`,`password`, `name`, `email`, `phone`) VALUES ('".$folder."','".$hash."','".$name1."','".$email."','".$phone."') ");

     return redirect()->action("PagesController@dispatcherview")->with("str",$password);

    }
    public function update_dispatcher(Request $request)
    {
        $id=$request->dispatcher_id;
        $name1=$request->name;
        $phone=$request->phone;
        $email=$request->email;
        $status=$request->status;
     $password=$request->password;
        $admin=DB::select(" SELECT * FROM `dispatcher` WHERE `id`='".$id."' ");
            foreach($admin as $ad)
            {
                $img_dir=$ad->img_dir;
                $password_old=$ad->password;
            }
       if(!empty($_FILES["dispatcher_img"]['name']))
       {
           unlink($img_dir);
        $six_digit_random_number = mt_rand(100000, 999999);
        $filename=$_FILES["dispatcher_img"]['name'];
         $tempname=$_FILES["dispatcher_img"]["tmp_name"];
         $file_ext=pathinfo($filename,PATHINFO_EXTENSION);
         $filename=pathinfo($filename,PATHINFO_FILENAME);
         $filename=$six_digit_random_number;
         $name=$filename.".".$file_ext;
         $folder="dispatcher_img/".$name;
     move_uploaded_file($tempname, $folder);
    }
    else
    {
        $folder=$img_dir;
    }

if($password=="")
{
    $new_password=$password_old;
}
else
{
    $hash=Hash::make($password);
    $new_password=$hash;
}

     DB::select(" UPDATE  `dispatcher` SET  `img_dir`='".$folder."', `name`='".$name1."', `email`='".$email."', `phone`='".$phone."',`status`='".$status."',`password`='".$new_password."' WHERE `id`='".$id."' ");

     return redirect()->action("PagesController@dispatcherview")->with("str1",$id);

    }

    public function product(Request $request)
    {
        $type=$request->type;
        if($request->color_id==NULL)
        {
            $boolcolor="No";
        }
        else
        {
            $boolcolor="Yes";
        }
        if($request->size_id==NULL)
        {
            $boolsize="No";

        }
        else
        {
            $boolsize="Yes";
        }

            $maxItem=DB::select(" SELECT COALESCE(MAX(`itemcode`),0) AS 'maxI' FROM `item` ");
        foreach($maxItem as $ff)
        {
            $maxII=$ff->maxI;
        }
        $name1=$request->name;
        $description=$request->description;

        $category_id=$request->category_id;
        $subcategory_id=$request->subcategory_id;

        $six_digit_random_number = mt_rand(100000, 999999);
        $filename=$_FILES["product_img"]['name'];
        $tempname=$_FILES["product_img"]["tmp_name"];
        $file_ext=pathinfo($filename,PATHINFO_EXTENSION);
        $filename=pathinfo($filename,PATHINFO_FILENAME);
        $filename=$six_digit_random_number;
        $name=$filename.".".$file_ext;
        $folder="product_img/".$name;
        $folder2="../../public_html/product_img/".$name;
        move_uploaded_file($tempname, $folder);
    copy($folder,$folder2);
    $dfrt=addslashes($description);
DB::select(" INSERT INTO `item`(`img_dir`,`itemcode`,`item_name`,`price`,`discription`,`category_id`,`subcategory_id`,`color`,`size`,`type`) VALUES ('".$folder."','".($maxII+1)."','".$name1."','','".$dfrt."','".$category_id."','".$subcategory_id."','".$boolcolor."','".$boolsize."','".$type."') ");


    if($request->check_box=="on")
    {
        return redirect()->action("PagesController@product_detailview")->with("itemcode",$maxII+1);
}
else

{
    return redirect()->action("PagesController@add_productview")->with("str",$maxII);
}

    }

    public function product_detail(Request $request)
    {


        $itemcode=$request->itemcode;
        $price=$request->price;
        $size_id=$request->size;
        $color_id=$request->color;
         $qty=$request->qty;

         $sizeDetails=Size::where("id",$size_id)->first();

             $size_name=$sizeDetails->name;
        // }


       // $i=0;
    foreach($color_id as $key=>$jj)
    {
        $colorDetails=Color::where("id",$color_id[$key])->first();
        // $saaaa1=DB::select(" SELECT * FROM `color` WHERE `id`='".$color_id[$i]."' ");
        // foreach($saaaa1 as $fgg1)
        // {
             $color_name=$colorDetails->name;
        // }
        $productDetails=new ProductDetails;
        $productDetails->itemcode=$itemcode;
        $productDetails->color_id=$color_id[$key];
        $productDetails->size_id=$size_id;
        $productDetails->color_name=$color_name;
        $productDetails->size_name=$size_name;
        $productDetails->price=$price;
        $productDetails->qty=$qty;
        $productDetails->save();
        //DB::select(" INSERT INTO `product_details`(`itemcode`, `color_id`, `size_id`, `color_name`,`size_name`,`price`) VALUES ('".$itemcode."','".$color_id[$i]."','".$size_id."','".$color_name."','".$size_name."','".$price."') ");
   // $i++;
    }
    return redirect()->action("PagesController@product_detailview")->with("itemcode",$itemcode);
    }


    public function product_delete($id)
    {
        $itemDetails=Item::where("itemcode",$id)->first();
        // $admin=DB::select(" SELECT * FROM `item` WHERE `itemcode`='".$id."' ");
        //     foreach($admin as $ad)
        //     {
                $img_dir=$itemDetails->img_dir;
           // }
          unlink($img_dir);
            $folder2="../../users.shimsanmerchandise.com/public/".$img_dir;


Item::where("itemcode",$id)->delete();
 //    DB::select(" DELETE FROM  `item`  WHERE `itemcode`='".$id."' ");

     return redirect()->action("PagesController@product_recordsview")->with("str2",$id);

    }



    public function productajax(Request $request)
    {
        $id=$request->id;
        //$itemDetails=Item::where("itemcode",$id)->first();
        //$colorDetails=Color::where("itemcode",$id)->get();
        $e=DB::select(" SELECT * FROM `item` WHERE `itemcode`='".$id."' ");
        $e1=DB::select(" SELECT * FROM `color` WHERE `itemcode`='".$id."' ");



        $arr=array($e,$e1);
        return json_encode($arr);
    }



    public function size_delete($id)
    {


    Size::where("id",$id)->delete();
     //DB::select(" DELETE FROM  `size`  WHERE `id`='".$id."' ");

     return redirect()->action("PagesController@add_sizeview")->with("str2",$id);

    }


    public function size(Request $request)
    {
        $name=$request->name;
        $size=new Size;
        $size->name=$name;
        $size->save();
    // DB::select(" INSERT INTO `size`(  `name`) VALUES ('".$name."') ");

     return redirect()->action("PagesController@add_sizeview")->with("str",$name);

    }

    function update_size(Request $request)
    {
        $id=$request->id;
        $name=$request->name;
       $size=Size::where("id",$id)->first();
       $size->name=$name;
       $size->save();
       // $e=DB::select(" UPDATE `size` SET `name`='".$name."' WHERE `id`='".$id."' ");
        return redirect()->action("PagesController@add_sizeview")->with("str1",$e);
    }


    public function color(Request $request)
    {
        $name=$request->name;
 $color=Color::where("id",$id)->first();
 $color->name=$name;
 $color->save();
     //DB::select(" INSERT INTO `color`(  `name`) VALUES ('".$name."') ");

     return redirect()->action("PagesController@add_colorview")->with("str",$name);

    }

    function update_color(Request $request)
    {
        $id=$request->id;
        $name=$request->name;
        //$id=$request->id;
        $color=Color::where("id",$id)->first();
 $color->name=$name;
 $color->save();

 //$productDetails=ProductDetails::where("color_id",$id)->first();

       // $e=DB::select(" UPDATE `color` SET `name`='".$name."' WHERE `id`='".$id."' ");
        DB::select(" UPDATE `product_details` SET `color_name`='".$name."' WHERE `color_id`='".$id."' ");
        return redirect()->action("PagesController@add_colorview")->with("str1",$e);
    }



    public function sizeajax(Request $request)
    {
        $id=$request->id;
        $e=DB::select(" SELECT * FROM `size` WHERE `id`='".$id."' ");
        $arr=array($e);
        return json_encode($arr);
    }

    public function colorajax(Request $request)
    {
        $id=$request->id;
        $e=DB::select(" SELECT * FROM `color` WHERE `id`='".$id."' ");
        $arr=array($e);
        return json_encode($arr);
    }




    function update_coupan(Request $request)
    {
        $id=$request->id;
        $name=$request->name;
        $discount=$request->discount;
        $valid=$request->valid;
        $e=DB::select(" UPDATE `coupan` SET `coupan`='".$name."',`discount`='".$discount."',`valid`='".$valid."' WHERE `id`='".$id."' ");
        return redirect()->action("PagesController@add_coupanview")->with("str1",$e);
    }

    public function coupan(Request $request)
    {
        $name=$request->name;
        $discount=$request->discount;
        $valid=$request->valid;

     DB::select(" INSERT INTO `coupan`(`coupan`,`discount`,`valid`) VALUES ('".$name."','".$discount."','".$valid."') ");

     return redirect()->action("PagesController@add_coupanview")->with("str",$name);

    }

    public function coupan_delete($id)
    {



     DB::select(" DELETE FROM  `coupan`  WHERE `id`='".$id."' ");

     return redirect()->action("PagesController@add_coupanview")->with("str2",$id);

    }

    public function accept_user($id)
    {

    DB::select(" UPDATE `customer` SET `status`='Accepted' WHERE `id`='".$id."' ");
    return redirect()->action("PagesController@pending_user")->with("str",$id);

    }

    public function cancell_user($id)
    {



     DB::select(" UPDATE `customer` SET `status`='Cancelled' WHERE `id`='".$id."' ");

     return redirect()->action("PagesController@pending_user")->with("str2",$id);

    }

     public function delete_user($id)
    {



     DB::select(" UPDATE `customer` SET `status`='Deleted' WHERE `id`='".$id."' ");

     return redirect()->action("PagesController@pending_user")->with("str2",$id);

    }

    public function add_custom_product(Request $request)
    {
        $fields=$request->fields;
        $type=$request->type;
        if($request->color_id==NULL)
        {
            $boolcolor="No";
        }
        else
        {
            $boolcolor="Yes";
        }
        if($request->size_id==NULL)
        {
            $boolsize="No";
        }
        else
        {
            $boolsize="Yes";

        }

            $maxItem=DB::select(" SELECT COALESCE(MAX(`itemcode`),0) AS 'maxI' FROM `item` ");
        foreach($maxItem as $ff)
        {
            $maxII=$ff->maxI;
        }
        $name1=$request->name;
        $description=$request->description;
        $nn=addslashes($description);
        $category_id=$request->category_id;
        $subcategory_id=$request->subcategory_id;

        $six_digit_random_number = mt_rand(100000, 999999);
        $filename=$_FILES["product_img"]['name'];
        $tempname=$_FILES["product_img"]["tmp_name"];
        $file_ext=pathinfo($filename,PATHINFO_EXTENSION);
        $filename=pathinfo($filename,PATHINFO_FILENAME);
        $filename=$six_digit_random_number;
        $name=$filename.".".$file_ext;
        $folder="product_img/".$name;
        $folder2="../../public_html/product_img/".$name;
        move_uploaded_file($tempname, $folder);
    copy($folder,$folder2);
DB::select(" INSERT INTO `item`(`img_dir`,`itemcode`,`item_name`,`price`,`discription`,`category_id`,`subcategory_id`,`color`,`size`,`product_type`,`type`) VALUES ('".$folder."','".($maxII+1)."','".$name1."','','".$nn."','".$category_id."','".$subcategory_id."','".$boolcolor."','".$boolsize."','Custom','".$type."') ");


$iii=0;
foreach($fields as $ffrt)
{
    DB::select(" INSERT INTO `custom_details`( `itemcode`, `field`,`type`) VALUES ('".($maxII+1)."','".$fields[$iii]."','".$type."') ");
$iii++;
}

    if($request->check_box=="on")
    {
        return redirect()->action("PagesController@product_detailview")->with("itemcode",$maxII+1);
}
else

{
    return redirect()->action("PagesController@add_custom_productview")->with("str",$maxII);
}



    }

    public function custom_fields_delete($id)
    {
        $e3=DB::select("SELECT * FROM `custom_details` WHERE `c_d_id`='".$id."'  ");
        foreach($e3 as $dd)
        {
            $itemcode=$dd->itemcode;
        }
    DB::select(" DELETE FROM `custom_details` WHERE `c_d_id`='".$id."' ");
      $e=DB::select(" SELECT * FROM `product_details` WHERE `itemcode`='".$itemcode."' ");
     $e2=DB::select(" SELECT * FROM `item` WHERE `itemcode`='".$itemcode."' ");
        return view("pages/item/update_details")->with("itemcode",$itemcode)->with("item",$e)->with("item2",$e2)->with("str12",$e);

    }

    public function add_custom_field(Request $request)
    {
        $field=$request->field;
        $itemcode=$request->itemcode;
        $e44=DB::select(" SELECT count(`c_d_id`) AS 'check' FROM `custom_details` WHERE `itemcode`='".$itemcode."' ");
        foreach($e44 as $wq)
        {
            $check=$wq->check;
        }
        if($check>0)
        {
         $e3=DB::select(" SELECT * FROM `custom_details` WHERE `itemcode`='".$itemcode."' ");
         foreach($e3 as $dd)
         {
             $type=$dd->type;
         }
        }
        else
        {
            $type="Uniform";
        }
         DB::select(" INSERT INTO `custom_details`(`itemcode`, `field`, `type`) VALUES ('".$itemcode."','".$field."','".$type."') ");
      $e=DB::select(" SELECT * FROM `product_details` WHERE `itemcode`='".$itemcode."' ");
     $e2=DB::select(" SELECT * FROM `item` WHERE `itemcode`='".$itemcode."' ");
        return view("pages/item/update_details")->with("itemcode",$itemcode)->with("item",$e)->with("item2",$e2)->with("str17",$e);
    }

public function remove_order($id)
{
   // dd($id);
    DB::select(" UPDATE `order_head` SET `status`='cancelled' WHERE `id`='".$id."' ");
    DB::select(" UPDATE `orders` SET `status`='cancelled' WHERE `orderid`='".$id."' ");
    return redirect()->back();
}

public function set_to_back($id,$qty)
{
    //dd($qty);
     $arr=explode(",",$id);
        //dd($arr);
        $ar_id=$arr[0];
        $random=$arr[1];
    //DB::select(" UPDATE `order_head` SET `status`='Removed' WHERE `orderid`='".$id."' ");
    DB::select(" UPDATE `orders` SET `remove_back`='Yes',`back`='Yes',`back_qty`='".$qty."' WHERE `orderid`='".$ar_id."' AND `random`='".$random."' ");
    return redirect()->back();
}

public function updatebackqty(Request $request)
{
    $back_arr=$request->unique_key;
    //dd($back_arr);
    $actual_qty=$request->actual_qty;
    $back_qty=$request->back_qty;
      $i=0;
        $arr=explode(",",$back_arr);
        $ar_id=$arr[0];
        $random=$arr[1];
        DB::select(" UPDATE `orders` SET `back`='Yes',`back_qty`='".$back_qty."' WHERE `orderid`='".$ar_id."' AND `random`='".$random."' ");
    return redirect()->back();
    //DB::select("  ")
}

public function back_to_deliver($id)
{
         $arr=explode(",",$id);
        $ar_id=$arr[0];
        $random=$arr[1];
        DB::select(" UPDATE `orders` SET `back`='No',`back_qty`='0' WHERE `orderid`='".$ar_id."' AND `random`='".$random."' ");
    return redirect()->back();
}

}
