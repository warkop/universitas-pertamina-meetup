<?php

namespace App\Http\Controllers;

use App\Helpers\HelperPublic;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $responseCode = 200;
    protected $responseStatus = '';
    protected $responseMessage = '';
    protected $responseData = [];
    protected $responseNote = '';

    public function __construct()
    {
        date_default_timezone_set("Asia/Jakarta");
    }

    public function getResponse()
    {
        return HelperPublic::helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus, $this->responseNote);
    }
}
