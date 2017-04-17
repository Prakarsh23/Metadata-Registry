<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class SetupAccessTables
 */
class SetupAccessTables extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table(config('access.users_table'),
        function (Blueprint $table)  {
          $table->tinyInteger('status')->after('password')->default(1)->unsigned();
        });
    Schema::create(config('access.roles_table'),
        function (Blueprint $table) {
          $table->increments('id')->unsigned();
          $table->string('name', 191);
          $table->string('display_name', 191)->nullable();
          $table->boolean('all')->default(false);
          $table->smallInteger('sort')->default(0)->unsigned();
          $table->timestamps();
          /**
           * Add Foreign/Unique/Index
           */
          $table->unique('name');
        });
    Schema::create(config('access.role_user_table'),
        function (Blueprint $table)  {
          $table->increments('id')->unsigned();
          $table->integer('user_id');
          $table->integer('role_id')->unsigned();
          /**
           * Add Foreign/Unique/Index
           */
          $table->foreign('user_id')
              ->references('id')
              ->on(config('access.users_table'))
              ->onDelete('cascade');
          $table->foreign('role_id')
              ->references('id')
              ->on(config('access.roles_table'))
              ->onDelete('cascade');
        });
    Schema::create(config('access.permissions_table'),
        function (Blueprint $table)  {
          $table->increments('id')->unsigned();
          $table->string('name', 191);
          $table->string('display_name', 191);
          $table->smallInteger('sort')->default(0)->unsigned();
          $table->timestamps();
          /**
           * Add Foreign/Unique/Index
           */
          $table->unique('name');
        });
    Schema::create(config('access.permission_role_table'),
        function (Blueprint $table)  {
          $table->increments('id')->unsigned();
          $table->integer('permission_id')->unsigned();
          $table->integer('role_id')->unsigned();
          /**
           * Add Foreign/Unique/Index
           */
          $table->foreign('permission_id')
              ->references('id')
              ->on(config('access.permissions_table'))
              ->onDelete('cascade');
          $table->foreign('role_id')
              ->references('id')
              ->on(config('access.roles_table'))
              ->onDelete('cascade');
        });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    if (Schema::hasColumn(config('access.users_table'), 'status')) {
      Schema::table(config('access.users_table'),
          function (Blueprint $table) {
            $table->dropColumn('status');
          });
    }
    /**
     * Remove Foreign/Unique/Index
     */
    Schema::table(config('access.roles_table'),
        function (Blueprint $table) {
          $table->dropUnique(config('access.roles_table') . '_name_unique');
        });
    Schema::table(config('access.role_user_table'),
        function (Blueprint $table) {
          $table->dropForeign(config('access.role_user_table') . '_user_id_foreign');
          $table->dropForeign(config('access.role_user_table') . '_role_id_foreign');
        });
    Schema::table(config('access.permissions_table'),
        function (Blueprint $table) {
          $table->dropUnique(config('access.permissions_table') . '_name_unique');
        });
    Schema::table(config('access.permission_role_table'),
        function (Blueprint $table) {
          $table->dropForeign(config('access.permission_role_table') . '_permission_id_foreign');
          $table->dropForeign(config('access.permission_role_table') . '_role_id_foreign');
        });
    /**
     * Drop tables
     */
    Schema::dropIfExists(config('access.permission_role_table'));
    Schema::dropIfExists(config('access.role_user_table'));
    Schema::dropIfExists(config('access.roles_table'));
    Schema::dropIfExists(config('access.permissions_table'));
  }
}
