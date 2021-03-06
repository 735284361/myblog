<?php

namespace App\Http\Controllers;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Think\Db\Driver\Pdo;

class WeChatController extends Controller
{

    protected $app;
//    protected $openId = 'ovFDq1VUIzL6ed56AAnOMgAULXW0';

    // 互助社
    protected $openId = 'oAkD2v1aQM1aBTYV4OKeYg7ecHns';
//    protected $openIdList = ['oAkD2v1aQM1aBTYV4OKeYg7ecHns','oAkD2vzZo1agrwdTdheJHJ8V9_o8'];

    public function __construct()
    {
        $this->app = app('wechat.official_account');
    }

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $this->app->server->push(function ($message) {
            Log::info($message);
            switch ($message['MsgType']) {
                case 'event':
                    return '收到事件消息'.$message['EventKey'];
                    break;
                case 'text':
                    return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                case 'file':
                    return '收到文件消息';
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }

            // ...
        });

        return $this->app->server->serve();
    }

//    public function groupMsg()
//    {
//        return $this->app->broadcasting->sendText("大家好！欢迎使用...",[$this->openIdList]);
//    }
//
//    public function broadcastingStatus()
//    {
//        return $this->app->broadcasting->status('3147483654');
//    }
//
//    public function template_message()
//    {
//        return $this->app->template_message->send([
//            'touser' => 'ovFDq1VUIzL6ed56AAnOMgAULXW0',
//            'template_id' => 'JEshBkIT1mapl4qHot9fG-7yQlxox3xCXPqJatMi4ng',
//            'url' => 'https://m.hrpindao.com/wechat.php',
//            'scene' => 1000,
//            'data' => [
//                'first' => '你已购买新的订单',
//                'key1' => '兰博基尼',
//                'key2' => '50W',
//                'key3' => date('Y-m-d H:i:s'),
//                'remark' => '欢迎您下次光临',
//            ],
//        ]);
//    }
//
//    public function getUserInfo()
//    {
//        return $this->app->user->get($this->openId);
//    }
//
    public function tag_list()
    {
        return $this->app->user_tag->list();
    }

    public function user_tag()
    {
//        for ($i = 0; $i < 10; $i++) {
//            $list = $this->getUserCity($i);
//            $this->app->user_tag->tagUsers($list, 119);
//        }
//        for ($i = 0; $i < 20; $i++) {
//            $list = $this->getVipList($i);
//            $this->app->user_tag->tagUsers($list, 120);
//        }
    }

    public function test()
    {
        for ($i = 0; $i < 20; $i++) {
            $list = $this->getVipList($i);
            dump($list);
        }
    }


//    public function getUserCity($offset)
//    {
//        $list = DB::table('user_info')
//            ->leftJoin('users', 'user_info.uid', '=', 'users.uid')
//            ->where('user_city','like','%北京%')
//            ->offset($offset*20)->limit(20)->get()->toArray();
//
//        $list = array_column($list,'OpenID');
//
//        return $list;
//    }
//
//
//    public function getVipList($offset)
//    {
//        $list = DB::table('vip_user')
//            ->leftJoin('users', 'vip_user.uid', '=', 'users.uid')
//            ->where('vip_user.end_time','>=',time())
//            ->where('vip_user.state','=',1)
//            ->offset($offset*20)->limit(20)->get()->toArray();
//
//        $list = array_column($list,'OpenID');
//
//        return $list;
//    }

//    public function login()
//    {
//        $app = app('wechat.official_account');
//        $oauth = $app->oauth;
//
//        // 未登录
//        if (empty($_SESSION['wechat_user'])) {
//
//            $_SESSION['target_url'] = 'user/profile';
//
//            return $oauth->redirect();
//            // 这里不一定是return，如果你的框架action不是返回内容的话你就得使用
//            // $oauth->redirect()->send();
//        }
//
//        // 已经登录过
//        $user = $_SESSION['wechat_user'];
//    }
//
//    public function oauth_callback()
//    {
//        $app = app('wechat.official_account');
//        $oauth = $app->oauth;
//
//        // 获取 OAuth 授权结果用户信息
//        $user = $oauth->user();
//
//        $_SESSION['wechat_user'] = $user->toArray();
//
//        $targetUrl = empty($_SESSION['target_url']) ? '/' : $_SESSION['target_url'];
//
//        header('location:'. $targetUrl); // 跳转到 user/profile
//    }

    public function jsskdConfig()
    {
       return $this->app->jssdk->buildConfig();
    }

    public function qrcode()
    {
        $result = $this->app->qrcode->temporary('test qrcode', 6 * 24 * 3600);
        $ticket = $result['ticket'];
        $url = $this->app->qrcode->url($ticket);

        $content = file_get_contents($url); // 得到二进制图片内容

        file_put_contents(__DIR__ . '/code.jpg', $content); // 写入文件

    }

    public function auto_replay()
    {
        return $this->app->auto_reply->current();
    }

    public function wx_pay()
    {
        $order_no = time().rand(100000,999999);
        $app = app('wechat.payment');
        $result = $app->order->unify([
            'body' => 'EasyWeChat',
            'out_trade_no' => $order_no,
            'total_fee' => 101,
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $this->openId,
        ]);

        $jssdk = $app->jssdk;
        $prepay_id = $result['prepay_id'];
        $json = $jssdk->bridgeConfig($prepay_id);
        return $json;
    }

    public function wxpay_callback()
    {
        Log::info('wxpay_callback');
        $app = app('wechat.payment');
        $app->payment->handleNotify(function($notify, $successful){
            Log::info($notify);
            Log::info($successful);
        });

    }

    public function mini_login(Request $request)
    {
        $code = $request->input('code');
        $app = app('wechat.mini_program');

        $data = $app->auth->session($code);

        $openId = $data['openid'];
        //查看对应的openid是否已被注册
        $userModel = User::where('openid', $openId)->first();
        //如果未注册，跳转到注册
        if (!$userModel) {
            return ['code' => 10000];
        } else {
            //如果已被注册，通过openid进行自动认证，
            //认证通过后重定向回原来的路由，这样就实现了自动登陆。
            if(Auth::attempt(['openid' => $openId])) {
                return redirect()->intended();
            }
        }
    }


    public function mini_register()
    {
        echo 1;
    }

//    public function test()
//    {
//        echo Str::random(60);
//    }

}
