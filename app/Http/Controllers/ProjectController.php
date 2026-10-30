<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\User;
use App\Support\RecordAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectController extends Controller
{
    private const PRODUCT_CATEGORIES = [
        'Server', 'Storage', 'Firewall', 'Network', 'Software', 'Hardware',
        'Cloud', 'Security', 'Backup', 'Database', 'Virtualization', 'Other',
    ];

    private const PROJECT_TYPES = ['New Project', 'Renewal'];

    private const PROGRESS_STATUSES = [
        'Prospecting', 'Lead', 'Quotation', 'Proposal', 'Negotiation',
        'PO Received', 'Delivery', 'Invoice', 'Payment', 'Completed',
        'Cancelled', 'Lost',
    ];

    public function index(Request $request): View
    {
        $search         = trim((string) $request->query('search'));
        $fromDate       = $request->query('from_date');
        $toDate         = $request->query('to_date');
        $customerId     = $request->query('customer_id');
        $supplierId     = $request->query('supplier_id');
        $accountManager = trim((string) $request->query('account_manager'));
        $type           = trim((string) $request->query('type'));
        $status         = trim((string) $request->query('status'));
        $record         = trim((string) $request->query('record'));

        $projects = RecordAccess::apply(
            Project::query(),
            $request->user(),
            'project_access_scope'
        )
            ->with(['customer', 'supplier', 'creator', 'assignedUser'])
            ->when($fromDate, fn($query) => $query->whereDate('date', '>=', $fromDate))
            ->when($toDate, fn($query) => $query->whereDate('date', '<=', $toDate))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('vendor', 'like', '%' . $search . '%')
                        ->orWhere('productCategory', 'like', '%' . $search . '%');
                });
            })
            ->when($customerId, fn($query) => $query->where('customer_id', $customerId))
            ->when($supplierId, fn($query) => $query->where('supplier_id', $supplierId))
            ->when($accountManager !== '', fn($query) => $query->where('accountManagerName', 'like', '%' . $accountManager . '%'))
            ->when($type !== '', fn($query) => $query->where('type', $type))
            ->when($status !== '', fn($query) => $query->where('status', $status))
            ->when($record === 'active', fn($query) => $query->where('is_active', true))
            ->when($record === 'inactive', fn($query) => $query->where('is_active', false))
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        $customers = Customer::query()->where('status', 'Active')->orderBy('name')->get();
        $suppliers = Supplier::query()->where('status', 'Active')->orderBy('name')->get();

        return view('projects', compact('projects', 'customers', 'suppliers'));
    }

    public function create(): View
    {
        return view('project-create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('projects.create'), 403);
        $validated = $this->validateProject($request);

        Project::query()->create([
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
            'created_by'         => $request->user()->user_id,
            'assigned_user_id'   => $request->user()->user_id,
        ]);

        return redirect()->route('projectsPage')->with('success', 'Project created successfully.');
    }

    public function show(Request $request, Project $project): View
    {
        RecordAccess::authorize($request->user(), $project, 'project_access_scope', 'projects.view');
        $project->load(['customer', 'supplier', 'creator', 'assignedUser']);

        return view('project-detail', compact('project'));
    }

    public function edit(Request $request, Project $project): View
    {
        RecordAccess::authorize($request->user(), $project, 'project_access_scope', 'projects.edit');

        /*
         * Do not load costItems or revenueItems here unless those relationships
         * really exist in Project.php. The edit view can use the aggregate
         * project.cost and project.revenue fields already stored on the project.
         */
        return view(
            'project-edit',
            array_merge($this->formData(), compact('project'))
        );
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        RecordAccess::authorize($request->user(), $project, 'project_access_scope', 'projects.edit');
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

        if ($request->user()->hasPermission('projects.deactivate')) {
            $projectData['is_active'] = $request->boolean('is_active');
        }

        $project->update($projectData);

        return redirect()->route('projectsPage')->with('success', 'Project updated successfully.');
    }

    public function deactivate(Request $request, Project $project): RedirectResponse
    {
        RecordAccess::authorize($request->user(), $project, 'project_access_scope', 'projects.deactivate');
        $project->update(['status' => 'Cancelled', 'is_active' => false]);
        User::query()->where('project_id', $project->project_id)->update(['project_id' => null]);

        return back()->with('success', 'Project cancelled successfully.');
    }

    public function toggleActive(Request $request, Project $project): RedirectResponse
    {
        RecordAccess::authorize($request->user(), $project, 'project_access_scope', 'projects.deactivate');
        $project->update(['is_active' => ! $project->is_active]);

        return back()->with(
            'success',
            $project->is_active ? 'Project activated successfully.' : 'Project deactivated successfully.'
        );
    }

    public function export(Request $request): StreamedResponse
    {
        abort_unless($request->user()->hasPermission('projects.view'), 403);

        $validated = $request->validate([
            'from_date'       => ['nullable', 'date'],
            'to_date'         => ['nullable', 'date', 'after_or_equal:from_date'],
            'search'          => ['nullable', 'string', 'max:255'],
            'customer_id'     => ['nullable', 'integer', Rule::exists('customers', 'customer_id')],
            'supplier_id'     => ['nullable', 'integer', Rule::exists('suppliers', 'supplier_id')],
            'account_manager' => ['nullable', 'string', 'max:255'],
            'type'            => ['nullable', Rule::in(self::PROJECT_TYPES)],
            'status'          => ['nullable', Rule::in(self::PROGRESS_STATUSES)],
            'record'          => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $search  = trim((string) ($validated['search'] ?? ''));
        $manager = trim((string) ($validated['account_manager'] ?? ''));

        $query = RecordAccess::apply(Project::query(), $request->user(), 'project_access_scope')
            ->with(['customer', 'supplier'])
            ->when($validated['from_date'] ?? null, fn($query, $date) => $query->whereDate('date', '>=', $date))
            ->when($validated['to_date'] ?? null, fn($query, $date) => $query->whereDate('date', '<=', $date))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('vendor', 'like', '%' . $search . '%')
                        ->orWhere('accountManagerName', 'like', '%' . $search . '%')
                        ->orWhere('productCategory', 'like', '%' . $search . '%');
                });
            })
            ->when($validated['customer_id'] ?? null, fn($query, $id) => $query->where('customer_id', $id))
            ->when($validated['supplier_id'] ?? null, fn($query, $id) => $query->where('supplier_id', $id))
            ->when($manager !== '', fn($query) => $query->where('accountManagerName', 'like', '%' . $manager . '%'))
            ->when($validated['type'] ?? null, fn($query, $value) => $query->where('type', $value))
            ->when($validated['status'] ?? null, fn($query, $value) => $query->where('status', $value))
            ->when(($validated['record'] ?? null) === 'active', fn($query) => $query->where('is_active', true))
            ->when(($validated['record'] ?? null) === 'inactive', fn($query) => $query->where('is_active', false));

        $fileName = 'projects_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Project ID', 'Project Name', 'Customer', 'Supplier', 'Status', 'Cost', 'Revenue']);

            $query->orderBy('project_id')->chunkById(500, function ($projects) use ($handle): void {
                foreach ($projects as $project) {
                    fputcsv($handle, [
                        $project->project_id,
                        $this->safeCsvValue($project->name),
                        $this->safeCsvValue($project->customer?->name),
                        $this->safeCsvValue($project->supplier?->name),
                        $this->safeCsvValue($project->status),
                        $project->cost,
                        $project->revenue,
                    ]);
                }
            }, 'project_id', 'project_id');

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function formData(): array
    {
        return [
            'customers'         => Customer::query()->where('status', 'Active')->orderBy('name')->get(),
            'suppliers'         => Supplier::query()->where('status', 'Active')->orderBy('name')->get(),
            'productCategories' => self::PRODUCT_CATEGORIES,
            'projectTypes'      => self::PROJECT_TYPES,
            'progressStatuses'  => self::PROGRESS_STATUSES,
        ];
    }

    private function validateProject(Request $request): array
    {
        return $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'customer_id'        => ['required', 'integer', Rule::exists('customers', 'customer_id')],
            'supplier_id'        => ['required', 'integer', Rule::exists('suppliers', 'supplier_id')],
            'vendor'             => ['required', 'string', 'max:255'],
            'accountManagerName' => ['required', 'string', 'max:255'],
            'productCategory'    => ['required', Rule::in(self::PRODUCT_CATEGORIES)],
            'type'               => ['required', Rule::in(self::PROJECT_TYPES)],
            'status'             => ['required', Rule::in(self::PROGRESS_STATUSES)],
            'is_active'          => ['sometimes', 'boolean'],
            'date'               => ['required', 'date'],
            'end_date'           => ['nullable', 'date', 'after_or_equal:date'],
            'remark'             => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function safeCsvValue(mixed $value): string
    {
        $value = (string) ($value ?? '');
        return $value !== '' && in_array($value[0], ['=', '+', '-', '@'], true) ? "'" . $value : $value;
    }
}
