<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2020/1/8
 * Time: 14:20
 */

namespace App\Http\Controllers\Api;

use App\Services\ApiChangeLogsService;
use Illuminate\Http\Request;

class ChangelogsController
{

    public function changelogs(Request $request)
    {
        $changeLogService = new ApiChangeLogsService();
        $param = $request->all();
        $platform = isset($param['platform']) && $param['platform'] != '' ? $param['platform'] : "iOS";
        $product = isset($param['product']) && $param['product'] != '' ? $param['product'] : "ComPDFKit PDF SDK";
        $data = $changeLogService->getChangeLogs($platform, $product);
        return json_encode(['data' => $data, 'code' => 200, 'msg' => "success"]);
    }

}