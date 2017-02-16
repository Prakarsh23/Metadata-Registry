<?php

use Illuminate\Database\Seeder;

/**
 * Class UserRoleSeeder
 */
class UserRoleSeeder extends Seeder
{

    use \database\DisablesForeignKeys;


  /**
   * Run the database seed.
   *
   * @return void
   */
    public function run()
    {
        $this->disableForeignKeys();

        if (DB::connection()->getDriverName() == 'mysql') {
            DB::table(config('access.role_user_table'))->truncate();
        } elseif (DB::connection()->getDriverName() == 'sqlite') {
            DB::statement('DELETE FROM ' . config('access.role_user_table'));
        } else {
            //For PostgreSQL or anything else
            DB::statement('TRUNCATE TABLE ' . config('access.role_user_table') . ' CASCADE');
        }

        //Attach admin role to admin user
        $user_model = config('auth.providers.users.model');
        $user_model = new $user_model();
        $user_model::first()->attachRole(1);

        //Attach admin role to jonphipps user
        $user_model = config('auth.providers.users.model');
        $user_model = new $user_model();
        $user_model::find(36)->attachRole(1);

        //Attach admin role to diane user
        $user_model = config('auth.providers.users.model');
        $user_model = new $user_model();
        $user_model::find(39)->attachRole(1);

        //Attach executive role to executive user
        $user_model = config('auth.providers.users.model');
        $user_model = new $user_model();
        $user_model::find(2)->attachRole(2);

        //Attach user role to general user
        $user_model = config('auth.providers.users.model');
        $user_model = new $user_model();
        $user_model::find(3)->attachRole(3);

        $this->enableForeignKeys();
    }
}
