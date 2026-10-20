PRACTICAL 12 - EVENT MANAGEMENT CRUD

1. Copy the practical12 folder to:
   C:\xampp\htdocs\practical12\

2. Start Apache and MySQL from XAMPP Control Panel.

3. Open http://localhost/phpmyadmin

4. Click SQL and run the contents of database.sql.
   If you already created practical12 and the events table, do not run the INSERT rows again,
   or you will get duplicate sample records.

5. Ensure the uploads folder exists. It is included in this project.

6. Open:
   http://localhost/practical12/index.php

FEATURES
- Add, view, update, delete events
- Poster upload accepts JPG, PNG, WEBP
- Maximum poster size is 2 MB
- MIME type and image validation on the server
- Random poster filenames
- Keeps existing poster if an event is updated without selecting a new one
- Search events, filter by category and Open/Closed status
- Delete confirmation and success/failure messages
- Upload folder directory listing disabled and PHP files denied
- Optional event audit log (table is included in database.sql)

FILES
index.php       Main CRUD page
db.php          MySQL connection
style.css       Page design
database.sql    Database and sample records
uploads/.htaccess Upload folder protection

If MySQL root has a password, change the connection in db.php.
