<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'products' => Product::query()->count(),
                'categories' => Category::query()->count(),
                'orders' => Order::query()->count(),
                'customers' => User::query()->where('is_admin', false)->count(),
                'revenue' => number_format((float) Order::query()->sum('subtotal'), 2, '.', ''),
            ],
            'recentOrders' => Order::query()->latest()->take(6)->get(),
            'featuredProducts' => Product::query()
                ->where('is_featured', true)
                ->orderBy('name')
                ->take(6)
                ->get(),
        ]);
    }
}
