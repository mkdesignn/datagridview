<?php 

namespace mkdesignn\datagridview;
use Illuminate\Support\ServiceProvider;

class MkDatagridviewServiceProvider extends ServiceProvider{


	public function register(){
		$this->app->bind("DataGrid", function($app){
			return new Core();
		});
	}

	public function provides()
    {
        return ['customPackage'];
    }

	public function boot(){
		$this->app['router']->post('datagridview', '\mkdesignn\datagridview\DataGridViewController@postIndex');
	}

}
