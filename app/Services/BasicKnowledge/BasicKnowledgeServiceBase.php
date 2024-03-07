<?php

namespace App\Services\BasicKnowledge;

use Exception;
use Illuminate\Support\Facades\Storage;
use App\Models\TBasicKnowledgeImage;
use App\Common\CommonMsg;

class BasicKnowledgeServiceBase
{
    /**
     * パラメータ文字列を元に基礎知識画像ファイルを保存する
     *
     * @param TBasicKnowledgeImage $basicKnowledgeImage 基礎知識画像ファイルオブジェクト
     * @param string $paramString ファイルパラメータ文字列
     */
    public function saveImageFiletFromParamString(TBasicKnowledgeImage $basicKnowledgeImage, $paramString)
    {
        preg_match('/^data:image\/(\w+);base64\,(.+)$/', $paramString, $matches);
        if (count($matches) <= 2) {
            throw new AppException(CommonMsg::MSG_ID_000804);
        }
        $ext = $matches[1];
        $base64code = $matches[2];
        $path = sprintf(
            'lp_order/%d/basicknowledge/%d.%s',
            $basicKnowledgeImage->basicKnowledge->lp_order_id,
            $basicKnowledgeImage->id,
            $ext
        );
        Storage::put($path, base64_decode($base64code));

        $basicKnowledgeImage->image_path = '/storage/' . $path;
        $basicKnowledgeImage->save();
    }
}
