<?php

namespace App\Http\Middleware;

// use Illuminate\Auth\Middleware\CSP as Middleware;

use Closure;

class CSP 
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */

    //private $urlOfShop ;
       
    // public function handle($request, Closure $next)
    // {
    //     // dd($request);
    //     $urlOfShop = $request->input("shop");
    //     // dd($request->input("shop"));
    //     if($request->input("shop"))
    //     {
    //         $response = $next($request);
    //         $response->headers->set('Content-Security-Policy', 'frame-ancestors https://'.$urlOfShop. ' https://admin.shopify.com ');
    //         $response->headers->set('X-Frame-Options', 'ALLOW-FROM '.$urlOfShop);
            
    //     } else {
    //         print_r('Accessed denied, shop not found');
    //         die;
    //     }
       
    //     $auth_status = getAuthValidate($request->query());
    //     if($auth_status) {
    //         return $response;
    //     } else {
    //         print_r('Accessed denied, hmac code not match');
    //         die;
    //     }
    // }

    public function handle($request, Closure $next)
    {
        try {
            $urlOfShop = $request->input("shop");
            if($request->input("shop")) {
                $response = $next($request);
                $response->headers->set('Content-Security-Policy', 'frame-ancestors https://'.$urlOfShop. ' https://admin.shopify.com ');
                $response->headers->set('X-Frame-Options', 'ALLOW-FROM '.$urlOfShop);
    
                $requestData = $request->query();
                $auth_status = getAuthValidate($requestData);
                if($auth_status) {
                    return $response;
                } else {
                    $message = "Hmac code Unauthorized!";
                    return "In CSP => " . $message;
                    return response(view('Error.401Error', compact('message')));
                }
            } else {
                $message = "Shop not found!";
                return "In CSP => " . $message;
                return response(view('Error.401Error', compact('message')));
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

   


}
