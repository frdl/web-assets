<?php

namespace frdl\WebAssets;

use GuzzleHttp\Psr7\Response;


class WebAssetsController
{
	
    const MIMETYPES = [
    'js' => 'application/javascript',
    'css' => 'text/css', 
    'html' => 'text/html',
    'xml' => 'application/xml',
    'txt' => 'text/plain',	
    'jpeg' => 'image/jpeg',
    'jpg' => 'image/jpeg',
    'gif' => 'image/gif',
    'bmp' => 'image/bmp',
    'png' => 'image/png',
    'ico' => 'image/x-ico',
    'json' => 'application/json',	
    'svg' => 'image/svg+xml',
    'svgz' => 'image/svg+xml',
   ];
	
	protected $project;
	
    public function __construct(\compiled\project $project){
	     $this->project = $project;	
	}
	
	public function serve($type, $hash, $vendor, $package, $path) {
		
		$file = $this->project->dir . \DIRECTORY_SEPARATOR;
		
		switch($type){
			case 'modules' :
				  $file.= $this->project->modules_dirname  . \DIRECTORY_SEPARATOR . $vendor .'.'. $package . \DIRECTORY_SEPARATOR;
				break;
			case 'themes' :
				  $file.= 'themes'  . \DIRECTORY_SEPARATOR . $vendor .\DIRECTORY_SEPARATOR. $package . \DIRECTORY_SEPARATOR;
				break;
		}
		
		$p = explode('?', $path);		
		
		$file.= 'public'  . \DIRECTORY_SEPARATOR . str_replace('./', '', str_replace('..', '.',$p[0]));

    	$fileParts = explode('.', $file);
	    $ext = $fileParts[count($fileParts)-1];		
		
		if(!file_exists($file) || !isset(self::MIMETYPES[$ext]) ){
		  return $this->notFoundResponse();	
		}
		
		if('GET' === $_SERVER['REQUEST_METHOD']){
			$this->lastModified($file);
		}		
		
		$this->originHeaders();
		
        header('Content-Type: '.self::MIMETYPES[$ext]);
		readfile($file);			
		die();
		/*
		return "<pre>
		  type : $type
		  hash : $hash
		  vendor : $vendor
		  package : $package
		  path : $path
		</pre>";
		*/
		
	}
	
	
   protected function notFoundResponse() : Response {
	    $status = 404;
           $headers = [];
        //   $body = 'hello!';
		 
		     $body = 'Not found.';
           $protocol = '2.0';
           $response = new Response($status, $headers, $body, $protocol);
	   
	   return $response;
   }
	
	
   protected function lastModified($file, $ctime = 604800){
     $x=filemtime($file);
     while($x>time())$x-=86000;#reduce by one day if touched in future date
	// $etag =  sha1(sha1_file($file).$_SERVER['REQUEST_URI']); 
	   $etag =  sha1_file($file); 
	   
	   
	//  $etag = $x;
     $date=gmdate('D, j M Y H:i:s',$x).' GMT';
     header('Cache-Control: "max-age='.$ctime.', public"',1);
     if($_SERVER['HTTP_IF_NONE_MATCH'] == $etag || $_SERVER['HTTP_IF_MODIFIED_SINCE']==$date){
     header('HTTP/1.1 304 Not Modified',1,304);
		 die();
	 }
     header('Etag: "'.$etag.'"',1);
	 header('Last-Modified: '.$date,1);
	 header('Date: '.$date,1);
	 header('Expires: '.gmdate('D, j M Y H:i:s',$x+$ctime),1);
  }
	
 protected function originHeaders(){
   header("Access-Control-Allow-Credentials: true");		
  header("Access-Control-Allow-Origin: ".strip_tags(((isset($_SERVER['HTTP_ORIGIN'])) ? $_SERVER['HTTP_ORIGIN'] : "*")));
  //header("Access-Control-Allow-Headers: X-Requested-With, X-Frame-Options"); 
  header("Access-Control-Allow-Headers: If-None-Match, X-Requested-With, Origin, X-Frdlweb-Bugs, Etag, X-Forgery-Protection-Token, X-CSRF-Token"); 
  // NUR EINSCHRKUNG * GEHT NICH ! header('X-Frame-Options: ALLOW-FROM *');  */
  //header('X-Frame-Options: ALLOW-FROM http://shell.frdl.de');
  //header_remove("X-Frame-Options"); 
  //if(isset($_SERVER['HTTP_ORIGIN'])){
  //	header('X-Frame-Options: ALLOW-FROM '.strip_tags($_SERVER['HTTP_ORIGIN']));
  //}else{
  //	header_remove("X-Frame-Options"); 
  //}
  //header_remove("X-Frame-Options"); 
   if(isset($_SERVER['HTTP_ORIGIN'])){
	  header('X-Frame-Options: ALLOW-FROM '.$_SERVER['HTTP_ORIGIN']);
   }else{
 	  header_remove("X-Frame-Options"); 
   }
   header_remove("X-Frame-Options"); 




   $expose = 'Etag, X-CSRF-Token';
   foreach(headers_list() as $num => $header){
	$h = explode(':', $header);
	$expose.= trim($h[0]).',';
  }
  $expose = trim($expose, ', ');
  header("Access-Control-Expose-Headers: ".$expose);	
  header("Vary: Origin");			 
 }
	
	
}