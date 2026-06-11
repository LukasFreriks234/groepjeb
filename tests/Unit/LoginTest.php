<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
	use RefreshDatabase;

	public function test_admin_can_log_in(): void
	{
		$admin = User::create([
			'name' => 'Admin User',
			'email' => 'admin@example.com',
			'password' => Hash::make('password'),
			'role' => 'admin',
		]);

		$this->post('/', [
			'email' => 'admin@example.com',
			'password' => 'password',
		])
			->assertRedirect('/grid');

		$this->assertAuthenticatedAs($admin);
	}

	public function test_city_planner_can_log_in(): void
	{
		$cityPlanner = User::create([
			'name' => 'City Planner',
			'email' => 'planner@example.com',
			'password' => Hash::make('password'),
			'role' => 'cityplanner',
		]);

		$this->post('/', [
			'email' => 'planner@example.com',
			'password' => 'password',
		])
			->assertRedirect('/grid');

		$this->assertAuthenticatedAs($cityPlanner);
	}
}
