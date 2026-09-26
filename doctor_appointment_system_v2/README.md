# MediBook – Doctor Appointment System (colourful edition)
PHP + MySQL (PDO) + HTML/CSS/JS. No frameworks.

## Setup (XAMPP)
1. Start Apache + MySQL. 2. Copy this folder to `htdocs/doctor_appointment`.
3. phpMyAdmin → Import `database/database.sql`. 4. If MySQL has a password, edit `includes/config.php`.
5. Open http://localhost/doctor_appointment/

Demo logins (password: `password`): admin@example.com · amit@example.com (doctor) · rahul@example.com (patient)

## Folder structure
- `index.php` – redirects to login or dashboard
- `login.php`, `register.php`, `logout.php` – authentication
- `dashboard.php` – role-based colourful stats
- `doctors.php` → `book_appointment.php` → `my_appointments.php` – patient flow
- `doctor_appointments.php` – doctor approves/rejects/completes
- `admin_doctors.php`, `admin_appointments.php` – admin tools
- `includes/` – `config.php` (DB + helpers), `header.php` (navbar), `footer.php`
- `assets/css/style.css` – all styling (colours at the top in `:root`)
- `assets/js/script.js` – all interactivity
- `database/database.sql` – tables + demo data
