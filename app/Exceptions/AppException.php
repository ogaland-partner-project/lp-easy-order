<?php

namespace App\Exceptions;

use Exception;

/**
 * 本アプリケーションで発生する例外クラス
 *
 * [仕様]
 * - HTTPステータス：200（正常）で返す※ゴリラにはいかない
 * - 想定される例外のため、メッセージは画面表示する
 *
 */
class AppException extends Exception {

    // コンストラクタ
    public function __construct($message, $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function validationErr($message, $code = 0)
    {
        parent::__construct($message, $code, null);
    }
}