<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ホーム画面API
Route::get('/lp_easy_order/home', 'API\HomeApi@index');
Route::post('/lp_easy_order/home', 'API\HomeApi@create');
Route::put('/lp_easy_order/home/{lpOrderId}', 'API\HomeApi@update');
Route::delete('/lp_easy_order/home/{lpOrderId}', 'API\HomeApi@delete');
Route::post('/lp_easy_order/home/copy', 'API\HomeApi@copy');

// レベル別質問事項画面API
Route::get('/lp_easy_order/level_select/{lpOrderId}', 'API\LevelSelectApi@show');
Route::post('/lp_easy_order/level_select', 'API\LevelSelectApi@create');
Route::put('/lp_easy_order/level_select/{lpOrderId}', 'API\LevelSelectApi@update');

// 基礎知識画面API
Route::get('/lp_easy_order/basic_knowledge/{lpOrderId}', 'API\BasicKnowledgeApi@show');
Route::post('/lp_easy_order/basic_knowledge', 'API\BasicKnowledgeApi@create');
Route::put('/lp_easy_order/basic_knowledge/{BasicKnowledgeId}', 'API\BasicKnowledgeApi@update');
Route::post('/lp_easy_order/basic_knowledge/copy', 'API\BasicKnowledgeApi@copy');

// 商品カルテ入力
Route::get('/lp_easy_order/item_karte/{lpOrderId}', 'API\ItemKarteApi@show');
Route::post('/lp_easy_order/item_karte', 'API\ItemKarteApi@create');
Route::put('/lp_easy_order/item_karte/{itemKarteId}', 'API\ItemKarteApi@update');
Route::post('/lp_easy_order/item_karte/copy', 'API\ItemKarteApi@copy');

// 他社比較入力画面API
Route::get('/lp_easy_order/comparison_insert/{lpOrderId}', 'API\ComparisonInsertApi@show');
Route::post('/lp_easy_order/comparison_insert', 'API\ComparisonInsertApi@create');
Route::put('/lp_easy_order/comparison_insert/{lpOrderId}', 'API\ComparisonInsertApi@update');
Route::post('/lp_easy_order/comparison_insert/copy', 'API\ComparisonInsertApi@copy');

// 他社構成比較画面API
Route::get('/lp_easy_order/companies_comparison/{lpOrderId}', 'API\CompaniesComparisonApi@show');
Route::post('/lp_easy_order/companies_comparison', 'API\CompaniesComparisonApi@create');
Route::put('/lp_easy_order/companies_comparison/{lpOrderId}', 'API\CompaniesComparisonApi@update');
Route::post('/lp_easy_order/companies_comparison/copy', 'API\CompaniesComparisonApi@copy');

// 構成の手順
Route::get('/lp_easy_order/constitution_process/{lpOrderId}', 'API\ConstitutionProcessApi@show');
Route::post('/lp_easy_order/constitution_process', 'API\ConstitutionProcessApi@create');
Route::put('/lp_easy_order/constitution_process/{lpOrderId}', 'API\ConstitutionProcessApi@update');
Route::post('/lp_easy_order/constitution_process/copy', 'API\ConstitutionProcessApi@copy');

// 構成案画面API
Route::get('/lp_easy_order/constitution_plan/{lpOrderId}', 'API\ConstitutionPlanApi@show');
Route::post('/lp_easy_order/constitution_plan', 'API\ConstitutionPlanApi@create');
Route::put('/lp_easy_order/constitution_plan/{lpOrderId}', 'API\ConstitutionPlanApi@update');
Route::post('/lp_easy_order/constitution_plan/copy', 'API\ConstitutionPlanApi@copy');

// 最終デザイン確認
Route::get('/lp_easy_order/final_design_confirmation/{lpOrderId}', 'API\FinalDesignConfirmationApi@show');
Route::post('/lp_easy_order/final_design_confirmation', 'API\FinalDesignConfirmationApi@create');
Route::put('/lp_easy_order/final_design_confirmation/{lpOrderId}', 'API\FinalDesignConfirmationApi@update');
Route::post('/lp_easy_order/final_design_confirmation/copy', 'API\FinalDesignConfirmationApi@copy');

// 編集ロックAPI
Route::post('/lp_easy_order/lock', 'API\LockApi@create');
Route::post('/lp_easy_order/lock/check', 'API\LockApi@show');
Route::delete('/lp_easy_order/lock', 'API\LockApi@delete');
Route::delete('/lp_easy_order/lock/all/{id}', 'API\LockApi@unlock');
