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

### Installation notes

The project uses a Canvas dev environment, which is required to run the project, due to the use of the canvas LTI callback information to identify the user and assignment details. In order to use the project, you must have a custom canvas domain, set up with JWT details for authentication. Since our environment will be deleted, and we do not have permissions to add new users, we will make videos of the full project in action to display our features.

### About

This repository contains the source code for CodeComprehension, a project from the University of Amsterdam (UvA). The goal of this project is to provide teachers with an easy and accessible environment for creating and grading homework assignments for code comprehension.
Teachers can create assignments, like they always do. However, with CodeComprehension, a teacher can get assisted with this with the help of AI. When the teacher creates a new question, they only need to give it a programming language, a question type and a difficulty level. Then, the teacher can create a question prompt. The AI then generates a response, which now also contains the question, the model answer and a maximum score.

The project was divided into three main subprojects: The front-end, backend and LLM.

**Front-end**

The front-end utilizes Livewire, which lets us build interactive components in PHP without needing to write JavaScript. We've chosen Livewire, since it allows developers to build rich, reactive interfaces using only PHP, which aligns perfectly with Laravel. This removes the need to switch contexts between PHP and JavaScript, reducing development complexity and cognitive load.<br>
Our front-end is fully integrated with Laravel, meaning everything stays in the same language and workflow. On top of that, we’re using Filament, which is built on Livewire and helps us quickly create clean admin panels, forms, and dashboards without having to worry too much about styling or front-end logic. Filament drastically reduces the boilerplate needed to create common administrative interfaces. It improves development speed and enforces good UI/UX patterns out-of-the-box.<br>
We also use TailwindCSS for our CSS, since Tailwind promotes utility-first styling, which allows developers to write CSS directly in markup. This speeds up the design process, helps enforce visual consistency, and reduces context switching.

**Backend**

For the backend, we have decided to use Laravel. Laravel is a PHP framework that helps you build web applications quickly and cleanly using modern tools and structure. It handles things like routing, authentication, database access, and more. This gave us the ability to focus on writing the actual features of our app. The Laravel backend talks to the front-end using Livewire.<br>
We use a MySQL database to store information about users, assignments, questions and submissions. We use MySQL because it is a reliable, mature relational database that integrates seamlessly with Laravel through Eloquent ORM. It's well-supported, performant for our use case, and widely understood by developers.<br>
We use FastAPI, from Python, to talk with the LLM api. FastAPI is a high-performance Python web framework ideal for serving machine learning models or APIs. It supports asynchronous programming, is easy to use, and integrates well with Python’s AI and machine learning tooling ecosystem.<br>
Because the LLM response takes a while, we use Laravel jobs to send requests to it.

**LLM**

At the core of our LLM framework is the Agent Orchestrator. When a prompt comes in, for example a request to generate a question, it is first processed by the Agent Orchestrator. The orchestrator determines which subtask is required and sends targeted prompts to specialized agents, such as the Test Agent, the Question Agent, and the Verification Agent. We use an agent-based architecture to decompose responsibilities into specialized agents leads to modularity, easier testing, and better fault isolation. Each agent can be fine-tuned for a narrow task, improving both accuracy and performance.<br>
These agents operate within a shared context, allowing them to communicate with each other. A shared context ensures that all agents have access to relevant history and metadata. This avoids redundant processing, improves coherence, and helps maintain state across multi-step reasoning.<br>
This prevents duplication of work and significantly reduces errors. The generated partial responses are merged, and the orchestrator ultimately sends the composed question or answer back to the user. For design decisions, refer to the LLM repository.

The llm container was developed in a separate repository and is visible at

```sh
https://github.com/codecomprehensionai/llm
```

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

- **Efficient Data Handling:** We use eager loading and caching to reduce unnecessary database queries and minimize server workload.
- **LLM Reduction:** Prompts sent to language models are optimized to use fewer tokens, reducing compute time and API usage.
- **Optimized Development Workflow:** Only essential services are run during development, and Docker is used for efficient resource allocation.
- **Less Redirects** The authentication and callback flows are streamlined to minimize HTTP redirects, reducing network overhead.

These choices help create a more efficient application, reducing energy consumption both on the server and for users.

### Credits 
- **Unseen Work**
    - **Ziaad:** Rate limiter / Optimization
    - **Tim:** Initial front-end / LLM integration
    - **Jimme:** Initial front-end / LLM integration
    - **Sven:** Inertia / LLM integration / Laravel Documentatie
    - **Daniel:** Authentication / Front-end
    - **Thijmen:** Front-end / Assigning task
    - **Lars:** Laravel Documentatie / Inertia 
    - **Mathieu:** Database & Models / Front-end teacher / Improved question workflow
    - **Stein:** Canvas grading API Peerwork / Code reviews
    - **Stijn:** Database design and model / Front-end submission result
    - **Luca:** Scrum master / Setup Cloudfare and Dokploy
    - **Reinout:** Design LLM 

Alot of the coding was done in pairs/groups.

### Sprint tracking
Sprint tracking was done on a 2-hour basis while meeting up and collaborating at the UvA, while Discord was used for tracking and communication during remote work. Especially during the 2 weeks of strikes at the NS, discord calls were used for sprints and standups to communicate plannings, update each other on progress and staying in touch.