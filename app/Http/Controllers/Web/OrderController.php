<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        return view('store.orders.index', [
            'orders' => $request->user()
                ->orders()
                ->withCount('items')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return view('store.orders.show', [
            'order' => $order->load('items.product'),
        ]);
    }
}
