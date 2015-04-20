<?php
namespace AwCore\Http\Controllers;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Routing\Controller; 
use Auth;
use URL;
use App;
use View;
use Validator;
use Module;


use Repositories\Event\EventInterface as EventInterface ;

class BaseController extends Controller {

	public $title;
	public $breadcrumbs = array();
	public $menu = "main";
	public $alert_count;
	
	protected $modules;
	protected $filters;
	protected $actions;
	
	public function __construct() {
		/*[[TODO need to make this work again but using modules]]
		if($event && Auth::check()) {
			$this->event = $event;
			$this->alert_count = $this->event->getAlertCount(Auth::user()->id);
		}*/
    	$this->title = str_replace(array("Controller", "AwCore"), "", get_class($this));
    	$this->breadcrumbs[] = array(URL::to('/'), "Home");
    	
		$modules = Module::enabled();
		if(is_array($modules) && count($modules)){
			foreach($modules as $module){
				$slug = $module['slug'];
				$path = "\AwCore\Modules\\".$slug."\\".$slug."";
				$this->modules[$slug] = App::make($path);
				if(isset($this->modules[$slug]->filters) && is_array($this->modules[$slug]->filters) && count($this->modules[$slug]->filters)){
					foreach($this->modules[$slug]->filters as $filter=>$method){
						$this->filters[$filter][] = array("module"=>$slug, "method"=>$method);
					}
				}
				if(isset($this->modules[$slug]->actions) && is_array($this->modules[$slug]->actions) && count($this->modules[$slug]->actions)){
					foreach($this->modules[$slug]->actions as $action=>$method){
						$this->actions[$action][] = array("module"=>$slug, "method"=>$method);
					}
				}
			}
		}
	}


	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		
		if ( ! is_null($this->layout))
		{
			
			$this->layout = View::make($this->layout)
				->with("alert_count", $this->alert_count)
				->with("product_name", $this->modulesFilterHTML("AwCore","setProductName"));
		}
		
	}
	
	public function doLayout($content, $path=false){
		return $this->_doLayout($content, $path);
	}
	
	protected function _doLayout($content, $path=false){
		
		$this->layout->breadcrumbs = View::make("layouts.breadcrumbs")
				->with("breadcrumbs", $this->breadcrumbs);
		
		$menucontent = $this->modulesFilterHTML("","getMenu_".$this->menu);
		
		$this->layout->menu = View::make("layouts.".$this->menu."menu")
				->with("menuContent", $menucontent);
		
		if($path){
			

			return $this->layout->content = View::make($path."::$content");
		}
		return $this->layout->content = View::make($content);	
	}


	/**
	 * Execute an action on the controller.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function callAction($method, $parameters)
	{
		$this->setupLayout();

		$response = call_user_func_array(array($this, $method), $parameters);

		// If no response is returned from the controller action and a layout is being
		// used we will assume we want to just return the layout view as any nested
		// views were probably bound on this view during this controller actions.
		if (is_null($response) && ! is_null($this->layout))
		{
			$response = $this->layout;
		}
		
		View::share('title', $this->title);
		
		return $response;
	}

	protected function modulesFilterHTML($html, $filter, $options=null){
		if(isset($this->filters[$filter]) && is_array($this->filters[$filter]) && count($this->filters[$filter])){
			foreach($this->filters[$filter] as $filter_arr){
				if(method_exists($this->modules[$filter_arr['module']], $filter_arr['method'])){
					$html = $this->modules[$filter_arr['module']]->$filter_arr['method']($html, $options);
				}
			}
		}
		return $html;
	}

	protected function modulesAction($action, $options=null){
		if(isset($this->filters[$filter]) && is_array($this->filters[$filter]) && count($this->filters[$filter])){
			$response = array("cnt"=>0);
			foreach($this->filters[$filter] as $filter_arr){
				if(method_exists($this->modules[$filter_arr['module']], $filter_arr['method'])){
					$this->modules[$filter_arr['module']]->$filter_arr['method']($response, $options);
				}
			}
		}
		return $response;
	}

	

}
