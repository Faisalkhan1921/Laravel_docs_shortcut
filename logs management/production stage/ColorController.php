<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Models\Color;
use Auth;
// use App\Models\User;
// use App\Models\Color;
// use App\Models\Color;
// use App\Models\Item;
use App\Models\Role;
// use App\Models\StockHistory;
// use App\Models\ProductDetails;
//use Illuminate\Support\Facades\Hash;
class ColorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    // @auther faisal 
   public function colorView(){
        $color = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
          ['color',1]
           ])
          ->first();
        if ($color ) {
            $role=Role::where("isDelete",0)->get();
            return view("pages.color.index",compact("role"));

            // return view("pages.user_management.index",compact("role"));

        } else {
            return abort(403, 'Unauthorized action.'); // or redirect to some other page
        }

    }
      public function delete($id)
    {


    Color::where("id",$id)->delete();
     //DB::select(" DELETE FROM  `color`  WHERE `id`='".$id."' ");

     return redirect()->action("ColorController@colorView")->with("str2",$id);

    }
       public function store(Request $request)
    {
        $name=$request->name;
        $color=new Color;
        $color->name=$name;
        $color->save();
    // DB::select(" INSERT INTO `color`(  `name`) VALUES ('".$name."') ");

     return redirect()->action("ColorController@colorView")->with("str",$name);

    }

    function update(Request $request)
    {
        $id=$request->id;
        $name=$request->name;
       $color=Color::where("id",$id)->first();
       $color->name=$name;
       $color->save();
       // $e=DB::select(" UPDATE `color` SET `name`='".$name."' WHERE `id`='".$id."' ");
        return redirect()->action("ColorController@colorView")->with("str1",$e);
    }
    
        public function colorDetails(Request $request)
    {
        $id=$request->id;
        $color=Color::where("id",$id)->first();
        return $color;
        // $e=DB::select(" SELECT * FROM `color` WHERE `id`='".$id."' ");
        // $arr=array($e);
        // return json_encode($arr);
    }
    
    public function colorData()
    {
        $color=Color::get();
    
       $colorData = array();
          foreach($color as $key=>$colorRecord)
          {
              $delete=route('color.delete',$colorRecord->id);
              //$cancel=route("color.cancel",$colorRecord->id);
            //  $confirm=return ConfirmDelete();
            //   if($colorRecord->image=="")
            //   {
            //==     $image=asset("no_image.jpg");
            //   }
            //   else
            //   {
                 $image=asset($colorRecord->img_dir);
            //   }
            //  $delete=route('item.edit',$colorRecord->id);
              // $ename=__("file.edit");
              // $dname=__("file.delete");
        $colorRows = array();			
    //    $colorRows[] =($key+1);
        $colorRows[] =$colorRecord->id;
        // $colorRows[] ="<td>
        // <img src=$image style='height:100px;width:100px;'>
        // </td>";
       
        $colorRows[] =$colorRecord->name;
      //  $colorRows[] =$colorRecord->role_name;
        $colorRows[] = "
           <a href='javascript:void(0);' >
                  <i class='fa fa-edit editcolorbtn'></i>
                  &nbsp;&nbsp;&nbsp;
                  <a onclick='return colorDelete();' href=$delete >
                      <i class='fa fa-times'></i>
                </a>
       ";
        $colorData[] = $colorRows;
          }
       
        $json["data"]=$colorData;
        return json_encode($json); 
        
        
    }
    
    
    //End of controller
}