<?php
use Psr\Container\ContainerInterface;


return  [
	
		
	\frdl\WebAssets\WebAssetsController::class => function(ContainerInterface $c){
		return new \frdl\WebAssets\WebAssetsController($c->get('project'));
	},
	
	

		
];