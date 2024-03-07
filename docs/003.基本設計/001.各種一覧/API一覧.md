# API 一覧

### 変更履歴

| ver   | 日付       | 概要 |
| :---- | :--------- | :--- |
| 1.0.0 | 2023-11-14 | 初版 |

## API 一覧


| Domain | Method | URI                                                     | Name | Action                                                     | Middleware |
| :----- | :----- | :------------------------------------------------------ | :--- | :--------------------------------------------------------- | :--------- |
|        | POST   | api/lp_easy_order/basic_knowledge                       |      | App\Http\Controllers\API\BasicKnowledgeApi@create          | api        |
|        | POST   | api/lp_easy_order/basic_knowledge/copy                  |      | App\Http\Controllers\API\BasicKnowledgeApi@copy            | api        |
|        | PUT    | api/lp_easy_order/basic_knowledge/{BasicKnowledgeId}    |      | App\Http\Controllers\API\BasicKnowledgeApi@update          | api        |
|        | GET    | api/lp_easy_order/basic_knowledge/{lpOrderId}           |      | App\Http\Controllers\API\BasicKnowledgeApi@show            | api        |
|        | POST   | api/lp_easy_order/companies_comparison                  |      | App\Http\Controllers\API\CompaniesComparisonApi@create     | api        |
|        | POST   | api/lp_easy_order/companies_comparison/copy             |      | App\Http\Controllers\API\CompaniesComparisonApi@copy       | api        |
|        | GET    | api/lp_easy_order/companies_comparison/{lpOrderId}      |      | App\Http\Controllers\API\CompaniesComparisonApi@show       | api        |
|        | PUT    | api/lp_easy_order/companies_comparison/{lpOrderId}      |      | App\Http\Controllers\API\CompaniesComparisonApi@update     | api        |
|        | POST   | api/lp_easy_order/comparison_insert                     |      | App\Http\Controllers\API\ComparisonInsertApi@create        | api        |
|        | POST   | api/lp_easy_order/comparison_insert/copy                |      | App\Http\Controllers\API\ComparisonInsertApi@copy          | api        |
|        | GET    | api/lp_easy_order/comparison_insert/{lpOrderId}         |      | App\Http\Controllers\API\ComparisonInsertApi@show          | api        |
|        | PUT    | api/lp_easy_order/comparison_insert/{lpOrderId}         |      | App\Http\Controllers\API\ComparisonInsertApi@update        | api        |
|        | POST   | api/lp_easy_order/constitution_plan                     |      | App\Http\Controllers\API\ConstitutionPlanApi@create        | api        |
|        | POST   | api/lp_easy_order/constitution_plan/copy                |      | App\Http\Controllers\API\ConstitutionPlanApi@copy          | api        |
|        | GET    | api/lp_easy_order/constitution_plan/{lpOrderId}         |      | App\Http\Controllers\API\ConstitutionPlanApi@show          | api        |
|        | PUT    | api/lp_easy_order/constitution_plan/{lpOrderId}         |      | App\Http\Controllers\API\ConstitutionPlanApi@update        | api        |
|        | POST   | api/lp_easy_order/constitution_process                  |      | App\Http\Controllers\API\ConstitutionProcessApi@create     | api        |
|        | POST   | api/lp_easy_order/constitution_process/copy             |      | App\Http\Controllers\API\ConstitutionProcessApi@copy       | api        |
|        | GET    | api/lp_easy_order/constitution_process/{lpOrderId}      |      | App\Http\Controllers\API\ConstitutionProcessApi@show       | api        |
|        | PUT    | api/lp_easy_order/constitution_process/{lpOrderId}      |      | App\Http\Controllers\API\ConstitutionProcessApi@update     | api        |
|        | POST   | api/lp_easy_order/final_design_confirmation             |      | App\Http\Controllers\API\FinalDesignConfirmationApi@create | api        |
|        | POST   | api/lp_easy_order/final_design_confirmation/copy        |      | App\Http\Controllers\API\FinalDesignConfirmationApi@copy   | api        |
|        | GET    | api/lp_easy_order/final_design_confirmation/{lpOrderId} |      | App\Http\Controllers\API\FinalDesignConfirmationApi@show   | api        |
|        | PUT    | api/lp_easy_order/final_design_confirmation/{lpOrderId} |      | App\Http\Controllers\API\FinalDesignConfirmationApi@update | api        |
|        | GET    | api/lp_easy_order/home                                  |      | App\Http\Controllers\API\HomeApi@index                     | api        |
|        | POST   | api/lp_easy_order/home                                  |      | App\Http\Controllers\API\HomeApi@create                    | api        |
|        | POST   | api/lp_easy_order/home/copy                             |      | App\Http\Controllers\API\HomeApi@copy                      | api        |
|        | PUT    | api/lp_easy_order/home/{lpOrderId}                      |      | App\Http\Controllers\API\HomeApi@update                    | api        |
|        | DELETE | api/lp_easy_order/home/{lpOrderId}                      |      | App\Http\Controllers\API\HomeApi@delete                    | api        |
|        | POST   | api/lp_easy_order/item_karte                            |      | App\Http\Controllers\API\ItemKarteApi@create               | api        |
|        | POST   | api/lp_easy_order/item_karte/copy                       |      | App\Http\Controllers\API\ItemKarteApi@copy                 | api        |
|        | PUT    | api/lp_easy_order/item_karte/{itemKarteId}              |      | App\Http\Controllers\API\ItemKarteApi@update               | api        |
|        | GET    | api/lp_easy_order/item_karte/{lpOrderId}                |      | App\Http\Controllers\API\ItemKarteApi@show                 | api        |
|        | POST   | api/lp_easy_order/level_select                          |      | App\Http\Controllers\API\LevelSelectApi@create             | api        |
|        | GET    | api/lp_easy_order/level_select/{lpOrderId}              |      | App\Http\Controllers\API\LevelSelectApi@show               | api        |
|        | PUT    | api/lp_easy_order/level_select/{lpOrderId}              |      | App\Http\Controllers\API\LevelSelectApi@update             | api        |
|        | POST   | api/lp_easy_order/lock                                  |      | App\Http\Controllers\API\LockApi@create                    | api        |
|        | DELETE | api/lp_easy_order/lock                                  |      | App\Http\Controllers\API\LockApi@delete                    | api        |
|        | DELETE | api/lp_easy_order/lock/all/{id}                         |      | App\Http\Controllers\API\LockApi@unlock                    | api        |
|        | POST   | api/lp_easy_order/lock/check                            |      | App\Http\Controllers\API\LockApi@show                      | api        |