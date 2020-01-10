<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(FormatGroupsSeed::class);
     //    $roles = [
     //    	['ADMINISTRATOR','Administrator'], 
     //    	['MEMBER', 'Member'],
     //    	['VISITOR', 'Visitor'],
     //    ];
	    // foreach ($roles as $role) {
	    //   Role::create([
	    //     'role' => $role[0],
	    //     'name' => $role[1]
	    //   ]);
	    // }


	    // $users = [
	    // 	[
	    // 		'1',
	    // 		'System',
	    // 		'Administrator',
	    // 		'IO',
	    // 		'Admin',
	    // 		null,
	    // 		null,
	    // 		'admin@storybook.com',
	    // 		'secret'
	    // 	]
	    // ];
	    // foreach ($users as $data) {
		   //  DB::table('users')->delete();
		   //  $user = new User([
		   //      'role_id' => $data[0],
		   //      'firstname' => $data[1],
		   //      'lastname' => $data[2],
		   //      'middlename' => $data[3],
		   //      'nickname' => $data[4],
		   //      'gender' => $data[5],
		   //      'birthdate' => $data[6],
		   //      'email' => $data[7],
		   //      'password' => bcrypt($data[8]),
		   //  ]);

		   //  $user->timestamps = false;
	    //     $user->save();
	    // }
    }
}
