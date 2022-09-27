<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemoveExportFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:export:file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清除导出按钮生成的excel文件';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 先不考虑目录下包含目录的递归写法，以防删除其他文件
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info('----清除导出文件：开始----');
        $dir = base_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'export';
        $files = scandir($dir);
        foreach ($files as $file){
            $file_path = $dir . DIRECTORY_SEPARATOR . $file;
            if($file == '.' || $file == '..'){
                continue;
            }
            if(is_file($file_path)){
                unlink($file_path);
            }
        }
        \Log::info('----清除导出文件：完成----');
    }
}
