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

        $recentCustomers = Customer::query()
            ->latest('created_at')
            ->limit(3)
            ->get();

        $recentSuppliers = Supplier::query()
            ->latest('created_at')
            ->limit(3)
            ->get();

        $monthStart = now()->startOfMonth()->subMonths(5);

        $projectCountsByMonth = Project::query()
            ->where('created_at', '>=', $monthStart)
            ->get(['project_id', 'created_at'])
            ->countBy(fn (Project $project): string =>
                $project->created_at->format('Y-m')
            );

        $monthlyProjectReport = collect(range(0, 5))
            ->map(function (int $offset) use (
                $monthStart,
                $projectCountsByMonth
            ): array {
                $month = $monthStart->copy()->addMonths($offset);
                $key = $month->format('Y-m');

                return [
                    'key' => $key,
                    'label' => $month->format('M'),
                    'full_label' => $month->format('F Y'),
                    'count' => (int) ($projectCountsByMonth[$key] ?? 0),
                ];
            });

        $monthlyProjectMaximum = max(
            0,
            (int) $monthlyProjectReport->max('count')
        );

        $sixMonthProjectTotal = (int) $monthlyProjectReport->sum('count');

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
                'recentCustomers',
                'recentSuppliers',
                'monthlyProjectReport',
                'monthlyProjectMaximum',
                'sixMonthProjectTotal'
            )
        );
    }
}
