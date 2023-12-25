<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Models\Size;
use Auth;
// use App\Models\User;
// use App\Models\Color;
// use App\Models\Size;
// use App\Models\Item;
use App\Models\Role;
// use App\Models\StockHistory;
// use App\Models\ProductDetails;
//use Illuminate\Support\Facades\Hash;
class SizeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    // @auther faisal 
      public function sizeView(){
        $size = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
          ['size',1]
           ])
          ->first();
        if ($size ) {
            $role=Role::where("isDelete",0)->get();
            return view("pages.size.index",compact("role"));

        } else {
            return abort(403, 'Unauthorized action.'); // or redirect to some other page
        }

    }
      public function delete($id)
    {


    Size::where("id",$id)->delete();
     //DB::select(" DELETE FROM  `size`  WHERE `id`='".$id."' ");

     return redirect()->action("SizeController@sizeView")->with("str2",$id);

    }
       public function store(Request $request)
    {
        $name=$request->name;
        $size=new Size;
        $size->name=$name;
        $size->save();
    // DB::select(" INSERT INTO `size`(  `name`) VALUES ('".$name."') ");

     return redirect()->action("SizeController@sizeView")->with("str",$name);

    }

    function update(Request $request)
    {
        $id=$request->id;
        $name=$request->name;
       $size=Size::where("id",$id)->first();
       $size->name=$name;
       $size->save();
       // $e=DB::select(" UPDATE `size` SET `name`='".$name."' WHERE `id`='".$id."' ");
        return redirect()->action("SizeController@sizeView")->with("str1",$e);
    }
    
        public function sizeDetails(Request $request)
    {
        $id=$request->id;
        $size=Size::where("id",$id)->first();
        return $size;
        // $e=DB::select(" SELECT * FROM `size` WHERE `id`='".$id."' ");
        // $arr=array($e);
        // return json_encode($arr);
    }
    
    public function sizeData()
    {
        $size=Size::get();
    
       $sizeData = array();
          foreach($size as $key=>$sizeRecord)
          {
              $delete=route('size.delete',$sizeRecord->id);
              //$cancel=route("size.cancel",$sizeRecord->id);
            //  $confirm=return ConfirmDelete();
            //   if($sizeRecord->image=="")
            //   {
            //==     $image=asset("no_image.jpg");
            //   }
            //   else
            //   {
                 $image=asset($sizeRecord->img_dir);
            //   }
            //  $delete=route('item.edit',$sizeRecord->id);
              // $ename=__("file.edit");
              // $dname=__("file.delete");
        $sizeRows = array();			
    //    $sizeRows[] =($key+1);
        $sizeRows[] =$sizeRecord->id;
        // $sizeRows[] ="<td>
        // <img src=$image style='height:100px;width:100px;'>
        // </td>";
       
        $sizeRows[] =$sizeRecord->name;
      //  $sizeRows[] =$sizeRecord->role_name;
        $sizeRows[] = "
           <a href='javascript:void(0);' >
                  <i class='fa fa-edit editsizebtn'></i>
                  &nbsp;&nbsp;&nbsp;
                  <a onclick='return sizeDelete();' href=$delete >
                      <i class='fa fa-times'></i>
                </a>
       ";
        $sizeData[] = $sizeRows;
          }
       
        $json["data"]=$sizeData;
        return json_encode($json); 
        
        
    }
    
    
    //End of controller
}