<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([

    'register' => true, // Register Routes...

    'reset' => false, // Reset Password Routes...

    'verify' => false, // Email Verification Routes...

  ]);



Route::middleware('auth')->group(function () {
    Route::view('about', 'about')->name('about');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::resource('/producttypes', App\Http\Controllers\ProductTypeController::class);
    Route::resource('/productmetrics', App\Http\Controllers\ProductMetricController::class);
    Route::resource('/storeunittypes', App\Http\Controllers\StoreUnitTypeController::class);
    Route::resource('/firms', App\Http\Controllers\FirmController::class);
    Route::resource('/storeunits', App\Http\Controllers\StoreUnitController::class);
    Route::get('/storeunits/{id}/generate-pdf',[App\Http\Controllers\StoreUnitController::class,'generatePDF'])->name('storeunit.generate-pdf');
    Route::get('/storeunits/{print}/generate-multi-pdf',[App\Http\Controllers\StoreUnitController::class,'generateMultiPDF'])->name('storeunit.generate-multi-pdf');
    Route::get('/storeunits/{print}/print-multi-pdf',[App\Http\Controllers\StoreUnitController::class,'printMultiPDF'])->name('storeunit.print-multi-pdf');
    Route::resource('/shipments', App\Http\Controllers\ShipmentController::class);
    Route::resource('/orders', App\Http\Controllers\OrdersController::class);

    // pozycje zamowienia
    Route::get('/orderdetail/{id}', [App\Http\Controllers\OrderDetailController::class,'index'])->name('orderdetail.index');
    Route::get('/orderdetail/create/{id}', [App\Http\Controllers\OrderDetailController::class,'create'])->name('orderdetail.create');
    Route::post('/orderdetail/add', [App\Http\Controllers\OrderDetailController::class,'save'])->name('orderdetail.save');
    Route::delete('/orderdetail/{id}/delete', [App\Http\Controllers\OrderDetailController::class,'destroy'])->name('orderdetail.destroy');
    Route::post('/orderdetail/{id}/send-pick', [App\Http\Controllers\OrderDetailController::class,'sendpick'])->name('orderdetail.sendpick');

    // pick
    Route::get('/picks', [App\Http\Controllers\PickController::class,'index'])->name('pick.index');
    Route::get('/picks-store-unit/{id}', [App\Http\Controllers\PickController::class,'picklistsu'])->name('pick.storeunit');
    Route::get('/picks-store-unit/{id}/create/{su}', [App\Http\Controllers\PickController::class,'licklistsu_save'])->name('pick.licklistsu_save');
    Route::post('/picks-store-unit/{id}/close/{su}', [App\Http\Controllers\PickController::class,'licklistsu_close'])->name('pick.licklistsu_close');

    Route::get('/pick-list/{id}', [App\Http\Controllers\PickController::class,'picklist'])->name('pick.picklist');
    Route::post('/pick-list/{id}', [App\Http\Controllers\PickController::class,'picklist2'])->name('pick.picklist2');
    Route::post('/pick-list2/save', [App\Http\Controllers\PickController::class,'picklistsave'])->name('pick.picklistsave');
    Route::get('/pick-list/view/{id}',[App\Http\Controllers\PickController::class,'view'])->name('pick.view');
    Route::post('/pick-list/view/{id}',[App\Http\Controllers\PickController::class,'pickclose'])->name('pick.close');

    // produkt
    Route::get('/products', [App\Http\Controllers\ProductController::class,'index'])->name('products.index');
    Route::get('/products/create', [App\Http\Controllers\ProductController::class,'create'])->name('products.create');
    Route::post('/products/create', [App\Http\Controllers\ProductController::class,'store'])->name('products.store');
    Route::get('/products/{id}/edit', [App\Http\Controllers\ProductController::class,'edit'])->name('products.edit');
    Route::put('/products/{product}/edit', [App\Http\Controllers\ProductController::class,'update'])->name('products.update');
    Route::delete('/products/{id}/delete', [App\Http\Controllers\ProductController::class,'destroy'])->name('products.destroy');

    // miejsce
    Route::get('/locations', [App\Http\Controllers\LocationController::class,'index'])->name('locations.index');
    Route::get('/locations/create', [App\Http\Controllers\LocationController::class,'create'])->name('locations.create');
    Route::post('/locations/create', [App\Http\Controllers\LocationController::class,'store'])->name('locations.store');
    Route::get('/locations/{id}/edit', [App\Http\Controllers\LocationController::class,'edit'])->name('locations.edit');
    Route::put('/locations/{location}/edit', [App\Http\Controllers\LocationController::class,'update'])->name('locations.update');
    Route::delete('/locations/{id}/delete', [App\Http\Controllers\LocationController::class,'destroy'])->name('locations.destroy');

    // pozycje dostawy
    Route::get('/shipmentdetail/{id}', [App\Http\Controllers\ShipmentDetailController::class,'index'])->name('shipmentdetail.index');
    Route::get('/shipmentdetail/{id}/show', [App\Http\Controllers\ShipmentDetailController::class,'show'])->name('shipmentdetail.show');
    Route::get('/shipmentdetail/{shipment}/create', [App\Http\Controllers\ShipmentDetailController::class,'create'])->name('shipmentdetail.create');
    Route::post('/shipmentdetail/{shipment}/create', [App\Http\Controllers\ShipmentDetailController::class,'store'])->name('shipmentdetail.store');
    Route::delete('/shipmentdetail/{id}/destroy', [App\Http\Controllers\ShipmentDetailController::class,'destroy'])->name('shipmentdetail.destroy');
    Route::post('/shipmentdetail/{id}/sendcontrol', [App\Http\Controllers\ShipmentDetailController::class,'sendcontrol'])->name('shipmentdetail.send');

    Route::get('/createmulti', [\App\Http\Controllers\LocationMultiController::class,'create'])->name('locations.createmulti');
    Route::post('/createmulti', [\App\Http\Controllers\LocationMultiController::class,'store'])->name('locations.storemulti');
    //Route::resource('/storearea', App\Http\Controllers\StoreAreaController::class);

    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // kontrola dostawy
    Route::get('/shipment-control',[App\Http\Controllers\ControlController::class,'index'])->name('control.index');
    Route::get('/shipment-control/{id}',[App\Http\Controllers\ControlController::class,'view'])->name('control.view');
    Route::get('/shipment-control/{id}/add/{loc}',[App\Http\Controllers\ControlController::class,'add'])->name('control.add');
    Route::post('/shipment-control/{id}/add/{loc}',[App\Http\Controllers\ControlController::class,'store'])->name('control.store');
    Route::post('/shipment-control/{id}/close',[App\Http\Controllers\ControlController::class,'close'])->name('control.close');
    Route::post('/shipment-control/{id}/closepos',[App\Http\Controllers\ControlController::class,'closePos'])->name('control.closepos');

    // przesuniecia opakowania
    Route::get('/move-storeunit',[App\Http\Controllers\MoveController::class,'storeunitshow'])->name('move.su');
    Route::get('/move-storeunit/{id}/step2',[App\Http\Controllers\MoveController::class,'storeunitshow2'])->name('move.su2');
    Route::post('/move-storeunit/move',[App\Http\Controllers\MoveController::class,'storeunitsave'])->name('move.savestoreunit');
    //przesuniecia produktu
    Route::get('/move-product',[App\Http\Controllers\MoveController::class,'productshow'])->name('move.product');
    Route::get('/move-product/{stock}/step2',[App\Http\Controllers\MoveController::class,'productshow2'])->name('move.product2');
    Route::post('/move-product/move',[App\Http\Controllers\MoveController::class,'productsave'])->name('move.saveproduct');
    // przesuniecie na mag logiczny
    Route::get('/move-area',[App\Http\Controllers\MoveController::class,'areashow'])->name('move.area');
    Route::get('/move-area/{stock}/step2',[App\Http\Controllers\MoveController::class,'areashow2'])->name('move.area2');
    Route::post('/move-area/move',[App\Http\Controllers\MoveController::class,'areasave'])->name('move.savearea');

    // wyszukiwanie opakowania
    Route::get('/findstoreunit',[App\Http\Controllers\MoveController::class,'findstoreunit'])->name('findstoreunit');
    Route::get('/findlocation',[App\Http\Controllers\MoveController::class,'findlocation'])->name('findlocation');

    // raporty
    Route::get('/report-stock',[App\Http\Controllers\ReportController::class,'reportstock'])->name('report.stock');
    Route::get('/report-product/{id}',[App\Http\Controllers\ReportController::class,'reportproduct'])->name('report.product');
});
