# Fault Handling Module — Build Plan

## What it is
Field service module: customer calls in → office logs complaint → assigns to field staff → field staff completes → office bills.

## Database

### 1. field_complaints table
- id, complaint_no (auto, e.g. FC-2605001)
- customer_name, phone_no, address, location_notes
- service_type (AC / Washing Machine / RO System / Solar Panel / Other — from service_types table)
- description (fault details)
- priority (Low / Normal / High / Urgent)
- status (Pending / Assigned / In Progress / Completed / Billed / Cancelled)
- assigned_to (employee_id, must be field/outbound staff)
- assigned_at
- scheduled_date (when to go)
- completed_at
- completion_notes
- field_staff_note (from field staff side)
- photos (JSON — field staff can attach, for future app)
- created_by (admin/employee)
- created_at, updated_at

### 2. service_types table
- id, name, base_charge (default), icon, active

### 3. field_complaint_items table (billing line items)
- id, complaint_id, description, qty, unit_price, total

### 4. Employee table
- Add `type` column: 'inbound' | 'outbound' (field staff)
- Keep existing `role` column

## Models
- FieldComplaint
- ServiceType
- FieldComplaintItem

## Controllers (Admin)
- FieldComplaintController (CRUD + assign + status + billing)
- ServiceTypeController (manage service categories)

## Views (Admin)
- field-complaints/index — list with status filter tabs
- field-complaints/create — log new complaint
- field-complaints/show — full detail + assign + bill generate
- service-types/index — manage categories

## Routes
- /admin/field-complaints (index, create, store, show, edit, update, destroy)
- /admin/field-complaints/{id}/assign (PATCH)
- /admin/field-complaints/{id}/status (PATCH — for office to update)
- /admin/field-complaints/{id}/complete (PATCH — simulate field staff completion)
- /admin/field-complaints/{id}/bill (GET — view invoice)
- /admin/service-types (CRUD)

## Sidebar
- New section "Field Services" under Operations
- Field Complaints (with badge for pending/assigned count)
- Service Types

## Notifications
- When field staff marks complete → notification in admin bell
- Reuse existing notification system pattern (poll or eager load)

## Build Order
1. Migrations (service_types, field_complaints, field_complaint_items, add type to employees)
2. Models
3. Seed service types (AC, Washing Machine, RO System, Solar Panel, Other)
4. Routes
5. ServiceTypeController (simple CRUD)
6. FieldComplaintController
7. Views: index → create → show (with assign + bill panels)
8. Sidebar
9. Employee type field on Employee create/edit
10. Notifications integration

## Status: IN PROGRESS
