<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Models\Category;
use App\Models\SubCategory;
use Auth;
use App\Models\Role;
// use App\Models\User;
use App\Models\Section;
// use App\Models\category;
// use App\Models\Item;
// use App\Models\Role;
// use App\Models\StockHistory;
// use App\Models\ProductDetails;
//use Illuminate\Support\Facades\Hash;
class CategoryToSubCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    // @auther faisal 
     public function categoryToSubCategoryView(){
      
        $add_category_to_sub_category = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
          ['add_category_to_sub_category',1]
           ])
          ->first();


        if ($add_category_to_sub_category ) {
            $role=Role::where("isDelete",0)->get();
            $category=Category::where("id","!=",7)->get();
            $subcategory=SubCategory::where("id","!=",20)->get();
            return view("pages.category_to_subcategory.index",compact("category","subcategory","role"));
    
        } else {
            return abort(403, 'Unauthorized action.'); // or redirect to some other page
        }

    }
      public function delete($id)
    {
        Section::where("id",$id)->delete();
        return redirect()->action("CategoryToSubCategoryController@categoryToSubCategoryView")->with("str2","");

    }
   
    public function categoryToSubCategoryData()
    {
         $section=Section::select([
             "category.name as category_name",
             "subcategory.name as sub_category_name",
             "section.id",
             "section.category_id",
             "section.subcategory_id"
             ])
             ->join("category","category.id","=","section.category_id")
             ->join("subcategory","subcategory.id","=","section.subcategory_id")
             ->get();
         $sectionData = array();
          foreach($section as $key=>$sectionRecord)
          {
              $delete=route('category.to.sub.category.delete',$sectionRecord->id);
        $sectionRows = array();			
        $sectionRows[] =$sectionRecord->id;
        $sectionRows[] ="<input type='hidden' value=$sectionRecord->category_id>".$sectionRecord->category_name;
        $sectionRows[] ="<input type='hidden' value=$sectionRecord->subcategory_id>".$sectionRecord->sub_category_name;
        $sectionRows[] = "
           <a href='javascript:void(0);' >
                  <i class='fa fa-edit editsectionbtn'></i>
                  &nbsp;&nbsp;&nbsp;
                  <a onclick='return sectionDelete();' href=$delete >
                      <i class='fa fa-times'></i>
                </a>
       ";
        $sectionData[] = $sectionRows;
          }
       
        $json["data"]=$sectionData;
        return json_encode($json);
    }
    
     public function store(Request $request)
    {
      $section=new Section;
        $section->category_id=$request->category_id;
        $section->subcategory_id=$request->scategory_id;
        $section->save();
        return redirect()->action("CategoryToSubCategoryController@categoryToSubCategoryView")->with("str","");
    }
 function update(Request $request)
    {
        $c_id=$request->category_id;
        $sc_id=$request->subcategory_id;
        $id=$request->id;
        $section=Section::where("id",$id)->first();
        $section->category_id=$request->category_id;
        $section->subcategory_id=$request->scategory_id;
        $section->save();
        return redirect()->action("CategoryToSubCategoryController@categoryToSubCategoryView")->with("str","");
    }

    
    //End of controller
}