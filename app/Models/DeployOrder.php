<?php

namespace App\Models;

use App\Events\OrderUpdated;
use Illuminate\Database\Eloquent\SoftDeletes;
use Appm\Models\Order;

class DeployOrder extends BaseModel
{

    use SoftDeletes;

    protected $table = 'orders_deploy';

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

}
