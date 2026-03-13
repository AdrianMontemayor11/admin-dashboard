 Admin Dashboard (PHP + Bootstrap 5) — XAMPP Ready

This is a **professional, clean Admin Dashboard** homepage for your system (the page the Admin sees after logging in).
It uses **PHP (PDO)** + **Bootstrap 5** and runs on **XAMPP** with MySQL on port **3306**.

## ✅ What's Included
- `admin_dashboard.php` (the main dashboard page)
- Bootstrap 5 + Bootstrap Icons (CDN)
- Sidebar layout + topbar + responsive cards
- PHP `SELECT COUNT()` queries for:
  - Total Students
  - Total Teachers
  - Total Absences Today
  - Total Absences (All Time)
- Simple session guard (requires login session)
- Database config file (`config/db.php`)
- SQL schema + sample data (`database.sql`)
- Example login page (`login.php`) and logout (`logout.php`) for testing

---

## 🚀 Setup (XAMPP)
1. Start **Apache** and **MySQL** in XAMPP.
2. Open phpMyAdmin and create a database, e.g. `school_db`.
3. Import `database.sql` into that database.
4. Copy the whole folder into:
   - `C:\xampp\htdocs\admin-dashboard-system\`
5. Edit DB settings in:
   - `config/db.php`

Default settings are:
- host: `127.0.0.1`
- port: `3306`
- user: `root`
- password: *(empty by default on XAMPP)*
- dbname: `school_db`

---

## 🔐 Login (Demo)
Open:
- `http://localhost/admin-dashboard-system/login.php`

Demo credentials:
- Username: `admin`
- Password: `admin123`

After login, it redirects to:
- `admin_dashboard.php`

---

## 🗃️ Tables Used
- `students`
- `teachers`
- `absences` (uses `absence_date` DATE column)

The dashboard counts absences **today** using:
```sql
SELECT COUNT(*) FROM absences WHERE absence_date = CURDATE()
```

---

## 🧩 Customize
- Add more stats by copying a card and adding a new COUNT query.
- Update the sidebar links to match your system modules.

Enjoy!
