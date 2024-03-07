<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            TLpOrderSeeder::class,
            TItemKarteSeeder::class,
            TRawMaterialSeeder::class,
            TBasicKnowledgeSeeder::class,
            TBasicKnowledgeDetailSeeder::class,
            TBasicKnowledgeImageSeeder::class,
            TBasicKnowledgeUrlSeeder::class,
        ]);
    }
}
