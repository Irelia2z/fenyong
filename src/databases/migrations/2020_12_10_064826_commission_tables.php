<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CommissionTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('user')) {
			if (!Schema::hasColumn('user', 'group_id')) {
				Schema::table('user', function (Blueprint $table) {
					$table->integer('group_id');
				});
			}
		} else {
			Schema::create('user', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('group_id');
				$table->dateTime('timestamp_create');
				$table->dateTime('timestamp_update');
			});
		}

		Schema::create('fy_group', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name', 32);
			$table->tinyInteger('rebate_level_1', false, true)->comment('一级代理返利百分比； 百分比*100');
			$table->tinyInteger('rebate_level_2', false, true)->comment('二级代理返利百分比； 百分比*100');
			$table->tinyInteger('rebate_level_3', false, true)->comment('三级代理返利百分比； 百分比*100');
		});

		Schema::create('fy_relation', function (Blueprint $table) {
			$table->primary(['ancestor_id', 'descendant_id', 'distance']);
			$table->integer('ancestor_id')->comment('祖先id');
			$table->integer('descendant_id')->comment('子代id');
			$table->integer('distance')->comment('距离');
		});

		Schema::create('fy_commission', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->decimal('level_1', 7, 2)->comment('	一级用户消费所获佣金');
			$table->decimal('level_2', 7, 2)->comment('	二级用户消费所获佣金');
			$table->decimal('level_3', 7, 2)->comment('	三级用户消费所获佣金');
			$table->dateTime('timestamp_create');
			$table->dateTime('timestamp_update');
		});

		Schema::create('fy_statistical', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->decimal('level_1', 7, 2)->comment('一级用户所反佣金');
			$table->decimal('level_2', 7, 2)->comment('二级用户所反佣金');
			$table->decimal('level_3', 7, 2)->comment('三级用户所反佣金');
			$table->date('date_create');
		});

		Schema::create('fy_withdrawal', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->decimal('amount', 7, 2);
			$table->dateTime('timestamp_create');
			$table->dateTime('timestamp_update');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_group', function (Blueprint $table) {
			//
		});
	}
}
