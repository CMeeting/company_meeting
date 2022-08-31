<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2020/1/8
 * Time: 14:20
 */

namespace App\Http\Controllers\Api;
use App\Services\ChangeLogsService;
use Illuminate\Http\Request;

class ChangelogsController
{

    public function changelogs(Request $request)
    {
        $changeLogService = new ChangeLogsService();
        $param = $request->all();
        $platform = isset($param['platform']) && $param['platform'] != '' ? $param['platform'] : "iOS";
        $data = $changeLogService->getChangeLogs($platform);
        return json_encode(['data' => $data, 'code' => 200, 'msg' => "success"]);
    }

}