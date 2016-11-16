<?php 

namespace mkdesignn\datagridview;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class MkDatagridviewServiceProvider extends ServiceProvider{


	public function register(){
		$this->app->bind("DataGrid", function($app){
			return new Core();
		});

		$this->app->singleton('db', function ($app) {
			return new DbMyClass($app, $app['db.factory']);
		});
	}

	public function provides()
    {
        return ['customPackage'];
    }

	public function boot(){

		Event::listen('illuminate.query', function($query, $params, $time, $conn)
		{
			Log::info($query);
		});

		$this->app['router']->post('datagridview', '\mkdesignn\datagridview\DataGridViewController@postIndex');
	}

}
