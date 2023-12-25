<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Models\Item;
use Auth;
use App\Models\CustomDetails;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Color;
use App\Models\Size;
use App\Models\StockHistory;
use App\Models\ProductDetails;
use App\Models\Warehouse;
use Image;
use Intervention\Image\Exception\NotReadableException;
use Illuminate\Support\Facades\Storage;
use App\Models\Role;
use App\Models\admin_access;

// use App\Models\StockHistory;
// use App\Models\ProductDetails;
//use Illuminate\Support\Facades\Hash;
class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    // @auther faisal 
    public function create(){
      
        $add_product = DB::table('admin_accesses')
                    
        ->where([
          ['admin_id',Auth::user()->id] ,
          ['add_product',1]
           ])
          ->first();
        if ($add_product) {
            $role=Role::where("isDelete",0)->get();
            $category=Category::where("id","!=",7)->get();
            $subCategory=SubCategory::where("id","!=",20)->get();
            $warehouse=Warehouse::get();
            return view("pages.item.create",compact("category","subCategory","warehouse","role"));
    
            // return view("pages.user_management.index",compact("role"));

        } else {
            return abort(403, 'Unauthorized action.'); // or redirect to some other page
        }

    }

        public function store(Request $request)
        {
        //    dd($request);
        //       $six_digit_random_number = mt_rand(100000, 999999);
        // $filename=$_FILES["product_img"]['name'];
        //  $tempname=$_FILES["product_img"]["tmp_name"];
        //  $file_ext=pathinfo($filename,PATHINFO_EXTENSION);
        //  $filename=pathinfo($filename,PATHINFO_FILENAME);
        //  $filename=$six_digit_random_number;
        //  $name=$filename.".".$file_ext;
        //  $request->file('product_img')->move(public_path('product_img'), $namestore);
        //  $folderMain="product_img/".$name;
        
        
        $six_digit_random_number = mt_rand(100000, 999999);

        $thumbnailImage = $request->file('avatar');
        //   $image = Image::make($thumbnailImage);
        //  $originalWidth = $image->width();
       //         $originalHeight = $image->height();
      //  dd($originalHeight);
       
        // Generate a unique filename
        $image_name = $six_digit_random_number . '.' . $thumbnailImage->getClientOriginalExtension();
        $mainFolderPath = "product_img/" . $image_name;
        $path = str_replace("public/", "", public_path('product_img'));
        $thumbnailImage->move($path,$image_name);
 //    $image->crop(270, 324, 0, 0);
        //$image->save(str_replace("public/", "", public_path('product_img')) . '/' . $image_name);      
     //   dd("Yes");
      //  $six_digit_random_number = mt_rand(100000, 999999);


        // Open the image using Intervention Image
       // $image = Image::make($thumbnailImage);
        
        //$image->crop(334, 440, 0, 0);
        //$image->save(public_path('product_img') . '/' . $image_name);

      $item=new Item;
            $item->img_dir=$mainFolderPath;
            $item->item_name=$request->name;
            $item->category_id=$request->category_id;
            $item->subcategory_id=$request->subcategory_id;
            $item->warehouse_id=$request->warehouse_id;
            $item->discription=addslashes($request->description);
            $item->type=$request->type;
            $item->product_type=$request->product_type;
             $item->is_featured=$request->is_feature;
            $item->save();
          //  $thumbnailPath = public_path('product_img/'.$name);
           // $this->createThumbnail($thumbnailPath, 367, 440,$name);

            if(!empty($request->size_id))
            {
                $arrayForProductVariants=[];
                foreach($request->size_id as $key=>$size)
                {
                    $color=Color::where("id",$request->color_id[$key])->first();
                    $size=Size::where("id",$size)->first();
                    $arrayForProductVariants[] = [
                        'size_id' => $size->id,
                        'size_name' =>$size->name,
                        'color_id' => $request->color_id[$key],
                        'color_name' =>$color->name,
                        'price' => $request->price[$key],
                        'cost' => $request->cost[$key],
                        "itemcode"=>$item->id,
                        "qty"=>$request->qty[$key],
                        "min_qty_alert"=>$request->min_qty_alert[$key],
                    ];
                }
                ProductDetails::insert($arrayForProductVariants);
            }

            if(!empty($request->fields))
            {
                $arrayForCustomAttributes=[];
                foreach($request->fields as $customKey=>$customAttribute)
                {
                    $arrayForCustomAttributes[] = [
                        'itemcode' => $item->id,
                        'field' =>$customAttribute,
                        'type' => $request->type,
                    ];
                }
                CustomDetails::insert($arrayForCustomAttributes);
            }

        return response()->json([
            "message"=>"Product added successfully."
            ],200);
        //return redirect()->back()->with("success","Product created successfully");
    }
    public function productDetailView()
    {
        $color=Color::get();
        $size=Size::get();
        return view("pages.item.item_detail",compact("color","size"));
    }

    public function productDetailStore(Request $request)
    {
   //  dd($request);
        $itemcode=$request->itemcode;
        $price=$request->price;
         $cost=$request->cost;
        $size_id=$request->size;
        $color_id=$request->color;
         $qty=$request->qty;

         $sizeDetails=Size::where("id",$size_id)->first();
             $size_name=$sizeDetails->name;
    foreach($color_id as $key=>$jj)
    {
        $colorDetails=Color::where("id",$color_id[$key])->first();
            $color_name=$colorDetails->name;
        $productDetails=new ProductDetails;
        $productDetails->itemcode=$itemcode;
        $productDetails->color_id=$color_id[$key];
        $productDetails->size_id=$size_id;
        $productDetails->color_name=$color_name;
        $productDetails->size_name=$size_name;
        $productDetails->cost=$cost;
        $productDetails->price=$price;
        $productDetails->qty=$qty;
        $productDetails->save();
         $stockHistory=new StockHistory;
        $stockHistory->module=" Save product detail ";
        $stockHistory->description=" Stock in qty =$qty ";
        $stockHistory->previous_stock=0;
        $stockHistory->current_stock=$qty;
        $stockHistory->product_detail_id=$productDetails->id;
        $stockHistory->save();
  }
    return redirect()->back();
    //return redirect()->action("ItemController@productDetailView")->with("itemcode",$itemcode);
    }

        public function productDetailUpdate(Request $request)
    {
    // dd($request->all());
        $itemcode=$request->itemcode;
       // $price=$request->price;
       //  $cost=$request->cost;
        $size_id=$request->size_id;
        $color_id=$request->color_id;
         //$qty=$request->qty;
         //$update_qty=$request->update_qty;

         $sizeDetails=Size::where("id",$size_id)->first();
             $size_name=$sizeDetails->name;
    // foreach($color_id as $key=>$jj)
    // {
        $colorDetails=Color::where("id",$color_id)->first();
            $color_name=$colorDetails->name;
        $productDetails=ProductDetails::where("id",$request->p_d_id)->first();
        $productDetails->itemcode=$itemcode;
        $productDetails->color_id=$color_id;
        $productDetails->size_id=$size_id;
        $productDetails->color_name=$color_name;
        $productDetails->size_name=$size_name;
        $productDetails->min_qty_alert=$request->min_qty_alert;
       // $productDetails->cost=$cost;
       // $productDetails->price=$price;
        // if($request->qty_type=="add")
        // {
        //       $updatedQty=($productDetails->qty+$update_qty);
        // $stockHistory=new StockHistory;
        // $stockHistory->module=" Update product detail ";
        // $stockHistory->description=" Stock in qty =$update_qty ";
        // $stockHistory->previous_stock=$productDetails->qty;
        // $stockHistory->current_stock=$updatedQty;
        // $stockHistory->product_detail_id=$productDetails->id;
        // $stockHistory->save();
        // }
        // else
        // {
        //     $updatedQty=($productDetails->qty-$update_qty);
        //     $stockHistory=new StockHistory;
        // $stockHistory->module=" Update product detail ";
        // $stockHistory->description=" Stock out qty =$update_qty ";
        // $stockHistory->previous_stock=$productDetails->qty;
        // $stockHistory->current_stock=$updatedQty;
        // $stockHistory->product_detail_id=$productDetails->id;
        // $stockHistory->save();
        // }

        $productDetails->save();

//  }
    return redirect()->back()->with("itemcode",$itemcode);
    }



       public function customItemPriceUpdate(Request $request)
    {
   // dd($request);
        $itemcode=$request->itemcode;
        $price=$request->price;
         $cost=$request->cost;
       ///$size_id=$request->size_id;
        //$color_id=$request->color_id;
        // $qty=$request->qty;
         //$update_qty=$request->update_qty;

         //$sizeDetails=Size::where("id",$size_id)->first();
          //   $size_name=$sizeDetails->name;
    // foreach($color_id as $key=>$jj)
    // {
       // $colorDetails=Color::where("id",$color_id)->first();
        //    $color_name=$colorDetails->name;
        $productDetails=ProductDetails::where("id",$request->p_d_id)->first();
        $productDetails->itemcode=$itemcode;
       // $productDetails->color_id=$color_id;
       // $productDetails->size_id=$size_id;
        //$productDetails->color_name=$color_name;
       // $productDetails->size_name=$size_name;
        $productDetails->cost=$cost;
        $productDetails->price=$price;
        // if($request->qty_type=="add")
        // {
        //       $updatedQty=($productDetails->qty+$update_qty);
        // $stockHistory=new StockHistory;
        // $stockHistory->module=" Update product detail ";
        // $stockHistory->description=" Stock in qty =$update_qty ";
        // $stockHistory->previous_stock=$productDetails->qty;
        // $stockHistory->current_stock=$updatedQty;
        // $stockHistory->product_detail_id=$productDetails->id;
        // $stockHistory->save();
        // }
        // else
        // {
        //     $updatedQty=($productDetails->qty-$update_qty);
        //     $stockHistory=new StockHistory;
        // $stockHistory->module=" Update product detail ";
        // $stockHistory->description=" Stock out qty =$update_qty ";
        // $stockHistory->previous_stock=$productDetails->qty;
        // $stockHistory->current_stock=$updatedQty;
        // $stockHistory->product_detail_id=$productDetails->id;
        // $stockHistory->save();
        // }
        // $productDetails->qty=$updatedQty;

        $productDetails->save();

//  }
    return redirect()->back()->with("itemcode",$itemcode);
    }




       public function customItemStockUpdate(Request $request)
    {
        $user = auth()->user();
        $itemcode=$request->itemcode;
        //$price=$request->price;
         //$cost=$request->cost;
        //$size_id=$request->size_id;
        //$color_id=$request->color_id;
         $qty=$request->qty;
         $update_qty=$request->update_qty;

         //$sizeDetails=Size::where("id",$size_id)->first();
          //   $size_name=$sizeDetails->name;
    // foreach($color_id as $key=>$jj)
    // {
        //$colorDetails=Color::where("id",$color_id)->first();
    //        $color_name=$colorDetails->name;
        $productDetails=ProductDetails::where("id",$request->p_d_id)->first();
        $productDetails->itemcode=$itemcode;
      //  $productDetails->color_id=$color_id;
       // $productDetails->size_id=$size_id;
        //$productDetails->color_name=$color_name;
    //    $productDetails->size_name=$size_name;
        //$productDetails->cost=$cost;
       // $productDetails->price=$price;
        if($request->qty_type=="add")
        {
               $updatedQty=($productDetails->qty+$update_qty);
                
                $stockHistory=new StockHistory;
                $stockHistory->module=" Update product detail ";
                $stockHistory->description=" Stock in qty =$update_qty by admin($user->email)";
                $stockHistory->previous_stock=$productDetails->qty;
                $stockHistory->current_stock=$updatedQty;
                $stockHistory->product_detail_id=$productDetails->id;
                $stockHistory->save();
                
                $productDetails->qty=$updatedQty;
                $productDetails->save();
        }
        else
        {
            if($productDetails->qty>$update_qty){
                $updatedQty=($productDetails->qty-$update_qty);
                $stockHistory=new StockHistory;
                $stockHistory->module=" Update product detail ";
                $stockHistory->description=" Stock out qty =$update_qty by admin($user->email)";
                $stockHistory->previous_stock=$productDetails->qty;
                $stockHistory->current_stock=$updatedQty;
                $stockHistory->product_detail_id=$productDetails->id;
                $stockHistory->save();
                
                $productDetails->qty=$updatedQty;
                $productDetails->save();
            }else{
                return redirect()->back()->with('unsuccess', 'Stock out qty is greater than present stock');
            }
        }

//  }
    return redirect()->back()->with("itemcode",$itemcode);
    }

   // itemDetailsUpdate


    public function itemRecords()
    {
    return view("pages/item/record");
    }

//         public function itemData()
//     {
//         $items=Item::select([
//             "item.*",
//             "warehouse.name as warehouse_name"
//             ])
//             ->leftJoin("warehouse","warehouse.id","=","item.warehouse_id")
//            // ->where("product_type","Normal")
//             ->get();


//     $itemsData = array();
//           foreach($items as $key=>$itemsRecord)
//           {
//               $delete=route('item.delete',$itemsRecord->id);
//               $active=route('item.active',$itemsRecord->id);
//               $update=route("item.update.view",$itemsRecord->id);
//                  $image=asset($itemsRecord->img_dir);
//         $itemsRows = array();

//         $itemsRows[] ="<td>
//         <img src=$image style='height:100px;width:100px;'>
//         </td>";
//         $itemsRows[] =$itemsRecord->id."/".$itemsRecord->view_number;

//         $itemsRows[] =$itemsRecord->item_name." / ".$itemsRecord->warehouse_name;
//         $option="
//          <a href=$update >
//                   <i class='fa fa-edit'></i>
//                   &nbsp;&nbsp;&nbsp;";
//            if($itemsRecord->isActive)
//            {
//                   $option.="
//                   <a class='btn btn primary' style='
//     background-color: #ff2f00;
//     color: white;
// ' type='button' onclick='return itemDelete();' href=$delete >
//                      De-activate
//                 </a>
//         ";
//           }
//           else
//           {
//                     $option.="
//                   <a style='
//     background-color: #009d3f;
//     color: white;
// ' class='btn btn success' type='button' href=$active >
//                  Activate
//                 </a>";
//           }
//         $itemsRows[] = $option;

//         $itemsData[] = $itemsRows;
//           }

//         $json["data"]=$itemsData;
//         return json_encode($json);

//     }
    public function itemData(Request $request)
    {
      //  return $request->type;
        $data=[];
        $draw = $request->input('draw');
        $start = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');
        
        session(['length' => $length, 'search' => $search]);

        $query = Item::query();

        // Apply filters and search if needed

       // ->where("orders.isOrder",1);
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('item.item_name', 'like', "%{$search}%")
                    ->orWhere('item.product_type', 'like', "%{$search}%")
                    ->orWhere('warehouse.name', 'like', "%{$search}%");
            });
        }


        $query=$query->select([
            "item.id",
            "item.img_dir",
            "item.item_name",
            "item.product_type",
            "item.isActive",
            "warehouse.name as warehouse_name",
          ])
          ->leftJoin("warehouse","warehouse.id","=","item.warehouse_id");
    if($request->type!="all")
    {
          if($request->type=="active")
       {
           $query=$query->where("isActive",1);
            //$type=1;
       }
       else 
       {
           $query=$query->where("isActive",0);
       }
    }
      
          $totalRecords = $query->count();

          $itemsData = $query->offset($start)
                        ->limit($length)
                        ->orderBy('item.id', 'desc')
                        ->get();


                        foreach($itemsData as $key=>$item)
        {
            $delete=route('item.delete',$item->id);
            $adminDelete=route('item.admin.delete',$item->id);
              $active=route('item.active',$item->id);
              $update=route("item.update.view",$item->id);
                 $image=asset($item->img_dir);
            $nestedData["sr_no"]=($key+1);
            $nestedData["img_dir"]="<td><img src=$image style='width:150px;height:150px;'></td>";
            $nestedData["item_name"]=$item->item_name;
            $nestedData["product_type"]=$item->product_type;
            $nestedData["warehouse_name"]=$item->warehouse_name;
          //  $delete=route("orders.delete",$item->id);
                    $option="
         <a href=$update >
                  <i class='fa fa-edit'></i>
                  &nbsp;&nbsp;&nbsp;";
           if($item->isActive)
           {
                  $option.="
                  <a class='btn btn primary' style='
    background-color: #ff2f00;
    color: white;
' type='button' onclick='return itemDelete();' href=$delete >
                     De-activate
                </a>
        ";
          }
          else
          {
                    $option.="
                  <a style='
    background-color: #009d3f;
    color: white;
' class='btn btn success' type='button' href=$active >
                 Activate
                </a>";
          }
                    $option.="
                  <a class='btn btn primary' style='
    background-color: #ff2f00;
    color: white;
' type='button' onclick='return itemDelete();' href=$adminDelete >
                     Delete
                </a>
        ";
          
            $nestedData["action"]=$option;
            $data[]=$nestedData;
        }
        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ];

        return response()->json($response);


    }
    public function updateItemDetailsView($id)
    {
           $item=Item::where("id",$id)->first();
           //DB::select(" SELECT * FROM `item` WHERE `itemcode`='".$id."' ");
           
        //   according to client requirement sort in this order
        $type = strtolower($item->type);

        $colorsInOrder = ["White", "Orange", "Yellow", "Camo", "Green", "Purple", "Blue", "Brown", "Red", "Red/Black"];
        
        $itemDetails = ProductDetails::where("itemcode", $id)
            ->when($type === 'belt', function ($query) use ($colorsInOrder) {
                // If $type is 'belt', order by color_name in the specified order
                $query->orderByRaw("FIELD(color_name, '" . implode("','", $colorsInOrder) . "')");
            })
            ->get();
        $warehouse=Warehouse::get();
        //DB::select(" SELECT * FROM `product_details` WHERE `itemcode`='".$id."' ");
        $id2 = Auth::user()->id;
        $data = admin_access::where('admin_id',$id2)->first();

        return view("pages/item/update_details",compact("warehouse","data"))->with("itemcode",$id)->with("item",$itemDetails)->with("item2",$item);
    }

      public function delete($id)
    {
        $itemDetails=Item::where("id",$id)->first();
               //$img_dir=$itemDetails->img_dir;
         // unlink($img_dir);
$itemDetails->isActive=0;
$itemDetails->save();
//$itemDetails->delete();
   return redirect()->back()->with("str2",$id);

    }
    
       public function adminDelete($id)
    {
        $itemDetails=Item::where("id",$id)->first();
               //$img_dir=$itemDetails->img_dir;
         if($itemDetails->img_dir!="product_img/no.jpg")
         {
             if(file_exists($itemDetails->img_dir))
             {
                 unlink($itemDetails->img_dir);
             }
         }
          
         
//$itemDetails->isActive=0;
//$itemDetails->save();
$itemDetails->delete();
   return redirect()->back()->with("unsuccess","Product deleted successfully.");

    }
    
//     public function productUpdate(Request $request)
//     {
//             $rules =  array("name"=>"required","warehouse_id"=>"required");
// $messages  = [
//   'name.required'=> 'name field is required.',
//   'warehouse_id.required'=> 'Please select Warehouse .',
//  // 'is_feature.required'=> 'City field is required.',
//  // 'password.required'=> 'Password field is required.'
// ];
// $validator =  Validator::make($request->all(),$rules,$messages);

//     if ($validator->fails()) {
//         return response()->json(['errors' => $validator->errors()], 422); // Return validation errors with status code 422
//     }
    
    
//     $item=Item::where("")->first();
    
//     }

  public function productActivate($id)
    {
        $itemDetails=Item::where("id",$id)->first();
               // $img_dir=$itemDetails->img_dir;
         // unlink($img_dir);
$itemDetails->isActive=1;
$itemDetails->save();
//$itemDetails->delete();
   return redirect()->back()->with("str3",$id);

    }


    public function createThumbnail($path, $width, $height,$imageName)
    {


      // Load the original image
$image = Image::make($path);
//dd($image);
// Get the original image’s height and width
$originalWidth = $image->width();
//dd($originalWidth);
$originalHeight = $image->height();
// Calculate the new height while maintaining the aspect ratio
$newHeight = intval($originalHeight * ($width / $originalWidth));
// Resize the image proportionally to a width of 200px
$image->resize($width, $newHeight, function ($constraint) {
    $constraint->aspectRatio();
});
// Check if the height is greater than 67px and crop if necessary
if ($newHeight > $height) {
    $image->crop($width, $height,0,0);
}
//dd($image);
$image->save($path);
    }
    
      public function cropImage(Request $request)
    {
        $six_digit_random_number = mt_rand(100000, 999999);

        $uploadedImage = $request->file('avatar');
        // Open the image using Intervention Image
        $image = Image::make($uploadedImage);
        $image->crop(270, 324, 0, 0);
        if($request->id){
            // Generate a unique filename
            $image_name = $six_digit_random_number . '.' . $uploadedImage->getClientOriginalExtension();
            $mainFolderPath = "product_img/" . $image_name;
            $path = str_replace("public/", "", public_path('product_img'));
            $uploadedImage->move($path,$image_name);
            
            $item = Item::find($request->id);
            $oldImagePath = $item->img_dir;

            // Delete the old image file using PHP's unlink function (if it exists)
            if ($oldImagePath && file_exists(base_path($oldImagePath))) {
                unlink(base_path($oldImagePath));
            }
            
            $item->img_dir=$mainFolderPath;
            $item->save();
            
        }
        $encodedThumbnail = $image->encode('data-url');

        return response()->json(['thumbnail' => $encodedThumbnail]);
    }

    public function getColorAndSizeLists()
    {
        $sizeList="";
        $colorList="";
        $size = Size::get();
        $color = Color::get();
        foreach($size as $sizeRow)
        {
            $sizeList.="<option value=$sizeRow->id>$sizeRow->name</option>";
        }
        foreach($color as $colorRow)
        {
            $colorList.="<option value=$colorRow->id>$colorRow->name</option>";
        }
        $arr=array($colorList,$sizeList);
        return $arr;
    }

 //End of controller
}
