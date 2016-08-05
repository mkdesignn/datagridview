<?php 

namespace mkdesign82\datagridview;
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
		$this->app['router']->post('test', '\mkdesign82\datagridview\TestController@postIndex');
	}

}
