<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\adminroles\alladminuser;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('apply-respective-dispatcher', function() {
//     $dispatchers = \DB::table('admin')->where('role_id', 3)->get(['id']);
//     foreach($dispatchers as $dispatcher){
//         $users = \DB::table('customer')->where('dispatcher_id', $dispatcher->id)->get(['id']);
//         if(count($users)){
//             foreach($users as $user){
//                 $orders = App\Models\OrderHead::where('customer_id', $user->id)->get();
//                 if(count($orders))
//                 {
//                     foreach($orders as $order){
//                         $order->dispatcher_id = $dispatcher->id;
//                         $order->save();
//                     }
//                 }
//             }
//         }
//     }
//     return "done";
// });
use App\Models\StockHistory;
use Illuminate\Http\Request;
use App\Models\Item;
// Route::get('filter-stock', function (Request $req) {
//     $item = Item::with('variants:id,itemcode,qty')->where('item_name', 'like', '%'.$req->name.'%')->first();
//     if($req->show){
//         dd($item, $req->name);
//     }
//     if($item) {
//         foreach($item->variants as $var) {
//             $lastHistory = DB::table('stock_history')
//                 ->where('created_at', '<', '2023-10-03 00:00:00')
//                 ->where('product_detail_id', $var->id)
//                 ->orderByDesc('id')
//                 ->first();
//             if(!$lastHistory){
//                 DB::table('product_details')
//                 ->where('id', $var->id)
//                 ->update(['qty' => 0]);
                
//                 $stockHistory = new StockHistory;
//                 $stockHistory->module = "Qty Initial Module";
//                 $stockHistory->description = "set current qty to initial qty";
//                 $stockHistory->previous_stock = $var->qty;
//                 $stockHistory->current_stock = 0;
//                 $stockHistory->product_detail_id = $var->id;
//                 $stockHistory->save();
//             }else{
//                 DB::table('product_details')
//                 ->where('id', $var->id)
//                 ->update(['qty' => $lastHistory->current_stock]);
                
//                 $stockHistory = new StockHistory;
//                 $stockHistory->module = "Qty Initial Module";
//                 $stockHistory->description = "set current qty to initial qty as of $lastHistory->created_at";
//                 $stockHistory->previous_stock = $var->qty;
//                 $stockHistory->current_stock = $lastHistory->current_stock;
//                 $stockHistory->product_detail_id = $var->id;
//                 $stockHistory->save();
//             }
//         }
//     }
//     // $stocksToReverse = DB::table('stock_history')
//     // ->where('created_at', '<', '2023-10-03 00:00:00')
//     // ->orderByDesc('id')
//     // ->get();
//     // foreach($stocksToReverse as $stockR) {
//     //     DB::table('product_details')
//     //         ->where('id', $stockR->product_detail_id)
//     //         ->update(['qty' => $stockR->previous_stock]);
//     //     $updatedQty = $stockR->previous_stock - $stockR->current_stock;
//     //     if($updatedQty>0) {
//     //         // means stock was out we have to make stock in history
//     //         $stockHistory = new StockHistory;
//     //         $stockHistory->module = "Qty Reverse Module";
//     //         $stockHistory->description = "Stock in qty = $updatedQty for stock history of date $stockR->created_at";
//     //         $stockHistory->previous_stock = $stockR->current_stock;
//     //         $stockHistory->current_stock = $stockR->previous_stock;
//     //         $stockHistory->product_detail_id = $stockR->product_detail_id;
//     //         $stockHistory->save();
            
//     //     } else {
//     //         $updatedQty = $stockR->current_stock-$stockR->previous_stock;
//     //         // means stock was in we have to make stock out history
//     //         $stockHistory = new StockHistory;
//     //         $stockHistory->module = "Qty Reverse Module";
//     //         $stockHistory->description = "Stock out qty = $updatedQty for stock history of date $stockR->created_at";
//     //         $stockHistory->previous_stock = $stockR->current_stock;
//     //         $stockHistory->current_stock = $stockR->previous_stock;
//     //         $stockHistory->product_detail_id = $stockR->product_detail_id;
//     //         $stockHistory->save();
//     //     }
//     // }
    
//     // 
//     // $stocksToReverse = DB::table('stock_history')
//     // ->whereBetween('created_at', ['2023-10-03 00:00:00', '2023-10-12 23:59:59'])
//     // ->orderByDesc('id')
//     // ->get();
//     // foreach($stocksToReverse as $stockR) {
//     //     if($stockR->previous_stock>=0){
//     //         DB::table('product_details')
//     //             ->where('id', $stockR->product_detail_id)
//     //             ->update(['qty' => $stockR->current_stock]);
//             // $updatedQty = $stockR->previous_stock - $stockR->current_stock;
//             // if($updatedQty>0) {
//             //     // means stock was out 
//             //     $stockHistory = new StockHistory;
//             //     $stockHistory->module = "Qty Reverse Module";
//             //     $stockHistory->description = "Stock out qty = $updatedQty as of stock history of date $stockR->created_at";
//             //     $stockHistory->previous_stock = $stockR->previous_stock;
//             //     $stockHistory->current_stock = $stockR->current_stock;
//             //     $stockHistory->product_detail_id = $stockR->product_detail_id;
//             //     $stockHistory->save();
                
//             // }
// //         }
// //     }

// // to remove -ve stock
//     // $itemsToNegs = DB::table('product_details')->where('qty', '<', 0)->get();
//     // foreach($itemsToNegs as $item) {
//     //     $updatedQty = 0-$item->qty;
//     //     DB::table('product_details')
//     //             ->where('id', $item->id)
//     //             ->update(['qty' => 0]);
//     //     $stockHistory = new StockHistory;
//     //     $stockHistory->module = "Qty Balance Module";
//     //     $stockHistory->description = "Stock in qty = $updatedQty to remove -ve";
//     //     $stockHistory->previous_stock = $item->qty;
//     //     $stockHistory->current_stock = 0;
//     //     $stockHistory->product_detail_id = $item->id;
//     //     $stockHistory->save();
//     // }
    
//     dd('done');
// });

Route::get('error-item', function(Request $req) {
    $item = Item::where('item_name', 'like', '%'.$req->name.'%')->first();
    if($req->show){
        dd($item, $req->all());
    }
    $orders = DB::table('orders')
        ->whereBetween('updated_at', ['2023-10-02', '2023-10-17'])
        ->where('itemcode', $item->id)
        ->where('split_status', 1)
        ->whereNull('deleted_at')
        ->get();
    foreach($orders as $order) {
        $detail = DB::table('product_details')->where('itemcode', $item->id)
                ->where('color_name', $order->color_id)
                ->where('size_name', $order->size_id)
                ->first();
        $updatedQty = $detail->qty-$order->quantity;
        $stockHistory = new StockHistory;
        $stockHistory->module = "Qty Balance Module";
        $stockHistory->description = "Stock out qty = $order->quantity to of previously picked order at $order->updated_at";
        $stockHistory->previous_stock = $detail->qty;
        $stockHistory->current_stock = $updatedQty;
        $stockHistory->product_detail_id = $detail->id;
        $stockHistory->save();
        
        DB::table('product_details')
                ->where('id', $detail->id)
                ->update(['qty' => $updatedQty]);
    }
    dd("done");
});
// @auther faisal
Route::get("/user-management-front","UserManagementController@userManagementView_front")->name("user.management.front.index");
Route::post("user-management-front-store","UserManagementController@user_manage_store")->name("user.management.front.store");

Route::get('min-alert-qty-pro', function() {
    $products = DB::table('product_details')
    ->select('product_details.color_name', 'product_details.size_name', 'product_details.qty', 'product_details.min_qty_alert', 'item.item_name as product_name')
    ->join('item', 'product_details.itemcode', '=', 'item.id')
    ->whereColumn('product_details.min_qty_alert', '>=', 'product_details.qty')
    ->whereNull('item.deleted_at')
    ->get();
    $email = "mora03033@gmail.com";
    $name = "Moeez";
    $subject = "Stock Qty Updates";
    //return view('emails.stock', compact('products'));
    $to = "muh.moeez03@gmail.com";
    $message = view('emails.stock', compact('products'))->render();
    $headers = "From: info@site.com\r\n";
    $headers .= "Reply-To: info@site.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    if (mail($to, $subject, $message, $headers)) {
        return "Email sent successfully!";
    } else {
        return "Email sending failed.";
    }
    Mail::send('emails.stock', ['products' => $products ?? []], function ($message) use ($email, $name, $subject) {
        $message->to($email, $name)->subject($subject);
    });
    dd($products);
});

//Dashoard route
Route::get("/admin-panel","DashboardController@homeView")->name("dashboard");
Route::post('/logout-admin',[DashboardController::class,'admin_logout'])->name('admin_logout');

Route::post("/save-realpassword","DataController@realPasswordStore")->name("real_password.save");



//User management route
Route::get("/user-management","UserManagementController@userManagementView")->name("user.management.index");
Route::get("/user-management-front-view","UserManagementController@userManagementView_front")->name("add.customers.front");
Route::post("user-management-front-store","UserManagementController@user_manage_store")->name("user.management.front.store");

Route::get("/user-details","UserManagementController@userDetails")->name("user.management.user.details");
Route::get("/user-management-delete/{id}","UserManagementController@delete")->name("user.management.delete");
Route::get("/user-management-data","UserManagementController@userManagementData")->name("user.management.data");
Route::post("user-management-store","UserManagementController@store")->name("user.management.store");
Route::post("/update-user-management","UserManagementController@update")->name("user.management.update");

//Category to sub category routes
Route::get("/category-to-subcategory","CategoryToSubCategoryController@categoryToSubCategoryView")->name("category.to.sub.category.view");
Route::get("/category-to-subcategory-data","CategoryToSubCategoryController@categoryToSubCategoryData")->name("category.to.sub.category.data");
Route::post("/add-category-to-subcategory","CategoryToSubCategoryController@store")->name("category.to.sub.category.store");
Route::post("/category-to-subcategory-update","CategoryToSubCategoryController@update")->name("category.to.sub.category.update");
Route::post("/category-to-subcategory-delete","CategoryToSubCategoryController@delete")->name("category.to.sub.category.delete");
Route::post("/get-sub-category-by-category-id","SubCategoryController@getSubCategoryByCategoryId")->name("get.sub.category.by.category.id");


//Logs routes
Route::get("logs","LogsController@logs")->name("logs");
Route::get("logs-data","LogsController@logsData")->name("logs.data");

//Order routes
Route::get("/pending-order","OrderController@pendingOrderView")->name("order.pending");
Route::get("/order-data/{status}","OrderController@orderData")->name("order.data");
Route::get("/shipped-order","OrderController@shipOrderView")->name("order.ship");
Route::get("/processing-order","OrderController@processingOrderView")->name("order.processing");
Route::get("/cancel-order","OrderController@cancelOrderView")->name("order.cancel");
Route::get("/back-order","PagesController@backview")->name("order.back");
Route::get("/pending-detail/{id}","PagesController@pending_detail");
Route::get("/order-detail/{id}","OrderController@orderDetail")->name("order.detail");
Route::post("/add-order-qty","OrderController@addOrderQty")->name("order.add.qty");
Route::get("/remove-order/{id}","OrderController@delete")->name("order.delete");
Route::get("/back-detail/{id}","PagesController@back_detail");
Route::get("/delete-product-image/{id}","DataController@delete_product_image");
Route::get("/confirm-detail/{id}","PagesController@confirmed_detail");
Route::get("/cancell-detail/{id}","PagesController@cancelled_detail");
Route::get("/deliver-detail/{id}","PagesController@delivered_detail");
Route::get("/cancell-order/{id}","DataController@cancell_order");
Route::get("/order-pay/{id}","OrderController@orderPayment")->name("order.pay");
Route::get("/order-pick/{id}","OrderController@orderPick")->name("order.pick");
Route::post("/order-pick-edit/{id}","OrderController@orderPickEdit")->name("order.pick.edit");
Route::get("orders-by-date","OrderController@orderByDate")->name("orders.by.date");
Route::post("orders-by-date-details","OrderController@orderByDateDetails")->name("get.order.details");
Route::post("upadte-order-status","OrderController@updateOrderStatus")->name("update.status.order");
Route::post("orders/get-order-tracking","OrderController@getOrderTracking")->name("get.order.track");
Route::post("orders/print-packing-list","OrderController@printPackingList")->name("print.pick");

//Route::get("/subcategory-delete/{id}","DataController@subcategory_delete");
Route::get("/detailcustombtn","DataController@detailcustombtn");


//Dispatcher routes

Route::get("/dispatcher-delete/{id}","DispatcherController@delete")->name("dispatcher.delete");
Route::get("/dispatcher","DispatcherController@dispatcherView")->name("dispatcher");
Route::get("/dispatcher-data","DispatcherController@dispatcherData")->name("dispatcher.data");
Route::get("/dispatcher-details","DispatcherController@dispatcherDetails")->name("dispatcher.details");
Route::post("/dispatcher-store","DispatcherController@store")->name("dispatcher.store");
Route::post("/update-dispatcher","DispatcherController@update")->name("dispatcher.update");
Route::post("/dispatcher-assign-customer","DispatcherController@dispatcherAssignCustomer")->name("dispatcher.assign.customer");
Route::post("/assign-dispatcher-to-customer","DispatcherController@AssignDispatcherToCustomer")->name("assign_dispatcher.to.customer");
Route::post("/update-dispatcher-to-customer","DispatcherController@updateDispatcherToCustomer")->name("update_dispatcher.to.customer");
Route::get("/dispatcher-assigned-customers/{id}","DispatcherController@dispatcherAssignedCustomers")->name("dispatcher.assigned.customer");
Route::get("/dispatcher-assigned-customers-data/{id}","DispatcherController@dispatcherAssignedCustomersData")->name("dispatcher.assigned.customer.data");
Route::get("/assigned-customers-remove/{id}","DispatcherController@assignedCustomerRemove")->name("assigned.customer.remove");

//Route::get("/dispatcher","PagesController@dispatcherview");
//Route::get("/dispatcherajax","DataController@dispatcherajax");
Route::post("/assign-dispatcher-to-order/{orderId}", "DispatcherController@assignedToOrder");


// Item routes
Route::get("/add-product","ItemController@create")->name("item.create");
Route::POST("/product-data","ItemController@itemData")->name("item.data");
Route::get("/update-details/{id}","ItemController@updateItemDetailsView")->name("item.update.view");
Route::post("/product-store","ItemController@store")->name("item.store");
Route::post("/product-detail-store","ItemController@productDetailStore")->name("item.product.detail.store");
Route::get("/product-detail","ItemController@productDetailView")->name("item.product.details");
Route::get("/product-records/{type?}","PagesController@product_recordsview")->name("item.index");
Route::get("/product-stock", "PagesController@stockCsv")->name('stock.csv');
Route::get("/product-stock-history", "PagesController@stockHistoryCsv")->name('stock.history.csv');
Route::get("/product-delete/{id}","ItemController@delete")->name("item.delete");
Route::get("/item-record/{id}","PagesController@item_record")->name("item.records");
Route::get("/stock-history/{id}","PagesController@stockHistory")->name("stock_history");
Route::get("/productajax","DataController@productajax")->name("item.details.ajax");
Route::get("/product-activate/{id}","ItemController@productActivate")->name("item.active");
Route::name("item.")->prefix("product/")->group(function(){
    Route::get("/delete/{id}","ItemController@adminDelete")->name("admin.delete");
     Route::post("/update","ItemController@productUpdate")->name("update");
});
Route::post("/get-color-and-size-lists","ItemController@getColorAndSizeLists")->name("get.lists");
Route::post("/crop-image","ItemController@cropImage")->name("crop.image");

//Custom items routes
Route::get("/add-custom-product","CustomItemController@customProductView")->name("custom.item.view");
Route::get("/custom-records","CustomItemController@customRecordsView")->name("custom.item.records");
Route::get("/custom-records-data","CustomItemController@customItemsData")->name("custom.item.data");
Route::get("/add-price-to-custom","CustomItemController@addPriceToCustomView")->name("add.price.to.custom");
Route::get("/custom-price-data","CustomItemController@customPriceData")->name("custom.price.data");
Route::post("/add-custom-product-store","CustomItemController@store")->name("custom.item.store");
Route::post("/custom-price-update","CustomItemController@updateCustomPrice")->name("custom.update.price");
Route::post("/custom-item-details-update","ItemController@productDetailUpdate")->name("custom.item.detail.update");
Route::post("/custom-item-price-update","ItemController@customItemPriceUpdate")->name("custom.item.price.update");
Route::post("/custom-item-stock-update","ItemController@customItemStockUpdate")->name("custom.item.stock.update");

//Size routes
Route::get("/size","SizeController@sizeView")->name("size");
Route::get("/size-data","SizeController@sizeData")->name("size.data");
Route::get("/size-delete/{id}","SizeController@delete")->name("size.delete");
Route::get("/size-details","SizeController@sizeDetails")->name("size.details");
Route::post("/size-store","SizeController@store")->name("size.store");
Route::post("/update-size","SizeController@update")->name("size.update");

//Color routes
Route::get("/color","ColorController@colorView")->name("color");
Route::get("/color-data","ColorController@colorData")->name("color.data");
Route::get("/color-delete/{id}","ColorController@delete")->name("color.delete");
Route::get("/color-details","ColorController@colorDetails")->name("color.details");
Route::post("/color-store","ColorController@store")->name("color.store");
Route::post("/update-color","ColorController@update")->name("color.update");

//Coupon routes
Route::get("/coupon","CouponController@couponView")->name("coupon");
Route::get("/coupon-data","CouponController@couponData")->name("coupon.data");
Route::get("/coupon-delete/{id}","CouponController@delete")->name("coupon.delete");
Route::post("/coupon-store","DataController@coupan")->name("coupon.store");
Route::post("/update-coupon","DataController@update_coupan")->name("coupon.update");
//Route::get("/coupon-details","DataController@couponDetails")->name("coupon.");
//Route::get("/coupon","PagesController@add_coupanview");
//Route::get("/coupon-delete/{id}","DataController@coupan_delete");
//Route::get("/couponajax","DataController@coupanajax");


//Customer routes
Route::get("/accept-customer/{id}","CustomerController@acceptCustomer")->name("customers.accept");
Route::get("/cancell-customer/{id}","CustomerController@cancelCustomer")->name("customers.cancel");
Route::get("/delete-customer/{id}","CustomerController@deleteCustomer")->name("customers.delete");
Route::get("/pending-customer","CustomerController@pendingCustomers")->name("customers.pending");
Route::get("/confirmed-customer","CustomerController@confirmedCustomers")->name("customers.confirmed");
Route::get("/pending-customer-data","CustomerController@pendingCustomersData")->name("customers.pending.data");
Route::get("/cofirmed-customer-data","CustomerController@confirmedCustomersData")->name("customers.confirmed.data");


Route::get('/get-customer/{id}', 'CustomerController@getCustomer');
Route::post('/update-customer', 'CustomerController@updateCustomer')->name('update.customer');





//Category routes
Route::get("/category","CategoryController@categoryView")->name("category");
Route::get("/category-data","CategoryController@categoryData")->name("category.data");
Route::get("/category-delete/{id}","CategoryController@delete")->name("category.delete");
Route::post("/update-category","CategoryController@update")->name("category.update");
Route::post("/category-store","CategoryController@store")->name("category.store");

//Sub category routes
Route::get("/sub-category","SubCategoryController@subCategoryView")->name("sub.category");
Route::get("/sub-category-data","SubCategoryController@subCategoryData")->name("sub.category.data");
Route::get("/sub-category-delete/{id}","SubCategoryController@delete")->name("sub.category.delete");
Route::post("/update-subcategory","SubCategoryController@update")->name("sub.category.update");
Route::post("/sub-category-store","SubCategoryController@store")->name("sub.category.store");



Route::post("/confirm-order","DataController@confirm_order");



//Route::get("/update-details/{id}","PagesController@update_detailsview");

Route::get("/product-details-delete/{id}","PagesController@product_details_delete");
Route::post("/update-custom-product","DataController@update_custom_product");
Route::get("/pages-confirm-order", "PagesController@completview")->name('page.confirm.order');

Route::post("/add-image-variant","DataController@add_image_variant");
Route::post("/item-variant-upodate","DataController@item_variant_upodate");
Route::get("/item-variant-delete/{id}","DataController@item_variant_delete");


Route::get("/product-detail/{id}","PagesController@product_detailwithitemcodeview");
Route::get("/custom-fields-delete/{id}","DataController@custom_fields_delete");
Route::post("/add-custom-field","DataController@add_custom_field");


Route::post("/sub-category-records","PagesController@sub_category_recordsview");
Route::post("/category-records","PagesController@category_recordsview");
Route::post("/update-back-qty","DataController@updatebackqty");

Route::get("/set-to-back/{id}/{qty}","DataController@set_to_back");
Route::get("/back-to-deliver/{id}","DataController@back_to_deliver");



//Role routes
Route::get("/role","RoleController@index")->name("role.index");
Route::get("/role-delete/{id}","RoleController@delete")->name("role.delete");
Route::get("/role-data","RoleController@data")->name("role.data");
Route::post("/role-save","RoleController@store")->name("role.store");
Route::post("/role-update","RoleController@update")->name("role.update");
Route::get("/role-get-details","RoleController@getDetails")->name("role.getDetails");



//Permissions routes
Route::get("/permission","PermissionController@index")->name("permission.index");
Route::get("/permission-delete/{id}","PermissionController@delete")->name("permission.delete");
Route::get("/permission-data","PermissionController@data")->name("permission.data");
Route::post("/permission-save","PermissionController@store")->name("permission.store");
Route::post("/permission-update","PermissionController@update")->name("permission.update");
Route::get("/permission-get-details","PermissionController@getDetails")->name("permission.getDetails");

// Assign permission to roles
Route::get("/assign-permission-to-roles","RoleHasPermissionController@index")->name("assign_permission.index");
Route::get("/assign-permission-to-roles-delete/{id}","RoleHasPermissionController@delete")->name("assign_permission.delete");
Route::get("/assign-permission-to-roles-data","RoleHasPermissionController@data")->name("assign_permission.data");
Route::post("/assign-permission-to-roles-save","RoleHasPermissionController@store")->name("assign_permission.store");
Route::post("/assign-permission-to-roles-update","RoleHasPermissionController@update")->name("assign_permission.update");
Route::get("/assign-permission-to-roles-get-details","RoleHasPermissionController@getDetails")->name("assign_permission.getDetails");

//Content management system route
Route::get("cms/about-us","CmsController@aboutUs")->name("about.us");
Route::post("cms/about-us-store","CmsController@aboutStore")->name("about.store");
Route::get("cms/contact-us","CmsController@contactUs")->name("contact.us");
Route::post("cms/contact-us-store","CmsController@contactStore")->name("contact.store");
//Route::get("cms/settings","CmsController@settings")->name("settings");
Route::get("cms/return-and-exchange-policy","CmsController@returnAndExchangePolicy")->name("return.and.exchange.policy");
Route::post("cms/return-and-exchange-policy-store","CmsController@returnAndExchangePolicyStore")->name("return.and.exchange.policy.store");

//Settings routes
Route::get("cms/settings","SettingsController@settings")->name("settings");
Route::post("cms/settings-store","SettingsController@settingsStore")->name("settings.store");

Route::prefix('email')->as('email.')->group(function() {
    Route::get('all', "EmailController@all")->name('all');
    Route::post('new', "EmailController@new")->name('new');
    Route::delete('delete', "EmailController@delete")->name('delete');
    Route::post('status', "EmailController@status")->name('status');
});


//Warehouse routes
Route::get("/warehouse","WarehouseController@warehouseView")->name("warehouse");
Route::get("/warehouse-data","WarehouseController@warehouseData")->name("warehouse.data");
Route::get("/warehouse-delete/{id}","WarehouseController@delete")->name("warehouse.delete");
Route::get("/warehouse-details","WarehouseController@warehouseDetails")->name("warehouse.details");
Route::post("/warehouse-store","WarehouseController@store")->name("warehouse.store");
Route::post("/update-warehouse","WarehouseController@update")->name("warehouse.update");


//Send email

Route::post("/send-email","EmailController@sendEmailItems")->name("send.email.items");

// Frontend routes.

Route::get("/","Front\PagesController@index")->name("front.home");
Route::get("/about-us","Front\PagesController@about")->name("front.about");
Route::get("/return-and-exchange-policy","Front\PagesController@exchange");
Route::get("/contact-us","Front\PagesController@contact")->name("front.contact");

//Cart routes
Route::get("/cart","Front\CartController@cart");
Route::post("/cart-body","Front\CartController@cartTableRecord")->name("cart.body");
Route::post("/cancell-product-from-cart","Front\CartController@cancellitem")->name("cancel.product.from.cart");


//Front->Auth controller
Route::get("/login-register","Front\AuthController@login_register")->name("front.login");


Route::middleware(['user.auth'])->group(function () {
    Route::get("/dashboard","Front\DashboardController@dashboard")->name("front.account");
    Route::get("/order-details/{id}","Front\OrderController@orderDetails")->name("front.order.details");
    Route::get("/back-order-history/{id}","Front\PagesController@backorder_history");
    Route::get("/checkout","Front\CheckoutController@checkout")->name("front.checkout");
    Route::get("/logout-front","Front\DataController@logout")->name("front.logout");
    Route::post("/update-account","Front\DataController@update_account");
    Route::post("/order-save","Front\OrderController@orderSave")->name("front.order.save");
    Route::post("/update-profile","Front\DataController@update_profile");
    Route::post("get-order-history","Front\AjaxController@getOrderHistory")->name("get.order.history");

});


Route::post("validate-coupon","Front\AjaxController@validateCoupon")->name("get.validate.coupon");
Route::get("/category/{id}","Front\PagesController@category");
Route::get("/subcategory/{id}","Front\PagesController@subcategory");


Route::get("/success","Front\PagesController@success")->name("front.success");
Route::get("/forgot-password","Front\PagesController@forgot_password")->name("front.forget");
Route::get("/six-digit-code","Front\PagesController@six");


//Front->product routes
Route::get("/product-details/{id}","Front\ProductController@productDetails")->name("front.product_details");




//Front->Ajax routes
Route::post("/addtocart","Front\AjaxController@addtocart")->name("front.addtocart");
Route::post("/addtocart2","Front\AjaxController@addtocart2")->name("front.addtocart2");
Route::post("/sizeprice","Front\AjaxController@sizeprice")->name("front.size.price");
Route::get("/custom_name","Front\DataController@custom_name");
Route::post("/sizeoption","Front\AjaxController@sizeoption")->name("front.size.options");
Route::post("/sizepricecustom","Front\AjaxController@sizepricecustom")->name("front.size.price.custom");
Route::post("/pricecalculatecustom","Front\AjaxController@pricecalculatecustom")->name("front.price.calculate.custom");
Route::post("/detailcustombtn","Front\AjaxController@detailcustombtn")->name("get.custom.details.by.id");
Route::get("/itemcount","Front\AjaxController@itemcount");
Route::get("/totalcart","Front\AjaxController@totalcart");
Route::get("/cartrecord","Front\AjaxController@cartrecord");
Route::post("/updateqty","Front\AjaxController@updateqty")->name("front.update.qty");
Route::post("/pricecalculate","Front\AjaxController@pricecalculate")->name("front.price.calculate");
Route::post("/get-search-results","Front\AjaxController@getSearchResults")->name("get.search.results");






Route::post("/register-front","Front\DataController@register")->name("front.register");

Route::post("/login-front","Front\DataController@login")->name("login.front");
Route::post("/coupan","Front\DataController@coupan");

Route::post("/forgot-pass","Front\DataController@forgot_password");
Route::post("/six-digit","Front\DataController@six_digit");
Route::post("/password-set","Front\DataController@password_set");


Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
Auth::routes();

//Route::get('/home', [HomeController::class, 'index'])->name('home');

// @auther faisal 
// all admin user route 
Route::get('/all',[alladminuser::class,'index'])->name('all.admin.user');
Route::get('/add/admin',[alladminuser::class,'addadmin'])->name('add.admin');
Route::post('/store/admin/user',[alladminuser::class,'store'])->name('admin.user.store');
Route::get('edit/admin/role/{id}',[alladminuser::class,'edit'])->name('edit.admin.role');
Route::post('/update/admin/user/{id}',[alladminuser::class,'update'])->name('admin.user.update');
Route::get('/delete/admin/user/{id}',[alladminuser::class,'delete'])->name('delete.admin.role');
