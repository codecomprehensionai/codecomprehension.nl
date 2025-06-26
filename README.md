<p align="center">
  <img src="https://groeidocument.nl/cms/wp-content/uploads/2017/05/logo-uva.png" alt="UvA"/>
</p>

<h1 align="center">
  codecomprehensionai/codecomprehension
</h1>

### Installation (without Herd)

```sh
# Clone repository
git clone https://github.com/codecomprehensionai/codecomprehension && cd codecomprehension

# Install PHP if not already installed
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"

# Install dependencies
npm install
composer install

# Build dependencies
npm run build

# Copy .env.example to .env
cp .env.example .env

# Generate a new application key
php artisan key:generate

# Run migrations and seeders
php artisan migrate:fresh --seed

# Run developer services
composer run dev

# Open app
open http://127.0.0.1:8000
```

### Installation (with Herd)

```sh
# Clone repository
git clone https://github.com/codecomprehensionai/codecomprehension && cd codecomprehension

# Configure Herd
herd link codecomprehension
herd secure codecomprehension

# Install dependencies
npm install
composer install

# Build dependencies
npm run build

# Copy .env.example to .env
cp .env.example .env

# Generate a new application key
php artisan key:generate

# Run migrations and seeders
php artisan migrate:fresh --seed

# Run developer services
composer run dev

# Open app
herd open
```

### About

### Security measures 

### Application Security

This application follows key security best practices:

- **punt 1** asdf
- **punt 2** asdf
- **punt 3** asdf

These measures help protect user data and maintain a

### Energy efficiency

To minimize energy consumption, we made several conscious design decisions in this project:

- **punt 1** asdf
- **punt 2** asdf
- **punt 3** asdf

These choices help create a more efficient application, reducing energy consumption both on the server and for

### Credits 
