<section class="field full user-assignment-panel" id="recordAssignmentPanel">
    <div class="user-assignment-heading">
        <span class="eyebrow">Assigned access</span>
        <h2>Assign accessible records</h2>
        <p>Assignment lists appear only when the selected role uses Assigned access for the matching module.</p>
    </div>

    <div class="user-assignment-grid">
        <div class="field assignment-group" data-scope-group="project" hidden>
            <label>Projects</label>
            <div class="assignment-list">
                @forelse ($projects as $project)
                    <label class="assignment-option">
                        <input type="checkbox" name="project_ids[]" value="{{ $project->project_id }}" @checked(in_array((int) $project->project_id, $selectedProjectIds, true))>
                        <span>{{ $project->name }}</span>
                    </label>
                @empty
                    <span class="small-muted">No projects available.</span>
                @endforelse
            </div>
            @error('project_ids')<span class="field-error">{{ $message }}</span>@enderror
            @error('project_ids.*')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="field assignment-group" data-scope-group="customer" hidden>
            <label>Customers</label>
            <div class="assignment-list">
                @forelse ($customers as $customer)
                    <label class="assignment-option">
                        <input type="checkbox" name="customer_ids[]" value="{{ $customer->customer_id }}" @checked(in_array((int) $customer->customer_id, $selectedCustomerIds, true))>
                        <span>{{ $customer->name }}</span>
                    </label>
                @empty
                    <span class="small-muted">No customers available.</span>
                @endforelse
            </div>
            @error('customer_ids')<span class="field-error">{{ $message }}</span>@enderror
            @error('customer_ids.*')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="field assignment-group" data-scope-group="supplier" hidden>
            <label>Suppliers</label>
            <div class="assignment-list">
                @forelse ($suppliers as $supplier)
                    <label class="assignment-option">
                        <input type="checkbox" name="supplier_ids[]" value="{{ $supplier->supplier_id }}" @checked(in_array((int) $supplier->supplier_id, $selectedSupplierIds, true))>
                        <span>{{ $supplier->name }}</span>
                    </label>
                @empty
                    <span class="small-muted">No suppliers available.</span>
                @endforelse
            </div>
            @error('supplier_ids')<span class="field-error">{{ $message }}</span>@enderror
            @error('supplier_ids.*')<span class="field-error">{{ $message }}</span>@enderror
        </div>
    </div>
</section>

@push('styles')
<style>
.user-assignment-panel{margin-top:22px;padding:18px;border:1px solid var(--line);border-radius:12px;background:#fafcfb}.user-assignment-heading h2{margin:4px 0;color:var(--ink);font-size:16px}.user-assignment-heading p{margin:0;color:var(--muted);font-size:10px}.user-assignment-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-top:16px}.assignment-list{display:grid;gap:7px;max-height:230px;padding:10px;overflow:auto;border:1px solid var(--line);border-radius:9px;background:var(--white)}.assignment-option{display:flex;align-items:center;gap:8px;padding:7px;border-radius:7px;font-size:10px;font-weight:700}.assignment-option:hover{background:var(--mint)}.assignment-option input{width:16px;height:16px}.assignment-group[hidden]{display:none}@media(max-width:850px){.user-assignment-grid{grid-template-columns:1fr}}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){const role=document.getElementById('role_id');if(!role)return;const groups={project:document.querySelector('[data-scope-group="project"]'),customer:document.querySelector('[data-scope-group="customer"]'),supplier:document.querySelector('[data-scope-group="supplier"]')};function refresh(){const option=role.options[role.selectedIndex];const scopes={project:option?.dataset.projectScope,customer:option?.dataset.customerScope,supplier:option?.dataset.supplierScope};Object.entries(groups).forEach(([key,group])=>{if(group)group.hidden=scopes[key]!=='assigned'});}role.addEventListener('change',refresh);refresh();});
</script>
@endpush
