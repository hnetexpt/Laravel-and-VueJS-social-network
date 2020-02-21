<?php

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
use App\Content;
Route::get('/permalink/{id}', function($id){
      $content=Content::find($id);
      echo "<pre>";
      var_dump(json_decode($content->json_content, true));


      die();
});

Route::get('/{any}', function(){
        return view('welcome');
})->where('any', '.*');
