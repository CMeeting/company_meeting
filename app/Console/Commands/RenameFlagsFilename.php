<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RenameFlagsFilename extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rename:flags:filename';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '国旗文件名转大写';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo "文件名转大写：开始\n";
        $dir = public_path() . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'flags';
        $files = scandir($dir);
        foreach ($files as $file){
            $file_path = $dir . DIRECTORY_SEPARATOR . $file;
            if($file == '.' || $file == '..'){
                continue;
            }
            if(file_exists($file_path)){
                $arr = explode('.', $file);
                $filename = $arr[0];
                $file_ext = $arr[1];
                $new_filename = $dir . DIRECTORY_SEPARATOR . strtoupper($filename) . '.' . $file_ext;
                rename($file_path, $new_filename);
            }
        }
        echo "文件名转大写：结束\n";
    }
}
