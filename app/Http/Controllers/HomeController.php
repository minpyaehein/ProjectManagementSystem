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
        $customerCount     = Customer::query()->count();
        $supplierCount     = Supplier::query()->count();

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

        /*
 * Display the latest six months represented by project dates.
 *
 * If the newest project date is in a future month, such as
 * November 2026, the chart will include November 2026.
 */
        $latestProjectDate = Project::query()
            ->whereNotNull('date')
            ->max('date');

        $reportEndMonth = $latestProjectDate
            ? \Illuminate\Support\Carbon::parse($latestProjectDate)
            ->startOfMonth()
            : now()->startOfMonth();

/*
 * Never end the chart before the current month.
 */
        if ($reportEndMonth->lessThan(now()->startOfMonth())) {
            $reportEndMonth = now()->startOfMonth();
        }

        $monthStart = $reportEndMonth
            ->copy()
            ->subMonths(5);

        $monthEnd = $reportEndMonth
            ->copy()
            ->addMonth();

        $projectCountsByMonth = Project::query()
            ->whereNotNull('date')
            ->whereDate(
                'date',
                '>=',
                $monthStart->toDateString()
            )
            ->whereDate(
                'date',
                '<',
                $monthEnd->toDateString()
            )
            ->get([
                'project_id',
                'date',
            ])
            ->countBy(
                fn(Project $project): string =>
                $project->date->format('Y-m')
            );

        $monthlyProjectReport = collect(range(0, 5))
            ->map(
                function (int $offset) use (
                    $monthStart,
                    $projectCountsByMonth
                ): array {
                    $month = $monthStart
                        ->copy()
                        ->addMonths($offset);

                    $monthKey = $month->format('Y-m');

                    return [
                        'key'        => $monthKey,
                        'label'      => $month->format('M'),
                        'full_label' =>
                        $month->format('F Y'),
                        'count'      => (int) (
                            $projectCountsByMonth[$monthKey] ?? 0
                        ),
                    ];
                }
            );

        $highestMonthlyProjectCount = (int)
        $monthlyProjectReport->max('count');
        $monthlyProjectMaximum = max(
            1,
            $highestMonthlyProjectCount
        );

        $sixMonthProjectTotal = (int)
        $monthlyProjectReport->sum('count');

        $monthlyProjectReport = collect(range(0, 5))
            ->map(function (int $offset) use (
                $monthStart,
                $projectCountsByMonth
            ): array {
                $month = $monthStart->copy()->addMonths($offset);
                $key   = $month->format('Y-m');

                return [
                    'key'        => $key,
                    'label'      => $month->format('M'),
                    'full_label' => $month->format('F Y'),
                    'count'      => (int) ($projectCountsByMonth[$key] ?? 0),
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
