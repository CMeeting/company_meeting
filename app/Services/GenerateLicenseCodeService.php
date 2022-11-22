<?php


namespace App\Services;


use phpDocumentor\Reflection\Types\Self_;

Class GenerateLicenseCodeService
{
    /**
     * PDF平台
     */
    const LICENSE_PLATFORM_IOS = 1;
    const LICENSE_PLATFORM_AND = 2;
    const LICENSE_PLATFORM_WIN = 4;
    const LICENSE_PLATFORM_MAC = 8;
    const LICENSE_PLATFORM_LINUX = 10;

    /**
     * 权限编码
     */
    /** Viewer 阅读器 **/
    // 编辑大纲
    const LICENSE_VIEWER_OUTLINE = 1;
    // 编辑书签
    const LICENSE_VIEWER_BOOKMARK = 2;
    // 渲染模式
    const LICENSE_VIEWER_RENDER = 4;
    // 文本搜索
    const LICENSE_VIEWER_SEARCH = 8;

    /** Annotations 注释 **/
    // 添加Note注释
    const LICENSE_ANNOT_NOTE = 1;
    // 添加Link注释
    const LICENSE_ANNOT_LINK = 2;
    // 添加FreeText注释
    const LICENSE_ANNOT_FREETEXT = 4;
    // 添加Shape（Square、Circle、Line）注释
    const LICENSE_ANNOT_SHAPE = 8;
    // 添加Markup（Highlight、StrikeOut、Underline、Squiggly）注释
    const LICENSE_ANNOT_MARKUP = 10;
    // 添加标准Stamp注释
    const LICENSE_ANNOT_STAMP_S = 20;
    // 添加自定义Stamp注释
    const LICENSE_ANNOT_STAMP_C = 40;
    // 添加Ink注释
    const LICENSE_ANNOT_INK = 80;
    // 添加Sound注释
    const LICENSE_ANNOT_SOUND = 100;
    // 删除注释
    const LICENSE_ANNOT_DELETE = 200;
    // Flatten注释
    const LICENSE_ANNOT_FLATTEN = 400;
    // 导入、导出注释
    const LICENSE_ANNOT_XFDF = 800;

    /** Forms 表单 **/
    // 添加、删除、编辑Form
    const LICENSE_FORM = 1;
    // 填写Form
    const LICENSE_FORM_FILL = 2;

    /** Document editor 文件编辑器 **/
    // Split、Extract、Merge、Delete、Insert、Crop、Move、Rotate、Replace、Exchange页面
    const LICENSE_EDITOR_PAGE = 1;
    // 提取图片
    const LICENSE_EDITOR_EXTRACT = 2;
    // 编辑文档属性
    const LICENSE_EDITOR_INFO = 4;
    // PDF转图片
    const LICENSE_EDITOR_CONVERT = 8;

   /** Security 安全 **/
    // 密码加密、文档权限设置
    const LICENSE_SECURITY_ENCRYPT = 1;
    // 解密、移除权限
    const LICENSE_SECURITY_DECRYPT = 2;
    // 获取、创建、编辑、删除水印
    const LICENSE_SECURITY_WATERMARK = 4;
    // 标记密文
    const LICENSE_SECURITY_REDACTION = 8;
    // 页眉页脚
    const LICENSE_SECURITY_HEADERFOOTER = 10;
    // 贝茨码
    const LICENSE_SECURITY_BATES = 20;
    // 背景
    const LICENSE_SECURITY_BACKGROUND = 40;

    /** Text Edit 文本编辑 **/
    // 文本编辑
    const LICENSE_EDIT_TEXT = 1;
    // 图片编辑
    const LICENSE_EDIT_IMAGE = 2;

    /** Conversion 转化 **/
    // PDF/A
    const LICENSE_CONVERSION_PDFA = 1;

    /** 转档相关功能 */
    const LICENSE_CONVERT_WORD = 1;
    const LICENSE_CONVERT_EXCEL = 2;
    const LICENSE_CONVERT_TABLE = 4;
    const LICENSE_CONVERT_PPT = 8;
    const LICENSE_CONVERT_CSV = 10;
    const LICENSE_CONVERT_TXT = 20;
    const LICENSE_CONVERT_IMG = 40;
    const LICENSE_CONVERT_RTF = 80;

    //PDF标准版套餐功能
    const PDF_SDK_STANDARD_FUNCTION = [
        'viewer' => [
            self::LICENSE_VIEWER_OUTLINE,
            self::LICENSE_VIEWER_BOOKMARK,
        ],
        'annotations' => [
            self::LICENSE_ANNOT_NOTE,
            self::LICENSE_ANNOT_LINK,
            self::LICENSE_ANNOT_SHAPE,
            self::LICENSE_ANNOT_MARKUP,
            self::LICENSE_ANNOT_DELETE,
        ],
        'form' => [
            self::LICENSE_FORM_FILL
        ],
        'document_editor' => [
            self::LICENSE_EDITOR_PAGE,
        ],
        'security' => [],
        'test_edit' => [],
        'conversion' => []
    ];

    //PDF专业版套餐功能
    const PDF_SDK_PROFESSIONAL_FUNCTION = [
        'viewer' => [
            self::LICENSE_VIEWER_OUTLINE,
            self::LICENSE_VIEWER_BOOKMARK,
            self::LICENSE_VIEWER_SEARCH,
            self::LICENSE_VIEWER_RENDER,
        ],
        'annotations' => [
            self::LICENSE_ANNOT_NOTE,
            self::LICENSE_ANNOT_LINK,
            self::LICENSE_ANNOT_SHAPE,
            self::LICENSE_ANNOT_MARKUP,
            self::LICENSE_ANNOT_DELETE,
            self::LICENSE_ANNOT_STAMP_S,
            self::LICENSE_ANNOT_STAMP_C,
            self::LICENSE_ANNOT_FREETEXT,
            self::LICENSE_ANNOT_INK
        ],
        'form' => [
            self::LICENSE_FORM,
            self::LICENSE_FORM_FILL,
        ],
        'document_editor' => [
            self::LICENSE_EDITOR_PAGE,
        ],
        'security' => [
            self::LICENSE_SECURITY_ENCRYPT,
            self::LICENSE_SECURITY_DECRYPT,
        ],
        'test_edit' => [],
        'conversion' => [],
    ];

    //PDF企业版套餐功能
    const PDF_SDK_ENTERPRISE_FUNCTION = [
        'viewer' => [
            self::LICENSE_VIEWER_OUTLINE,
            self::LICENSE_VIEWER_BOOKMARK,
            self::LICENSE_VIEWER_SEARCH,
            self::LICENSE_VIEWER_RENDER
        ],
        'annotations' => [
            self::LICENSE_ANNOT_NOTE,
            self::LICENSE_ANNOT_LINK,
            self::LICENSE_ANNOT_SHAPE,
            self::LICENSE_ANNOT_MARKUP,
            self::LICENSE_ANNOT_DELETE,
            self::LICENSE_ANNOT_STAMP_S,
            self::LICENSE_ANNOT_STAMP_C,
            self::LICENSE_ANNOT_FREETEXT,
            self::LICENSE_ANNOT_INK,
            self::LICENSE_ANNOT_SOUND,
            self::LICENSE_ANNOT_XFDF,
            self::LICENSE_ANNOT_FLATTEN,
        ],
        'form' => [
            self::LICENSE_FORM,
            self::LICENSE_FORM_FILL,
        ],
        'document_editor' => [
            self::LICENSE_EDITOR_PAGE,
            self::LICENSE_EDITOR_INFO,
            self::LICENSE_EDITOR_EXTRACT,
            self::LICENSE_EDITOR_CONVERT
        ],
        'security' => [
            self::LICENSE_SECURITY_ENCRYPT,
            self::LICENSE_SECURITY_DECRYPT,
            self::LICENSE_SECURITY_WATERMARK,
            self::LICENSE_SECURITY_REDACTION,
            self::LICENSE_SECURITY_HEADERFOOTER,
            self::LICENSE_SECURITY_BATES,
            self::LICENSE_SECURITY_BACKGROUND
        ],
        'test_edit' => [
            self::LICENSE_EDIT_TEXT,
            self::LICENSE_EDIT_IMAGE,
        ],
        'conversion' => [
            self::LICENSE_CONVERSION_PDFA
        ]
    ];

    //转档标准版套餐功能
    const CONVERSION_SDK_STANDARD_FUNCTION = [
        self::LICENSE_CONVERT_TXT,
        self::LICENSE_CONVERT_CSV,
        self::LICENSE_CONVERT_IMG,
        self::LICENSE_CONVERT_WORD,
        self::LICENSE_CONVERT_TABLE,
    ];

    //转档标准版套餐功能
    const CONVERSION_SDK_PROFESSIONAL_FUNCTION = [
        self::LICENSE_CONVERT_TXT,
        self::LICENSE_CONVERT_CSV,
        self::LICENSE_CONVERT_IMG,
        self::LICENSE_CONVERT_WORD,
        self::LICENSE_CONVERT_TABLE,
        self::LICENSE_CONVERT_PPT
    ];

    //转档标准版套餐功能
    const CONVERSION_SDK_ENTERPRISE_FUNCTION = [
        self::LICENSE_CONVERT_TXT,
        self::LICENSE_CONVERT_CSV,
        self::LICENSE_CONVERT_IMG,
        self::LICENSE_CONVERT_WORD,
        self::LICENSE_CONVERT_TABLE,
        self::LICENSE_CONVERT_PPT,
        self::LICENSE_CONVERT_EXCEL
    ];

    /**
     * 生成序列码
     * @param $product
     * @param $license_type
     * @param $platform
     * @param $start_time
     * @param $end_time
     * @param $ids
     * @param $email
     * @return array
     * @throws \Exception
     */
    public function generate($product, $license_type, $platform, $start_time, $end_time, $ids, $email){
        $permission = $this->getPermission($product, $license_type);
        \Log::info('生成序列码permission:' . $permission);
        $platform = $this->getPlatformCode($platform);

        $license_demo_path = '/php_compdf_server' . DIRECTORY_SEPARATOR . 'licensedemo';
        $filename = $license_demo_path . DIRECTORY_SEPARATOR . 'licensefile' . DIRECTORY_SEPARATOR . $email . '_' . time() . '.xml';

        //秘钥
        $private_key = $license_demo_path . DIRECTORY_SEPARATOR . 'private_key.pem';

        //拼接生成序列码命令
        $command = $license_demo_path . DIRECTORY_SEPARATOR. "LicenseDemo -pem \"$private_key\" -plat \"$platform\" -stt \"$start_time\" -edt \"$end_time\" -t \"2\" -parms \"$permission\"";
        //拼接ids
        foreach ($ids as $id){
            $command .= " -ids \"$id\"";
        }
        $command .= " -output \"$filename\"";

        \Log::info('生成序列码命令:' . $command);

        exec("php -v", $result);
        \Log::info('生成序列码结果：', $result);

        $str = file_get_contents($filename);
        //获取key
        $first_key = strpos($str, '<key>') + strlen('<key>');
        $len_key = strripos($str, '</key>') - $first_key;
        $key = substr($str, $first_key, $len_key);

        //获取secret
        $first_secret = strpos($str, '<secret>') + strlen('<secret>');
        $len_secret = strripos($str, '</secret>') - $first_secret;
        $secret = substr($str, $first_secret, $len_secret);

        return ['key'=>$key, 'secret'=>$secret];
    }

    /**
     * 根据产品 套餐获取权限码
     * @param $product
     * @param $license_type
     * @return string
     * @throws \Exception
     */
    private function getPermission($product, $license_type){
        $permissions = [];
        if($product == 'ComPDFKit PDF SDK'){
            switch ($license_type){
                case 'Standard License':
                    $permissions = self::PDF_SDK_STANDARD_FUNCTION;
                    break;
                case 'Professional License':
                    $permissions = self::PDF_SDK_PROFESSIONAL_FUNCTION;
                    break;
                case 'Enterprise License':
                    $permissions = self::PDF_SDK_ENTERPRISE_FUNCTION;
                    break;
            }

            return $this->formatSDKPermission($permissions);
        }elseif($product == 'ComPDFKit Conversion SDK'){
            switch ($license_type){
                case 'Standard License':
                    $permissions = self::CONVERSION_SDK_STANDARD_FUNCTION;
                    break;
                case 'Professional License':
                    $permissions = self::CONVERSION_SDK_PROFESSIONAL_FUNCTION;
                    break;
                case 'Enterprise License':
                    $permissions = self::CONVERSION_SDK_ENTERPRISE_FUNCTION;
                    break;
            }
            return $this->formatConversionPermission($permissions);
        }

        throw new \Exception('系统错误');
    }

    /**
     * 获得SDK权限码
     * @param $permissions
     * @return string
     */
    private function formatSDKPermission($permissions){
        //按照这个顺序拼接
        $str = '';
        $modules = ['viewer', 'annotations', 'form', 'document_editor', 'security', 'test_edit', 'conversion'];
        foreach ($modules as $module){
            $function_arr = $permissions[$module];
            if(empty($function_arr)){
                $str .= '0000';
            }else{
                $str .= $this->getFunctionStr($function_arr);
            }
        }

        return $str;
    }

    /**
     * 获得转档权限码
     * @param $permissions
     * @return string
     */
    private function formatConversionPermission($permissions){
        return $this->getFunctionStr($permissions);
    }

    /**
     * 将模块的功能点进行位运算得到 这个模块的权限码
     * @param $function_arr
     * @return string
     */
    private function getFunctionStr($function_arr){
        $start = $function_arr[0];
        $len = count($function_arr);

        //将各个功能位运算
        for($i = 1; $i < $len; $i++){
            $start = $start | $function_arr[$i];
        }

        //十进制转十六进制
        $result = dechex($start);

        //不足4位补0
        $result = str_pad($result, 4, '0', STR_PAD_LEFT);

        return $result;
    }

    /**
     * 获取平台码
     * @param $platform
     * @return int|string
     */
    private function getPlatformCode($platform){
        switch ($platform){
            case 'iOS':
                return self::LICENSE_PLATFORM_IOS;
            case 'Android':
                return self::LICENSE_PLATFORM_AND;
            case 'Windows':
                return self::LICENSE_PLATFORM_WIN;
            case 'Mac':
                return self::LICENSE_PLATFORM_MAC;
            case 'Linux':
                return self::LICENSE_PLATFORM_LINUX;
        }

        return '';
    }
}