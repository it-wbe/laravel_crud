<?php namespace Wbe\Crud\Controllers\Adminer;

use Illuminate\Routing\Controller;

class AdminerController extends Controller {

    public function index()
    {
        require('adminer-4.2.4-en.php');
        return new EmptyResponse();
    }

}
