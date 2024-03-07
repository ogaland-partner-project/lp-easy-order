<?php

namespace App\Services\BasicKnowledge;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Common\CommonMsg;
use App\Exceptions\AppException;
use App\Models\TLpOrder;
use App\Models\TBasicKnowledge;
use App\Models\TBasicKnowledgeDetail;
use App\Models\TBasicKnowledgeImage;
use App\Models\TBasicKnowledgeUrl;
use App\Services\Common\CommonService;

class UpdateService extends BasicKnowledgeServiceBase
{
    /**
     * 基礎知識情報更新
     *
     * @param int $basicKnowledgeId
     * @param array $param
     */
    public function execUpdate($basicKnowledgeId, $param)
    {
        $savedImageFiles = collect([]);
        try {
            DB::beginTransaction();

            $basicKnowledge = TBasicKnowledge::find($basicKnowledgeId);
            if (!$basicKnowledge) {
                throw new AppException(CommonMsg::MSG_ID_000006);
            }

            $row = $param;
            unset($row['details']);
            unset($row['images']);
            unset($row['urls']);
            $row = array_merge($row, [
                'created_pg' => 'BasicKnowledge.CreateService.execCreate',
                'updated_pg' => 'BasicKnowledge.CreateService.execCreate',
            ]);
            $basicKnowledge->fill($row)->save();

            // 詳細情報
            $this->updateBasicKnowledgeDetails($basicKnowledge, $param['details']);

            // 画像情報
            $this->updateBasicKnowledgeImages($basicKnowledge, $param['images'], $savedImageFiles);

            // URL情報
            $this->updateBasicKnowledgeUrls($basicKnowledge, $param['urls']);

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            // セーブした画像ファイルを削除する
            $savedImageFiles->each(function ($file) {
                Storage::delete(preg_replace('/^\/storage\//', '', $file));
            });
            throw $ex;
        }
    }

    /**
     * 基礎知識詳細情報の登録・更新・削除
     *
     * @param BasicKnowledge $basicKnowledge
     * @param array $rows
     */
    private function updateBasicKnowledgeDetails(TBasicKnowledge $basicKnowledge, $rows)
    {
        // パラメータにidが含まれていないレコードは削除
        $ids = collect($rows)->reduce(function ($ids, $rows) {
            return $ids->merge(collect($rows)->map(function ($row) {
                return isset($row['id']) ? $row['id'] : null;
            }));
        }, collect([]))
        ->filter(function ($id) {
            return !is_null($id);
        });
        $basicKnowledge->basicKnowledgeDetails->each(function ($basicKnowledgeDetail) use ($ids) {
            if (!$ids->contains($basicKnowledgeDetail->id)) {
                $basicKnowledgeDetail->delete();
            }
        });

        collect($rows)->each(function ($colGroup, $colIndex) use ($basicKnowledge) {
            collect($colGroup)->each(function ($row, $i) use ($basicKnowledge, $colIndex) {
                // idが指定されている場合は更新、そうでなければ登録
                $basicKnowledgeDetail = null;
                if (isset($row['id'])) {
                    $basicKnowledgeDetail = TBasicKnowledgeDetail::find($row['id']);
                    if (!$basicKnowledgeDetail) {
                        throw new AppException(CommonMsg::MSG_ID_000806 . $row['id']);
                    }
                    if ($basicKnowledgeDetail->basic_knowledge_id !== $basicKnowledge->id) {
                        throw new AppException(CommonMsg::MSG_ID_000807 . $row['id']);
                    }
                } else {
                    $basicKnowledgeDetail = new TBasicKnowledgeDetail();
                    $row = array_merge($row, [
                        'basic_knowledge_id' => $basicKnowledge->id,
                        'created_pg' => 'BasicKnowledge.UpdateService.updateBasicKnowledgeDetails',
                    ]);
                }

                $row = array_merge($row, [
                    'col' => $colIndex,     // details[]のインデックス番号
                    'sort_order' => $i,     // details[][]のインデックス番号
                    'updated_pg' => 'BasicKnowledge.UpdateService.updateBasicKnowledgeDetails',
                ]);
                $basicKnowledgeDetail->fill($row)->save();
            });
        });
    }

    /**
     * 基礎知識画像情報の登録・更新・削除
     *
     * @param BasicKnowledge $basicKnowledge
     * @param array $rows
     * @param Collection $savedImageFiles セーブした画像ファイルパス
     */
    private function updateBasicKnowledgeImages(TBasicKnowledge $basicKnowledge, $rows, $savedImageFiles)
    {
        // パラメータにidが含まれていないレコードは削除
        $ids = collect($rows)->filter(function ($row) {
            return isset($row['id']) && !is_null($row['id']);
        })->map(function ($row) {
            return $row['id'];
        });
        $basicKnowledge->basicKnowledgeImages->each(function ($basicKnowledgeImage) use ($ids) {
            if (!$ids->contains($basicKnowledgeImage->id)) {
                $basicKnowledgeImage->delete();
            }
        });

        collect($rows)->each(function ($row) use ($basicKnowledge, $savedImageFiles) {
            // idが指定されている場合は更新、そうでなければ登録
            $basicKnowledgeImage = null;
            if (isset($row['id'])) {
                $basicKnowledgeImage = TBasicKnowledgeImage::find($row['id']);
                if (!$basicKnowledgeImage) {
                    throw new AppException(CommonMsg::MSG_ID_000808 . $row['id']);
                }
                if ($basicKnowledgeImage->basic_knowledge_id !== $basicKnowledge->id) {
                    throw new AppException(CommonMsg::MSG_ID_000809 . $row['id']);
                }
            } else {
                $basicKnowledgeImage = new TBasicKnowledgeImage();
                $row = array_merge($row, [
                    'basic_knowledge_id' => $basicKnowledge->id,
                    'created_pg' => 'BasicKnowledge.UpdateService.updateBasicKnowledgeImages',
                ]);
            }
            $row['updated_pg'] = 'BasicKnowledge.UpdateService.updateBasicKnowledgeImages';
            $basicKnowledgeImage->fill($row)->save();

            // fileパラメータに値がある場合は画像ファイルを保存
            if (isset($row['file']) && !is_null($row['file'])) {
                $service = new CommonService();
                $service->saveImageFiletFromParamString($basicKnowledgeImage, $basicKnowledge->lp_order_id ,$row['file'],'BasicKnowledge','image_path',null);
                // $this->saveImageFiletFromParamString($basicKnowledgeImage, $row['file']);
                // $savedImageFiles->push($basicKnowledgeImage->image_path);
            }
        });
    }

    /**
     * 基礎知識URL情報の登録・更新・削除
     *
     * @param BasicKnowledge $basicKnowledge
     * @param array $rows
     */
    private function updateBasicKnowledgeUrls(TBasicKnowledge $basicKnowledge, $rows)
    {
        // パラメータにidが含まれていないレコードは削除
        $ids = collect($rows)->filter(function ($row) {
            return isset($row['id']) && !is_null($row['id']);
        })->map(function ($row) {
            return $row['id'];
        });
        $basicKnowledge->basicKnowledgeUrls->each(function ($basicKnowledgeUrl) use ($ids) {
            if (!$ids->contains($basicKnowledgeUrl->id)) {
                $basicKnowledgeUrl->delete();
            }
        });

        collect($rows)->each(function ($row) use ($basicKnowledge) {
            // - idが指定されている場合は更新、そうでなければ登録
            $basicKnowledgeUrl = null;
            if (isset($row['id'])) {
                $basicKnowledgeUrl = TBasicKnowledgeUrl::find($row['id']);
                if (!$basicKnowledgeUrl) {
                    throw new AppException(CommonMsg::MSG_ID_000810 . $row['id']);
                }
                if ($basicKnowledgeUrl->basic_knowledge_id !== $basicKnowledge->id) {
                    throw new AppException(CommonMsg::MSG_ID_000811 . $row['id']);
                }
            } else {
                $basicKnowledgeUrl = new TBasicKnowledgeUrl();
                $row = array_merge($row, [
                    'basic_knowledge_id' => $basicKnowledge->id,
                    'created_pg' => 'BasicKnowledge.UpdateService.updateBasicKnowledgeUrls',
                ]);
            }
            $row['updated_pg'] = 'BasicKnowledge.UpdateService.updateBasicKnowledgeUrls';
            $basicKnowledgeUrl->fill($row)->save();
        });
    }
}
