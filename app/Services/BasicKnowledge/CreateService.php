<?php

namespace App\Services\BasicKnowledge;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Common\CommonMsg;
use App\Exceptions\AppException;
use App\Models\TLpOrder;
use App\Models\TBasicKnowledge;
use App\Models\TBasicKnowledgeDetail;
use App\Models\TBasicKnowledgeImage;
use App\Models\TBasicKnowledgeUrl;
use App\Services\Common\CommonService;
class CreateService extends BasicKnowledgeServiceBase
{
    /**
     * 基礎知識情報新規登録
     *
     * @param array $param
     */
    public function execCreate($param)
    {
        try {
            DB::beginTransaction();

            // t_lp_ordersテーブルにlp_order_idが存在しない場合はエラー
            if (!TLpOrder::find($param['lp_order_id'])) {
                throw new AppException(CommonMsg::MSG_ID_000805 . $param['lp_order_id']);
            }

            // t_basic_knowledgesテーブルへの登録
            $basicKnowledge = new TBasicKnowledge();
            $row = $param;
            unset($row['details']);
            unset($row['images']);
            unset($row['urls']);
            $row = array_merge($row, [
                'created_pg' => 'BasicKnowledge.CreateService.execCreate',
                'updated_pg' => 'BasicKnowledge.CreateService.execCreate',
            ]);
            $basicKnowledge->fill($row)->save();

            // t_basic_knowledge_detailsテーブルへの登録
            collect($param['details'])->each(function ($colGroup, $colIndex) use ($basicKnowledge) {
                collect($colGroup)->each(function ($row, $i) use ($basicKnowledge, $colIndex) {
                    $basicKnowledgeDetail = new TBasicKnowledgeDetail();
                    $row = array_merge($row, [
                        'basic_knowledge_id' => $basicKnowledge->id,
                        'col' => $colIndex,     // details[]のインデックス番号
                        'sort_order' => $i,     // details[][]のインデックス番号
                        'created_pg' => 'BasicKnowledge.CreateService.execCreate',
                        'updated_pg' => 'BasicKnowledge.CreateService.execCreate',
                    ]);
                    $basicKnowledgeDetail->fill($row)->save();
                });
            });

            // t_basic_knowledge_imagesテーブルへの登録
            collect($param['images'])->each(function ($row) use ($basicKnowledge,$param) {
                $basicKnowledgeImage = new TBasicKnowledgeImage();
                $row = array_merge($row, [
                    'basic_knowledge_id' => $basicKnowledge->id,
                    'created_pg' => 'BasicKnowledge.CreateService.execCreate',
                    'updated_pg' => 'BasicKnowledge.CreateService.execCreate',
                ]);
                $basicKnowledgeImage->fill($row)->save();

                // fileパラメータに値がある場合は画像ファイルを保存
                if (isset($row['file']) && !is_null($row['file'])) {
                    $service = new CommonService();
                    $service->saveImageFiletFromParamString($basicKnowledgeImage, $param['lp_order_id'] ,$row['file'],'BasicKnowledge','image_path',null);
                }
            });

            // t_basic_knowledge_urlsテーブルへの登録
            collect($param['urls'])->each(function ($row) use ($basicKnowledge) {
                $basicKnowledgeUrl = new TBasicKnowledgeUrl();
                $row = array_merge($row, [
                    'basic_knowledge_id' => $basicKnowledge->id,
                    'created_pg' => 'BasicKnowledge.CreateService.execCreate',
                    'updated_pg' => 'BasicKnowledge.CreateService.execCreate',
                ]);
                $basicKnowledgeUrl->fill($row)->save();
            });

            DB::commit();
            return $basicKnowledge->id;
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
