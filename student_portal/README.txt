VelTech Student Grievance Portal - Ready to Upload
-------------------------------------------------
Domain: https://VeltechStudentPortal.com

Steps to deploy:
1) Upload the entire project folder (files above) into your hosting public_html (or into a subfolder).
2) Edit db_connection.php with your InfinityFree MySQL credentials.
3) Import sql/schema.sql into your hosting DB using phpMyAdmin.
4) Download PHPMailer (https://github.com/PHPMailer/PHPMailer) and copy these files into src/:
   - src/PHPMailer.php
   - src/SMTP.php
   - src/Exception.php
   (Alternatively use composer to install PHPMailer and update require paths.)
5) In signup.php replace:
   - 'YOUR_GMAIL_ADDRESS' with your Gmail address (e.g., pavaniorsu99@gmail.com)
   - 'YOUR_APP_PASSWORD' with the Gmail App Password (generate from Google Account > Security > App passwords)
   Ensure there are no spaces in the App Password string.
6) Enable HTTPS for your domain (recommended). The verification link uses:
   https://VeltechStudentPortal.com/verify_email.php?token=...
7) Change admin credentials in admin_login.php (ADMIN_USER and ADMIN_PASS).
8) Test signup -> verify email -> login -> file upload (if added) -> submit complaint -> admin view/update.

Notes:
- If Gmail SMTP is blocked by host, consider transactional service (Mailgun, SendGrid).
- Never store real passwords in version control.