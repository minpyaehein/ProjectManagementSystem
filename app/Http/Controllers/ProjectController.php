<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectController extends Controller
{
    private const PRODUCT_CATEGORIES = [
        'Server',
        'Storage',
        'Firewall',
        'Network',
        'Software',
        'Hardware',
        'Cloud',
        'Security',
        'Backup',
        'Database',
        'Virtualization',
        'Other',
    ];

    private const PROJECT_TYPES = [
        'New Project',
        'Renewal',
    ];

    private const PROGRESS_STATUSES = [
        'Prospecting',
        'Lead',
        'Quotation',
        'Proposal',
        'Negotiation',
        'PO Received',
        'Delivery',
        'Invoice',
        'Payment',
        'Completed',
        'Cancelled',
        'Lost',
    ];

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'from_date'       => ['nullable', 'date'],
            'to_date'         => ['nullable', 'date', 'after_or_equal:from_date'],
            'search'          => ['nullable', 'string', 'max:255'],
            'customer_id'     => ['nullable', 'integer'],
            'supplier_id'     => ['nullable', 'integer'],
            'account_manager' => ['nullable', 'string', 'max:255'],
            'type'            => ['nullable', 'string', 'max:255'],
            'status'          => ['nullable', 'string', 'max:255'],
            'record'          => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $search         = trim((string) ($validated['search'] ?? ''));
        $accountManager = trim((string) ($validated['account_manager'] ?? ''));

        $projects = Project::query()
            ->with(['customer', 'supplier'])
            ->when($validated['from_date'] ?? null, function ($query, $fromDate): void {
                $query->whereDate('date', '>=', $fromDate);
            })
            ->when($validated['to_date'] ?? null, function ($query, $toDate): void {
                $query->whereDate('date', '<=', $toDate);
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('vendor', 'like', '%' . $search . '%')
                        ->orWhere('accountManagerName', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', function ($customerQuery) use ($search): void {
                            $customerQuery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('supplier', function ($supplierQuery) use ($search): void {
                            $supplierQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($validated['customer_id'] ?? null, function ($query, $customerId): void {
                $query->where('customer_id', $customerId);
            })
            ->when($validated['supplier_id'] ?? null, function ($query, $supplierId): void {
                $query->where('supplier_id', $supplierId);
            })
            ->when($accountManager !== '', function ($query) use ($accountManager): void {
                $query->where('accountManagerName', 'like', '%' . $accountManager . '%');
            })
            ->when($validated['type'] ?? null, function ($query, $type): void {
                $query->where('type', $type);
            })
            ->when($validated['status'] ?? null, function ($query, $status): void {
                $query->where('status', $status);
            })
            ->when(($validated['record'] ?? null) === 'active', function ($query): void {
                $query->whereNotIn('status', ['Completed', 'Cancelled', 'Lost']);
            })
            ->when(($validated['record'] ?? null) === 'closed', function ($query): void {
                $query->whereIn('status', ['Completed', 'Cancelled', 'Lost']);
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $customers = Customer::query()
            ->orderBy('name')
            ->get(['customer_id', 'name']);

        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get(['supplier_id', 'name']);

        return view(
            'projects',
            compact('projects', 'customers', 'suppliers')
        );
    }

    public function create(): View
    {
        return view('project-create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProject($request);

        Project::create([
            'name'               => $validated['name'],
            'customer_id'        => $validated['customer_id'],
            'supplier_id'        => $validated['supplier_id'],
            'vendor'             => $validated['vendor'],
            'accountManagerName' => $validated['accountManagerName'],
            'productCategory'    => $validated['productCategory'],
            'type'               => $validated['type'],
            'status'             => $validated['status'],
            'is_active'          => true,
            'date'               => $validated['date'],
            'end_date'           => $validated['end_date'] ?? null,
            'remark'             => $validated['remark'] ?? null,
            'cost'               => 0,
            'revenue'            => 0,
            'log_id'             => null,
        ]);

        return redirect()
            ->route('projectsPage')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $project->load([
            'customer',
            'supplier',
            'costItems',
            'revenueItems',
        ]);

        return view('project-detail', compact('project'));
    }

    public function edit(Project $project): View
    {
        $project->load([
            'costItems',
            'revenueItems',
        ]);

        return view(
            'project-edit',
            array_merge(
                $this->formData(),
                compact('project')
            )
        );
    }

    public function update(
        Request $request,
        Project $project
    ): RedirectResponse {
        $validated = $this->validateProject($request);

        $projectData = [
            'name'               => $validated['name'],
            'customer_id'        => $validated['customer_id'],
            'supplier_id'        => $validated['supplier_id'],
            'vendor'             => $validated['vendor'],
            'accountManagerName' => $validated['accountManagerName'],
            'productCategory'    => $validated['productCategory'],
            'type'               => $validated['type'],
            'status'             => $validated['status'],
            'date'               => $validated['date'],
            'end_date'           => $validated['end_date'] ?? null,
            'remark'             => $validated['remark'] ?? null,
        ];

        if (
            $request->user()->hasPermission(
                'projects.deactivate'
            )
        ) {
            $projectData['is_active'] =
            $request->boolean('is_active');
        }

        $project->update($projectData);

        return redirect()
            ->route('projectsPage')
            ->with(
                'success',
                'Project updated successfully.'
            );
    }

    public function deactivate(Project $project): RedirectResponse
    {
        $project->update([
            'status' => 'Cancelled',
        ]);

        User::query()
            ->where('project_id', $project->project_id)
            ->update([
                'project_id' => null,
            ]);

        return back()->with(
            'success',
            'Project cancelled successfully.'
        );
    }

    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'from_date'       => [
                'nullable',
                'date',
            ],

            'to_date'         => [
                'nullable',
                'date',
                'after_or_equal:from_date',
            ],

            'search'          => [
                'nullable',
                'string',
                'max:255',
            ],

            'customer_id'     => [
                'nullable',
                'integer',
                Rule::exists('customers', 'customer_id'),
            ],

            'supplier_id'     => [
                'nullable',
                'integer',
                Rule::exists('suppliers', 'supplier_id'),
            ],

            'account_manager' => [
                'nullable',
                'string',
                'max:255',
            ],

            'type'            => [
                'nullable',
                Rule::in(self::PROJECT_TYPES),
            ],

            'status'          => [
                'nullable',
                Rule::in(self::PROGRESS_STATUSES),
            ],

            'record'          => [
                'nullable',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ]);

        $search = trim(
            (string) ($validated['search'] ?? '')
        );

        $accountManager = trim(
            (string) ($validated['account_manager'] ?? '')
        );

        $query = Project::query()
            ->with([
                'customer',
                'supplier',
            ])
            ->when(
                $validated['from_date'] ?? null,
                function ($query, $fromDate): void {
                    $query->whereDate('date', '>=', $fromDate);
                }
            )
            ->when(
                $validated['to_date'] ?? null,
                function ($query, $toDate): void {
                    $query->whereDate('date', '<=', $toDate);
                }
            )
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $query->where(
                        function ($subQuery) use ($search): void {
                            $subQuery
                                ->where(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'vendor',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'accountManagerName',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'productCategory',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhereHas(
                                    'customer',
                                    function ($customerQuery) use ($search): void {
                                        $customerQuery->where(
                                            'name',
                                            'like',
                                            '%' . $search . '%'
                                        );
                                    }
                                )
                                ->orWhereHas(
                                    'supplier',
                                    function ($supplierQuery) use ($search): void {
                                        $supplierQuery->where(
                                            'name',
                                            'like',
                                            '%' . $search . '%'
                                        );
                                    }
                                );
                        }
                    );
                }
            )
            ->when(
                $validated['customer_id'] ?? null,
                function ($query, $customerId): void {
                    $query->where('customer_id', $customerId);
                }
            )
            ->when(
                $validated['supplier_id'] ?? null,
                function ($query, $supplierId): void {
                    $query->where('supplier_id', $supplierId);
                }
            )
            ->when(
                $accountManager !== '',
                function ($query) use ($accountManager): void {
                    $query->where(
                        'accountManagerName',
                        'like',
                        '%' . $accountManager . '%'
                    );
                }
            )
            ->when(
                $validated['type'] ?? null,
                function ($query, $type): void {
                    $query->where('type', $type);
                }
            )
            ->when(
                $validated['status'] ?? null,
                function ($query, $status): void {
                    $query->where('status', $status);
                }
            )
            ->when(
                ($validated['record'] ?? null) === 'active',
                function ($query): void {
                    $query->where('is_active', true);
                }
            )
            ->when(
                ($validated['record'] ?? null) === 'inactive',
                function ($query): void {
                    $query->where('is_active', false);
                }
            );

        $fileName = 'projects_'
        . now()->format('Y-m-d_H-i-s')
            . '.csv';

        return response()->streamDownload(
            function () use ($query): void {
                $handle = fopen('php://output', 'w');

                if ($handle === false) {
                    return;
                }

                // UTF-8 BOM helps Microsoft Excel recognize UTF-8 text.
                fwrite($handle, "\xEF\xBB\xBF");

                fputcsv($handle, [
                    'Project ID',
                    'Project Code',
                    'Project Name',
                    'End User',
                    'Supplier',
                    'Vendor',
                    'Account Manager',
                    'Product Category',
                    'Project Type',
                    'Progress Status',
                    'Start Date',
                    'End Date',
                    'Remark',
                    'Cost (MMK)',
                    'Revenue (MMK)',
                    'Gross Profit (MMK)',
                    'GP Percentage',
                    'Created At',
                    'Updated At',
                ]);

                $query
                    ->orderBy('project_id')
                    ->chunkById(
                        500,
                        function ($projects) use ($handle): void {
                            foreach ($projects as $project) {
                                $cost               = (float) ($project->cost ?? 0);
                                $revenue            = (float) ($project->revenue ?? 0);
                                $grossProfit        = $revenue - $cost;
                                $grossProfitPercent = $revenue > 0
                                    ? ($grossProfit / $revenue) * 100
                                    : 0;

                                fputcsv($handle, [
                                    $project->project_id,

                                    'PRJ-' . str_pad(
                                        (string) $project->project_id,
                                        4,
                                        '0',
                                        STR_PAD_LEFT
                                    ),

                                    $this->safeCsvValue($project->name),
                                    $this->safeCsvValue($project->customer?->name),
                                    $this->safeCsvValue($project->supplier?->name),
                                    $this->safeCsvValue($project->vendor),
                                    $this->safeCsvValue($project->accountManagerName),
                                    $this->safeCsvValue($project->productCategory),
                                    $this->safeCsvValue($project->type),
                                    $this->safeCsvValue($project->status),
                                    $project->date?->format('Y-m-d') ?? '',
                                    $project->end_date?->format('Y-m-d') ?? '',
                                    $this->safeCsvValue($project->remark),
                                    number_format($cost, 2, '.', ''),
                                    number_format($revenue, 2, '.', ''),
                                    number_format($grossProfit, 2, '.', ''),
                                    number_format(
                                        $grossProfitPercent,
                                        2,
                                        '.',
                                        ''
                                    ) . '%',
                                    $project->created_at?->format(
                                        'Y-m-d H:i:s'
                                    ) ?? '',
                                    $project->updated_at?->format(
                                        'Y-m-d H:i:s'
                                    ) ?? '',
                                ]);
                            }
                        },
                        'project_id',
                        'project_id'
                    );

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type'  => 'text/csv; charset=UTF-8',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'Pragma'        => 'no-cache',
                'Expires'       => '0',
            ]
        );
    }

    public function toggleActive(
        Request $request,
        Project $project
    ): RedirectResponse {
        abort_unless(
            $request->user()->hasPermission(
                'projects.deactivate'
            ),
            403
        );

        $project->update([
            'is_active' => ! $project->is_active,
        ]);

        return back()->with(
            'success',
            $project->is_active
                ? 'Project activated successfully.'
                : 'Project deactivated successfully.'
        );
    }

    private function formData(): array
    {
        return [
            'customers'         => Customer::query()
                ->where('status', 'Active')
                ->orderBy('name')
                ->get(),

            'suppliers'         => Supplier::query()
                ->where('status', 'Active')
                ->orderBy('name')
                ->get(),

            'productCategories' => self::PRODUCT_CATEGORIES,
            'projectTypes'      => self::PROJECT_TYPES,
            'progressStatuses'  => self::PROGRESS_STATUSES,
        ];
    }

    private function validateProject(Request $request): array
    {
        return $request->validate([
            'name'               => [
                'required',
                'string',
                'max:255',
            ],
            'customer_id'        => [
                'required',
                'integer',
                Rule::exists('customers', 'customer_id'),
            ],
            'supplier_id'        => [
                'required',
                'integer',
                Rule::exists('suppliers', 'supplier_id'),
            ],
            'vendor'             => [
                'required',
                'string',
                'max:255',
            ],
            'accountManagerName' => [
                'required',
                'string',
                'max:255',
            ],
            'productCategory'    => [
                'required',
                Rule::in(self::PRODUCT_CATEGORIES),
            ],
            'type'               => [
                'required',
                Rule::in(self::PROJECT_TYPES),
            ],
            'status'             => [
                'required',
                Rule::in(self::PROGRESS_STATUSES),
            ],
            'is_active'          => [
                'sometimes',
                'boolean',
            ],
            'date'               => [
                'required',
                'date',
            ],
            'end_date'           => [
                'nullable',
                'date',
                'after_or_equal:date',
            ],
            'remark'             => [
                'nullable',
                'string',
                'max:2000',
            ],
            'cost'               => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],
            'revenue'            => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],
        ]);
    }

    private function safeCsvValue(mixed $value): string
    {
        $value = (string) ($value ?? '');

        if (
            $value !== '' &&
            in_array(
                $value[0],
                ['=', '+', '-', '@'],
                true
            )
        ) {
            return "'" . $value;
        }

        return $value;
    }
}
