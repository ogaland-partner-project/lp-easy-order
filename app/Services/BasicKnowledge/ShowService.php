<?php

namespace App\Services\BasicKnowledge;

use Illuminate\Http\Request;
use Exception;
use App\Models\TBasicKnowledge;

class ShowService extends BasicKnowledgeServiceBase
{
    /**
     * 基礎知識情報取得
     *
     * @param int $lpOrderId
     */
    public function execShow($lpOrderId)
    {
        $basicKnowledges = TBasicKnowledge::where('lp_order_id', $lpOrderId)
            ->orderBy('id')
            ->get();

        $data = $basicKnowledges->map(function ($basicKnowledge) {
            // 詳細情報
            $details = $basicKnowledge->basicKnowledgeDetails
                ->sortBy('col')->groupBy('col')->values()
                ->map(function ($rows) {
                    return $rows->sortBy('sort_order')
                        ->map(function ($row) {
                            return [
                                'id' => $row->id,
                                'basic_knowledge_id' => $row->basic_knowledge_id,
                                'title' => $row->title,
                                'detail' => $row->detail,
                                'col' => $row->col,
                                'sort_order' => $row->sort_order,
                            ];
                        })
                        ->values()->toArray();
                })
                ->toArray();

            // 画像情報
            $images = $basicKnowledge->basicKnowledgeImages
                ->sortBy('id')
                ->map(function ($basicKnowledgeImage) {
                    return [
                        'id' => $basicKnowledgeImage->id,
                        'image_path' => $basicKnowledgeImage->image_path,
                        'image_memo' => $basicKnowledgeImage->image_memo,
                    ];
                })
                ->values()->toArray();

            // URL情報
            $urls = $basicKnowledge->basicKnowledgeUrls
                ->sortBy('id')
                ->map(function ($basicKnowledgeUrl) {
                    return [
                        'id' => $basicKnowledgeUrl->id,
                        'url' => $basicKnowledgeUrl->url,
                        'url_text' => $basicKnowledgeUrl->url_text,
                    ];
                })
                ->values()->toArray();

            return [
                'basic_knowledge_id' => $basicKnowledge->id,
                'question' => $basicKnowledge->question,
                'others' => $basicKnowledge->others,
                'details' => $details,
                'images' => $images,
                'urls' => $urls,
            ];
        })->toArray();

        return $data;
    }
}
