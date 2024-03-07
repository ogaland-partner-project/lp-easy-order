<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * 不要なキーを除外し、配列同士を比較する。
     * 
     * @param array|\Illuminate\Support\Collection $expect
     * @param array|\Illuminate\Support\Collection $actual
     * @param array $exceptKeys 比較対象外とするキー
     * @return void
     */
    protected function assertArrayEquals($expect, $actual, $exceptKeys = [])
    {
        $exceptKeys = array_merge($exceptKeys, [
            'created_pg', 'created_at', 'updated_pg', 'updated_at', 'deleted_pg', 'deleted_at',
        ]);
        if (!is_array($expect)) {
            $expect = $expect->toArray();
        }
        if (!is_array($actual)) {
            $actual = $actual->toArray();
        }
        $expect = array_diff_key($expect, array_flip($exceptKeys));
        $actual = array_diff_key($actual, array_flip($exceptKeys));
        $this->assertEquals($expect, $actual);
    }

    /**
     * 画像ファイルのパラメータ文字列を取得する。
     *
     * @param string $filepath
     * @return string
     */
    protected function getImageFileString($filepath)
    {
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        $base64code = base64_encode(file_get_contents(base_path($filepath)));
        return 'data:image/' . $ext . ';base64,' . $base64code;
    }
}