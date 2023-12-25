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
class DashboardController extends Controller
{

    public function dashboard()
    {


      $customer=Customer::where("id",$_COOKIE["user"])
      ->first();
      return view("pages/front/account",compact("customer"));

    }
    
      public function admin_logout(Request $request)
    {
      $auth = Auth::user()->id;
      $username_cust = Auth::user()->name;
      $auth_role_id = Auth::user()->role_id;
                       
      Auth::logout();
 if($auth_role_id == 1)
                  {
                      $auth_role_id = 'SuperAdmin';
                  }
                  elseif($auth_role_id == 2)
                  {
                      $auth_role_id = 'Admin';
                  }
                  elseif($auth_role_id == 3)
                  {
                      $auth_role_id = 'Representative';
                  }
                  elseif($auth_role_id == 5)
                  {
                      $auth_role_id = 'Worker';
                  }
                  else {
                      $auth_role_id = 'Normal';
                  }
                  $ipaddress = $request->ip();
                  try {
            
                      Log::channel('customer')->info('Admin Logout', [
  
                          
                          'user' => $auth_role_id,
                          'name' => $username_cust,
                          'ipaddress' => $ipaddress,
                          'type' => 'general',
                          'section' => 'Admin',
                          'description' => 'LogedOut Succesfully',
                          'date' => now()->toDateString(),
                          'time' => now()->toTimeString()
                      ]);
                  } catch (\Exception $e) {
                      // Log the error with all columns filled
                      Log::channel('customer')->error('Unexpected Error', [
                          'user' => 'customer',
                          'name' => $username_cust,
                          'ipaddress' => $ipaddress,
                          'type' => 'error',
                          'section' => 'Admin update',
                          'description' => 'Unexpected error occurred: ' . $e->getMessage(),
                          'date' => now()->toDateString(),
                          'time' => now()->toTimeString()
                      ]);
                    }






        return redirect('/admin-panel');
    }


    //End of controller
}
