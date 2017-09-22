<?php
namespace Wbe\Crud\seeds;
use Illuminate\Database\Seeder;
//use Wbe\Crud\migrations;
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
		$this->call(CreateContentTypeTable::class);
		$this->call(CreateContentTypeDescriptionTable::class);
		$this->call(CreateContentTypeFieldsDescriptionTable::class);
		$this->call(CreateContentTypeFieldsTable::class);
		$this->call(CreateLanguagesTable::class);
		$this->call(CreateRelationsDescriptionTable::class);
		$this->call(CreateRelationsTable::class);
		$this->call(CreateRolesTable::class);
		$this->call(CreateUsersTable::class);
		//$this->call(CreateContentTypeDescriptionTable::class);
    }
}
