<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

	public function run()
	{
		DB::table('users')->insert([
	        'name'     => 'Libardo Henao',
	        'email'    => 'libardo2011@gmail.com',
	        'password' => Hash::make('123456'),
	    ]);
	}

}
