<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //封装成功返回的json
    protected function successJson( $data = null )
    {
        $ret = array(
            'err_no' => 0,
            'msg' => '成功',
            'data' => $data ? $data : new \stdClass
        );

        return response()->json( $ret );
    }
}
