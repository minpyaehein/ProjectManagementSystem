<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCost;
use App\Models\ProjectRevenue;
use App\Support\RecordAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProjectFinancialItemController extends Controller
{
    private const AMOUNT_PATTERN = '/^\d{1,20}(\.\d{1,2})?$/';

    public function storeCost(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProjectEdit($request, $project);
        $validated = $this->validateFinancialItem($request);

        DB::transaction(function () use ($project, $validated): void {
            $project->costItems()->create([
                'item_date'   => $validated['item_date'],
                'description' => $validated['description'],
                'amount'      => $validated['amount'],
                'is_active'   => true,
            ]);

            $this->synchronizeProjectTotals($project);
        });

        return back()->with('success', 'Cost item added successfully.');
    }

    public function updateCost(
        Request $request,
        Project $project,
        ProjectCost $cost
    ): RedirectResponse {
        $this->authorizeProjectEdit($request, $project);
        $this->ensureCostBelongsToProject($project, $cost);
        $validated = $this->validateFinancialItem($request);

        DB::transaction(function () use ($project, $cost, $validated): void {
            $cost->update([
                'item_date'   => $validated['item_date'],
                'description' => $validated['description'],
                'amount'      => $validated['amount'],
            ]);

            $this->synchronizeProjectTotals($project);
        });

        return back()->with('success', 'Cost item updated successfully.');
    }

    public function destroyCost(
        Request $request,
        Project $project,
        ProjectCost $cost
    ): RedirectResponse {
        $this->authorizeProjectEdit($request, $project);
        $this->ensureCostBelongsToProject($project, $cost);

        DB::transaction(function () use ($project, $cost): void {
            $cost->delete();
            $this->synchronizeProjectTotals($project);
        });

        return back()->with('success', 'Cost item removed successfully.');
    }

    public function storeRevenue(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProjectEdit($request, $project);
        $validated = $this->validateFinancialItem($request);

        DB::transaction(function () use ($project, $validated): void {
            $project->revenueItems()->create([
                'item_date'   => $validated['item_date'],
                'description' => $validated['description'],
                'amount'      => $validated['amount'],
                'is_active'   => true,
            ]);

            $this->synchronizeProjectTotals($project);
        });

        return back()->with('success', 'Revenue item added successfully.');
    }

    public function updateRevenue(
        Request $request,
        Project $project,
        ProjectRevenue $revenue
    ): RedirectResponse {
        $this->authorizeProjectEdit($request, $project);
        $this->ensureRevenueBelongsToProject($project, $revenue);
        $validated = $this->validateFinancialItem($request);

        DB::transaction(function () use ($project, $revenue, $validated): void {
            $revenue->update([
                'item_date'   => $validated['item_date'],
                'description' => $validated['description'],
                'amount'      => $validated['amount'],
            ]);

            $this->synchronizeProjectTotals($project);
        });

        return back()->with('success', 'Revenue item updated successfully.');
    }

    public function destroyRevenue(
        Request $request,
        Project $project,
        ProjectRevenue $revenue
    ): RedirectResponse {
        $this->authorizeProjectEdit($request, $project);
        $this->ensureRevenueBelongsToProject($project, $revenue);

        DB::transaction(function () use ($project, $revenue): void {
            $revenue->delete();
            $this->synchronizeProjectTotals($project);
        });

        return back()->with('success', 'Revenue item removed successfully.');
    }

    /**
     * @return array{item_date: string, description: string, amount: string}
     */
    private function validateFinancialItem(Request $request): array
    {
        $validated = $request->validate(
            [
                'item_date'   => ['required', 'date'],
                'description' => ['required', 'string', 'max:255'],
                'amount'      => ['required', 'string', 'regex:' . self::AMOUNT_PATTERN],
            ],
            [
                'item_date.required'   => 'Please select the item date.',
                'item_date.date'       => 'Please enter a valid item date.',
                'description.required' => 'Please enter a description.',
                'description.max'      => 'The description cannot exceed 255 characters.',
                'amount.required'      => 'Please enter an amount.',
                'amount.regex'         => 'The amount may contain up to 20 digits and 2 decimal places.',
            ]
        );

        $validated['description'] = trim($validated['description']);
        $validated['amount']      = trim($validated['amount']);

        if ($validated['description'] === '') {
            throw ValidationException::withMessages([
                'description' => 'Please enter a description.',
            ]);
        }

        $validated['amount'] = $this->normalizeAmount($validated['amount']);

        return $validated;
    }

    private function normalizeAmount(string $amount): string
    {
        if (! str_contains($amount, '.')) {
            return $amount . '.00';
        }

        [$whole, $decimal] = explode('.', $amount, 2);

        return $whole . '.' . str_pad($decimal, 2, '0');
    }

    private function authorizeProjectEdit(Request $request, Project $project): void
    {
        RecordAccess::authorize(
            $request->user(),
            $project,
            'project_access_scope',
            'projects.edit'
        );
    }

    private function ensureCostBelongsToProject(Project $project, ProjectCost $cost): void
    {
        abort_unless(
            (int) $cost->project_id === (int) $project->project_id,
            404
        );
    }

    private function ensureRevenueBelongsToProject(
        Project $project,
        ProjectRevenue $revenue
    ): void {
        abort_unless(
            (int) $revenue->project_id === (int) $project->project_id,
            404
        );
    }

    private function synchronizeProjectTotals(Project $project): void
    {
        $costTotal = $project
            ->costItems()
            ->where('is_active', true)
            ->sum('amount');

        $revenueTotal = $project
            ->revenueItems()
            ->where('is_active', true)
            ->sum('amount');

        $project->update([
            'cost'    => $costTotal,
            'revenue' => $revenueTotal,
        ]);

        $project->refresh();
    }
}
