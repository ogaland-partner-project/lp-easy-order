<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use App\Models\TConstitutionProcess;
use App\Models\TConstitutionFixBlock;

class TConstitutionFixBlockTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $row_1 = TConstitutionProcess::where("concept_word", "コンセプト01")->first();
        $row_2 = TConstitutionProcess::where("concept_word", "コンセプト02")->first();
        $model = TConstitutionFixBlock::create([
            'constitution_process_id' => $row_1['id'],
            'block_detail' => 'ブロックFix01',
            'sort_order' => '1',
        ]);
        $model = TConstitutionFixBlock::create([
            'constitution_process_id' => $row_2['id'],
            'block_detail' => 'ブロックFix02',
            'sort_order' => '1',
        ]);
    }
    
}
