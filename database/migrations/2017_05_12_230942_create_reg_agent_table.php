<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegAgentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reg_agent', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->timestamp('last_updated')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->softDeletes();
			$table->string('org_email', 100)->default('');
			$table->string('org_name')->default('');
			$table->string('ind_affiliation')->nullable();
			$table->string('ind_role', 45)->nullable();
			$table->string('address1')->nullable();
			$table->string('address2')->nullable();
			$table->string('city', 45)->nullable();
			$table->char('state', 2)->nullable();
			$table->string('postal_code', 15)->nullable();
			$table->char('country', 3)->nullable();
			$table->string('phone', 45)->nullable();
			$table->string('web_address')->nullable();
			$table->char('type', 15)->nullable();
			$table->string('repo')->nullable();
			$table->boolean('is_private')->default(0);
			$table->string('license')->nullable();
			$table->text('description')->nullable();
			$table->integer('created_by')->unsigned()->nullable()->index();
			$table->integer('updated_by')->unsigned()->nullable()->index();
			$table->integer('deleted_by')->unsigned()->nullable()->index();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('reg_agent');
	}

}