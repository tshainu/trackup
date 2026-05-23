# TrackUp Laravel Rebuild

## Status: IN PROGRESS

## Done
- [x] Laravel installed
- [x] SQLite configured
- [x] Migration created (admins, employees, device_lists, device_brands, device_faults, job_cards, store_info)
- [x] Seeder (sample data: 10 job cards, 3 employees, 7 device types, brands, faults)
- [x] Models: Admin, Employee, DeviceList, DeviceBrand, DeviceFault, JobCard, StoreInfo
- [x] Auth controllers: AdminLoginController, EmployeeLoginController

## TODO
- [ ] Middleware: AdminAuth, EmployeeAuth
- [ ] Admin controllers: Dashboard, JobCard CRUD, Employee CRUD, Device CRUD, Track, Reports
- [ ] Employee controllers: Dashboard, Assigned Jobs
- [ ] Ajax controllers: DeviceBrand/Fault cascade
- [ ] Routes (web.php)
- [ ] Layout blade: admin layout (dark sidebar, Bootstrap 5)
- [ ] Views: login pages
- [ ] Views: admin dashboard, job card list/create/edit/track, employee list/create, device mgmt, reports
- [ ] Views: employee dashboard, assigned job list
- [ ] Barcode tracking (QuaggaJS)
- [ ] Run migrations + seed
- [ ] Start dev server, verify in preview
