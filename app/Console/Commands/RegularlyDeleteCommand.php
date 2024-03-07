<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TLpOrder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;

class RegularlyDeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batch:regularlyDelete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '作成してから2年経過し、保持設定されていない構成とその画像を削除する';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            // 二年前日付取得
            $now = Carbon::now();
            $two_years_ago = $now->subYear(2);

            // 二年前データ削除処理
            $lp_order = TLpOrder::where('created_at', '<=', $two_years_ago)->where('requirement_flag', 0);
            $lps = $lp_order->get();
            $lp_order->delete();

            // 画像フォルダ削除処理
            $lps->each(function ($lp) {
                $path = 'public/lp_order/' . $lp->id;
                Storage::deleteDirectory($path);
            });
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
