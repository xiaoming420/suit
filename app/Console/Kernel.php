<?php

namespace App\Console;

use App\Libs\JSSDK;
use App\Models\goods;
use App\Models\order_helps;
use App\Models\orders;
use App\Models\users;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Ixudra\Curl\Facades\Curl;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\MakeCode'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $jssdk = new JSSDK();
            $list = orders::resetorder();
            foreach ($list as $v) {
                $user_info = users::where(['unionid'=>$v['unionid']])->first();
                $goods_info = goods::where(['id'=>$v['goods_id']])->first();
                orders::where(['id'=>$v['id']])->update(['order_status'=>2, 'ut'=>date('Y-m-d H:i:s')]);
                goods::where(['id'=>$v['goods_id']])->increment('stock_use');
                $count = order_helps::where(['order_id'=>$v['id'], 'is_valid'=>1])->count();
                $touser = $user_info['openid'];
                $formId = $v['form_id'];
                $template_id = env('f_template_id');
                $pages = 'pages/index/index';
                $prompt = '您在3天活动期间内，助力人数未满足。可以重新参与》';
                //示例数据根据消息模板填充
                $data = array(
                    'keyword1'=>array('value'=>$goods_info['name'],'color'=>'#7167ce'),
                    'keyword2'=>array('value'=>(string)$goods_info['price'].'元','color'=>'#7167ce'),
                    'keyword3'=>array('value'=>'0元','color'=>'#ff0000'),
                    'keyword4'=>array('value'=>$goods_info['helps'].'人','color'=>'#7167ce'),
                    'keyword5'=>array('value'=>$count.'人','color'=>'#7167ce'),
                    'keyword6'=>array('value'=>$prompt,'color'=>'#ff0000'),
                );
                $res = $jssdk->sendTemplate($touser,$template_id,$pages,$data,$formId);
            }
        })->cron('*/1 * * * *');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
