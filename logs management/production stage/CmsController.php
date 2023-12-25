<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cms;
use DB;
use Auth;
use App\Models\Role;
class CmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    // @auther faisal 
 public function aboutUs()
    {
       
        $aboutus = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
          ['aboutus',1]
           ])
          ->first();
        if ($aboutus) {
            $role=Role::where("isDelete",0)->get();
            // return view("pages.user_management.index",compact("role"));
            $aboutUs=Cms::select([
                "about_us",
                "id"
            ])->first();
            return view("pages.cms.about",compact("aboutUs","role"));

        } else {
            return abort(403, 'Unauthorized action.'); // or redirect to some other page
        }

    }
    public function aboutStore(Request $request)
    {
       // dd($request);
        Cms::where("id",$request->id)
        ->update([
            "about_us"=>$request->content
        ]);
        return redirect()->back()->with("success","Content updated successfully");
    }
    // @auther faisal 
     public function contactUs()
    {
        
        $contactus = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
          ['contactus',1]
           ])
          ->first();
        if ($contactus ) {
            $role=Role::where("isDelete",0)->get();
            $contactUs=Cms::select([
                "contact_us",
                "id"
            ])->first();
            return view("pages.cms.contact",compact("contactUs","role"));
            // return view("pages.user_management.index",compact("role"));

        } else {
            return abort(403, 'Unauthorized action.'); // or redirect to some other page
        }

    }
    public function contactStore(Request $request)
    {
       // dd($request);
        Cms::where("id",$request->id)
        ->update([
            "contact_us"=>$request->content
        ]);
        return redirect()->back()->with("success","Content updated successfully");
    }
    // @auther faisal 
    public function returnAndExchangePolicy()
    {
       
        $echange_policy = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
          ['echange_policy',1]
           ])
          ->first();

        if ($echange_policy ) {
            $role=Role::where("isDelete",0)->get();
            $returnAndExchangePolicy=Cms::select([
                "policy",
                "id"
            ])->first();
            return view("pages.cms.policy",compact("returnAndExchangePolicy","role"));
            // return view("pages.user_management.index",compact("role"));

        } else {
            return abort(403, 'Unauthorized action.'); // or redirect to some other page
        }

    }
    public function returnAndExchangePolicyStore(Request $request)
    {
       // dd($request);
        Cms::where("id",$request->id)
        ->update([
            "policy"=>$request->content
        ]);
        return redirect()->back()->with("success","Content updated successfully");
    }
}
