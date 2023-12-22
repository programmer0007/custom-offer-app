<?php
use App\Models\storedetail;


if(!function_exists('app_credentials')){
    function app_credentials($shop){
        $data = array(
            'API_KEY' => env('API_KEY'),
            'API_SECRET' => env('API_SECRET'),
            'SHOP_DOMAIN' => $shop,
            'ACCESS_TOKEN' => '',
            'SCOPES'=> env('SCOPES'),
            'REDIRECT'=> env('REDIRECT')
        );
        return $data;
    }
}

// @Author: Sahista
function getAuthValidate($query_output = array()){

	$result = false; 
	// dd($query_output);
	if(empty($query_output)){
		
	    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
	        $link = "https";
	    else
	        $link = "http";
	    // Here append the common URL characters.
	    $link .= "://";
	    // Append the host(domain name, ip) to the URL.
	    $link .= $_SERVER['HTTP_HOST'];
		
	    // Append the requested resource location to the URL
	    $link .= $_SERVER['REQUEST_URI'];
	
	    $url_new = parse_url($link);

	    if(!empty($url_new)){
		    $query = $url_new['query'];
		    parse_str($query, $query_output);
		}
	}
	
	if(!empty($query_output)){
		// 	dd($query_output);
        $hmac = $query_output['hmac'];
		
        unset($query_output['hmac']);
		$computed_hmac = hash_hmac('sha256', http_build_query($query_output), env('API_SECRET'));
	
		if(hash_equals($hmac, $computed_hmac)) {
		
			$result = true;
		} else {
		
			$result = false;
		}
		// var_dump($result);
    }
    return $result;

    exit;
}
function getClientIdFromUrl($url){
	// dd($url);
	// if(!empty($id))
	// {
	// 	$clients_model = new Settings();
	
	// 	$id = '';
	// 	$data = $clients_model->where('id',$id)->first();
	// }

	$id = '';

	$data = storedetail::where('url',$url)->first();
	if(!empty($data)){
		$id = $data['id'];
	}
	return $id;
	
	// if(!empty($data)){
	// 	$id = $data['id'];
	// }
	// dd($data['id']);
	// return $id;
}

function getUpsellName($upsellId)
{
	$data = DB::table('products')->where('id', $upsellId)->first('title');
	return $data->title ?? 'upsell has deleted';
}
?>