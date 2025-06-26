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

This repository contains the source code for CodeComprehension, a project from the University of Amsterdam (UvA). The goal of this project is to provide teachers with an easy and accessible environment for creating and grading homework assignments for code comprehension.
Teachers can create assignments, like they always do. However, with CodeComprehension, a teacher can get assisted with this with the help of AI. When the teacher creates a new question, they only need to give it a programming language, a question type and a difficulty level. Then, the teacher can create a question prompt. The AI then generates a response, which now also contains the question, the model answer and a maximum score.

The project was divided into three main subprojects: The front-end, backend and LLM.

**Front-end**

The front-end utilizes Livewire, which lets us build interactive components in PHP without needing to write JavaScript. It’s fully integrated with Laravel, meaning everything stays in the same language and workflow. On top of that, we’re using Filament, which is built on Livewire and helps us quickly create clean admin panels, forms, and dashboards without having to worry too much about styling or front-end logic. We also use TailwindCSS, that handles all of our CSS.

**Backend**

For the backend, we have decided to use Laravel. Laravel is a PHP framework that helps you build web applications quickly and cleanly using modern tools and structure. It handles things like routing, authentication, database access, and more. This gave us the ability to focus on writing the actual features of our app. The Laravel backend talks to the front-end using Livewire. We use a MySQL database to store information about users, assignments, questions and submissions. We use FastAPI, from python, to talk with the LLM api. Because the LLM response takes a while, we use Laravel jobs to send requests to it.

**LLM**

At the core of our LLM framework is the Agent Orchestrator. When a prompt comes in, for example a request to generate a question, it is first processed by the Agent Orchestrator. The orchestrator determines which subtask is required and sends targeted prompts to specialized agents, such as the Test Agent, the Question Agent, and the Verification Agent. These agents operate within a shared context, allowing them to communicate with each other. This prevents duplication of work and significantly reduces errors. The generated partial responses are merged, and the orchestrator ultimately sends the composed question or answer back to the user.

### Security measures 

This application has the following security measures:

- **Input Validation:**  
  All incoming requests are validated using Laravel’s `$request->validate()`.

- **Authentication & Authorization:**  
  OpenID Connect authentication is implemented for secure integration with Canvas LMS [`config/auth.php`](config/auth.php).

- **CSRF Protection:**  
  CSRF protection is enabled for all web routes by default.

- **Password Hashing:**  
  User passwords are hashed using Laravel’s default hashing before storage [`OidcController`](app/Http/Controllers/OidcController.php).

- **Environment Variables:**  
  Sensitive credentials (such as database, Canvas, and API keys) are stored in the `.env` file.

- **JWT Signing & Verification:**  
  JWTs are signed and verified using ECDSA key pairs managed by the [`JwtKey`](app/Models/JwtKey.php) model and [`JwtService`](app/Services/Jwt/JwtService.php). 

These measures help protect user data and maintain a secure environment.

### Energy efficiency

To minimize energy consumption, we made several conscious design decisions in this project:

- **punt 1** asdf
- **punt 2** asdf
- **punt 3** asdf

These choices help create a more efficient application, reducing energy consumption both on the server and for

### Credits 

- **pers 1**
- **pers 2**
- **pers 3**
