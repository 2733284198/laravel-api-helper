<?php

namespace DummyApiNamespace;

use DummyServicesNamespace\ResponseServe;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class DummyApiName extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ResponseServe;

}
