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

use App\Models\Color;

use App\Models\CustomCartDetails;

use App\Models\ProductDetails;

use App\Models\CustomOrders;

use App\Models\CustomPrices;

use App\Models\TempCart;

use App\Models\StockHistory;

use App\Models\Item;

use App\Models\MultiplePictures;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DataController extends Controller

{






public function register(Request $request)
{
    $username=$request->username;
    $email=$request->email;
    $pass=$request->password;
    $re_password=$request->re_password;

    $customerUserCheck=Customer::where("username",$username)->first();

//    $check=DB::select(" SELECT count(customer_id) AS 'ch_us' FROM `customer` WHERE `username`='".$username."' ");
    // foreach($check as $cc)
    // {
    //         $c=$cc->ch_us;
    // }
        if(!empty($customerUserCheck))
        {
    //               activity()
    // ->useLog(" Register module  : username checking ")
    // ->performedOn($customerUserCheck)
    // ->withProperties(['attributes' => $customerUserCheck])
    // ->log(" Username already exists: Registration failed. ");
                return redirect()->action("PagesController@login_register")->with("str",$c);
        }
        $customerEmailCheck=Customer::where("email",$email)->first();
        //$check1=DB::select(" SELECT count(customer_id) AS 'ch_em' FROM `customer` WHERE `email`='".$email."' ");
    // foreach($check1 as $cc1)
    // {
    //         $c1=$cc1->ch_em;
    // }
        if(!empty($customerEmailCheck))
        {
    //          activity()
    // ->useLog(" Register module  : email checking ")
    // ->performedOn($customerEmailCheck)
    // ->withProperties(['attributes' => $customerEmailCheck])
    // ->log(" Email already exists: Registration failed. ");
                return redirect()->action("PagesController@login_register")->with("str1",$c1);
        }
    if($pass!=$re_password)
    {
    //       activity()
    // ->useLog(" Register module  : email checking ")
    // ->performedOn([])
    // ->withProperties(['attributes' => []])
    // ->log(" Passwords do not match with each other. : Registration failed. ");
        return redirect()->action("PagesController@login_register")->with("str2",$c1);
    }

    else
    {

        $password=hash('gost', $pass);
        $customer=new Customer;
        $customer->email=$email;
        $customer->password=$password;
        $customer->username=$username;
        $customer->real_password=$pass;
        $customer->save();
    //           activity()
    // ->useLog(" Customer created ")
    // ->performedOn($customer)
    // ->withProperties(['attributes' => $customer])
    // ->log(" A customer has been registered successfully.id is $customer->id ");

    $cookie_name = "user";
$cookie_value = $customer->id;
setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
       // DB::select(" INSERT INTO `customer`(`customer_id`,`email`, `password`, `username`) VALUES ('".$ord."','".$email."','".$password."','".$username."') ");
        return redirect()->action("PagesController@login_register")->with("str3",$ord);
    }

}


    public function logout(Request $request)
    {
          $c_id=$_COOKIE["user"];
        $customer=Customer::where("id",$c_id)
        ->first();
        $id= $customer->username;
        $customerId=$_COOKIE['user'];
        unset($_COOKIE['user']);
        setcookie('user', '', time() - 3600, '/');
         $user = $request->username;
        $password =$request->password;
        $ip = $request->ip();
         Log::channel('customer')->info('customer logout', ['user' => 'customer', 'name' => $id, 'ipaddress' => $ip, 'type' => 'general', 'section' => 'logout', 'description' => 'Customer Logout Succesfully', 'date' => now()->toDateString(), 'time' => now()->toTimeString()]);

        return redirect()->back();
    }

    public function login(Request $request)
    {
        $user=$request->username;
        $password=$request->password;
        //$pass=Hash::make($password);
        $hash=hash('gost', $password);

        $customer=Customer::where("username",$user)
        ->where("password",$hash)
        ->first();
        if(empty($customer))
        {
        //         activity()
        // ->useLog(" Login module ")
        // ->performedOn([])
        // ->withProperties(['attributes' => []])
        // ->log(" Wrong credentials :Login failed . username is $user and password is $password ");
             $ip = $request->ip();
            Log::channel('customer')->info('Wrong Credential', ['user' => 'customer', 'name' => $request->username, 'ipaddress' => $ip, 'type' => 'general', 'section' => 'login', 'description' => 'Wrong Credential' .json_encode($request->all()), 'date' => now()->toDateString(), 'time' => now()->toTimeString()]);

            return redirect()->back()->with("error","Wrong credentials.");
        }
       // $aa=DB::select(" SELECT count(`customer`.`customer_id`) AS 'cc_ss',`customer`.* FROM `customer` WHERE `username`='".$user."' AND `password`='".$hash."' ");
        // foreach($aa as $hh)
        // {
        //     $cc=$hh->cc_ss;
        //     $id=$hh->customer_id;
        //     $status=$hh->status;
        // }
        if($customer->status=="Pending")
        {
        //       activity()
        // ->useLog(" Login module ")
        // ->performedOn($customer)
        // ->withProperties(['attributes' => $customer])
        // ->log(" Credentials matched successfully but the user id $customer->id is in pending list : Login failed. ");
        return redirect()->back()->with("error","  Your Account's Approval is in Pending .");
        //  return redirect()->action("PagesController@login_register")->with("error",$user);
        }
        if(!empty($customer))
        {
            //dd("Yes");

                $cookie_name = "user";
                $cookie_value = $customer->id;
                $customer->real_password=$password;
                $customer->save();
        //                       activity()
        // ->useLog(" Customer loged in successfully ")
        // ->performedOn($customer)
        // ->withProperties(['attributes' => $customer])
        // ->log(" A customer has been logged in successfully.id is $customer->id ");
                setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
                        $ip = $request->ip();

                  Log::channel('customer')->info('Successful Login', ['user' => 'customer', 'name' => $request->username, 'ipaddress' => $ip, 'type' => 'general', 'section' => 'login', 'description' => 'Login Successful', 'date' => now()->toDateString(), 'time' => now()->toTimeString()]);

                return redirect()->action("Front\PagesController@index");

            }
            else
            {
                //dd("No");
                return redirect()->action("Front\PagesController@login_register")->with("str4",$user);
            }



    }

    public function product_details($id)
    {
        $item=Item::where("id",$id)->get();
        $multiplePictures=MultiplePictures::where("itemcode",$id)->get();
        //$e=DB::select(" SELECT * FROM `item` WHERE `itemcode`='".$id."' ");
        return view("pages/front/product_details",compact("multiplePictures"))->with("item",$item);
    }

public function update_profile(Request $request)
{
   // dd($request);
    $username=$request->username;
    $fname=$request->fname;
    $lname=$request->lname;
    $email=$request->email;
    $phone=$request->phone;
    $address=$request->address;
    $address2=$request->address2;
    $town=$request->town;
    $state=$request->state;
    $postcode=$request->postcode;
    $account_no=$request->acccount_no;
    $credit_card=$request->credit_card;

    $customer=Customer::where("id",$_COOKIE["user"])
    ->first();
    $username_cust= $customer->username;

    $customer->username=$username;
    $customer->postcode=$postcode;
    $customer->email=$email;
    $customer->fname=$fname;
    $customer->lname=$lname;
    $customer->address1=$address;
    $customer->address2=$address2;
    $customer->phone=$phone;
    $customer->town=$town;
    $customer->state=$state;
    $customer->account_no=$account_no;
    $customer->credit_card=$credit_card;
    $customer->save();
        //               activity()
        // ->useLog(" Customer profile ")
        // ->performedOn($customer)
        // ->withProperties(['attributes' => $customer])
        // ->log(" A customer profile has been updated .id is $customer->id ");
        
        
        $ipaddress = $request->ip();
        try {
          
          Log::channel('customer')->info('Updated Profile', [
              'user' => 'customer',
              'name' => $username_cust,
              'ipaddress' => $ipaddress,
              'type' => 'general',
              'section' => 'Profile Customer',
              'description' => $request->all(),
              'date' => now()->toDateString(),
              'time' => now()->toTimeString()
          ]);
      } catch (Exception $e) {
          // Log the error with all columns filled
          Log::channel('customer')->error('Unexpected Error', [
              'user' => 'customer',
              'name' => $username_cust,
            'ipaddress' => $ipaddress,
              'type' => 'error',
              'section' => 'Customer Profile',
              'description' => 'Unexpected error occurred: ' . $e->getMessage(),
              'date' => now()->toDateString(),
              'time' => now()->toTimeString()
          ]);
      
        }
     return redirect()->back()->with("success","Profile updated");
     //return redirect()->action("DataController@myaccount")->with("str9",$username);

}
public function update_account(Request $request)
{
    $username=$request->username;
    $email=$request->email;
    $current_password=$request->currentpassword;
    $hash=hash("gost",$current_password);



    $c_id=$_COOKIE["user"];
$customer=Customer::where("id",$c_id)
->first();
    $username_cust= $customer->username;

//$us=DB::select(" SELECT * FROM `customer` WHERE `customer_id`='".$c_id."' ");
// foreach($us as $uu)
// {
//     $pass=$uu->password;
// }

if($customer->password!=$hash)
{
//dd("Yes");
    //       activity()
    // ->useLog(" Update account : Failed ")
    // ->performedOn($customer)
    // ->withProperties(['attributes' => $customer])
    // ->log(" Current password do not match.id is $customer->id ");
return redirect()->action("DataController@myaccount")->with("str",$hash);
}





if($request->newpassword!=$request->confirmpassword)
{
    //   activity()
    // ->useLog(" Update account : Failed ")
    // ->performedOn([])
    // ->withProperties(['attributes' => []])
    // ->log(" New passwords do not match with each other ");
return redirect()->action("DataController@myaccount")->with("str1",$hash);
}
else
{
$new=$request->newpassword;
$hash1=hash("gost",$new);
$customer->email=$email;
$customer->password=$hash1;
$customer->real_password=$new;
$customer->username=$username;
$customer->save();
    //   activity()
    // ->useLog(" Update account : successful ")
    // ->performedOn($customer)
    // ->withProperties(['attributes' => $customer])
    // ->log(" User updated successfully . the customer id is $customer->id ");
//DB::select(" UPDATE `customer` SET `email`='".$email."',`password`='".$hash1."',`username`='".$username."' WHERE `customer_id`='".$c_id."' ");

$ipaddress = $request->ip();
try {
          
    Log::channel('customer')->info('Account Credential Updated', [
        'user' => 'customer',
        'name' => $username_cust,
        'ipaddress' => $ipaddress,
        'type' => 'general',
        'section' => 'Account Passwords changed',
        'description' => $request->all(),
        'date' => now()->toDateString(),
        'time' => now()->toTimeString()
    ]);
} catch (Exception $e) {
    // Log the error with all columns filled
    Log::channel('customer')->error('Unexpected Error', [
        'user' => 'customer',
        'name' => $username_cust,
        'ipaddress' => $ipaddress,
        'type' => 'error',
        'section' => 'Account Credential Updated error',
        'description' => 'Unexpected error occurred: ' . $e->getMessage(),
        'date' => now()->toDateString(),
        'time' => now()->toTimeString()
    ]);

  }
return redirect()->route('front.account')->with("str2",$hash);
}
}




    public function coupan(Request $request)
    {
        session_set_cookie_params(3600 * 24 * 7);
        session_start();
        $cou_name=$request->name;

        $ee=DB::select(" SELECT count(id) AS 'c_id',`coupan`.* FROM `coupan` WHERE `coupan`='".$cou_name."' ");
        foreach($ee as $nn)
        {
            $cou=$nn->c_id;
            $discount=$nn->discount;
        }

        if($cou>0)
        {
                $_SESSION["coupan"]=$discount;
                return redirect()->action("PagesController@cart")->with("cou",$cou_name);
        }
        else
        {
             return redirect()->action("PagesController@cart")->with("cou2",$cou_name);
        }

    }













// public function custom_name(Request $request)

// {

//     $uniform_type=$request->uniform_type;

//     $qty=$request->qty;

//     $size=$request->size;

//     $city=$request->city;

//     $state=$request->state;

//     $itemcode=$request->itemcode;



//     $itdb=DB::select(" SELECT * FROM `item` WHERE `itemcode`='".$itemcode."' ");

//     foreach($itdb as $hhyy)

//     {

//         $name=$hhyy->item_name;

//     }



//     $ata=$request->ata;

//     $color="Custom Product";

//     $nn=$uniform_type."".$size;

//      $product=array($uniform_type,$qty,$color,$size,$nn,$city,$state,$name,$ata);

//     $_SESSION[$nn]=$product;





//         $option="";

//     //$itemcode=$request->itemcode;

//     $si=DB::select(" SELECT * FROM `product_details` WHERE `itemcode`='".$itemcode."' ");

//     foreach($si as $sizessee)

//     {

//                                 $option.="<option  value='.$sizessee->p_d_id.'>$sizessee->size_name</option>";

//     }

//     return json_encode($option);









//     //return json_encode($_SESSION);

// }





public function forgot_password(Request $request)
{
    $email=$request->email;

    // $fr=DB::select(" SELECT COALESCE(COUNT(`customer`.`customer_id`),0) AS 'check',`customer`.* FROM `customer` WHERE `email`='".$email."' ");
    // foreach($fr as $ff)
    // {
    //     $check=$ff->check;
    // }
       $customer=Customer::where("email",$email)->first();
    if(!empty($customer))
    {
        $six_digit_random_number = random_int(100000, 999999);
        $to = $email;
        $headers = 'From: <customercare@shimsanmerchandise.com>';
        $subject = "Six Digit Code";
        $message="Your Six Digit Code is $six_digit_random_number ";
        mail($to,$subject,$message,$headers);
        $str="asd";
         // $customer=Customer::where("email",$to)->first();
          $customer->code=$six_digit_random_number;
          $customer->save();
        //       activity()
        // ->useLog(" Forget password : email sent ")
        // ->performedOn($customer)
        // ->withProperties(['attributes' => $customer])
        // ->log(" Email sent successfully . the customer id is $customer->id ");
      //  DB::select(" UPDATE `customer` SET code='".$six_digit_random_number."' WHERE email='".$to."' ");
        // Mail::send("emails.forgetEmails",$six_digit_random_number,function($mailMessage)
        // {
        //     $mailMessage->to($to,"Shimsaan Merchandise")->subject("Six Digit code");
        // });
         return redirect()->action("PagesController@six")->with("str",$str);
    }
    else
    {
        //      activity()
        // ->useLog(" Forget password : email sent failed ")
        // ->performedOn([])
        // ->withProperties(['attributes' => []])
        // ->log(" Email sent failed  ");
        $str="asd";
        return redirect()->action("PagesController@forgot_password")->with("str2",$str);
    }

}



public function six_digit(Request $request)
{
    $code=$request->six;
    $customer=Customer::where("code",$code)->first();

    // $fr=DB::select(" SELECT COALESCE(COUNT(customer_id),0) AS 'check',`customer`.* FROM `customer` WHERE `code`='".$code."' ");
    // foreach($fr as $ff)
    // {
    //     $check=$ff->check;
    //     $email=$ff->email;
    // }
    if(!empty($customer))
    {
        $str="sad";
        //         activity()
        // ->useLog(" Code match module : Code matched successfully ")
        // ->performedOn($customer)
        // ->withProperties(['attributes' => $customer])
        // ->log(" Code matched . the user id is $customer->id  ");
        session()->put('forget_email', $customer->email);
    return view("pages/front/new_password")->with("str",$str);
    }
    else
    {
        $str="sda";
        return redirect()->action("PagesController@six")->with("str2",$str);
    }

}



public function password_set(Request $request)

{

    //$password=$

    $password=$request->new_password;

    $re_password=$request->re_password;

    $email=$request->email;



    if($password==$re_password)
    {
      $hash=hash('gost', $password);
        $str="asd";
        $customer=Customer::where("email",$email)->first();
        $customer->password=$hash;
        $customer->code='';
        $customer->real_password=$password;
        $customer->save();
        //   activity()
        // ->useLog(" Password update module ")
        // ->performedOn($customer)
        // ->withProperties(['attributes' => $customer])
        // ->log(" Password updated for the user $customer->id  ");
   // DB::select(" UPDATE `customer` SET `password`='".$hash."',`code`='' WHERE `email`='".$email."' ");
    session()->forget('forget_email');
       return redirect()->action("PagesController@login_register")->with("str9",$str);
    }
    else
    {
        //          activity()
        // ->useLog(" Password update module : Failed ")
        // ->performedOn([])
        // ->withProperties(['attributes' => []])
        // ->log(" Passwords do not match.  ");
        $str="sda";
        return view("pages/front/new_password")->with("str3",$str)->with("email",$email);
    }



}







}

