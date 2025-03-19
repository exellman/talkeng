# WordPress Courses Theme

## Requirements
To run this project, you need a local server. I used **XAMPP**, but you can use any local server environment that supports the latest version of **PHP** and **WordPress**.

## Installation Steps
1. **Set up WordPress**  
   - Install the latest version of **WordPress** on your local server.

2. **Import the Database**  
   - The database file is located in the `DB` folder:  
     **`DB/courses_db.sql`**  
   - Import it into your WordPress database via **phpMyAdmin** or any database management tool.

3. **Install the Theme**  
   - Copy the theme folder (`courses-theme`) to:  
     ```
     wp-content/themes/
     ```
   - Activate the theme from the WordPress admin panel.

4. **Install Required Plugins**  
   - Install and activate the following plugins:
     - **Timber** (for Twig templating)
     - **Advanced Custom Fields (ACF)** (for custom fields)

## Notes
- Ensure that the required plugins are installed and activated for the theme to function correctly.
- If you experience issues, check your **PHP version** and **WordPress configuration**.

---

Now, your WordPress site should be ready! 🚀  
If you have any issues, feel free to ask.  
