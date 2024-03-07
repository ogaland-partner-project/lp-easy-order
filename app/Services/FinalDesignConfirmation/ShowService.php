<?php

namespace App\Services\FinalDesignConfirmation;

use App\Models\TFinalDesignConfirmation;

class ShowService
{
    /**
     * ### 最終デザイン情報取得
     *
     * * t_final_design_confirmations.lp_order_id とリクエストパラメータのlp_order_idが一致するレコードを取得
     * 
     * @param int $lpOrderId
     * @return array
     */
    public function execShow($lpOrderId)
    {
        // 抽出対象の最終デザイン情報取得
        $finalDesignConfirmations = TFinalDesignConfirmation::where('lp_order_id', $lpOrderId)
            ->orderBy('id')
            ->get();

        // 抽出データをレスポンス用データとして設定
        $data = $finalDesignConfirmations->map(function ($finalDesignConfirmation) {
            return [
                'id' => $finalDesignConfirmation->id,
                'lp_order_id' => $finalDesignConfirmation->lp_order_id,
                'image_path' => $finalDesignConfirmation->image_path,
                'file' => null,
                'design_memo' => $finalDesignConfirmation->design_memo,
                'law_support_memo' => $finalDesignConfirmation->law_support_memo,
                'info_manage_memo' => $finalDesignConfirmation->info_manage_memo,
                'sort_order' => $finalDesignConfirmation->sort_order,
            ];
        })->toArray();
        
        $data = ['design_parts' => $data];
        return $data;
    }
}