<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Order;
use App\Item;
use Cart;

class ShopController extends Controller
{
    protected $goods = [
        1=>['goods_id'=>1,'goods_name'=>'白百合 清香宜人', 'price'=>23.1],
        2=>['goods_id'=>2,'goods_name'=>'红玫瑰 热烈奔放', 'price'=>23.2],
        3=>['goods_id'=>3,'goods_name'=>'黄牡丹 雍容华贵', 'price'=>23.3],
        4=>['goods_id'=>4,'goods_name'=>'狗尾巴 淡泊名利', 'price'=>23.4],
        ];
    //
    public function index()
    {
        return view('index', ['goods'=>$this->goods]);
    }

    public function goods($goods_id)
    {
        $goods_info = $this->goods[$goods_id];
        return view('goods', ['goods_info'=>$goods_info]);
    }

    public function buy($goods_id)
    {
        $goods = $this->goods[$goods_id];
        Cart::add(array(
            'id' => $goods['goods_id'],
            'name' => $goods['goods_name'],
            'price' => $goods['price'],
            'quantity' => 1,
        ));
        return redirect('cart');
    }

    public function cart()
    {
        $goods = Cart::getContent();
        $total = Cart::getTotal();
        return view('cart', ['goods'=>$goods, 'total'=>$total]);
    }

    public function clear()
    {
        Cart::clear();
        return redirect('/');
    }

/*
+------------+
| Field      |
+------------+
| order_id   |
| order_sn   |
| name       |
| address    |
| tel        |
| money      |
| is_pay     |
| order_time |
+------------+
*/
    public function submit_order(Request $req)
    {
        $order = new Order;
        $order->order_sn = date('Ymd').mt_rand(10000, 99999);
        $order->name = $req->name;
        $order->address = $req->address;
        $order->tel = $req->tel;
        $order->money = Cart::getTotal();
        $order->is_pay = 0;
        $order->order_time = time();

        $order->save();
/*
+------------+
| Field      |
+------------+
| item_id    |
| order_id   |
| goods_id   |
| goods_name |
| price      |
| amount     |
+------------+
*/
        foreach(Cart::getContent() as $i) {
            $item = new Item;
            $item->order_id = $order->order_id;
            $item->goods_id = $i->id;
            $item->goods_name = $i->name;
            $item->price = $i->price;
            $item->amount = $i->quantity;
            $item->save();
        }
        Cart::clear();
        return view('pay', ['order_id'=>$order->order_id, 'money'=>$order->money]);
    }

    public function finish()
    {
        
    }
}
