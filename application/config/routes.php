<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route['api/healthcheck'] = 'AuthController/index';
$route['api/register'] = 'AuthController/register';
$route['api/login'] = 'AuthController/login';

// dispatcher route

$route['api/dispatchers']['get'] = 'DispatcherController/index';
$route['api/dispatchers/(:num)']['get'] = 'DispatcherController/index/$1';
$route['api/dispatchers']['post'] = 'DispatcherController/create';
$route['api/dispatchers/(:num)']['put'] = 'DispatcherController/update/$1';
$route['api/dispatchers/(:num)']['delete'] = 'DispatcherController/delete/$1';

// Driver route

$route['api/drivers']['post'] = 'DriverController/create';
$route['api/drivers']['get'] = 'DriverController/index';
$route['api/drivers/(:num)']['get'] = 'DriverController/show/$1';
$route['api/drivers/(:num)']['put'] = 'DriverController/update/$1';
$route['api/drivers/(:num)']['delete'] = 'DriverController/delete/$1';

//Truck route

$route['api/trucks']['get'] = 'TruckController/index';
$route['api/trucks/(:num)']['get'] = 'TruckController/view/$1';
$route['api/trucks']['post'] = 'TruckController/create';
$route['api/trucks/(:num)']['put'] = 'TruckController/update/$1';
$route['api/trucks/(:num)']['delete'] = 'TruckController/delete/$1';

// Trailer route

$route['api/trailers']['POST'] = 'TrailerController/create';
$route['api/trailers/(:num)']['GET'] = 'TrailerController/get/$1';
$route['api/trailers/(:num)']['PUT'] = 'TrailerController/update/$1';
$route['api/trailers/(:num)']['DELETE'] = 'TrailerController/delete/$1';



