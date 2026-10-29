<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Carbon;
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

        /*
         * Build the six-month project report from the Project Create Date
         * selected by the user. In this project, that column is `date`.
         * The reporting window ends at the later of the current month or
         * the latest project-date month, so future-dated projects can appear.
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

        $monthStart = $reportEndMonth->copy()->subMonths(5);
        $monthEnd = $reportEndMonth->copy()->addMonth();

        $projectCountsByMonth = Project::query()
            ->whereNotNull('date')
            ->whereDate('date', '>=', $monthStart->toDateString())
            ->whereDate('date', '<', $monthEnd->toDateString())
            ->get(['project_id', 'date'])
            ->countBy(
                fn (Project $project): string =>
                    $project->date->format('Y-m')
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

        $highestMonthlyProjectCount = (int) $monthlyProjectReport
            ->max('count');

        // Keep this divisor at least 1 to prevent division by zero in Blade.
        $monthlyProjectMaximum = max(1, $highestMonthlyProjectCount);

        $sixMonthProjectTotal = (int) $monthlyProjectReport
            ->sum('count');

        /*
         * Actual project workflow statuses from the Project form.
         * Examples: Proposal, Quotation, Negotiation, Delivery,
         * Payment, Completed, Cancelled, or other stored values.
         */
        $projectStatusCounts = Project::query()
            ->selectRaw(
                "COALESCE(NULLIF(TRIM(status), ''), 'Not Set') AS status_label"
            )
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('status_label')
            ->orderByDesc('total')
            ->get()
            ->map(function ($status): array {
                $name = (string) $status->status_label;
                $normalized = strtolower(trim($name));

                $statusClass = match ($normalized) {
                    'completed', 'payment' => 'status-success',
                    'cancelled', 'lost' => 'status-danger',
                    'proposal', 'quotation', 'negotiation' => 'status-warning',
                    'delivery', 'in progress', 'ongoing' => 'status-info',
                    default => 'status-neutral',
                };

                $statusIcon = match ($normalized) {
                    'completed' => '✓',
                    'payment' => 'P',
                    'cancelled', 'lost' => '×',
                    'proposal' => 'P',
                    'quotation' => 'Q',
                    'negotiation' => 'N',
                    'delivery' => 'D',
                    'in progress', 'ongoing' => '●',
                    default => '•',
                };

                return [
                    'name' => $name,
                    'count' => (int) $status->total,
                    'class' => $statusClass,
                    'icon' => $statusIcon,
                ];
            })
            ->values();

        return view('home', compact(
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
            'highestMonthlyProjectCount',
            'sixMonthProjectTotal',
            'projectStatusCounts'
        ));
    }
}
