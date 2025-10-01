<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Anaia's Motorcycle Rental System

A modern web application for motorcycle rentals built with Laravel and Vue.js. This system allows users to browse, rent, and manage motorcycle rentals with an intuitive user interface.

## Features

- User authentication and authorization
- Motorcycle catalog with filtering options
- Real-time availability checking
- Secure payment integration
- Rental history tracking
- Admin dashboard for fleet management
- Responsive design for mobile and desktop

## Prerequisites

- PHP >= 8.1
- Node.js >= 16.x
- Composer
- MySQL >= 8.0
- Git

## Installation

1. Clone the repository
```bash
git clone https://github.com/yourusername/anaias-rental-system.git
cd anaias-rental-system
```

2. Install PHP dependencies
```bash
composer install
```

3. Install NPM dependencies
```bash
npm install
```

4. Configure environment variables
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in `.env` file
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=anaias_rental
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run database migrations
```bash
php artisan migrate
```

7. Build frontend assets
```bash
npm run build
```

## Running the Application

1. Start the Laravel development server
```bash
php artisan serve
```

2. Start the Vite development server (for frontend development)
```bash
npm run dev
```

The application will be available at `http://localhost:8000`

## Development Workflow

- Run tests: `php artisan test`
- Watch for frontend changes: `npm run watch`
- Format code: `./vendor/bin/pint`

## Project Structure

```
anaias-rental-system/
├── app/                    # PHP application logic
├── resources/             
│   ├── js/                # Vue.js components
│   └── views/             # Blade templates
├── routes/                # Application routes
├── database/              # Database migrations and seeders
└── public/                # Public assets
```

## Technology Stack

- **Backend:** Laravel 10.x
- **Frontend:** Vue.js 3.x, Inertia.js
- **Styling:** Tailwind CSS
- **Database:** MySQL
- **Authentication:** Laravel Breeze

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## Authors

- Your Name - Initial work - [YourGithub](https://github.com/yourusername)

## Acknowledgments

- Laravel Team
- Vue.js Team
- Tailwind CSS Team
