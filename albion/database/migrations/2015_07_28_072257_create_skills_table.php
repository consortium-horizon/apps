<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->increments('id');
            // FIGHTER
			// Trainee Fighter
            $table->integer('f_tf')->default(0); 
            // WARRIOR
			// Journeyman Warrior
            $table->integer('f_jw')->default(0); 
			// Epee
            $table->integer('f_wa1')->default(0);
            $table->integer('f_wa2')->default(0);
            $table->integer('f_wa3')->default(0);
            $table->integer('f_wa4')->default(0);
            $table->integer('f_wa5')->default(0);
            $table->integer('f_wa6')->default(0);
			// Hache
            $table->integer('f_wb1')->default(0);
            $table->integer('f_wb2')->default(0);
            $table->integer('f_wb3')->default(0);
            $table->integer('f_wb4')->default(0);
            $table->integer('f_wb5')->default(0);
            $table->integer('f_wb6')->default(0);
			// Mace
            $table->integer('f_wc1')->default(0);
            $table->integer('f_wc2')->default(0);
            $table->integer('f_wc3')->default(0);
            $table->integer('f_wc4')->default(0);
            $table->integer('f_wc5')->default(0);
            $table->integer('f_wc6')->default(0);
			// Marteau
            $table->integer('f_wd1')->default(0);
            $table->integer('f_wd2')->default(0);
            $table->integer('f_wd3')->default(0);
            $table->integer('f_wd4')->default(0);
            $table->integer('f_wd5')->default(0);
            $table->integer('f_wd6')->default(0);
			// Arbalete
            $table->integer('f_we1')->default(0);
            $table->integer('f_we2')->default(0);
            $table->integer('f_we3')->default(0);
            $table->integer('f_we4')->default(0);
            $table->integer('f_we5')->default(0);
            $table->integer('f_we6')->default(0);
			// Bouclier
            $table->integer('f_wf1')->default(0);
            $table->integer('f_wf2')->default(0);
            $table->integer('f_wf3')->default(0);
            $table->integer('f_wf4')->default(0);
            $table->integer('f_wf5')->default(0);
            $table->integer('f_wf6')->default(0);
			// Tete plaque
            $table->integer('f_wg1')->default(0);
            $table->integer('f_wg2')->default(0);
            $table->integer('f_wg3')->default(0);
            $table->integer('f_wg4')->default(0);
            $table->integer('f_wg5')->default(0);
            $table->integer('f_wg6')->default(0);
			// Torse plaque
            $table->integer('f_wh1')->default(0);
            $table->integer('f_wh2')->default(0);
            $table->integer('f_wh3')->default(0);
            $table->integer('f_wh4')->default(0);
            $table->integer('f_wh5')->default(0);
            $table->integer('f_wh6')->default(0);
			// Botte plaque
            $table->integer('f_wi1')->default(0);
            $table->integer('f_wi2')->default(0);
            $table->integer('f_wi3')->default(0);
            $table->integer('f_wi4')->default(0);
            $table->integer('f_wi5')->default(0);
            $table->integer('f_wi6')->default(0);
 
            // HUNTER
			// Journeyman Hunter
            $table->integer('f_jh')->default(0);
			// Arc
            $table->integer('f_ha1')->default(0);
            $table->integer('f_ha2')->default(0);
            $table->integer('f_ha3')->default(0);
            $table->integer('f_ha4')->default(0);
            $table->integer('f_ha5')->default(0);
            $table->integer('f_ha6')->default(0);
			// Lance
            $table->integer('f_hb1')->default(0);
            $table->integer('f_hb2')->default(0);
            $table->integer('f_hb3')->default(0);
            $table->integer('f_hb4')->default(0);
            $table->integer('f_hb5')->default(0);
            $table->integer('f_hb6')->default(0);
			// Baton Nature
            $table->integer('f_hc1')->default(0);
            $table->integer('f_hc2')->default(0);
            $table->integer('f_hc3')->default(0);
            $table->integer('f_hc4')->default(0);
            $table->integer('f_hc5')->default(0);
            $table->integer('f_hc6')->default(0);
			// Dague
            $table->integer('f_hd1')->default(0);
            $table->integer('f_hd2')->default(0);
            $table->integer('f_hd3')->default(0);
            $table->integer('f_hd4')->default(0);
            $table->integer('f_hd5')->default(0);
            $table->integer('f_hd6')->default(0);
			// Arme de lancer
            $table->integer('f_he1')->default(0);
            $table->integer('f_he2')->default(0);
            $table->integer('f_he3')->default(0);
            $table->integer('f_he4')->default(0);
            $table->integer('f_he5')->default(0);
            $table->integer('f_he6')->default(0);
			// Torche
            $table->integer('f_hf1')->default(0);
            $table->integer('f_hf2')->default(0);
            $table->integer('f_hf3')->default(0);
            $table->integer('f_hf4')->default(0);
            $table->integer('f_hf5')->default(0);
            $table->integer('f_hf6')->default(0);
			// Tete cuir
            $table->integer('f_hg1')->default(0);
            $table->integer('f_hg2')->default(0);
            $table->integer('f_hg3')->default(0);
            $table->integer('f_hg4')->default(0);
            $table->integer('f_hg5')->default(0);
            $table->integer('f_hg6')->default(0);
			// Torse cuir
            $table->integer('f_hh1')->default(0);
            $table->integer('f_hh2')->default(0);
            $table->integer('f_hh3')->default(0);
            $table->integer('f_hh4')->default(0);
            $table->integer('f_hh5')->default(0);
            $table->integer('f_hh6')->default(0);
			// Botte cuir
            $table->integer('f_hi1')->default(0);
            $table->integer('f_hi2')->default(0);
            $table->integer('f_hi3')->default(0);
            $table->integer('f_hi4')->default(0);
            $table->integer('f_hi5')->default(0);
            $table->integer('f_hi6')->default(0);
 
            // MAGE
			// Journeyman Mage
            $table->integer('f_jm')->default(0);
			// Baton Feu
            $table->integer('f_ma1')->default(0);
            $table->integer('f_ma2')->default(0);
            $table->integer('f_ma3')->default(0);
            $table->integer('f_ma4')->default(0);
            $table->integer('f_ma5')->default(0);
            $table->integer('f_ma6')->default(0);
			// Baton Holy
            $table->integer('f_mb1')->default(0);
            $table->integer('f_mb2')->default(0);
            $table->integer('f_mb3')->default(0);
            $table->integer('f_mb4')->default(0);
            $table->integer('f_mb5')->default(0);
            $table->integer('f_mb6')->default(0);
			// Baton Arcane
            $table->integer('f_mc1')->default(0);
            $table->integer('f_mc2')->default(0);
            $table->integer('f_mc3')->default(0);
            $table->integer('f_mc4')->default(0);
            $table->integer('f_mc5')->default(0);
            $table->integer('f_mc6')->default(0);
			// Baton Glace
            $table->integer('f_md1')->default(0);
            $table->integer('f_md2')->default(0);
            $table->integer('f_md3')->default(0);
            $table->integer('f_md4')->default(0);
            $table->integer('f_md5')->default(0);
            $table->integer('f_md6')->default(0);
			// Baton Maudit
            $table->integer('f_me1')->default(0);
            $table->integer('f_me2')->default(0);
            $table->integer('f_me3')->default(0);
            $table->integer('f_me4')->default(0);
            $table->integer('f_me5')->default(0);
            $table->integer('f_me6')->default(0);
			// Tome
            $table->integer('f_mf1')->default(0);
            $table->integer('f_mf2')->default(0);
            $table->integer('f_mf3')->default(0);
            $table->integer('f_mf4')->default(0);
            $table->integer('f_mf5')->default(0);
            $table->integer('f_mf6')->default(0);
			// Tete tissus
            $table->integer('f_mg1')->default(0);
            $table->integer('f_mg2')->default(0);
            $table->integer('f_mg3')->default(0);
            $table->integer('f_mg4')->default(0);
            $table->integer('f_mg5')->default(0);
            $table->integer('f_mg6')->default(0);
			// torse tissus
            $table->integer('f_mh1')->default(0);
            $table->integer('f_mh2')->default(0);
            $table->integer('f_mh3')->default(0);
            $table->integer('f_mh4')->default(0);
            $table->integer('f_mh5')->default(0);
            $table->integer('f_mh6')->default(0);
			// Botte tissus
            $table->integer('f_mi1')->default(0);
            $table->integer('f_mi2')->default(0);
            $table->integer('f_mi3')->default(0);
            $table->integer('f_mi4')->default(0);
            $table->integer('f_mi5')->default(0);
            $table->integer('f_mi6')->default(0);
 
            // GATHER
			// Trainee Gatherer
            $table->integer('g_tg')->default(0);
			// Fiber Harverst
            $table->integer('g_fh')->default(0);
			// Animal Skinner
            $table->integer('g_as')->default(0);
			// Ore Miner
            $table->integer('g_om')->default(0);
			// Quarry Man
            $table->integer('g_qm')->default(0);
			// LumberJack
            $table->integer('g_lj')->default(0);
 
            // FARMER
			// Trainee Farmer
            $table->integer('fa_tf')->default(0);
			// Goat Kebab
            $table->integer('fa_gk')->default(0);
			// Pork Chops
            $table->integer('fa_pc')->default(0);
			// Meat Pie
            $table->integer('fa_mp')->default(0);
			// Cake
            $table->integer('fa_ck')->default(0);
			// Farmer's Meal
            $table->integer('fa_fm')->default(0);
			// Journeyman Breeder
            $table->integer('fa_jb')->default(0);
			// Adepts Breeder
            $table->integer('fa_ab')->default(0);
			// Expert Breeder
            $table->integer('fa_eb')->default(0);
			// Journeyman Farmer
            $table->integer('fa_jf')->default(0);
			// Adepts Farmer
            $table->integer('fa_af')->default(0);
			// Expert Farmer
            $table->integer('fa_ef')->default(0);
			// Journeyman Alchemist
            $table->integer('fa_ja')->default(0);
			// Adepts Alchemist
            $table->integer('fa_aa')->default(0);
			// Expert Alchemist
            $table->integer('fa_ea')->default(0);
 
            // CRAFTER
			// Trainee Crafter
            $table->integer('c_tc')->default(0);
 
            // WARRIOR
			// Journeyman Warrior
            $table->integer('c_jw')->default(0);
            // Epee
            $table->integer('c_wa1')->default(0);
            $table->integer('c_wa2')->default(0);
            $table->integer('c_wa3')->default(0);
            $table->integer('c_wa4')->default(0);
            $table->integer('c_wa5')->default(0);
            $table->integer('c_wa6')->default(0);
			// Hache
            $table->integer('c_wb1')->default(0);
            $table->integer('c_wb2')->default(0);
            $table->integer('c_wb3')->default(0);
            $table->integer('c_wb4')->default(0);
            $table->integer('c_wb5')->default(0);
            $table->integer('c_wb6')->default(0);
			// Mace
            $table->integer('c_wc1')->default(0);
            $table->integer('c_wc2')->default(0);
            $table->integer('c_wc3')->default(0);
            $table->integer('c_wc4')->default(0);
            $table->integer('c_wc5')->default(0);
            $table->integer('c_wc6')->default(0);
			// Marteau
            $table->integer('c_wd1')->default(0);
            $table->integer('c_wd2')->default(0);
            $table->integer('c_wd3')->default(0);
            $table->integer('c_wd4')->default(0);
            $table->integer('c_wd5')->default(0);
            $table->integer('c_wd6')->default(0);
			// Arbalete
            $table->integer('c_we1')->default(0);
            $table->integer('c_we2')->default(0);
            $table->integer('c_we3')->default(0);
            $table->integer('c_we4')->default(0);
            $table->integer('c_we5')->default(0);
            $table->integer('c_we6')->default(0);
			// Bouclier
            $table->integer('c_wf1')->default(0);
            $table->integer('c_wf2')->default(0);
            $table->integer('c_wf3')->default(0);
            $table->integer('c_wf4')->default(0);
            $table->integer('c_wf5')->default(0);
            $table->integer('c_wf6')->default(0);
			// Tete plaque
            $table->integer('c_wg1')->default(0);
            $table->integer('c_wg2')->default(0);
            $table->integer('c_wg3')->default(0);
            $table->integer('c_wg4')->default(0);
            $table->integer('c_wg5')->default(0);
            $table->integer('c_wg6')->default(0);
			// Torse plaque
            $table->integer('c_wh1')->default(0);
            $table->integer('c_wh2')->default(0);
            $table->integer('c_wh3')->default(0);
            $table->integer('c_wh4')->default(0);
            $table->integer('c_wh5')->default(0);
            $table->integer('c_wh6')->default(0);
			// Botte plaque
            $table->integer('c_wi1')->default(0);
            $table->integer('c_wi2')->default(0);
            $table->integer('c_wi3')->default(0);
            $table->integer('c_wi4')->default(0);
            $table->integer('c_wi5')->default(0);
            $table->integer('c_wi6')->default(0);
 
            // HUNTER
			// Journeyman Hunter
            $table->integer('c_jh')->default(0);
			// Arc
            $table->integer('c_ha1')->default(0);
            $table->integer('c_ha2')->default(0);
            $table->integer('c_ha3')->default(0);
            $table->integer('c_ha4')->default(0);
            $table->integer('c_ha5')->default(0);
            $table->integer('c_ha6')->default(0);
			// Lance
            $table->integer('c_hb1')->default(0);
            $table->integer('c_hb2')->default(0);
            $table->integer('c_hb3')->default(0);
            $table->integer('c_hb4')->default(0);
            $table->integer('c_hb5')->default(0);
            $table->integer('c_hb6')->default(0);
			// Baton Nature
            $table->integer('c_hc1')->default(0);
            $table->integer('c_hc2')->default(0);
            $table->integer('c_hc3')->default(0);
            $table->integer('c_hc4')->default(0);
            $table->integer('c_hc5')->default(0);
            $table->integer('c_hc6')->default(0);
			// Dague
            $table->integer('c_hd1')->default(0);
            $table->integer('c_hd2')->default(0);
            $table->integer('c_hd3')->default(0);
            $table->integer('c_hd4')->default(0);
            $table->integer('c_hd5')->default(0);
            $table->integer('c_hd6')->default(0);
			// Arme de lancer
            $table->integer('c_he1')->default(0);
            $table->integer('c_he2')->default(0);
            $table->integer('c_he3')->default(0);
            $table->integer('c_he4')->default(0);
            $table->integer('c_he5')->default(0);
            $table->integer('c_he6')->default(0);
			// Torche
            $table->integer('c_hf1')->default(0);
            $table->integer('c_hf2')->default(0);
            $table->integer('c_hf3')->default(0);
            $table->integer('c_hf4')->default(0);
            $table->integer('c_hf5')->default(0);
            $table->integer('c_hf6')->default(0);
			// Tete cuir
            $table->integer('c_hg1')->default(0);
            $table->integer('c_hg2')->default(0);
            $table->integer('c_hg3')->default(0);
            $table->integer('c_hg4')->default(0);
            $table->integer('c_hg5')->default(0);
            $table->integer('c_hg6')->default(0);
			// Torse cuir
            $table->integer('c_hh1')->default(0);
            $table->integer('c_hh2')->default(0);
            $table->integer('c_hh3')->default(0);
            $table->integer('c_hh4')->default(0);
            $table->integer('c_hh5')->default(0);
            $table->integer('c_hh6')->default(0);
			// Botte cuir
            $table->integer('c_hi1')->default(0);
            $table->integer('c_hi2')->default(0);
            $table->integer('c_hi3')->default(0);
            $table->integer('c_hi4')->default(0);
            $table->integer('c_hi5')->default(0);
            $table->integer('c_hi6')->default(0);
 
            // MAGE
			// Journeyman Mage
            $table->integer('c_jm')->default(0);
			// Baton Feu
            $table->integer('c_ma1')->default(0);
            $table->integer('c_ma2')->default(0);
            $table->integer('c_ma3')->default(0);
            $table->integer('c_ma4')->default(0);
            $table->integer('c_ma5')->default(0);
            $table->integer('c_ma6')->default(0);
			// Baton Holy
            $table->integer('c_mb1')->default(0);
            $table->integer('c_mb2')->default(0);
            $table->integer('c_mb3')->default(0);
            $table->integer('c_mb4')->default(0);
            $table->integer('c_mb5')->default(0);
            $table->integer('c_mb6')->default(0);
			// Baton Arcane
            $table->integer('c_mc1')->default(0);
            $table->integer('c_mc2')->default(0);
            $table->integer('c_mc3')->default(0);
            $table->integer('c_mc4')->default(0);
            $table->integer('c_mc5')->default(0);
            $table->integer('c_mc6')->default(0);
			// Baton Glace
            $table->integer('c_md1')->default(0);
            $table->integer('c_md2')->default(0);
            $table->integer('c_md3')->default(0);
            $table->integer('c_md4')->default(0);
            $table->integer('c_md5')->default(0);
            $table->integer('c_md6')->default(0);
			// Baton Maudit
            $table->integer('c_me1')->default(0);
            $table->integer('c_me2')->default(0);
            $table->integer('c_me3')->default(0);
            $table->integer('c_me4')->default(0);
            $table->integer('c_me5')->default(0);
            $table->integer('c_me6')->default(0);
			// Tome
            $table->integer('c_mf1')->default(0);
            $table->integer('c_mf2')->default(0);
            $table->integer('c_mf3')->default(0);
            $table->integer('c_mf4')->default(0);
            $table->integer('c_mf5')->default(0);
            $table->integer('c_mf6')->default(0);
			// Tete tissus
            $table->integer('c_mg1')->default(0);
            $table->integer('c_mg2')->default(0);
            $table->integer('c_mg3')->default(0);
            $table->integer('c_mg4')->default(0);
            $table->integer('c_mg5')->default(0);
            $table->integer('c_mg6')->default(0);
			// torse tissus
            $table->integer('c_mh1')->default(0);
            $table->integer('c_mh2')->default(0);
            $table->integer('c_mh3')->default(0);
            $table->integer('c_mh4')->default(0);
            $table->integer('c_mh5')->default(0);
            $table->integer('c_mh6')->default(0);
			// Botte tissus
            $table->integer('c_mi1')->default(0);
            $table->integer('c_mi2')->default(0);
            $table->integer('c_mi3')->default(0);
            $table->integer('c_mi4')->default(0);
            $table->integer('c_mi5')->default(0);
            $table->integer('c_mi6')->default(0);
 
            // TOOL CRAFTER
			// Journeyman Toolmaker
            $table->integer('c_jt')->default(0);
			// Bag Tailor
            $table->integer('c_bt')->default(0);
			// Cape Tailor
            $table->integer('c_ct')->default(0);
			// Axe Crafter
            $table->integer('c_ac')->default(0);
			// Stone Hammer Crafter
            $table->integer('c_shc')->default(0);
			// Picaxe Crafter
            $table->integer('c_px')->default(0);
			// Skinning Knife Crafter
            $table->integer('c_skc')->default(0);
			// Sickle Crafter
            $table->integer('c_sc')->default(0);
			// Demolition Hammer Crafter
            $table->integer('c_dhc')->default(0);
 
            $table->integer('userID')->index()->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('skills');
    }
}
