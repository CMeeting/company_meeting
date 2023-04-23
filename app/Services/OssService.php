<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/7/28
 * @Time: 14:35
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

namespace App\Services;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\UploadedFile;
use OSS\OssClient;
use OSS\Core\OssException;

class OssService
{
    private static function ossConfig()
    {
        return [
            'accessId' => env('ACCESS_KEY_ID', ''),
            'accessKey' => env('ACCESS_KEY_SECRET', ''),
            'endpoint' => env('ENDPOINT', ''),
            'bucket' => env('BUCKET', '')
        ];
    }
//    /**
//     * 上传文件到阿里云
//     * @param $file
//     * @param array $type
//     * @return array
//     */
//    public static function ossUpload($file, $type)
//    {
//        try {
//            if ($file["file"]["error"] == 0) {
//                if ($type == 'img') {
//                    return self::uploadImg($file);
//                } else {
//                    return self::uploadFile($file);
//                }
//            }
//            return ['msg'=>'文件不能为空！','code'=>1000];
//
//        } catch (\Exception $exception) {
//            return ['msg'=>$exception->getMessage(),'code'=>1000];
//        }
//
//    }
//
//    /**
//     * 上传图片到阿里云
//     * @param $file
//     * @return array
//     */
//    public static function uploadImg($file)
//    {
//        try {
//            if ($file['size'] > 10485760) {
//                return ['msg'=>'上传的图片不能大于10MB','code'=>1000];
//            }
//            $pixel = getimagesize($file['tmp_name']);//获取图片大小
//            $suffix = explode('.', $file['name']);
//            $pathName = 'blog/imgs/' . date('Y-m') . '/' . uniqid() . '.' . $suffix[1]; //生成文件名
//            $filePath = $file['tmp_name']; //临时文件路径
//
//            $config = self::ossConfig();
//            $ossClient = new OssClient($config['accessId'], $config['accessKey'], $config['endpoint']);
//            $options = array(OssClient::OSS_CONTENT_TYPE => 'image/jpg');
//            $result = $ossClient->uploadFile($config['bucket'], $pathName, $filePath, $options);
//
//            if ($result['info']) {
//                $resData = [
//                    'url' => $result['oss-request-url'],
//                    'src' => $pathName . '?w=' . $pixel[0] . '&h=' . $pixel[1]
//                ];
//                return ['msg'=>'上传成功','data'=>$resData,'code'=>200];
//            }
//
//            return ['msg'=>'上传失败！','code'=>1000];
//
//        } catch (\Exception $exception) {
//            return ['msg'=>$exception->getMessage(),'code'=>1000];
//        }
//    }
//
//    /**
//     * 富文本编辑器上传文件到阿里云
//     * @param $file
//     * @param $fileName
//     * @return array
//     */
//    public static function uedUploadFile($file, $fileName)
//    {
//        try {
//            if ($file['size'] > 10485760) {
//                return hello_error('上传的图片不能大于10MB');
//            }
//
//            $pathName = 'men_hu/ueditor/' . date('Y-m') . '/' . $fileName; //生成文件名
//            $filePath = $file['tmp_name']; //临时文件路基
//
//            $config = self::ossConfig();
//            $ossClient = new OssClient($config['accessId'], $config['accessKey'], $config['endpoint']);
//            $options = array(OssClient::OSS_CONTENT_TYPE => 'image/jpg');
//            $result = $ossClient->uploadFile($config['bucket'], $pathName, $filePath, $options);
//
//            if ($result['info']) {
//                $resData = ['src' => $pathName, 'url' => $result['info']['url']];
//                return hello_success('上传成功', $resData);
//            }
//
//            return hello_error('上传失败！');
//
//        } catch (\Exception $exception) {
//            return hello_error('失败！', $exception->getMessage());
//        }
//    }

    /**
     * 上传文件到阿里云
     * @param $file
     * @return array
     */
    public static function uploadFile($file)
    {
//        dd($file->getClientOriginalName());
//        dd($file->getSize());
//        dd($file->getError());
//        dd($file->getMimeType());
        try {
            if ($file->getSize() > 31457280) {
                return ['msg'=>'上传的文件不能大于30MB','code'=>1000];
            }

            $suffix = explode('.',$file->getClientOriginalName());
            $pathName = 'blog/files/' . date('Y-m') . '/' . uniqid() . '.' . $suffix[1]; //生成文件名
            $filePath = $file->getRealPath(); //临时文件路基

            $config = self::ossConfig();
            $ossClient = new OssClient($config['accessId'], $config['accessKey'], $config['endpoint']);
//            $ossClient->createObjectDir($config['bucket'], '/blog/files/' . date('Y-m'));      //创建文件夹
            $result = $ossClient->uploadFile($config['bucket'], $pathName, $filePath);
            if ($result['info']) {
                $resData = ['name' => $file->getClientOriginalName(), 'url' => $result['info']['url'],'type' => $file->getMimeType(), 'size' => $file->getSize()];
                return ['msg'=>'上传成功','data'=>$resData,'code'=>200];
            }

            return ['msg'=>'上传失败！','code'=>1000];

        } catch (OssException $exception) {
            return ['msg'=>$exception->getMessage(),'code'=>1000];
        }
    }

    /**
     * 上传文件到OSS
     * @param $file
     * @param $path
     * @return array|bool
     */
    public static function uploadFileNew(UploadedFile $file, $path)
    {
        try {
            if ($file->getSize() > 31457280) {
                return ['msg'=>'上传的文件不能大于30MB','code'=>1000];
            }

            $suffix = $file->getClientOriginalExtension();
            $pathName = "$path/" . date('Ym') . '/' . uniqid() . '.' . $suffix; //生成文件名
            $filePath = $file->getRealPath(); //临时文件路基

            $config = self::ossConfig();
            $ossClient = new OssClient($config['accessId'], $config['accessKey'], $config['endpoint']);
            $result = $ossClient->uploadFile($config['bucket'], $pathName, $filePath);
            if ($result['info']) {
                return $result['info']['url'];
            }

            return false;
        } catch (OssException $exception) {
            \Log::info('OSS文件上传失败', ['msg'=>$exception->getMessage()]);
            return false;
        }
    }
}