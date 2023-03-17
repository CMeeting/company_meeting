<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\WebViewerLicense;
use App\Services\WebViewerLicenseService;
use Illuminate\Http\Request;

class WebViewerLicenseController extends Controller
{
    /**
     * 生成序列码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $request){
        $validate = \Validator::make($request->all(), [
            'email' => 'required|email',
            'full_name' => 'required',
            'company' => 'required',
            'type' => 'required',
            'expiration' => 'required'
        ]);

        if($validate->fails()){
            return \Response::json(['code' => 500, 'message' => $validate->errors()->first(), 'data' => []]);
        }

        $type = $request->input('type');
        if(!in_array($type, [WebViewerLicense::TYPE_1_TRIAL, WebViewerLicense::TYPE_2_BUY])){
            return \Response::json(['code' => 500, 'message' => 'type类型错误']);
        }

        $domain_info = $request->input('domain_info');
        if(empty($domain_info)){
            return \Response::json(['code' => 500, 'message' => '域名以及服务器地区必填']);
        }

        $license_service = new WebViewerLicenseService();
        $result = $license_service->generate($request['email'], $request['full_name'], $request['company'], intval($request['type']), $request['expiration'], $domain_info);

        return \Response::json($result);
    }

    /**
     * license验证
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request){
        $license = $request->input('license');

        $source_domain = $_SERVER['HTTP_REFERER'] ?? '';

        $licenseService = new WebViewerLicenseService();

        $result = $licenseService->verify($license, $source_domain);

        return \Response::json($result);
    }
}