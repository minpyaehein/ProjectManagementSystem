<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $activeProjectCount = Project::query()
            ->where('is_active', true)
            ->count();

        $inactiveProjectCount = Project::query()
            ->where('is_active', false)
            ->count();

        $totalProjectCount = Project::query()->count();

        $customerCount = Customer::query()->count();
        $supplierCount = Supplier::query()->count();
        $activeUserCount = User::query()
            ->where('status', 'Active')
            ->count();
        $roleCount = Role::query()->count();

        $recentProjects = Project::query()
            ->with([
                'customer',
                'supplier',
            ])
            ->latest('created_at')
            ->limit(5)
            ->get();

        $recentCustomers = Customer::query()
            ->latest('created_at')
            ->limit(3)
            ->get();

        $recentSuppliers = Supplier::query()
            ->latest('created_at')
            ->limit(3)
            ->get();

        return view(
            'home',
            compact(
                'activeProjectCount',
                'inactiveProjectCount',
                'totalProjectCount',
                'customerCount',
                'supplierCount',
                'activeUserCount',
                'roleCount',
                'recentProjects',
                'recentCustomers',
                'recentSuppliers'
            )
        );
    }
}
