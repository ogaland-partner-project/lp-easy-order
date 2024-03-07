<?php

namespace App\Http\Middleware;

use Closure;

class Referrer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // config経由でenvの値を取得
        $accessAllowFlag = config('app.ACCESS_ALLOW_FLAG');
        $app_url = config('app.url');
        $access_allow_urls = config('app.ACCESS_ALLOW_URLS');
        $accessErrorUrl = config('app.ACCESS_ERROR_URL');

        // 許可フラグがtrueならアクセス制御処理不要(直アクセス許可：開発時など)
        if($accessAllowFlag) return $next($request);

        // http_refererが存在しない場合→エラーページ
        if(!isset($_SERVER['HTTP_REFERER'])) return redirect($accessErrorUrl);

        // http_refererが存在する場合
        $ref = stripslashes($_SERVER['HTTP_REFERER']);
        $isArrowUrlFlag = $this->isArrowUrlAccess($ref, $access_allow_urls);
        $isOwnUrlFlag = $this->isOwnUrlAccess($ref, $app_url);
        if(!$isArrowUrlFlag && !$isOwnUrlFlag) {
            // 許可されたURLまたは自分のアプリURL以外からのアクセスの場合はエラーページにリダイレクト
            return redirect($accessErrorUrl);
        }

        return $next($request);
    }

    /**
     * 許可されたURLからのアクセスか判定
     *
     * @param [type] $referrer
     * @param [type] $allowUrls
     * @return boolean
     */
    private function isArrowUrlAccess($referrer, $allowUrls) {
        if(!in_array($referrer, $allowUrls)) {
            return false;
        }
        return true;
    }

    /**
     * 自アプリからのアクセスか判定
     *
     * @param [type] $referrer
     * @param [type] $appUrl
     * @return boolean
     */
    private function isOwnUrlAccess($referrer, $appUrl) {
        $str = "{".$appUrl."}";
        if(!preg_match($str, $referrer)) {
            return false;
        }
        return true;
    }
}
