<?php

namespace App\Services\BasicKnowledge;

use App\Models\TLpOrder;

class DeleteService
{
    /**
     * ### データ削除処理
     *
     * @param array $param
     */
    public function execDelete($param){

        $lpOrder = TLpOrder::find($param['lp_order_id']);

        if($lpOrder){
            $lpOrder->basicKnowledges->each(function ($basicKnowledge) {
                $basicKnowledge->basicKnowledgeDetails->each(function ($basicKnowledgeDetails) {
                    $basicKnowledgeDetails->delete();
                });
                $basicKnowledge->basicKnowledgeImages->each(function ($basicKnowledgeImages) {
                    $basicKnowledgeImages->delete();
                });
                $basicKnowledge->basicKnowledgeUrls->each(function ($basicKnowledgeUrls) {
                    $basicKnowledgeUrls->delete();
                });
                $basicKnowledge->delete();
            });
        }
    }
}
