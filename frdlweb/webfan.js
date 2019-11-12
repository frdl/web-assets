exports = module.exports = function(config){

	
	var preferences = require('./../preferences.json');
	/*  $preferences->routes->assets->route /app/assets/[modules|themes:type]/[:hash]/[:vendor]/[:package]/[**:path] */
	var route = preferences.routes.assets.route;
	
	var frdl = require('@frdl/functions'); 
	
	var project = require('./../../../frdl.project.json');
	
	route = frdl.str_replace('[:vendor]/', '', route);
	route = frdl.str_replace('[:package]/', '', route);
	route = frdl.str_replace('[**:path]', '', route);
	route = frdl.str_replace('[:hash]', project.hash, route);
	
	var route_module_assets = project.ce_baseUrl + frdl.str_replace('[modules|themes:type]', 'modules', route);
	var route_theme_assets = project.ce_baseUrl + frdl.str_replace('[modules|themes:type]', 'themes', route);
	
	config.hps.scriptengine.requirejs.paths['module-assets'] =  frdl.str_replace('//', '/',route_module_assets);
	config.hps.scriptengine.requirejs.paths['theme-assets'] = frdl.str_replace('//', '/',route_theme_assets);
	
};