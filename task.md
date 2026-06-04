# TrackUp Feature Updates – Status

## DONE ✅
1. **Device photo upload** — create form has `enctype` + file input; controller stores to `public/device-photos`
2. **Device photo display** — show page now renders thumbnail (clickable to full size)
3. **Reference No** — `reference_no` column + badge in ticket hero header
4. **Ticket Milestones** — `ticket_milestones` table, auto-seeded on ticket create, status/help/transfer UI on show page
5. **Nested form fix** — delete milestone form moved outside update form
6. **Dashboard uncollected card URL** — uses `['report'=>'uncollected']` ✅
7. **Report queries in controller** — `uncollectedJobs` + `mileData` via `ReportController`, shop-scoped ✅
8. **Service type Add modal** — milestone editor added (addAddMilestoneRow / serializeAddMilestones)
9. **WhatsApp quotation** — template + button on jobcard show page
10. **Uncollected reminders** — `SendUncollectedReminders` command + `reminder_sent_count`/`last_reminder_sent_at` columns; scheduled hourly
11. **WhatsApp settings** — `uncollected_reminder_enabled/count/interval_hours` fields in view ✅

## REMAINING ❓
- [ ] **Cron setup**: `php artisan schedule:run` needs to be in server crontab (`* * * * * cd /path && php artisan schedule:run`)
- [ ] **Test end-to-end**: create jobcard with photo, create ticket with service type that has milestones, check milestone auto-seed
- [ ] **WhatsApp settings visual check**: enable uncollected reminder, verify saves correctly
