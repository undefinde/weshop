<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Overtrue\Wechat\Auth;
use Session;

class UserController extends Controller
{
    protected $auth;

    public function __construct()
    {
        $this->auth = new Auth(env('WSHOP_ID'), env('WSHOP_SEC'));
    }

    public function index()
    {
        return 'this is index';
    }

    public function login(Request $req)
    {
        $to = 'http://8cltlst3jd.proxy.qqbrowser.cc/login';
        $user = $this->auth->authorize($to);
        $req->session()->put('user', $user->all());
        dd(Session::all());
    }

    public function logout(Request $req)
    {
        $req->session()->forget('user');
    }
}
