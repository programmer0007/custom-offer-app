<?php

namespace App\Http\Controllers;

use App\Models\storedetail;
use App\Models\RecurringApplicationCharge;
use Illuminate\Http\Request;
use App\Http\Controllers\ShopifyController;
use Illuminate\Support\Facades\DB;
use App\Models\shpyAuth\webhook;
use App\Http\Controllers\Controller;
// use App\Http\Traits\Traits;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Http\Controllers\GraphqlController;



class storeController extends Controller
{
    /*
        @Author : Sahista Sengha
    */

    public function access(Request $request)
    {
        // print_r('done'); die;
        // dd($request);
        if ($request->input('shop')) {
            
            $shop = $request->input('shop');
            $request->session()->put('shop', $shop);
            $req_data = $request->all();
            
            // dd($request->input('hmac'));
            $auth_status = getAuthValidate($req_data);
            // dd($auth_status);
            if($auth_status) {
                try {
                    $store_data =  storedetail::where('url', $shop)->first();
                    // dd($store_data);
                    if (!empty($store_data)) {
                        $request->session()->put('accesstoken', $store_data['accesstoken']);
                        // dd($request->session()->put('accessToken', $store_data['accessToken']));
                        if ($request->session()->has('accesstoken')) {
                            // dd('yes');
                            $data = array(
                                'api_key' => env('API_KEY'),
                                'shop' => $shop
                            );
                            $data['id'] = $store_data['id'];
                            $data['data'] = $store_data;

                            // get recurring:
                            $recur_data = RecurringApplicationCharge::where('client_id', $store_data['id'])->where('is_active', 1)->first();
                            if (!empty($recur_data)) {
                                if ((int) env('RECURRING_APP_CHARGE_PRICE') == (int) $recur_data['price']) {
                                    $data['is_recur_approve'] = $recur_data['is_approve'];
                                    $data['recur_charge_id'] = $recur_data['id'];
                                    $data['hmac'] = $request->input('hmac');
                                    $response = $recur_data['response'];
                                    $confirmation_url = '';
                                    if ($response != '') {
                                        $res = json_decode($response, true);
                                        if (!empty($res)) {
                                            $confirmation_url = $res['recurring_application_charge']['confirmation_url'];
                                            $data['confirmation_url'] = $confirmation_url;
                                        }
                                    }
                                } else {
                                    $this->DeleteRecurringApplicationCharge($shop, $store_data['accesstoken'], $recur_data['charge_id']);
                                    $recurring_charge = $this->getRecurringCharge($shop, $store_data['accesstoken'], $recur_data['charge_id']);
                                    if (isset($recurring_charge['recurring_application_charge'])) {
                                        if (!empty($recurring_charge['recurring_application_charge'])) {
                                            if ($recurring_charge['recurring_application_charge']['status'] == 'cancelled') {
                                                // Disable recurring application charge:
                                                $up_recu_data['is_active'] = 0;
                                                RecurringApplicationCharge::where('client_id', $store_data['id'])->where('id', $recur_data['id'])->where('is_active', 1)->update($up_recu_data);
                                                $recurring_api_responce = $this->RecurringApplicationCharge($shop, $store_data['accesstoken']);
                                                if (!isset($recurring_api_responce['recurring_application_charge'])) {
                                                    echo "Recurring charges have been not created.";
                                                } else {
                                                    // $recurring_api_responce['recurring_application_charge']['confirmation_url'];
                                                    return view('auth/escapeIframe', ['installUrl' => $recurring_api_responce['recurring_application_charge']['confirmation_url']]);
                                                }
                                                exit;
                                            }
                                        }
                                    }
                                }
                            }

                            $request->request->add($data);
                            // return view('welcome');
                            return redirect()->route('dashboard', $request->query());
                            // return view('admin.help', $data);
                            // return redirect('/help?'.http_build_query(request()->all()))->with('data', $data);
                            // dd($data);
                            // return redirect()->route('dashboard', $request->query())->with('data', $data);
                            // dd('hey therre');
                            // return 'helo there';
                            // return redirect()->route('welcome', base64_encode($store_data['id']));
                           
                        } else {
                            $this->auth($request);
                        }
                    } else {
                        $this->auth($request);
                    }
                } catch (\Exception $e) {
                    echo ($e->getMessage());
                    die;
                }
            } else {
                echo "This URL is not authorized for you!";
                exit;
            }
            
            
        } else {
            // dd('is it');
            return view('welcome');
        }
    }
   
    public function auth(Request $request)
    {

        $shop = $request->input('shop');

        $data = array(
            'API_KEY' => env('API_KEY'),
            'API_SECRET' => env('API_SECRET'),
            'SHOP_DOMAIN' => $shop,
            'ACCESS_TOKEN' => ''
        );

        $shopify = new ShopifyController($data); // create an instance of Library
        $scopes = explode(',', env('SCOPES')); //what app can do
        $redirect_url = env('REDIRECT'); //redirect url specified in app setting at shopify

        $paramsforInstallURL = array(
            'scopes' => $scopes,
            'redirect' => $redirect_url
        );
        $permission_url = $shopify->installURL($paramsforInstallURL);
        // dd($permission_url);
        echo $permission_url;die;
        redirect()->to($permission_url)->send();
    }

    public function authCallback(Request $request)
    {
        // dd($request->all());
        $code = $request->input('code');
        $shop = $request->input('shop');
      
        if (isset($code)) {
            $data = array(
                'API_KEY' => env('API_KEY'),
                'API_SECRET' => env('API_SECRET'),
                'SHOP_DOMAIN' => $shop,
                'ACCESS_TOKEN' => ''
            );

            $shopify = new ShopifyController($data);

        }
        $accesstoken = $shopify->getAccessToken($code);
        // $accesstoken = '9f7d3876ee5bb1f6a68adefbc7e0b5ee';
        $data = array(
            'url' => $shop,
            'accesstoken' => $accesstoken
        );
     
        try {
            $res = storedetail::where('url', $shop)->first();
           
            if (!empty($res)) {
                storedetail::where('url', $shop)->delete();
            }
            try {
                $storeData = storedetail::insert($data);
                if($storeData) {
                    $shopData = storedetail::where('url', $shop)->first();
                    
                    $shopConfig = $this->getShopConfig($shop, $shopData->accesstoken);
                    if(!empty($shopConfig)) {
                        $shopData->name = $shopConfig['name'];
                        $shopData->email = $shopConfig['email'];
                        $shopData->time_zone = $shopConfig['timezone'];
                        $shopData->money_format = $shopConfig['money_format'];
                        $shopData->save();
                    }

                    $storeHistory = DB::table('store_history')->insert([
                        'shop' => $shop,
                        'email' => $shopConfig['email'] ?? null,
                        'is_installed' => 1,
                        'date_time' => date('y-m-d H:i:s'),
                    ]);

                }

                $request->session()->put('access_token', $accesstoken);

                $id = DB::getPdo()->lastInsertId();

                $delete_address = env('APP_URL') . '/uninstall';
                $delete_topic = 'app/uninstalled';
                $delete_app_webhook_response = $this->createWebhook($shop, $accesstoken, $delete_topic, $delete_address);
                // dd($delete_app_webhook_response);
                //for insertion 
                if (!empty($delete_app_webhook_response)) {
                    $webhook_array_2 = array(
                        'client_id' => $id,
                        'webhook_id' => $delete_app_webhook_response['webhook']['id'],
                        'topic' => $delete_topic,
                        'address' => $delete_address,
                        'response' => json_encode($delete_app_webhook_response)
                    );

                    webhook::insert($webhook_array_2);
                }

                $generateStorefrontToken = $this->generateStoreFrontAccess($shop, $accesstoken);
                if($generateStorefrontToken) {
                    storeDetail::where('url', $shop)->update([
                        'storefront_token' => $generateStorefrontToken['access_token'],
                    ]);
                }

                $store_data =  storeDetail::where('url', $shop)->first();
                // if (!empty($store_data)) {
                //     // create recurring application charge API:
                //     return redirect()->to('https://' . $store_data['url'] . '/admin/apps/' . env('APP_PATH_NAME'));
                //     // return redirect()->to($recurring_api_responce['recurring_application_charge']['confirmation_url']);

                //     exit;
                // }

                //fetching store id
                // $store_data = storedetail::where('url',$shop)->first();
                if (!empty($store_data)) {
                    // create recurring application charge API:
                    $recurring_api_responce = $this->RecurringApplicationCharge($shop, $accesstoken);
                    if (!isset($recurring_api_responce['recurring_application_charge'])) {
                        echo "Recurring charges have been not created.";
                    } else {
                        return redirect()->to($recurring_api_responce['recurring_application_charge']['confirmation_url']);
                    }
                    exit;
                }

            } catch (\Exception $e) {
                echo ($e->getMessage());
                exit;
            }
        } catch (\Exception $e) {
            echo ($e->getMessage());
            exit;
        }
    }

    public function approveRecurringCharge(Request $request, $shop)
    {
        $shopUrl = $shop;
        $recurring_status = 0;
        $charge_id = $request->input('charge_id');

        if ($charge_id != '') {
            $recur_data = RecurringApplicationCharge::where('charge_id', $charge_id)->where('is_active', 1)->first();
            if (!empty($recur_data)) {
                $client_data = storedetail::where('id', $recur_data['client_id'])->first();
                if (!empty($client_data)) {
                    // Get recurring charge:
                    $recurring_charge = $this->getRecurringCharge($client_data['url'], $client_data['accesstoken'], $recur_data['charge_id']);
                    if (isset($recurring_charge['recurring_application_charge'])) {
                        if (!empty($recurring_charge['recurring_application_charge'])) {
                            if ($recurring_charge['recurring_application_charge']['status'] == 'declined') {
                                // Disable recurring application charge:    
                                $up_recu_data['is_active'] = 0;
                                RecurringApplicationCharge::where('client_id', $client_data['id'])->where('id', $recur_data['id'])->where('is_active', 1)->update($up_recu_data);
                                $recurring_api_responce = $this->RecurringApplicationCharge($client_data['url'], $client_data['accesstoken']);
                                if (!isset($recurring_api_responce['recurring_application_charge'])) {
                                    echo "Recurring charges have been not created.";
                                } else {
                                    $recurring_status = 1;
                                    return view('auth/escapeIframe', ['installUrl' => $recurring_api_responce['recurring_application_charge']['confirmation_url']]);
                                }
                                exit;
                            } else {
                                if ($recurring_charge['recurring_application_charge']['status'] == 'active') {
                                    $recurring_status = 0;
                                }
                            }
                        }
                    }
                    if ($recurring_status == 0) {
                        $up_data['is_approve'] = 1;
                        $up_data['modified'] = date('Y-m-d H:i:s');
                        RecurringApplicationCharge::where('charge_id', $charge_id)->where('is_active', 1)->update($up_data);
                      
                        return redirect()->to('https://' . $client_data['url'] . '/admin/apps/' . env('APP_PATH_NAME'));
                       
                    }
                } else {
                    echo "Something went to wrong!";
                }
            } else {
                echo "Something went to wrong!";
            }
        } else {
            // echo "Something went to wrong!";
            $store = storedetail::where('url', $shopUrl)->first();
            // $dlt = DB::table('webhooks')->where('client_id', $store_data['id'])->delete();
            $dlt = RecurringApplicationCharge::where('client_id', $store['id'])->first()->delete();
            $client_data = storedetail::where('url', $shopUrl)->where('is_active', 1)->first();
            // if (!empty($store_data)) {
            // create recurring application charge API:
            $recurring_api_responce = $this->RecurringApplicationCharge($shopUrl, $store['accesstoken']);
            // dd($recurring_api_responce);
            if (!isset($recurring_api_responce['recurring_application_charge'])) {
                echo "Recurring charges have been not created.";
            } else {
                return view('auth/escapeIframe', ['installUrl' => $recurring_api_responce['recurring_application_charge']['confirmation_url']]);
            }
            exit;

            return redirect()->to('https://' . $shopUrl . '/admin/apps/' . env('APP_PATH_NAME'));

            exit;
        }
    }

    public function RecurringApplicationCharge($shopUrl, $access_token)
    {
        $store_data = storedetail::where('url', $shopUrl)->where('is_active', 1)->first();
        $client_id = '';
        if (!empty($store_data)) {
            $client_id = $store_data['id'];
        }
        $data['recurring_application_charge'] = array(
            "name" => env('RECURRING_APP_CHARGE_NAME'),
            "price" => env('RECURRING_APP_CHARGE_PRICE'),
            "test" => env('DROPD_CHARGE_TEST'),
            "trial_days" => env('RECURRING_TRIAL_DAYS'),
            "return_url" => url('approve_charge/' . $shopUrl),
            "capped_amount" => env('RECURRING_APP_CAPPED_AMOUNT'),
            "terms" => env('RECURRING_APP_TERMS')
        );
        $data = json_encode($data);
        $url = "https://" . $shopUrl . "/" . env('API_VERSION_URL') . "/recurring_application_charges.json";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "cache-control: no-cache",
            "content-type: application/json",
            "Accept: application/json",
            "x-shopify-access-token: " . $access_token . ""
        ));
        $response = curl_exec($ch);
        $response_array = json_decode($response, TRUE);
        if (isset($response_array['recurring_application_charge'])) {
            $recurring_application_charge = $response_array['recurring_application_charge'];
            $recurring_data = array(
                'charge_id' => $recurring_application_charge['id'],
                'client_id' => $client_id,
                'name' => $recurring_application_charge['name'],
                'price' => $recurring_application_charge['price'],
                'response' => json_encode($response_array)
            );
            RecurringApplicationCharge::insert($recurring_data);
        } else {
            if ($response_array['errors']) {
                foreach ($response_array['errors'] as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $key1 => $value1) {
                            echo $key . " - " . $value1;
                        }
                    } else {
                        echo $key . " - " . $value;
                    }
                }
            }
        }
        return $response_array;
    }
    public function DeleteRecurringApplicationCharge($shopUrl, $access_token, $recurring_charge)
    {
        $store_data = storedetail::where('url', $shopUrl)->where('is_active', 1)->first();
        $client_id = '';
        if (!empty($store_data)) {
            $client_id = $store_data['id'];
        }
        $url = "https://" . $shopUrl . "/" . env('API_VERSION_URL') . "/recurring_application_charges/" . $recurring_charge . ".json";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "cache-control: no-cache",
            "content-type: application/json",
            "Accept: application/json",
            "x-shopify-access-token: " . $access_token . ""
        ));
        $response = curl_exec($ch);
    }

    public function getRecurringCharge($shopUrl, $access_token, $recurring_charge)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://" . $shopUrl . "/" . env('API_VERSION_URL') . "/recurring_application_charges/" . $recurring_charge . ".json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "cache-control: no-cache",
                "content-type: application/json",
                "x-shopify-access-token: " . $access_token
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }


    public function welcome($id){

       return view('/welcome');
    }
    /*
     @end Author : Sahista Sengha
    */

    public function createWebhook($shopUrl, $access_token, $topic, $address)
    {
        // echo $shopUrl, $access_token, $topic, $address;
        try {
            $data = array(
                'webhook' => array(
                    "topic" => $topic,
                    "address" => $address,
                    "format" => "json",
                    // "api_version" => env('API_VERSION'),
                )
            );
            // admin/api/2022-04/webhooks.json
            $url = "https://" . $shopUrl . env('API_VERSION_URL') . "/webhooks.json";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "cache-control: no-cache",
                "Content-Type: application/json",
                "Accept: application/json",
                "x-shopify-access-token:" . $access_token
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            echo "<br><br>array => <br>";
            print_r($response);
            // print_r(json_decode($response,TRUE));
            return json_decode($response, TRUE);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function uninstall()
    {
        $myfile = fopen(public_path('/files/uninstall.txt'), "w") or die("Unable to open file!");
        fwrite($myfile, 'calleddd');

        $inputJSON = file_get_contents('php://input');
        $response = json_decode($inputJSON, TRUE);
        fwrite($myfile, $inputJSON);
        fwrite($myfile, '===============================');
        fwrite($myfile, $response['domain']);
        fwrite($myfile, '===============================');
        try {
            if ($response) {
                // dd($response);
                if (!empty($response)) {
                    $domain = $response['domain'];
                    $store_data = DB::table('storedetails')->where('url', $domain)->first();
                    // dd($store_data);
                    if (!empty($store_data)) {
                        //         // Get webhooks:
                        $webhooks = webhook::where('client_id', $store_data->id)->first();
    
                        if (!empty($webhooks)) {
                            $this->removeWebhook($store_data->url, $store_data->accesstoken, $webhooks->webhook_id);
                            $dlt = DB::table('webhooks')->where('client_id', $store_data->id)->delete();
                        }
    
                        //store data
                        // DB::table('upsell_history')->where('shopdomain', $domain)->delete();
    
                        //recurring charge
                        // DB::table('recurring_application_charge')->where('client_id', $store_data->id)->delete();
    
                        //quote data
                        // $prentIdArray = DB::table('products')->where('shop_domain', $store_data->url)->get()->pluck('id');
                        // DB::table('products')->where('shop_domain', $store_data->url)->delete();
                        // DB::table('upsell_criteria')->whereIn('upsell_id', $prentIdArray)->delete();
                        // $discountIds = DB::table('discounts')->where('shop', $store_data->url)->get()->pluck('discount_id');
                        // if(!empty($discountIds)) {
                        //     $GraphqlObj = new GraphqlController;
                        //     foreach ($discountIds as $key => $value) {
                        //         $deleteDiscount = $GraphqlObj->deleteDiscount($value, $store_data->url);
                        //     }
                        // }
                        // DB::table('discounts')->where('shop', $store_data->url)->delete();
                        // DB::table('setting')->where('shop_domain', $store_data->url)->delete();
    
                        $data = "Application has been uninstalled from shopify store ".$domain;
                        // $storeHistory = DB::table('store_history')->where('shop', $store_data->url)->update([
                        //     'is_installed' => 0,
                        //     'date_time' => date('y-m-d H:i:s'),
                        // ]);
                        $storeHistory = DB::table('store_history')->insert([
                            'shop' => $store_data->url,
                            'email' => $store_data->email,
                            'is_installed' => 0,
                            'date_time' => date('y-m-d H:i:s'),
                        ]);
                        DB::table('storedetails')->where('url', $domain)->delete();

    
                        // if(env('UNINSTALL_MAIL')) {
                        //     $mail = new PHPMailer(true);
        
                        //     try {
                        //         $mail->SMTPDebug = 0;
                        //         $mail->isSMTP();
                        //         $mail->Host = 'email-smtp.ap-south-1.amazonaws.com';             //  smtp host
                        //         $mail->SMTPAuth = true;
                        //         $mail->Username = 'AKIAYLG4UAVD4M7XAO5A';   //  sender username
                        //         $mail->Password = 'BIuw+c8xNOEc0EvZRUtOR8MA1dJGU4CfOukmrMAsQdc5';       // sender password
                        //         $mail->SMTPSecure = 'tls';                  // encryption - ssl/tls
                        //         $mail->Port = 587;                          // port - 587/465
        
                        //         $mail->setFrom('no-reply@dynamicdreamz.com', 'Up-Sell');
                        //         $mail->addAddress($response['email']);
                        //         $mail->isHTML(true);                // Set email content format to HTML
                        //         $mail->Subject = "Up-Sell";
                        //         $mail->Body    = $data;
                        //         fwrite($myfile, 'send => ');
                        //         if (!$mail->send()) {
                        //             echo $mail->ErrorInfo;
                        //             echo 'Message could not be sent.';
                        //             fwrite($myfile, 'Message could not be sent');
                        //         } else {
                        //             echo "Email has been sent.";
                        //             fwrite($myfile, 'Email has been sent');
                        //         }
                        //     } catch (Exception $e) {
                        //         // echo $e;
                        //         fwrite($myfile, '===>mail error<=');
                        //         fwrite($myfile, $e->getTraceAsString());
                        //     }
                        // }
                    }
                }
            }
            fwrite($myfile, 'run success');
        } catch (\Throwable $th) {
            fwrite($myfile, $th->getMessage());
            fwrite($myfile, $th->getTraceAsString());

        }
        fclose($myfile);
        return true;
    }

    public function removeWebhook($shopUrl, $access_token, $webhook_id)
    {
        $url = "https://" . $shopUrl . env('API_VERSION_URL') . "/webhooks/" . $webhook_id . ".json";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => array(
                'x-shopify-access-token: ' . $access_token,
                'content-type: application/json',
                'cache-control: no-cache',
                'Accept: application/json'
            ),
        ));
        $response = curl_exec($curl);
        return json_decode($response, true);
    }

    public function getShopConfig($shop, $access_token)
    {
        $url = "https://" . $shop . env('API_VERSION_URL') . "/shop.json";
        // dd($shop, $access_token, $url);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'x-shopify-access-token: ' . $access_token,
                'content-type: application/json',
                'cache-control: no-cache'
            ),
        ));
        $response = curl_exec($curl);
        $response = json_decode($response, true);
        return $response['shop'] ?? [];
    }

    public function redirectApproval(Request $request)
    {
        if ($request->input('url')) {
            // header("Content-Security-Policy: frame-ancestors https://".$request->input('url'). " https://admin.shopify.com");
            return view('auth/escapeIframe', ['installUrl' => base64_decode($request->input('url'))]);
            exit;
        }
    }

    public function generateStoreFrontAccess($shop, $access_token)
    {
        $curl = curl_init();
        $url = "https://" . $shop . env('API_VERSION_URL') . "/storefront_access_tokens.json";
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "storefront_access_token":{ 
                "title":"store-front-token"
            }
        }',
        CURLOPT_HTTPHEADER => array(
            'X-Shopify-Access-Token:'.$access_token,
            'Content-Type: application/json',
        ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);
        return $response['storefront_access_token'] ?? [];

    }

    public function data_request(Request $request)
    {
        $webhook_payload = file_get_contents('php://input');
        // $headers = apache_request_headers();
        $hmac_header = $request->header('X-Shopify-Hmac-Sha256'); //get single value
        // $calculated_hmac = base64_encode(hash_hmac('sha256', $webhook_payload ,env('API_SECRET') ,true));
        
        $verified = $this->verify_webhook($webhook_payload, $hmac_header);
        // print_r($verified);
        $f0 = fopen(public_path('/files/dataRequest.txt'),"a");
            // return hash_equals($hmac, $calculated_hmac);
  
            fwrite($f0,"\n");
            fwrite($f0,"New Data :");
            fwrite($f0,"\n");
            fwrite($f0,"webhook_payload :");
            fwrite($f0,$webhook_payload);
            fwrite($f0,"\n");
            fwrite($f0,"hmac_header :");
            fwrite($f0,$hmac_header);
            fwrite($f0,"\n");
            fwrite($f0,"verification :");
            fwrite($f0,json_decode($verified));
           
        // // } 

        fclose($f0);

        // $f0 = fopen('req.txt',"w");
        // fwrite($f0, $webhook_payload);
        
        // fclose($f0);

        // // $f0 = fopen('req.txt',"r");
        // // fclose($f0);
        if($verified == 1) 
        {
            $data = array(
                'response' => json_encode($webhook_payload,true),
                'topic' => 'customers/data_request'
            );
            DB::table('GDPR')->insert($data);
            
        }else {
            
            return response("",401);
        }
        
    }

    public function customer_redact(Request $request)
    {

        $webhook_payload = file_get_contents('php://input');
        $hmac_header = $request->header('X-Shopify-Hmac-Sha256'); //get single value
        
        $verified = $this->verify_webhook($webhook_payload, $hmac_header);
        // $f0 = fopen('cust.txt',"w");
        // fwrite($f0, $webhook_payload);
        
        // fclose($f0);

        // $f0 = fopen('cust.txt',"r");
        // fclose($f0);
        $f0 = fopen(public_path('/files/customerRedact.txt'),"a");
        // return hash_equals($hmac, $calculated_hmac);

        fwrite($f0,"\n");
        fwrite($f0,"New Data :");
        fwrite($f0,"\n");
        fwrite($f0,"webhook_payload :");
        fwrite($f0,$webhook_payload);
        fwrite($f0,"\n");
        fwrite($f0,"hmac_header :");
        fwrite($f0,$hmac_header);
        fwrite($f0,"\n");
        fwrite($f0,"verification :");
        fwrite($f0,json_decode($verified));
       
    // // } 

        fclose($f0);

        if($verified == 1) 
        {
            $data = array(
                'response' => json_encode($webhook_payload,true),
                'topic' => 'customers/redact'
            );

            DB::table('GDPR')->insert($data);
        }else{
            return response("",401);
        }

    }

    public function shop_redact(Request $request)
    {
        $webhook_payload = file_get_contents('php://input');
        // $response = json_decode($webhook_payload, TRUE);
        $hmac_header = $request->header('X-Shopify-Hmac-Sha256'); //get single value
        $verified = $this->verify_webhook($webhook_payload, $hmac_header);
       
        // $f0 = fopen('hmacVAlid.txt',"a");
        // $calculated_hmac = base64_encode(hash_hmac('sha256', $webhook_payload ,env('API_SECRET') ,true));
        // fwrite($f0,$hmac);
        // fwrite($f0,$calculated_hmac);

        $f0 = fopen(public_path('/files/shopRedact.txt'),"a");
        // return hash_equals($hmac, $calculated_hmac);

        fwrite($f0,"\n");
        fwrite($f0,"New Data :");
        fwrite($f0,"\n");
        fwrite($f0,"webhook_payload :");
        fwrite($f0,$webhook_payload);
        fwrite($f0,"\n");
        fwrite($f0,"hmac_header :");
        fwrite($f0,$hmac_header);
        fwrite($f0,"\n");
        fwrite($f0,"verification :");
        fwrite($f0,json_decode($verified));
       
        fclose($f0);


        if($verified) 
        {
            // fwrite($f0,'inside if');
            // fwrite($f0,$hmac);
            // fwrite($f0,$calculated_hmac);

            $data = array(
                'response' => json_encode($webhook_payload,true),
                'topic' => 'shop/redact'
            );
    
            DB::table('GDPR')->insert($data);
        }else{
            return response("",401);
        }

        // fclose($f0);

    }

    public function verify_webhook($data, $hmac_header)
    {
        $data = $data ?: '';
        $hmac_header = $hmac_header ?: '';
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, env('API_SECRET'), true));
        
        $f0 = fopen(public_path('/files/validation.txt'),"a");

        fwrite($f0,"\n");
        fwrite($f0,"New Data :");
        fwrite($f0,"\n");
        fwrite($f0,"hmac_header");
        fwrite($f0,$hmac_header);
        fwrite($f0,"\n");
        fwrite($f0,"calculated_hmac");
        fwrite($f0, $calculated_hmac);

        fclose($f0);
        // $hmac_header = ''; 
        // $calculated_hmac = '1253';

        if(hash_equals($hmac_header, $calculated_hmac))
        {
            return hash_equals($hmac_header, $calculated_hmac);

        }else if ($hmac_header == '' || $calculated_hmac == '' || $hmac_header != $calculated_hmac){
            return 0;
        }
    }
}