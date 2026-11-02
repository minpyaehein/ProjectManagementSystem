<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
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
         * Project outcome counts.
         * Won: Completed or Payment.
         * Lost: Cancelled or Lost.
         * In Progress: every other non-empty status.
         */
        $normalizedStatus = DB::raw(
            "LOWER(TRIM(COALESCE(status, '')))"
        );

        $wonStatuses = [
            'completed',
            'payment',
        ];

        $lostStatuses = [
            'cancelled',
            'lost',
        ];

        $wonProjectCount = Project::query()
            ->whereIn($normalizedStatus, $wonStatuses)
            ->count();

        $lostProjectCount = Project::query()
            ->whereIn($normalizedStatus, $lostStatuses)
            ->count();

        $inProgressProjectCount = Project::query()
            ->whereNotIn(
                $normalizedStatus,
                array_merge($wonStatuses, $lostStatuses)
            )
            ->count();

        /*
         * Calculate financial totals from active line items. This avoids
         * depending on potentially stale aggregate values in project.cost
         * and project.revenue.
         */
        $totalProjectCost = (float) DB::table('project_costs')
            ->where('is_active', true)
            ->sum('amount');

        $totalProjectRevenue = (float) DB::table('project_revenues')
            ->where('is_active', true)
            ->sum('amount');

        $totalGrossProfit =
            $totalProjectRevenue - $totalProjectCost;

        $totalGrossProfitPercent = $totalProjectRevenue > 0
            ? ($totalGrossProfit / $totalProjectRevenue) * 100
            : 0;

        /*
         * Build the six-month report from the user-selected Project date.
         * The window ends at the later of the current month or the latest
         * Project date so future-dated records can be represented.
         */
        $latestProjectDate = Project::query()
            ->whereNotNull('date')
            ->max('date');

        $currentMonth = now()->startOfMonth();

        $reportEndMonth = $latestProjectDate
            ? Carbon::parse($latestProjectDate)->startOfMonth()
            : $currentMonth->copy();

        if ($reportEndMonth->lessThan($currentMonth)) {
            $reportEndMonth = $currentMonth->copy();
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
            ->map(function (int $offset) use (
                $monthStart,
                $projectCountsByMonth
            ): array {
                $month = $monthStart
                    ->copy()
                    ->addMonths($offset);

                $key = $month->format('Y-m');

                return [
                    'key'        => $key,
                    'label'      => $month->format('M'),
                    'full_label' => $month->format('F Y'),
                    'count'      => (int) (
                        $projectCountsByMonth[$key] ?? 0
                    ),
                ];
            });

        $highestMonthlyProjectCount = (int) $monthlyProjectReport
            ->max('count');

        $monthlyProjectMaximum = max(
            1,
            $highestMonthlyProjectCount
        );

        $sixMonthProjectTotal = (int) $monthlyProjectReport
            ->sum('count');

        $projectStatusCounts = Project::query()
            ->selectRaw(
                "COALESCE(NULLIF(TRIM(status), ''), 'Not Set') AS status_label"
            )
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('status_label')
            ->orderByDesc('total')
            ->get()
            ->map(function ($status): array {
                $name       = (string) $status->status_label;
                $normalized = strtolower(trim($name));

                $statusClass = match ($normalized) {
                    'completed', 'payment' => 'status-success',
                    'cancelled', 'lost'    => 'status-danger',
                    'proposal', 'quotation', 'negotiation' => 'status-warning',
                    'delivery', 'in progress', 'ongoing'   => 'status-info',
                    default => 'status-neutral',
                };

                $statusIcon = match ($normalized) {
                    'completed'   => '✓',
                    'payment'     => 'P',
                    'cancelled', 'lost'      => '×',
                    'proposal'    => 'P',
                    'quotation'   => 'Q',
                    'negotiation' => 'N',
                    'delivery'    => 'D',
                    'in progress', 'ongoing' => '●',
                    default       => '•',
                };

                return [
                    'name'  => $name,
                    'count' => (int) $status->total,
                    'class' => $statusClass,
                    'icon'  => $statusIcon,
                ];
            })
            ->values();

        return view(
            'home',
            compact(
                'totalProjectCount',
                'inProgressProjectCount',
                'wonProjectCount',
                'lostProjectCount',
                'totalProjectCost',
                'totalProjectRevenue',
                'totalGrossProfit',
                'totalGrossProfitPercent',
                'customerCount',
                'supplierCount',
                'activeUserCount',
                'roleCount',
                'recentCustomers',
                'recentSuppliers',
                'monthlyProjectReport',
                'monthlyProjectMaximum',
                'highestMonthlyProjectCount',
                'sixMonthProjectTotal',
                'projectStatusCounts'
            )
        );
    }
}
