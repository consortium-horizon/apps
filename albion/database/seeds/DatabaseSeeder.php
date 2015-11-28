<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Items;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call('ItemsTableSeeder');

        $this->command->info('Items table seeded!');

        Model::reguard();
    }
}

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('items')->delete();
        // 'name', 'skillName', 'skillLevel', 'slot', 'tier', 'level', 'image', 'guildprice', 'marketprice', 'twoHand'
        // - ARMOR
        // -- Tier 2
        // --- Plate
        Items::create(
            ['name' => 'Novice\'s Plate Helmet' ,
             'skillName' => 'f_tf',
             'skillLevel' => 1,
             'slot' => 'Head',
             'tier' => 2,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Novice\'s Plate Armor' ,
             'skillName' => 'f_tf',
             'skillLevel' => 1,
             'slot' => 'Body',
             'tier' => 2,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Novice\'s Plate Boots' ,
             'skillName' => 'f_tf',
             'skillLevel' => 1,
             'slot' => 'Boots',
             'tier' => 2,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        // --- Leather
        Items::create(
            ['name' => 'Novice\'s Leather Hood' ,
             'skillName' => 'f_tf',
             'skillLevel' => 1,
             'slot' => 'Head',
             'tier' => 2,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Novice\'s Leather Armor' ,
             'skillName' => 'f_tf',
             'skillLevel' => 1,
             'slot' => 'Body',
             'tier' => 2,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Novice\'s Leather Shoes' ,
             'skillName' => 'f_tf',
             'skillLevel' => 1,
             'slot' => 'Boots',
             'tier' => 2,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        // --- Cloth
        Items::create(
            ['name' => 'Novice\'s Cloth Hood' ,
             'skillName' => 'f_tf',
             'skillLevel' => 1,
             'slot' => 'Head',
             'tier' => 2,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Novice\'s Robe' ,
             'skillName' => 'f_tf',
             'skillLevel' => 1,
             'slot' => 'Body',
             'tier' => 2,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Novice\'s Cloth Shoes' ,
             'skillName' => 'f_tf',
             'skillLevel' => 1,
             'slot' => 'Boots',
             'tier' => 2,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        // -- Tier 3
        // --- Plate
        Items::create(
            ['name' => 'Journeyman\'s Plate Helmet' ,
             'skillName' => 'f_jw',
             'skillLevel' => 1,
             'slot' => 'Head',
             'tier' => 3,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Journeyman\'s Plate Armor' ,
             'skillName' => 'f_jw',
             'skillLevel' => 1,
             'slot' => 'Body',
             'tier' => 3,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Journeyman\'s Plate Boots' ,
             'skillName' => 'f_jw',
             'skillLevel' => 1,
             'slot' => 'Boots',
             'tier' => 3,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        // --- Leather
        Items::create(
            ['name' => 'Journeyman\'s Leather Hood' ,
             'skillName' => 'f_jh',
             'skillLevel' => 1,
             'slot' => 'Head',
             'tier' => 3,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Journeyman\'s Leather Armor' ,
             'skillName' => 'f_jh',
             'skillLevel' => 1,
             'slot' => 'Body',
             'tier' => 3,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Journeyman\'s Leather Shoes' ,
             'skillName' => 'f_jh',
             'skillLevel' => 1,
             'slot' => 'Boots',
             'tier' => 3,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        // --- Cloth
        Items::create(
            ['name' => 'Journeyman\'s Cloth Hood' ,
             'skillName' => 'f_jm',
             'skillLevel' => 1,
             'slot' => 'Head',
             'tier' => 3,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Journeyman\'s Robe' ,
             'skillName' => 'f_jm',
             'skillLevel' => 1,
             'slot' => 'Body',
             'tier' => 3,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Journeyman\'s Cloth Shoes' ,
             'skillName' => 'f_jm',
             'skillLevel' => 1,
             'slot' => 'Boots',
             'tier' => 3,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        // Tier 4 Level 0
        // --- Plate
        Items::create(
            ['name' => 'Adept\'s Plate Helmet' ,
             'skillName' => 'f_wg1',
             'skillLevel' => 1,
             'slot' => 'Head',
             'tier' => 4,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Adept\'s Plate Armor' ,
             'skillName' => 'f_wh1',
             'skillLevel' => 1,
             'slot' => 'Body',
             'tier' => 4,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Adept\'s Plate Boots' ,
             'skillName' => 'f_wi1',
             'skillLevel' => 1,
             'slot' => 'Boots',
             'tier' => 4,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        // --- Leather
        Items::create(
            ['name' => 'Adept\'s Leather Hood' ,
             'skillName' => 'f_hg1',
             'skillLevel' => 1,
             'slot' => 'Head',
             'tier' => 4,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Adept\'s Leather Armor' ,
             'skillName' => 'f_hh1',
             'skillLevel' => 1,
             'slot' => 'Body',
             'tier' => 4,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Adept\'s Leather Shoes' ,
             'skillName' => 'f_hi1',
             'skillLevel' => 1,
             'slot' => 'Boots',
             'tier' => 4,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        // --- Cloth
        Items::create(
            ['name' => 'Adept\'s Cloth Hood' ,
             'skillName' => 'f_mg1',
             'skillLevel' => 1,
             'slot' => 'Head',
             'tier' => 4,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Adept\'s Robe' ,
             'skillName' => 'f_mh1',
             'skillLevel' => 1,
             'slot' => 'Body',
             'tier' => 4,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
        Items::create(
            ['name' => 'Adept\'s Cloth Shoes' ,
             'skillName' => 'f_mi1',
             'skillLevel' => 1,
             'slot' => 'Boots',
             'tier' => 4,
             'level' => 0,
             'image' => '',
             'guildprice' => '',
             'marketprice' => '',
             'twoHand' => false]);
    }
}
