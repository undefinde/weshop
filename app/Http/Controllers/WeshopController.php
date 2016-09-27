<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Overtrue\Wechat\Server;
use Overtrue\Wechat\User as WeUser;
use Overtrue\Wechat\QRCode;
use App\User;
use DB;

class WeshopController extends Controller
{
    //

    public function index()
    {
        $server = new Server(env('WSHOP_ID'), env('WSHOP_TOKEN'));
        $server->on('event', 'subscribe', [$this, 'subscribe']);
        $server->on('event', 'unsubscribe', [$this, 'unsubscribe']);
        return $server->serve();
    }

    public function subscribe($event)
    {
        $userService = new WeUser(env('WSHOP_ID'), env('WSHOP_SEC'));
        $user = $userService->get($event->FromUserName);
        $userName = $user->nickname;
        $msg = '欢迎您，'.$userName;
        $users = new User;
        $userinfo = $users->where('openid', $event->FromUserName)->first();
        if ($userinfo && $userinfo->status == 1) {
            return $msg;
        }
        if ($userinfo && $userinfo->status == 0) {
            $userinfo->status = 1;
            $userinfo->save();
            return $msg;
            //$userinfo->update(['status'=>1]);
        } else {
            if ($event->EventKey) {
                $puid = substr($event->EventKey, 8);
                $pu = $users->find($puid);
                $users->p1 = $puid;
                $users->p2 = $pu->p1;
                $users->p3 = $pu->p3;
            }
            $users->openid = $event->FromUserName;
            $users->name = $userName;
            $users->subtime = time();
            $users->save();
        }
        $this->createQR($users->uid);
        return $msg;
    }

    public function createQR($uid)
    {
        $qrcode = new QRCode(env('WSHOP_ID'), env('WSHOP_SEC'));
        $result = $qrcode->forever($uid);
        $ticket = $result->ticket;
        $qrcode->download($ticket, $this->mkdir().'/'.$uid.'.jpg');
    }

    public function mkdir() {
        $path = public_path().'/'.date('Y/md');
        if ( !is_dir($path) ) {
            mkdir($path, 0777, true);
        }
        return $path;
    }

    public function unsubscribe($event)
    {
        $user = User::where('openid', $event->FromUserName)
                ->update(['status'=>0]);
    }


}
