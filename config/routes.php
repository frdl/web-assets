<?php
use function DI\add;
//'GET|POST|PUT|DELETE|OPTIONS', '/bar/[:controller]/[:action].[:type]?', 'bar_action', 'bar_route'

 $preferences = json_decode(file_get_contents(__DIR__ .\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'preferences.json'));

 

return  [

	
   'routes' => add([
                  ['GET', $preferences->routes->assets->route, [\frdl\WebAssets\WebAssetsController::class, 'serve'], $preferences->routes->assets->name],
                 
	
         ]),
		
];