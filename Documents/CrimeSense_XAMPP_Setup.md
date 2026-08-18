# Setting Up CrimeSense on XAMPP

## Steps

1. **Download and install XAMPP** (skip if already installed)
   Download it from the official site:
   ```
   https://www.apachefriends.org/download.html
   ```
   Choose the Windows installer, run it, and install with the default options (make sure **Apache** and **MySQL** are checked during installation).

2. **Extract the .rar file**
   Use WinRAR or 7-Zip to extract it anywhere convenient (e.g., Desktop or Downloads).

3. **Open the extracted folder**
   Navigate inside until you find the actual **CrimeSense** folder — the one that contains the PHP files directly, not just a wrapper folder.

4. **Copy the CrimeSense folder into XAMPP's htdocs**
   Paste it into:
   ```
   C:\xampp\htdocs\
   ```

5. **Start Apache and MySQL**
   Open the XAMPP Control Panel and click **Start** on both **Apache** and **MySQL**.

6. **Open the app in your browser**
   Go to:
   ```
   http://localhost/CrimeSense
   ```

7. **Import the database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Click **New** to create a new database (e.g., name it `crimesense`), then click **Create**
   - Select the new database, go to the **Import** tab
   - Click **Choose File** and browse to:
     ```
     C:\xampp\htdocs\CrimeSense\db\crimesense.sql
     ```
   - Click **Go** at the bottom to run the import
   - Once it finishes, you should see the CrimeSense tables listed under that database — that confirms the import worked

8. **Log in**
   Use the provided admin credentials:
   - **Username:** jmv
   - **Password:** 12345

## Troubleshooting

If you get a "connection failed" error after setup, check the database config file (often named `db.php` or `connection.php` inside the CrimeSense folder). Confirm the host, username, password, and database name match your XAMPP MySQL setup (default is usually user `root` with no password).
