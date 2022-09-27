<?php


namespace App\Providers;


use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResponseMacServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    /**
     * @param ResponseFactory $factory
     */
    public function boot(ResponseFactory $factory)
    {
        $factory->macro('downloadEx', function($file, $name = null, array $headers = array(), $disposition = 'attachment'){
            $response = new BinaryFileResponse($file, 200, $headers, true);
            if (is_null($name))
            {
                $name = basename($file);
            }
            return $response->setContentDisposition($disposition, $name, Str::ascii($name));
        });
    }
}