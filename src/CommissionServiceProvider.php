<?php

namespace WinTop;

use Illuminate\Support\ServiceProvider;

class CommissionServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->loadMigrationsFrom(__DIR__.'/databases/migrations');
	}
}
