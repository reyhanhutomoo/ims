# How to Drop English-Named and Unused Tables (Windows CMD)

Prerequisites:
- Ensure MySQL client binaries are on PATH (Laragon usually provides them).
- Verify the target DB name in [.env()](../.env:12) is `u230210512_ims`.
- Confirm the prepared drop script exists at [backups.drop_english_tables.sql](drop_english_tables.sql).

Important:
- Do NOT drop Indonesian-named tables used by models.
- Keep core Laravel tables unless you are certain they are unused (e.g., `password_resets`, `failed_jobs`, `personal_access_tokens`).
- A full backup is strongly recommended.

Steps:

1) Create a full backup (recommended)
- Command:
  mysqldump -u root -h 127.0.0.1 --port=3306 u230210512_ims > backups\backup_before_drop.sql

2) Generate or review the current table list
- Generate (if not already created):
  mysql -u root -h 127.0.0.1 --port=3306 -N -e "SHOW TABLES IN u230210512_ims" > backups\tables_list.txt
- Review:
  type backups\tables_list.txt

3) Review the drop script targets
- Open and review [backups.drop_english_tables.sql](drop_english_tables.sql). It includes only English-named legacy/duplicate tables:
  - attendances, campus, division, employees, holidays, leaves, moas, weeklyreports, role_user, roles, users
- Core tables are commented out; only uncomment if truly unused:
  - failed_jobs, password_resets, personal_access_tokens

4) Execute the drop
- Command:
  mysql -u root -h 127.0.0.1 --port=3306 u230210512_ims < backups\drop_english_tables.sql

5) Verify the result
- Re-list tables:
  mysql -u root -h 127.0.0.1 --port=3306 -N -e "SHOW TABLES IN u230210512_ims" > backups\tables_after_drop.txt
- Review:
  type backups\tables_after_drop.txt

6) (Optional) Rollback if needed
- Restore from the backup:
  mysql -u root -h 127.0.0.1 --port=3306 u230210512_ims < backups\backup_before_drop.sql

References:
- DB settings: [.env()](../.env:12), [config.database()](../config/database.php:46)
- Models using Indonesian tables (do not drop):
  - [app.User()](../app/User.php), [app.Role()](../app/Role.php), [app.Employee()](../app/Employee.php), [app.Campus()](../app/Campus.php), [app.Division()](../app/Division.php), [app.Attendance()](../app/Attendance.php), [app.Holiday()](../app/Holiday.php), [app.WeeklyReports()](../app/WeeklyReports.php), [app.Moa()](../app/Moa.php)

Notes:
- If your MySQL has a password, append -p and enter it when prompted:
  mysql -u root -p -h 127.0.0.1 --port=3306 u230210512_ims < backups\drop_english_tables.sql
- All DROP statements in the script use IF EXISTS to avoid errors if tables are already missing.