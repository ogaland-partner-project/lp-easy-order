<?php

namespace App\Http\Controllers\API;

use App\Exceptions\AppException;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\NeCsvImport;
use App\Imports\PacketCsvImport;
use App\Imports\PackCsvImport;
use App\Services\Common\CommonService;
use \Illuminate\Http\Response;
/**
 * APIの親クラス
 * @group ApiController
 */
class ApiController
{
    /**
     * レスポンスデータのセット
     *
     * @param array $datas
     * @param string $normalMsg
     * @param string $errMsg
     * @return \Illuminate\Http\JsonResponse
     */
    public function setResponse($datas, $normalMsg='', $errMsg='')
    {
        return response()->json([
            "dataArray"     => $datas,
            "normalMessage" => $normalMsg,
            "errorMessage"  => $errMsg,
        ]);
    }

    /**
     * CSVダウンロード入口
     *
     * @param [type] $file_name:ファイル名
     * @param [type] $headerArr:CSVで1行目に出すヘッダー
     * @param [type] $dataArray:データ
     * @return \Illuminate\Http\Response
     */
    public function csvDownload($fileName, $headerArray, $dataArray){

        array_unshift($dataArray, $headerArray);
        $csv = $this->toCsv($dataArray);
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        );

        return response()->make($csv, 200, $headers);
    }

    /**
     * $csv用にデータを整形
     *
     * @param [type] $data
     * @param string $toEncoding
     * @param string $srcEncoding
     * @return string
     */
    public function toCsv($data, $toEncoding='sjis-win', $srcEncoding='UTF-8'){
        $csv = '';
        foreach ($data as $row) {
            // カンマ区切り
            $row = implode(',', $row);
            $csv .= $row . "\r\n";
        }
        $csv = mb_convert_encoding($csv, $toEncoding, $srcEncoding);
        return $csv;
    }
}
