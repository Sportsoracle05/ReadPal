# ReadPal
ReadPal is an all-in-one academic learning platform designed to help students streamline their studies, stay organized, and connect with their peers. It combines essential academic tools into a single, focused application.

# 🚀 Features
Lecture Notes: Centralized repository to access and organize study materials.

Self-Assessment Quizzes: Interactive quizzes to test knowledge and track learning progress.

Live Class Alerts: Real-time notifications and reminders for upcoming lectures.

Personal Notes: Built-in notepad for quick brainstorming and study summaries.

CGPA Calculator: Simple tool to compute and track academic performance over semesters.

Student Community: A collaborative space for students to interact, share ideas, and help one another.

# 🛠️ Prerequisites
Before deploying ReadPal, ensure you have the following installed on your system:

PHP (>= 8.1 recommended)

Composer

Node.js & NPM

MySQL or any supported relational database

Web Server (Apache, Nginx, or Laravel Artisan for local development)

# 📦 Installation & Deployment Instructions
Since dependencies (vendor and node_modules) are excluded from the repository, follow these steps to set up and deploy the application:

1. Clone the Repository
Bash
git clone https://github.com/your-username/readpal.git
cd readpal
2. Install Backend Dependencies
Install the required PHP packages using Composer:

Bash
composer install
3. Install Frontend Dependencies
Install and build the frontend assets using NPM:

Bash
npm install
npm run build
(Use npm run dev if you are deploying in a local development environment).

4. Environment Configuration
Copy the example environment file and configure your database/application settings:

Bash
cp .env.example .env
Open the .env file in your preferred text editor and update the database credentials:

Code snippet
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=readpal_db
DB_USERNAME=root
DB_PASSWORD=your_password

5. Generate Application Key
Bash
php artisan key:generate

6. Run Database Migrations & Seeders
Create the database tables and populate them with necessary initial data:

Bash
php artisan migrate --seed

7. Link Storage
Create a symbolic link from public/storage to storage/app/public to handle file uploads (like lecture notes):

Bash
php artisan storage:link

8. Serve the Application
For local deployment, start the Laravel development server:

Bash
php artisan serve
Your application will be live at [http://127.0.0.1:8000](http://127.0.0.1:8000).

# 🔒 Production Deployment Considerations
When moving ReadPal to a live production server (e.g., DigitalOcean, AWS, Heroku):

Ensure .env has APP_ENV=production and APP_DEBUG=false.

Optimize configuration and route loading using:

Bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
Set up a proper web server configuration (like Nginx) pointing to the /public directory of the project.
