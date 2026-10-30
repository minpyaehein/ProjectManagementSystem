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
    public function storeCost(
        Request $request,
        Project $project
    ): RedirectResponse {
        $this->authorizeProjectEdit(
            $request,
            $project
        );

        $validated = $this->validateFinancialItem(
            $request
        );

        DB::transaction(
            function () use (
                $project,
                $validated
            ): void {
                $project->costItems()->create([
                    'item_date' => $validated['item_date'],
                    'description' => $validated['description'],
                    'amount' => $validated['amount'],
                    'is_active' => true,
                ]);

                $this->synchronizeProjectTotals(
                    $project
                );
            }
        );

        return back()->with(
            'success',
            'Cost item added successfully.'
        );
    }

    public function updateCost(
        Request $request,
        Project $project,
        ProjectCost $cost
    ): RedirectResponse {
        $this->authorizeProjectEdit(
            $request,
            $project
        );

        $this->ensureCostBelongsToProject(
            $project,
            $cost
        );

        $validated = $this->validateFinancialItem(
            $request
        );

        DB::transaction(
            function () use (
                $project,
                $cost,
                $validated
            ): void {
                $cost->update([
                    'item_date' => $validated['item_date'],
                    'description' => $validated['description'],
                    'amount' => $validated['amount'],
                ]);

                $this->synchronizeProjectTotals(
                    $project
                );
            }
        );

        return back()->with(
            'success',
            'Cost item updated successfully.'
        );
    }

    public function destroyCost(
        Request $request,
        Project $project,
        ProjectCost $cost
    ): RedirectResponse {
        $this->authorizeProjectEdit(
            $request,
            $project
        );

        $this->ensureCostBelongsToProject(
            $project,
            $cost
        );

        DB::transaction(
            function () use (
                $project,
                $cost
            ): void {
                $cost->delete();

                $this->synchronizeProjectTotals(
                    $project
                );
            }
        );

        return back()->with(
            'success',
            'Cost item removed successfully.'
        );
    }

    public function storeRevenue(
        Request $request,
        Project $project
    ): RedirectResponse {
        $this->authorizeProjectEdit(
            $request,
            $project
        );

        $validated = $this->validateFinancialItem(
            $request
        );

        DB::transaction(
            function () use (
                $project,
                $validated
            ): void {
                $project->revenueItems()->create([
                    'item_date' => $validated['item_date'],
                    'description' => $validated['description'],
                    'amount' => $validated['amount'],
                    'is_active' => true,
                ]);

                $this->synchronizeProjectTotals(
                    $project
                );
            }
        );

        return back()->with(
            'success',
            'Revenue item added successfully.'
        );
    }

    public function updateRevenue(
        Request $request,
        Project $project,
        ProjectRevenue $revenue
    ): RedirectResponse {
        $this->authorizeProjectEdit(
            $request,
            $project
        );

        $this->ensureRevenueBelongsToProject(
            $project,
            $revenue
        );

        $validated = $this->validateFinancialItem(
            $request
        );

        DB::transaction(
            function () use (
                $project,
                $revenue,
                $validated
            ): void {
                $revenue->update([
                    'item_date' => $validated['item_date'],
                    'description' => $validated['description'],
                    'amount' => $validated['amount'],
                ]);

                $this->synchronizeProjectTotals(
                    $project
                );
            }
        );

        return back()->with(
            'success',
            'Revenue item updated successfully.'
        );
    }

    public function destroyRevenue(
        Request $request,
        Project $project,
        ProjectRevenue $revenue
    ): RedirectResponse {
        $this->authorizeProjectEdit(
            $request,
            $project
        );

        $this->ensureRevenueBelongsToProject(
            $project,
            $revenue
        );

        DB::transaction(
            function () use (
                $project,
                $revenue
            ): void {
                $revenue->delete();

                $this->synchronizeProjectTotals(
                    $project
                );
            }
        );

        return back()->with(
            'success',
            'Revenue item removed successfully.'
        );
    }

    /**
     * Validate a Cost or Revenue line item.
     *
     * @return array{
     *     item_date: string,
     *     description: string,
     *     amount: int|float|string
     * }
     */
    private function validateFinancialItem(
        Request $request
    ): array {
        $validated = $request->validate(
            [
                'item_date' => [
                    'required',
                    'date',
                ],

                'description' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'amount' => [
                    'required',
                    'numeric',
                    'min:0.01',
                    'max:999999999999.99',
                ],
            ],
            [
                'item_date.required' =>
                    'Please select the item date.',

                'item_date.date' =>
                    'Please enter a valid item date.',

                'description.required' =>
                    'Please enter a description.',

                'description.max' =>
                    'The description cannot exceed 255 characters.',

                'amount.required' =>
                    'Please enter an amount.',

                'amount.numeric' =>
                    'The amount must be a valid number.',

                'amount.min' =>
                    'The amount must be at least 0.01.',

                'amount.max' =>
                    'The amount is greater than the supported limit.',
            ]
        );

        $validated['description'] = trim(
            $validated['description']
        );

        if ($validated['description'] === '') {
            throw ValidationException::withMessages([
                'description' =>
                    'Please enter a description.',
            ]);
        }

        return $validated;
    }

    /**
     * Require both projects.edit permission and Project
     * record access according to Own, Assigned, or All.
     */
    private function authorizeProjectEdit(
        Request $request,
        Project $project
    ): void {
        RecordAccess::authorize(
            $request->user(),
            $project,
            'project_access_scope',
            'projects.edit'
        );
    }

    /**
     * Prevent a Cost belonging to another Project from being
     * updated or deleted through a modified URL.
     */
    private function ensureCostBelongsToProject(
        Project $project,
        ProjectCost $cost
    ): void {
        abort_unless(
            (int) $cost->project_id
                === (int) $project->project_id,
            404
        );
    }

    /**
     * Prevent a Revenue item belonging to another Project
     * from being updated or deleted through a modified URL.
     */
    private function ensureRevenueBelongsToProject(
        Project $project,
        ProjectRevenue $revenue
    ): void {
        abort_unless(
            (int) $revenue->project_id
                === (int) $project->project_id,
            404
        );
    }

    /**
     * Keep the aggregate Project cost and revenue synchronized
     * with all active financial line items.
     */
    private function synchronizeProjectTotals(
        Project $project
    ): void {
        $costTotal = $project
            ->costItems()
            ->where('is_active', true)
            ->sum('amount');

        $revenueTotal = $project
            ->revenueItems()
            ->where('is_active', true)
            ->sum('amount');

        $project->update([
            'cost' => $costTotal,
            'revenue' => $revenueTotal,
        ]);

        /*
         * Refresh values for any code that uses the current
         * Project instance after synchronization.
         */
        $project->refresh();
    }
}
