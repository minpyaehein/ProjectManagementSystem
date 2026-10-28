@extends('layouts.master')

@section('title', 'Project Detail | Golden Pride')
@section('page-name', 'Projects / Detail')

@section('content')
    @php
        $costItems = $project->costItems ?? collect();
        $revenueItems = $project->revenueItems ?? collect();

        $activeCostTotal = $costItems->where('is_active', true)->sum('amount');
        $activeRevenueTotal = $revenueItems->where('is_active', true)->sum('amount');

        // Fall back to the project columns when no financial line items exist.
        $costTotal = $costItems->isNotEmpty() ? $activeCostTotal : (float) ($project->cost ?? 0);

        $revenueTotal = $revenueItems->isNotEmpty() ? $activeRevenueTotal : (float) ($project->revenue ?? 0);

        $grossProfit = $revenueTotal - $costTotal;
        $grossProfitPercent = $revenueTotal > 0 ? ($grossProfit / $revenueTotal) * 100 : 0;
    @endphp

    <section class="content project-detail-page">
        <div class="project-detail-card">
            <div class="project-detail-accent" aria-hidden="true"></div>

            <div class="project-detail-header">
                <div>

                    <div class="project-detail-title-row">
                        <div>
                            <p class="project-detail-code">
                                PRJ-{{ str_pad((string) $project->project_id, 4, '0', STR_PAD_LEFT) }}
                            </p>
                            <h1>{{ $project->name }}</h1>
                        </div>
                    </div>

                    <p class="project-detail-description">
                        Project profile, schedule, commercial summary, cost items, and revenue items.
                    </p>
                </div>

                <a href="{{ route('projectsPage') }}" class="project-entry-close" aria-label="Close and return to project">
                    &times;
                </a>

            </div>

            @if (session('success'))
                <div class="alert alert-success project-detail-alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="project-detail-section">
                <div class="project-detail-section-heading">
                    <span class="project-detail-section-number">1</span>
                    <div>
                        <h2>Project information</h2>
                        <p>Customer, supplier, ownership, and progress details.</p>
                    </div>
                </div>

                <div class="project-detail-grid">
                    <div class="project-detail-item">
                        <span>Project</span>
                        <strong>{{ $project->name }}</strong>
                    </div>

                    <div class="project-detail-item">
                        <span>End User</span>
                        <strong>{{ $project->customer?->name ?? '—' }}</strong>
                    </div>

                    <div class="project-detail-item">
                        <span>Supplier</span>
                        <strong>{{ $project->supplier?->name ?? '—' }}</strong>
                    </div>

                    <div class="project-detail-item">
                        <span>Vendor</span>
                        <strong>{{ $project->vendor ?: '—' }}</strong>
                    </div>

                    <div class="project-detail-item">
                        <span>Account Manager</span>
                        <strong>{{ $project->accountManagerName ?: '—' }}</strong>
                    </div>

                    <div class="project-detail-item">
                        <span>Product Category</span>
                        <strong>{{ $project->productCategory ?: '—' }}</strong>
                    </div>

                    <div class="project-detail-item">
                        <span>Project Type</span>
                        <strong>{{ $project->type ?: '—' }}</strong>
                    </div>

                    <div class="project-detail-item">
                        <span>Progress Status</span>
                        <strong>{{ $project->status ?: '—' }}</strong>
                    </div>

                </div>
            </div>

            <div class="project-detail-section">
                <div class="project-detail-section-heading">
                    <span class="project-detail-section-number">2</span>
                    <div>
                        <h2>Schedule and notes</h2>
                        <p>Project timeline and additional information.</p>
                    </div>
                </div>

                <div class="project-detail-grid project-detail-schedule-grid">
                    <div class="project-detail-item">
                        <span>Start Date</span>
                        <strong>
                            {{ $project->date ? $project->date->format('d M Y') : '—' }}
                        </strong>
                    </div>

                    <div class="project-detail-item">
                        <span>End Date</span>
                        <strong>
                            {{ $project->end_date ? $project->end_date->format('d M Y') : 'No deadline' }}
                        </strong>
                    </div>

                    <div class="project-detail-item project-detail-remark">
                        <span>Remark</span>
                        <strong>{{ $project->remark ?: '—' }}</strong>
                    </div>
                </div>
            </div>

            <div class="project-detail-section">
                <div class="project-detail-section-heading">
                    <span class="project-detail-section-number">3</span>
                    <div>
                        <h2>Commercial summary</h2>
                        <p>Active cost and revenue totals with gross profit.</p>
                    </div>
                </div>

                <div class="financial-overview">
                    <div class="financial-overview-card">
                        <span>Cost</span>
                        <strong>{{ number_format($costTotal, 2) }} MMK</strong>
                    </div>

                    <div class="financial-overview-card">
                        <span>Revenue</span>
                        <strong>{{ number_format($revenueTotal, 2) }} MMK</strong>
                    </div>

                    <div class="financial-overview-card">
                        <span>GP</span>
                        <strong class="{{ $grossProfit < 0 ? 'negative' : '' }}">
                            {{ number_format($grossProfit, 2) }} MMK
                        </strong>
                    </div>

                    <div class="financial-overview-card">
                        <span>GP %</span>
                        <strong class="{{ $grossProfitPercent < 0 ? 'negative' : '' }}">
                            {{ number_format($grossProfitPercent, 2) }}%
                        </strong>
                    </div>
                </div>
            </div>

            <div class="project-detail-section">
                <div class="project-detail-section-heading">
                    <span class="project-detail-section-number">4</span>
                    <div>
                        <h2>Cost items</h2>
                        <p>All cost line items for this project.</p>
                    </div>
                </div>

                <div class="financial-table-wrap">
                    <table class="financial-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th class="financial-number">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($costItems as $item)
                                <tr class="{{ $item->is_active ? '' : 'financial-row-inactive' }}">
                                    <td>{{ $item->item_date?->format('d M Y') ?? '—' }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td class="financial-number">
                                        {{ number_format((float) $item->amount, 2) }} MMK
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="financial-empty">
                                        No cost items have been added.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="project-detail-section">
                <div class="project-detail-section-heading">
                    <span class="project-detail-section-number">5</span>
                    <div>
                        <h2>Revenue items</h2>
                        <p>All revenue line items for this project.</p>
                    </div>
                </div>

                <div class="financial-table-wrap">
                    <table class="financial-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th class="financial-number">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($revenueItems as $item)
                                <tr class="{{ $item->is_active ? '' : 'financial-row-inactive' }}">
                                    <td>{{ $item->item_date?->format('d M Y') ?? '—' }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td class="financial-number">
                                        {{ number_format((float) $item->amount, 2) }} MMK
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="financial-empty">
                                        No revenue items have been added.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="project-detail-header-actions" style="margin: 20px;">
                @if (auth()->user()->hasPermission('projects.edit'))
                    <a class="project-detail-edit-button" href="{{ route('projects.edit', $project) }}">
                        Edit Project
                    </a>
                @endif

                <a class="project-entry-cancel" href="{{ route('projectsPage') }}">
                    Back
                </a>
            </div>
        </div>
    </section>
@endsection
