<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCost;
use App\Models\ProjectRevenue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectFinancialItemController extends Controller
{
    public function storeCost(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'item_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
        ]);

        $project->costItems()->create($validated + ['is_active' => true]);
        $this->synchronizeProjectTotals($project);

        return back()->with('success', 'Cost item added successfully.');
    }

    public function updateCost(
        Request $request,
        Project $project,
        ProjectCost $cost
    ): RedirectResponse {
        abort_unless($cost->project_id === $project->project_id, 404);

        $validated = $request->validate([
            'item_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
        ]);

        $cost->update($validated);
        $this->synchronizeProjectTotals($project);

        return back()->with('success', 'Cost item updated successfully.');
    }

    public function destroyCost(Project $project, ProjectCost $cost): RedirectResponse
    {
        abort_unless($cost->project_id === $project->project_id, 404);

        $cost->delete();
        $this->synchronizeProjectTotals($project);

        return back()->with('success', 'Cost item removed successfully.');
    }

    public function storeRevenue(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'item_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
        ]);

        $project->revenueItems()->create($validated + ['is_active' => true]);
        $this->synchronizeProjectTotals($project);

        return back()->with('success', 'Revenue item added successfully.');
    }

    public function updateRevenue(
        Request $request,
        Project $project,
        ProjectRevenue $revenue
    ): RedirectResponse {
        abort_unless($revenue->project_id === $project->project_id, 404);

        $validated = $request->validate([
            'item_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
        ]);

        $revenue->update($validated);
        $this->synchronizeProjectTotals($project);

        return back()->with('success', 'Revenue item updated successfully.');
    }

    public function destroyRevenue(
        Project $project,
        ProjectRevenue $revenue
    ): RedirectResponse {
        abort_unless($revenue->project_id === $project->project_id, 404);

        $revenue->delete();
        $this->synchronizeProjectTotals($project);

        return back()->with('success', 'Revenue item removed successfully.');
    }

    private function synchronizeProjectTotals(Project $project): void
    {
        $project->update([
            'cost' => $project->costItems()->where('is_active', true)->sum('amount'),
            'revenue' => $project->revenueItems()->where('is_active', true)->sum('amount'),
        ]);
    }
}
