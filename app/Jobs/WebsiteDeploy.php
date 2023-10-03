<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WebsiteDeploy implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数。
     *
     * @var int
     */
    public $tries = 1;

    /**
     * 任务运行的超时时间。
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * 订单号
     * @var string
     */
    private $orderSN;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $orderSN)
    {
        $this->orderSN = $orderSN;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = app('Service\OrderService')->detailOrderSN($this->orderSN);
        if ($order && $order->status == Order::STATUS_PROCESSING) {
            $deployInfo = app('Service\OrderService')->detailDeployOrderSN($this->orderSN);
            $fp = fsockopen($deployInfo->ssh_host, $deployInfo->ssh_port, $errno, $errstr, 3);
            if ($fp) {
                $deployUrl = 'http://'.env('AUTO_DEPLOY_HOST').':'.env('AUTO_DEPLOY_PORT').'/AutoDeploy/deploy';
                $req['ansible_host'] = $deployInfo->ssh_host;
                $req['ansible_port'] = strval($deployInfo->ssh_port);
                $req['ansible_user'] = $deployInfo->ssh_user;
                $req['ansible_ssh_pass'] = $deployInfo->ssh_verify;
                $req['ssh_method'] = strval($deployInfo->ssh_method);
                $req['mate_app_name'] = $deployInfo->app_name;
                $req['mate_app_version'] = '1.0';
                $req['mate_website_domain'] = $deployInfo->website_domain;
                $req['mate_website_protocol'] = "http";
                // 转为json
                $req = json_encode($req);
                // 记录日志
                Log::info('deployReq: '.$req);
                // 发送http stream请求
                $opts = array(
                    'http' => array(
                        'method' => 'POST',
                        'header' => 'Content-type: application/json',
                        'content' => $req,
                        'timeout' => 3,
                    )
                );
                try {
                    $context = stream_context_create($opts);
                    $result = '';
                    $fp = fopen($deployUrl, 'r', false, $context);
                    if ($fp) {
                        while (!feof($fp)) {
                            $line = fgets($fp);
                            $result .= $line;
                        }
                        fclose($fp);
                    }
                    //$order->log = $result;
                    Log::info('deployResult: '.$result);
                    // 字符串查找unreachable=0和failed=0
                    $unreachable = strpos($result, 'unreachable=0');
                    $failed = strpos($result, 'failed=0');
                    if ($unreachable === false || $failed === false) {
                        $order->status = Order::STATUS_FAILURE;
                    }
                    else {
                        $order->status = Order::STATUS_COMPLETED;
                    }
                    DB::beginTransaction();
                    try {
                        $order->save();
                        if ($order->statuss == Order::STATUS_COMPLETED) {
                            // 商品库存减去
                            $goodsService = app('Service\GoodsService');
                            $goodsService->inStockDecr($order->goods_id, 1);
                        }
                        DB::commit();
                    }
                    catch (\Exception $e) {
                        Log::error('deployResult: '. $e->getMessage());
                        DB::rollBack();
                    }
                } catch (\Exception $e) {
                    //$order->log = $e->getMessage();
                    Log::error('deployResult: '. $e->getMessage());
                    $order->status = Order::STATUS_FAILURE;
                    $order->save();
                    return;
                }
            } else {
                //$order->log = $errstr;
                Log::error('deployResult: '. $errstr);
                $order->status = Order::STATUS_FAILURE;
                $order->save();
            }
        }
    }
}

