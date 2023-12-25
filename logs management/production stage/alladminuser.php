<?php

namespace App\Http\Controllers\adminroles;

use Image;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\admin_access;
use App\Models\Dispatcher;
use App\Models\RoleHasPermission;
use Illuminate\Support\Facades\Hash;
use DB;
use Auth;

class alladminuser extends Controller
{
    //
    public function index()
    {
        // $data2 = DB::table('admin_accesses')
                    
        // ->where([
        //   ['admin_id',Auth::user()->id] ,
        //    ])
        //   ->first();

        $data3 = User::all();
        $data4 = admin_access::all();
              $data2 = DB::table('admin')
                    ->join('admin_accesses', 'admin.id', '=', 'admin_accesses.admin_id')
                      ->get();
        $admin = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
          ['admin',1]
           ])
          ->first();
        if ($admin ) {
            $admin = User::latest()->get();
            $role=Role::where("isDelete",0)->get();
            return view('pages.adminroles.index',compact('admin','role','data2','data3','data4'));
    
        } else {
            return abort(403, 'Unauthorized action.'); // or redirect to some other page
        }
    }

    public function addadmin()
    {
        $customer=Customer::select([
            DB::raw(" CONCAT(fname,' ',lname) as customer_name "),
            "id"
            ])
            ->where("status","Accepted")
            ->whereNull("dispatcher_id")
            ->get();
        $role=Role::where("isDelete",0)->get();
        $data2 = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
           ])
          ->first();

         
        return view('pages.adminroles.addadmin',compact('role','customer','data2'));
        // dd($data2)
    }

    public function store(Request $request)
    {

        $password_hash = $request->password;
        $hash=hash('gost', $password_hash);

        $image = $request->file('img_dir');
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();  // 3434343443.jpg

        Image::make($image)->resize(300,300)->save('admin_img/'.$name_gen);
        $save_url = 'admin_img/'.$name_gen;
        $roleid = $request->role_id;
    
         
            $role_id = $request->role_id;
          
            $admin_id = User::insertGetId([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                    'img_dir' => $save_url,
                    'real_password' => $request->password,
                    'role_id' => $request-> role_id,
                    // 'type' => 2,
                    'created_at' => Carbon::now(),
    
                ]); 
          
                admin_access::insert([
                    'admin_id' => $admin_id,
                    'admin' => $request->admin,
                    'add_user' => $request->add_users,
                    'pending_user' => $request->pending_users,
                    'user_records' => $request->user_records,
                    'back_orders' => $request->back_orders,
                    'pending_orders' => $request->pending_orders,
                    'processing_orders' => $request->processing_orders,
                    'shipped_orders' => $request->shipped_orders,
                    'canceled_orders' => $request->cancelled_orders,
                    'orders_by_date' => $request->orders_by_date,
                    'category' => $request->category,
                    'category_records' => $request->category_records,
                    'sub_category' => $request->sub_category,
                    
                    'sub_category_records' => $request->sub_category_records,
                    'add_category_to_sub_category' => $request->add_category_to_sub_category,
                    'add_product' => $request->add_products,
                    'product_records' => $request->product_records,
                    'add_custom_products' => $request->add_custom_products,
                    'custom_products_records' => $request->custom_products_records,
                    'prices_to_custom_fields' => $request->prices_to_custom_fields,
                    'dispatcher' => $request->dispatcher,
                    'emails_side' => $request->email_side,
                    // 'roles' => $request->roles,
                    // 'permissions' => $request->permissions,
                    'size' => $request->size,
                    'color' => $request->color,
                    'coupon' => $request->coupon,
                    // 'assign_permission' => $request->assign_permission,
                    'logs' => $request->logs,
                    'warehouse' => $request->warehouse,
                    'aboutus' => $request->aboutus,
                    'contactus' => $request->contactus,
                    'setting' => $request->setting,
                    'echange_policy' => $request->echange_policy,
                    'role_id' => $request->role_id,
                  
                ]);
           
        return redirect()->route('all.admin.user')->with("str",$save_url);    
      
    }

    public function edit($id)
    {
        $role=Role::where("isDelete",0)->get();
        $data = User::findOrFail($id);
        // $data2 = DB::table('admin_accesses')
                    
        // ->where([
        //   ['admin_id',Auth::user()->id] ,
        //    ])
        //   ->first();

          $data2 = admin_access::where('admin_id',$id)->first();

        return view('pages.adminroles.editadminrole',compact('data','role','data2'));
    }

    public function update(Request $request,$id)
    {
       
        $password_hash = $request->password;
        $hash=hash('gost', $password_hash);

        $admin_id = $request->id;
        $old_image = $request->old_image;

        if($request->file('img_dir'))
        {
            unlink($old_image);
            
            $image = $request->file('img_dir');
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();  // 3434343443.jpg
    
            Image::make($image)->resize(300,300)->save('admin_img/'.$name_gen);
            $save_url = 'admin_img/'.$name_gen;
            $roleid = $request->role_id;
        
             
                $role_id = $request->role_id;
              
                    User::FindOrFail($id)->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'password' => Hash::make($request->password),
                        'img_dir' => $save_url,
                        'real_password' => $request->password,
                        'role_id' => $request-> role_id,
                        // 'type' => 2,
                        'updated_at' => Carbon::now(),
        
                    ]); 

                    admin_access::Where('admin_id',$admin_id)->update([
                     
                        'admin' => $request->admin,
                        'add_user' => $request->add_user,
                        'pending_user' => $request->pending_user,
                        'user_records' => $request->user_records,
                        'back_orders' => $request->back_orders,
                        'pending_orders' => $request->pending_orders,
                        'processing_orders' => $request->processing_orders,
                        'shipped_orders' => $request->shipped_orders,
                        'canceled_orders' => $request->cancelled_orders,
                        'orders_by_date' => $request->orders_by_date,
                        'category' => $request->category,
                        'category_records' => $request->category_records,
                        'sub_category' => $request->sub_category,
                        
                        'sub_category_records' => $request->sub_category_records,
                        'add_category_to_sub_category' => $request->add_category_to_sub_category,
                        'add_product' => $request->add_product,
                        'product_records' => $request->product_records,
                        'add_custom_products' => $request->add_custom_products,
                        'custom_products_records' => $request->custom_products_records,
                        'prices_to_custom_fields' => $request->prices_to_custom_fields,
                        'dispatcher' => $request->dispatcher,
                        'emails_side' => $request->email_side,
                        // 'roles' => $request->roles,
                        // 'permissions' => $request->permissions,
                        'size' => $request->size,
                        'color' => $request->color,
                        'coupon' => $request->coupon,
                        // 'assign_permission' => $request->assign_permission,
                        'logs' => $request->logs,
                        'warehouse' => $request->warehouse,
                        'aboutus' => $request->aboutus,
                        'contactus' => $request->contactus,
                        'setting' => $request->setting,
                        'echange_policy' => $request->echange_policy,
                        'role_id' => $request->role_id,
                        'updated_at' => Carbon::now(),
                    ]);
        return redirect()->route('all.admin.user')->with("str",$save_url);    
              
        }
        else 

        {
            $role_id = $request->role_id;
              
                    User::FindOrFail($id)->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'password' => Hash::make($request->password),
                        'real_password' => $request->password,
                        'role_id' => $request-> role_id,
                        // 'type' => 2,
                        'updated_at' => Carbon::now(),
        
                    ]); 

                    admin_access::Where('admin_id',$admin_id)->update([
                      
                        'admin' => $request->admin,
                        'add_user' => $request->add_user,
                        'pending_user' => $request->pending_user,
                        'user_records' => $request->user_records,
                        'back_orders' => $request->back_orders,
                        'pending_orders' => $request->pending_orders,
                        'processing_orders' => $request->processing_orders,
                        'shipped_orders' => $request->shipped_orders,
                        'canceled_orders' => $request->cancelled_orders,
                        'orders_by_date' => $request->orders_by_date,
                        'category' => $request->category,
                        'category_records' => $request->category_records,
                        'sub_category' => $request->sub_category,
                        
                        'sub_category_records' => $request->sub_category_records,
                        'add_category_to_sub_category' => $request->add_category_to_sub_category,
                        'add_product' => $request->add_product,
                        'product_records' => $request->product_records,
                        'add_custom_products' => $request->add_custom_products,
                        'custom_products_records' => $request->custom_products_records,
                        'prices_to_custom_fields' => $request->prices_to_custom_fields,
                        'dispatcher' => $request->dispatcher,
                        'emails_side' => $request->email_side,
                        // 'roles' => $request->roles,
                        // 'permissions' => $request->permissions,
                        'size' => $request->size,
                        'color' => $request->color,
                        'coupon' => $request->coupon,
                        // 'assign_permission' => $request->assign_permission,
                        'logs' => $request->logs,
                        'warehouse' => $request->warehouse,
                        'aboutus' => $request->aboutus,
                        'contactus' => $request->contactus,
                        'setting' => $request->setting,
                        'echange_policy' => $request->echange_policy,
                        'role_id' => $request->role_id,
                        'updated_at' => Carbon::now(),
                    ]);
        return redirect()->route('all.admin.user')->with("str1",$role_id);    
              
        }
    }

    // public function delete($id)
    // {
    //     $admimg = User::FindOrFail($id);
        
    //     $img = $admimg->img_dir;
    //     if($img == true)
    //     {
    //         unlink($img);
    //     }

    //     User::FindOrFail($id)->delete();
    //     return redirect()->back()->with("str2",$img);    

    // }
    public function delete($id)
    {
        $admimg = User::FindOrFail($id);
        
        $img = $admimg->img_dir;
        if($img == true)
        {
            unlink($img);
        }

        User::FindOrFail($id)->delete();
        $data2 = admin_access::where('admin_id',$id)->first();
        $data2->delete();
        return redirect()->back()->with("str2",$img);    

    }
}
