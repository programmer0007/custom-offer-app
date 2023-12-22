<?php

namespace App\Http\Controllers;

use Error;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\storedetail;
use Illuminate\Support\Facades\Artisan;
use App\Models\paymentRules;
use App\Jobs\ProductSyncToJson;
use App\Models\JobsHistory;


class DashboardController extends Controller
{
    public $store = null;
    public $accessToken = null;
    public $apiVersion = null;
    public $endpoint = null;

    // mutation variable
    private $discountAutomaticAppGraphqlGet = '{"query":"{\\n  discountNodes(first: 20) {\\n    edges {\\n      node {\\n        id\\n        discount {\\n          ... on DiscountAutomaticApp {\\n            title\\n            discountId\\n            appDiscountType {\\n              functionId\\n              title\\n              targetType\\n              discountClass\\n              description\\n              appKey\\n            }\\n            status\\n            startsAt\\n            createdAt\\n            discountClass\\n            updatedAt\\n            endsAt\\n            asyncUsageCount\\n          }\\n        }\\n        metafields(first: 5) {\\n          nodes {\\n            id\\n            key\\n            description\\n            createdAt\\n            value\\n            updatedAt\\n            type\\n            ownerType\\n            namespace\\n            legacyResourceId\\n          }\\n        }\\n      }\\n    }\\n  }\\n}"}';
    private $discountAutomaticAppGraphqlSingle = '{"query":"{\\n   discountNode(id: \\"$DiscountId\\") {\\n    id\\n    metafields(first: 5) {\\n        nodes {\\n            id\\n            value\\n        }\\n    }\\n  }\\n}"}';
    private $discountAutomaticAppGraphqlUpdate = '{"query":"mutation discountAutomaticAppUpdate($automaticAppDiscount: DiscountAutomaticAppInput!, $id: ID!) {\\n  discountAutomaticAppUpdate(automaticAppDiscount: $automaticAppDiscount, id: $id) {\\n    automaticAppDiscount {\\n      discountId\\n      title\\n    }\\n    userErrors {\\n      field\\n      message\\n    }\\n  }\\n}\\n","variables":{"automaticAppDiscount":{"metafields":[{"id":"$metafieldId","value":$updateValue}]},"id":"$discountId"}}';
    private $discountAutomaticAppGraphqlDelte = '{"query": "mutation discountAutomaticDelete($id: ID!) { discountAutomaticDelete(id: $id) { deletedAutomaticDiscountId userErrors { field code message } } }","variables": {"id": "$deleteId"}}';
    // private $discountAutomaticAppGraphqlSingle = '{"query":"{\\n  shop {\\n    name\\n  }\\n  discountNode(id: \\"$DiscountId\\") {\\n    id\\n    events(first: 5) {\\n        nodes {\\n            appTitle\\n            attributeToApp\\n            attributeToUser\\n            createdAt\\n            criticalAlert\\n            id\\n            message\\n        }\\n    }\\n    discount {\\n        ... on DiscountAutomaticApp {\\n          endsAt\\n          appDiscountType {\\n            functionId\\n          }\\n        }\\n    }\\n    metafields(first: 5) {\\n        nodes {\\n        id\\n        key\\n        description\\n        createdAt\\n        value\\n        updatedAt\\n        type\\n        ownerType\\n        namespace\\n        legacyResourceId\\n        }\\n    }\\n  }\\n}","variables":{}}';

    // private $paymentCustomizationCreate = '{"query":"mutation hidePayment($title: String, $functionId: String, $status: Boolean, $metafields: [MetafieldInput!]) {\\n  paymentCustomizationCreate(\\n    paymentCustomization: {title: $title, enabled: $status, functionId: $functionId, metafields: $metafields}\\n  ) {\\n    paymentCustomization {\\n      id\\n      title\\n      enabled\\n      functionId\\n      metafields(first: 10) {\\n        edges {\\n          node {\\n            id\\n            key\\n            createdAt\\n            description\\n            value\\n            updatedAt\\n            type\\n            namespace\\n            legacyResourceId\\n            ownerType\\n          }\\n        }\\n      }\\n    }\\n    userErrors {\\n      message\\n      field\\n      code\\n    }\\n  }\\n}","variables":{"title":"$titleValue","status":$statusValue,"functionId":"$functionIdValue","metafields":$metafieldsValue}}';
    // private $paymentCustomization = '{"query":"query hidePayment {\\n  paymentCustomizations(first: 10, query: \\"function_id:\'$functionId\'\\") {\\n    nodes {\\n      id\\n      functionId\\n      enabled\\n      title\\n    }\\n  }\\n}"}';
    // private $paymentCustomizationSingle = '{"query":"query hidePayment {\\n  paymentCustomization(id: \\"$PaymentCustomizationId\\") {\\n    enabled\\n    title\\n    functionId\\n    id\\n    metafields(first: 10) {\\n      nodes {\\n        createdAt\\n        description\\n        id\\n        key\\n        legacyResourceId\\n        namespace\\n        ownerType\\n        type\\n        updatedAt\\n        value\\n      }\\n    }\\n  }\\n}"}';
    // private $paymentCustomizationUpdate = '{"query":"mutation hidePayment($PaymentCustomizationId:ID!, $title: String, $status: Boolean, $metafields: [MetafieldInput!]) {\\n  paymentCustomizationUpdate(\\n    id: $PaymentCustomizationId\\n    paymentCustomization: {enabled: $status, title: $title, metafields: $metafields}\\n  ) {\\n    paymentCustomization {\\n      enabled\\n      functionId\\n      id\\n      title\\n      metafields(first: 10) {\\n        nodes {\\n          createdAt\\n          id\\n          description\\n          key\\n          legacyResourceId\\n          namespace\\n          ownerType\\n          type\\n          updatedAt\\n          value\\n        }\\n      }\\n    }\\n    userErrors {\\n      message\\n      field\\n      code\\n    }\\n  }\\n}","variables":{"PaymentCustomizationId":"$PaymentCustomizationIdValue","title":"$titleValue","$statusValue":true,"metafields":$metafieldsValue}}';
    // private $paymentCustomizationStatusUpdate = '{"query":"mutation hidePayment($PaymentCustomizationId: ID!, $status: Boolean) {\\n  paymentCustomizationUpdate(\\n    id: $PaymentCustomizationId\\n    paymentCustomization: {enabled: $status}\\n  ) {\\n    userErrors {\\n      message\\n      field\\n      code\\n    }\\n    paymentCustomization {\\n      enabled\\n      functionId\\n      id\\n      title\\n    }\\n  }\\n}","variables":{"PaymentCustomizationId":"$PaymentCustomizationIdValue","status":$statusValue}}';
    // private $shopMarketQuery = '{"query":"query ShopMarket {\\n  markets(first: 25) {\\n    nodes {\\n      id\\n      name\\n    }\\n  }\\n}"}';
    // private $paymentCustomizationDelete = '{"query":"mutation paymentCustomizationDelete {\\n  paymentCustomizationDelete(id: \\"$PaymentCustomizationId\\") {\\n    deletedId\\n    userErrors {\\n      code\\n      field\\n      message\\n    }\\n  }\\n}","variables":{}}';




    public function __construct(Request $request)
    {
        if($request->shop) {
            $shopData = storedetail::where('url', $request->shop)->first();
            // dd($shopData);
            if($shopData) {
                $this->store = $shopData->url;
                $this->accessToken = $shopData->accesstoken;
                $this->apiVersion = env('API_VERSION_URL');
                $this->endpoint = 'https://'. $this->store . $this->apiVersion;
            } else {
                $message = "Shop not found!";

                return $message;
                // return response(view('Error.401Error', compact('message')));
            }
        } else {
            $message = "Shop not found!";
            return $message;

            // return response(view('Error.401Error', compact('message')));
        }
    }


    public function dashboard(Request $request)
    {
            $shopifyStore = $this->store;
            $accessToken = $this->accessToken;

            $apiUrl = "https://$shopifyStore/admin/api/2023-10/graphql.json";

            // GraphQL query to fetch collections with pagination
            $query = <<<'GRAPHQL'
            {
            collections(first: 5) {
                pageInfo {
                hasNextPage
                }
                edges {
                cursor
                node {
                    id
                    title
                    handle
                    updatedAt
                    productsCount
                    sortOrder
                }
                }
            }
            }
            GRAPHQL;

            $hasNextPage = true;
            $cursor = null;

            while ($hasNextPage) {
                // Add cursor to the query if available
                if ($cursor) {
                    $query = str_replace('first: 5', 'first: 5, after: "' . $cursor . '"', $query);
                }

                // cURL request
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $apiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $query]));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'X-Shopify-Access-Token: ' . $accessToken,
                ]);

                $response = curl_exec($ch);
                // dd($response);
                // Check for errors
                if (curl_errno($ch)) {
                    echo 'Curl error: ' . curl_error($ch);
                }

                curl_close($ch);

                // Output the collections
                $data = json_decode($response, true);
                $collections = [];

                if (isset($data['data']['collections']['edges'])) {
                    foreach ($data['data']['collections']['edges'] as $edge) {
                        $collection[] = $edge['node'];
                    }
                } else {
                    echo 'No collections found.' . PHP_EOL;
                }

                // Update hasNextPage and cursor for the next iteration
                $pageInfo = $data['data']['collections']['pageInfo'];
                $hasNextPage = $pageInfo['hasNextPage'];
                $cursor = end($data['data']['collections']['edges'])['cursor'];
            }
            // dd($collection);

            $allDiscounts = [];
            $allDiscounts = $this->discountAutomaticAppGraphql($this->store, $this->accessToken, null, $this->discountAutomaticAppGraphqlGet, null, null, null, 'get');
            // dd($allDiscounts);
            if(isset($allDiscounts['discountNodes'])) {
                if(isset($allDiscounts['discountNodes']['edges'])) {
                    // dd($allDiscounts);
                    $allDiscounts = $allDiscounts['discountNodes']['edges'];
                }
            }

            // dd($allDiscounts);

            return view('dashboard',['data' =>$collection, "allDiscounts" => $allDiscounts]);
    }

    function Createcollections(Request $request){

        // dd($request);
        if($request->discountId != null) {
            // $updateCustomization = $this->discountAutomaticAppGraphql($this->store, $this->accessToken, null, $this->paymentCustomizationUpdate, $request->discountId, $request->metaId, null, 'status');
            $metafields =json_encode([
                    "collection_id" => $request->collection_id,
                    "condition" => $request->condition,
                    "total_amount" => $request->total_amount,
                    "discount_value" => $request->discount_value
            ], true);

            $updateCustomization = $this->discountAutomaticAppGraphql($this->store, $this->accessToken, $request->discountId, $this->discountAutomaticAppGraphqlUpdate, $request->metaId, true, json_encode($metafields, true), 'update');
            // dd($updateCustomization);
        } else {

            $data = json_encode($request->all(), true);
            $escapedValue = str_replace('"', '\"', $data);
            $title = 'custom-offer'.time();
            // dd($title);
            $curl = curl_init();
    
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://checkout-discount-app.myshopify.com/admin/api/2023-10/graphql.json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{"query":"mutation discountAutomaticAppCreate($automaticAppDiscount: DiscountAutomaticAppInput!) {\\n    discountAutomaticAppCreate(automaticAppDiscount: $automaticAppDiscount) {\\n            automaticAppDiscount {\\n                discountId\\n                status\\n                title\\n                updatedAt\\n                startsAt\\n                endsAt\\n                discountClass\\n                createdAt\\n                combinesWith {\\n                    orderDiscounts\\n                    productDiscounts\\n                    shippingDiscounts\\n                }\\n                asyncUsageCount\\n            }\\n            userErrors {\\n                code\\n                field\\n                message\\n            }\\n    }\\n}","variables":{"automaticAppDiscount":{"combinesWith":{"orderDiscounts":true},"functionId":"a1846418-34d3-4a41-98ca-9c9db10dcea8","metafields":[{"key":"function-configuration","namespace":"order-discount","type":"json","value":"'.$escapedValue.'"}],"startsAt":"2023-12-07T00:00:00","title":"'.$title.'"}}}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-Shopify-Access-Token: shpua_d4bf32e20da3657afb1b3cb15fff9e78'
            ),
            ));
    
            $response = curl_exec($curl);
            // dd($response);
            curl_close($curl);
            echo $response;
        }


    }

    public function getSingleDiscount(Request $request)
    {
        $status = false;
        $finalData = [];
        if(!empty($request->id)) {
            $singleData = $this->discountAutomaticAppGraphql($this->store, $this->accessToken, null, $this->discountAutomaticAppGraphqlSingle, $request->id, null, null, $type = 'edit');

            if(isset($singleData['discountNode'])) {
                $status = true;
                $finalData = $singleData['discountNode'];
            }
            // dd($singleData);
            return response()->json([
                "status" => $status,
                "singleData" => $finalData
            ], 200);
        }
    }

    public function discountAutomaticAppGraphql($store, $accessToken, $title, $mutation, $paymentCustomizationId = null, $status = null, $metafields = null, $type = 'get')
    {
        $status = ($status == '1' || $status == 'true') ? 'true' : 'false';
        switch ($type) {
            case "create":
                $mutation = strtr($mutation, ['$titleValue' => $title, '$functionIdValue' => env('FUNCTION_ID'),'$statusValue' => $status, '$metafieldsValue' => json_encode($metafields, true)]);
                break;
            case "update":
                // dd("sfdsf", $title, $metafields, $paymentCustomizationId);
                $mutation = strtr($mutation, ['$discountId' => $title, '$updateValue' => $metafields, '$metafieldId' => $paymentCustomizationId]);
                break;
            case "status":
                $mutation = strtr($mutation, ['$PaymentCustomizationIdValue' => $paymentCustomizationId, '$statusValue' => $status]);
                break;
            case "edit":
                $mutation = strtr($mutation, ['$DiscountId' => $paymentCustomizationId]);
                break;
            case "delete":
                $mutation = strtr($mutation, ['$deleteId' => $paymentCustomizationId]);
                // dd($mutation);
                break;
            default:
                $mutation = strtr($mutation, ['$functionId' => env('FUNCTION_ID')]);
                break;
        }
        // dd($this, $type, $mutation);
        // dd('https://'.$store . env('API_VERSION_URL') . '/graphql.json');
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://'.$store . env('API_VERSION_URL') . '/graphql.json',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $mutation,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'X-Shopify-Access-Token: '.$accessToken,
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        // dd(curl_error($curl), $response, $this, $type, $mutation);
        $response = json_decode($response, true);
        return isset($response['data']) ? $response['data'] : $response;

    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct(Request $request)
    // {
    //     if($request->shop) {
    //         $shopData = StoreDetails::where('url', $request->shop)->first();
    //         if($shopData) {
    //             $this->store = $shopData->url;
    //             $this->accessToken = $shopData->accesstoken;
    //             $this->apiVersion = env('API_VERSION_URL');
    //             $this->endpoint = 'https://'. $this->store . $this->apiVersion;
    //         } else {
    //             $message = "Shop not found!";
    //             return response(view('Error.401Error', compact('message')));
    //         }
    //     } else {
    //         $message = "Shop not found!";
    //         return response(view('Error.401Error', compact('message')));
    //     }
    // }

    
    // public function dashboard(Request $request)
    // {
    //     // $ruleData = paymentRules::where('shop', $this->store)->get();
    //     $jobId = $this->dispatch(new ProductSyncToJson($this->store, $this->accessToken, $this->apiVersion));
    //     $this->createJobHistory($jobId);

    //     $ruleData = [];
    //     $ruleData = $this->paymentCustomizationGraphql($this->store, $this->accessToken, null, $this->paymentCustomization, null, null, null, 'get');
    //     if(isset($ruleData['paymentCustomizations'])) {
    //         if(isset($ruleData['paymentCustomizations']['nodes'])) {
    //             $ruleData = $ruleData['paymentCustomizations']['nodes'];
    //         }
    //     }
    //     return view('dashboard', compact('ruleData'));
    // }

    // public function addRule(Request $request, $ruleId=null)
    // {
    //     $message = null;
    //     $tags = ['null'];
    //     $color = 'alert-danger';
    //     if ($request->isMethod('post')) {
    //         $message = 'Something went wrong!';            
    //         $paymentMethods = DB::table('payment_methods')->get()->pluck('name');
            
    //         $globalID = null;
    //         $type = 'create';
    //         $mutation = $this->paymentCustomizationCreate;
    //         if(!empty($request->globalID) || $request->globalID != null) {
    //             $globalID = base64_decode($request->globalID);
    //             $type = 'update';
    //             $mutation = $this->paymentCustomizationUpdate;
    //         }

    //         $message = 'Rule save successfully.';
    //         $color = 'alert-success';
            
    //         // convert tag string to array
    //         if(!empty($request->rule_type)) {
    //             $newRuleValue = array();
    //             $prepareTags = '';
    //             foreach ($request->rule_type as $key => $rule) {
    //                 if(in_array($rule, ['Customer Tag', 'Product/Variant Tag'])) {
    //                     $prepareTags .= $request->rule_value[$key] . ',';
    //                     $test = explode(',', $request->rule_value[$key]);
    //                     $newRuleValue[$key] = $test;
    //                 } else {
    //                     $newRuleValue[$key] = $request->rule_value[$key];
    //                 }
    //             }
    //             $tags = array_filter(explode(',', $prepareTags));
    //             if(empty($tags)) {
    //                 $tags = ['null'];
    //             }
    //         }
    //         $request->rule_value = $newRuleValue;

    //         $metafields = [
    //             "key" => env('METAFIELD_KEY'),
    //             "value" => json_encode([
    //                 "hidePaymentMethod" => implode(',', $request->payment_method) ?? null,
    //                 "rule" => $request->rule_type ?? null,
    //                 "comparison" => $request->rule_condition ?? null,
    //                 // "value" => ["hello","hey"],
    //                 "value" => (!empty($request->rule_value) ? $request->rule_value : null),
    //                 "hideOrSort" => $request->hide_or_sort ?? null,
    //                 "tags" => $tags,
    //                 "paymentMethod" => $paymentMethods,
    //             ], true),
    //             "type" => "json",
    //             "namespace" => env('NAMESPACE'),
    //         ];
    //         // dd($metafields, $request->rule_value);
    //         $newCustomization = $this->paymentCustomizationGraphql($this->store, $this->accessToken, $request->rule_title, $mutation, $globalID, true, $metafields, $type);
    //         if(isset($newCustomization['errors'])){
    //             $message = 'Something went wrong!';
    //             $color = 'alert-danger';
    //         } else {
    //             if(!empty($newCustomization)) {
    //                 if(!empty($newCustomization['paymentCustomizationCreate']['userErrors'])) {
    //                     $message = $newCustomization['paymentCustomizationCreate']['userErrors'][0]['message'] ?? 'Something went wrong!';
    //                     $color = 'alert-danger';
    //                 } else {
    //                     $message = 'Rule save successfully.';
    //                     $color = 'alert-success';
    //                 }
    //             }
    //         }

    //         return redirect()->route('dashboard', $request->query())->with('message', $message)->with('color', $color);
    //     } else {
    //         $singleRule = [];
    //         $rules = DB::table('rules')->where('is_active', '1')->get()->toArray();
    //         // $paymentMethods = DB::table('payment_methods')->get()->toArray();
    //         $currencyList = $this->getDataFromREST('currencies');
    //         $countriesList = $this->getDataFromREST('countries');
    //         $shopMarket = $this->getShopMarket($this->shopMarketQuery);
    //         $productList = $this->readProductJson($this->store)['productOption'];

    //         if($ruleId != null) {
    //             $singleRule = $this->paymentCustomizationGraphql($this->store, $this->accessToken, null, $this->paymentCustomizationSingle, $ruleId, null, null, $type = 'edit');
    //             if(isset($singleRule['paymentCustomization'])) {
    //                 $singleRule = $singleRule['paymentCustomization'];
    //                 if(!empty($singleRule)) {
    //                     if(!empty($singleRule['metafields']['nodes'])) {
    //                         foreach ($singleRule['metafields']['nodes'] as $key => $value) {
    //                             if($value['value'] != null || $value['value'] != '') {
    //                                 $singleRule['metafields']['nodes'][$key]['value'] = json_decode($value['value'], true);
    //                             }
    //                         }
    //                     }
    //                 }
    //             } else {
    //                 return redirect()->route('dashboard', $request->query())->with('message', 'Somehing went wrong!')->with('color', 'alert-danger');
    //             }
    //         }
    //         // paymentMethods
    //         return view('rule', compact('rules', 'singleRule', 'currencyList', 'shopMarket', 'countriesList', 'productList'));
    //     }
    // }

    // public function cacheClear()
    // {
    //     dd(\Artisan::call('cache:clear'));
    // }

    // public function updateRuleStatus(Request $request)
    // {
    //     try {
    //         $singleRule = paymentRules::where('id', $request->ruleId)->where('shop', $this->store)->first();
    //         $updateCustomization = $this->paymentCustomizationGraphql($this->store, $this->accessToken, null, $this->paymentCustomizationStatusUpdate, base64_decode($request->ruleId), $request->ruleStatus, null, 'status');
    //         $status = false;
    //         $message = 'Something went wrong!';
    //         $color = 'alert-danger';
    //         if(!isset($updateCustomization['errors'])){
    //             if(!empty($updateCustomization['paymentCustomizationUpdate']['userErrors'])) {
    //                 if(count($updateCustomization['paymentCustomizationUpdate']['userErrors']) <= 0) {
    //                     $status = true;
    //                     $message = 'Rule status changed.';
    //                     $color = 'alert-success';
    //                 } else {
    //                     $message = $updateCustomization['paymentCustomizationUpdate']['userErrors'][0]['message'];
    //                 }
    //             } else {
    //                 $status = true;
    //                 $message = 'Rule status changed.';
    //                 $color = 'alert-success';
    //             }
    //         } else {
                

    //             // if(!empty($updateCustomization)) {
    //             //     if(!empty($updateCustomization['paymentCustomizationUpdate']['userErrors'])) {
    //             //         $message = $updateCustomization['paymentCustomizationUpdate']['userErrors'][0]['message'] ?? 'Something went wrong!';
    //             //         $color = 'alert-danger';
    //             //     } else {
    //             //         $singleRule->is_active = ($updateCustomization['paymentCustomizationUpdate']['paymentCustomization']['enabled'] == 'true' ? '1' : '0') ?? null;
    //             //         $singleRule->save();
    //             //         $status = true;
    //             //     }
    //             // }
    //         }
    
    //         return response()->json([
    //             'status' => $status,
    //             'message' => $message,
    //             'color' => $color
    //         ], 200);
    //     } catch (\Throwable $th) {
    //         dd($th);
    //         return response()->json([
    //             'status' => false,
    //             'message' => $th->getMessage(),
    //             'color' => 'test-denger'
    //         ], 500);
    //     }
    // }

    // public function getDataFromREST($endpoint = null)
    // {
    //     // countries.json
    //     if($endpoint == null) {
    //         return [];
    //     }

    //     if($endpoint == 'currencies') {
    //         $endpointUrl = '/currencies.json';
    //     }
    //     if($endpoint == 'countries') {
    //         $endpointUrl = '/countries.json';
    //     }


    //     $status = false;
    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //     CURLOPT_URL => 'https://'.$this->store . env('API_VERSION_URL') . $endpointUrl,
    //     CURLOPT_RETURNTRANSFER => true,
    //     CURLOPT_ENCODING => '',
    //     CURLOPT_MAXREDIRS => 10,
    //     CURLOPT_TIMEOUT => 0,
    //     CURLOPT_FOLLOWLOCATION => true,
    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //     CURLOPT_CUSTOMREQUEST => 'GET',
    //     CURLOPT_HTTPHEADER => array(
    //         'Content-Type: application/json',
    //         'X-Shopify-Access-Token: '.$this->accessToken,
    //     ),
    //     ));
    //     $response = curl_exec($curl);
    //     curl_close($curl);
    //     $currencyList = json_decode($response, true);

    //     if(!empty($currencyList)) {
    //         $status = true;
    //     }

    //     return $currencyList[$endpoint] ?? [];
    //     // return response()->json([
    //     //     'currencyList' => $currencyList['currencies'] ?? [],
    //     //     'status' => $status
    //     // ], 200);
    // }

    // public function getShopMarket($mutation)
    // {
    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //     CURLOPT_URL => 'https://'.$this->store . env('API_VERSION_URL') . '/graphql.json',
    //     CURLOPT_RETURNTRANSFER => true,
    //     CURLOPT_ENCODING => '',
    //     CURLOPT_MAXREDIRS => 10,
    //     CURLOPT_TIMEOUT => 0,
    //     CURLOPT_FOLLOWLOCATION => true,
    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //     CURLOPT_CUSTOMREQUEST => 'POST',
    //     CURLOPT_POSTFIELDS => $mutation,
    //     CURLOPT_HTTPHEADER => array(
    //         'Content-Type: application/json',
    //         'X-Shopify-Access-Token: '.$this->accessToken,
    //     ),
    //     ));

    //     $response = curl_exec($curl);
    //     curl_close($curl);
    //     $response = json_decode($response, true);
    //     return isset($response['data']['markets']) ? $response['data']['markets']['nodes'] : [];
    // }

    // public function createJobHistory($jobId)
    // {
    //     $jobHistory = JobsHistory::create([
    //         'job' => $jobId,
    //         'store' => $this->store,
    //         'created_at' => date('Y-m-d H:i:s'),
    //     ]);

    //     return $jobHistory->id ?? null;
    // }

    // public function readProductJson($store)
    // {
    //     // Read the JSON file 
    //     $json = file_get_contents(public_path().'/product-json/'. $store. '.json');
    //     $jsonDecode = json_decode($json,true);

    //     if(!empty($jsonDecode)) {
    //         $productArray = [];
    //         foreach ($jsonDecode as $value) {
    //             if(!empty($value)) {
    //                 foreach ($value as $avalue) {
    //                     $productArray[$avalue['admin_graphql_api_id']] = $avalue['title'];
    //                 }
    //             }
    //         }
    //     }
    //     return ['productOption' => json_encode($productArray)];
    // }

    public function deleteRule(Request $request)
    {
        $code = 500;
        $status = false;
        $response = $this->discountAutomaticAppGraphql($this->store, $this->accessToken, null, $this->discountAutomaticAppGraphqlDelte, $request->id, null, null, 'delete');
        if(!empty($response)) {
            if(!empty($response['discountAutomaticDelete'])) {
                if($response['discountAutomaticDelete']['deletedAutomaticDiscountId'] != null) {
                    $code = 200;
                    $status = true;
                }
            }
        }

        return response()->json([
            'data' => $response['discountAutomaticDelete'] ?? [],
            'status' => $status,
        ], $code);
    }

    public function getDiscount(Request $request)
    {
            $allDiscounts = [];
            $allDiscounts = $this->discountAutomaticAppGraphql($this->store, $this->accessToken, null, $this->discountAutomaticAppGraphqlGet, null, null, null, 'get');
            // dd($allDiscounts);
            if(isset($allDiscounts['discountNodes'])) {
                if(isset($allDiscounts['discountNodes']['edges'])) {
                    // dd($allDiscounts);
                    $allDiscounts = $allDiscounts['discountNodes']['edges'];
                }
            }

            return response()->json(['allDiscounts' => $allDiscounts ?? []]);

    }
}
