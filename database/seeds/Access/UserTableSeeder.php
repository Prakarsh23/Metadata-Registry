<?php

use Carbon\Carbon as Carbon;
use Illuminate\Database\Seeder;

/**
 * Class UserTableSeeder
 */
class UserTableSeeder extends Seeder
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
      DB::table(config('access.users_table'))->truncate();
    } elseif (DB::connection()->getDriverName() == 'sqlite') {
      DB::statement('DELETE FROM ' . config('access.users_table'));
    } else {
      //For PostgreSQL or anything else
      DB::statement('TRUNCATE TABLE ' . config('access.users_table') . ' CASCADE');
    }

    //Add the master administrator, user id of 1
    $users = [
        [
            'id'                => '1',
            'name'              => 'Admin User',
            'first_name'        => 'Admin',
            'last_name'         => 'User',
            'nickname'          => 'adminuser',
            'email'             => 'admin@admin.com',
            'password'          => bcrypt('1234'),
            'confirmation_code' => md5(uniqid(mt_rand(), true)),
            'confirmed'         => true,
            'culture'           => 'en_US',
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ],
        [
            'id'                => '2',
            'name'              => 'Backend User',
            'first_name'        => 'Backend',
            'last_name'         => 'User',
            'nickname'          => 'backenduser',
            'email'             => 'executive@executive.com',
            'password'          => bcrypt('1234'),
            'confirmation_code' => md5(uniqid(mt_rand(), true)),
            'confirmed'         => true,
            'culture'           => 'en_US',
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ],
        [
            'id'                => '3',
            'name'              => 'Default User',
            'first_name'        => 'Default',
            'last_name'         => 'User',
            'nickname'          => 'defaultuser',
            'email'             => 'user@user.com',
            'password'          => bcrypt('1234'),
            'confirmation_code' => md5(uniqid(mt_rand(), true)),
            'confirmed'         => true,
            'culture'           => 'en_US',
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ],
        [
            'id'                => '36',
            'name'              => 'Jon Phipps',
            'first_name'        => 'Jon',
            'last_name'         => 'Phipps',
            'nickname'          => 'jonphipps',
            'email'             => 'jphipps@madcreek.com',
            'password'          => bcrypt('1234'),
            'confirmation_code' => md5(uniqid(mt_rand(), true)),
            'confirmed'         => true,
            'culture'           => 'en_US',
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ],
        [
            'id'                => '39',
            'name'              => 'Diane Hillmann',
            'first_name'        => 'Diane',
            'last_name'         => 'Hillmann',
            'nickname'          => 'Diane',
            'email'             => 'diane.hillmann@cornell.edu',
            'password'          => bcrypt('1234'),
            'confirmation_code' => md5(uniqid(mt_rand(), true)),
            'confirmed'         => true,
            'culture'           => 'en_US',
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ],
    ];

    DB::table(config('access.users_table'))->insert($users);

    $this->enableForeignKeys();
  }
}