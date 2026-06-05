# CCTV Pipeline Chain Task

## Status Flow
Lead → Survey → Quotation → Project → Invoice

### Lead statuses (DB enum - needs ALTER):
New Lead → Survey Scheduled → Survey Completed → Quotation Sent → Approved → Installation → Completed | Cancelled | Rejected | Postponed | Rescheduled | Lost

### Survey statuses: Scheduled → Completed | Cancelled | Need More Time | Postponed | Rescheduled

### Quotation statuses: Draft → Sent → Approved | Rejected | Postponed | Rescheduled

### Project statuses (status col): scheduled → in_progress → completed | cancelled | postponed | rescheduled
### Project stage (separate): Survey Complete → Materials Ready → Installation Started → Configuration → Testing → Customer Handover → Warranty Activated

## What needs doing:

### 1. DB migrations
- ALTER lead status enum: add Cancelled, Rejected, Postponed, Rescheduled, Installation
- ALTER survey status enum: add Postponed, Rescheduled  
- ALTER quotation status enum: add Postponed, Rescheduled
- ALTER project status enum: add postponed, rescheduled, cancelled (check if string col)
- CREATE cctv_invoices table (project_id, lead_id, quotation_id, invoice_no, customer_name, mobile, address, equipment_list JSON, labour_cost, installation_cost, transport_cost, discount, tax, grand_total, paid_amount, status, due_date, notes)
- Add invoice_id FK to cctv_projects

### 2. Models
- CctvLead: add Cancelled/Rejected/Postponed/Rescheduled/Installation to status
- CctvInvoice model (new)
- CctvProject: add invoice() relation

### 3. Status auto-update chain
- SurveyController@store → lead status = "Survey Scheduled" (currently sets Completed)
  Actually: if status=Scheduled → lead=Survey Scheduled; if Completed → lead=Survey Completed ✓
- QuotationController@store → lead status = "Quotation Sent" ✓  
- QuotationController@update status=Approved → lead=Approved ✓
- QuotationController@update status=Rejected → lead=Rejected (NEW)
- ProjectController@store → lead=Installation, quotation=Approved (keep), add project link
- ProjectController@update status=completed → lead=Completed + quotation stays Approved
- ProjectController@update status=cancelled → lead=Cancelled
- ProjectController@update status=postponed → lead=Postponed
- NEW: InvoiceController (create from project)

### 4. "Create Quotation" button on Survey show
- survey show → "Create Quotation" btn (lead_id, pre-fill cx data)

### 5. Invoice creation from Project
- Project show → "Generate Invoice" btn
- CctvInvoiceController: create/store/show/pdf
- Pre-fill from project (cx name, mobile, address, equipment_list, contract_amount)

### 6. Pipeline banner on each show page
- Visual step tracker: Lead → Survey → Quotation → Project → Invoice
- Shows which step is done/current/pending
- Each done step is a clickable link

### 7. Status action buttons on each show
- Lead show: status change buttons (Postponed, Cancelled, Rejected, Rescheduled)
- Survey show: + "Create Quotation" btn  
- Quotation show: + status buttons (Rejected, Postponed, Rescheduled)
- Project show: + status buttons (Cancel, Postpone, Reschedule, Complete)

## Files to create/edit:
- database/migrations/..._extend_cctv_statuses.php
- database/migrations/..._create_cctv_invoices.php
- app/Models/CctvInvoice.php
- app/Http/Controllers/Admin/Cctv/CctvInvoiceController.php
- resources/views/admin/cctv/invoices/ (index, create, show, pdf)
- routes/web.php (add invoice routes)
- Edit: all 4 show views (pipeline banner + action buttons)
- Edit: CctvLeadController, CctvSurveyController, CctvQuotationController, CctvProjectController
