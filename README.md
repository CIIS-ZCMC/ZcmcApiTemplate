# ZCMC API Template

This template provides a minimal setup for the ZCMC API with built-in Single Sign-On (SSO) capabilities integrated with the ZCMC User Management Information System.

## Features

-   **Single Sign-On (SSO):** Seamless authentication across multiple applications.
-   **User Management:** Efficient handling of user data and roles within the system.
-   **Fast Refresh:** Development with instant updates using Vite.
-   **ESLint Configuration:** Maintains code quality and standards.

## Quick Start

To get started with the ZCMC API Template, follow these steps:

### Prerequisites

-   Ensure you have [Node.js](https://nodejs.org/) installed.
-   Install [Composer](https://getcomposer.org/) for managing PHP dependencies.
-   Ensure you have a working Laravel environment.

### Installation

1. **Clone the repository:**

    ```bash
    git clone <repository-url>
    cd zcmc-api-template
    ```

2. **Create a new repository on your Git hosting platform** (e.g., GitHub, GitLab).

3. **Remove the existing remote origin:**

    ```bash
    git remote remove origin
    ```

4. **Add your new remote origin:**

    ```bash
    git remote add origin <your-new-repo-url>
    ```

5. **Push the project to your new repository:**

    ```bash
    git push -u origin main  # or 'master' depending on your branch naming
    ```

6. **Create the `.env` file:**

    Copy the example environment file to create your own:

    ```bash
    cp .env.example .env
    ```

7. **Update your `.env` file:**

    Change the necessary information in your `.env` file, such as database credentials, application URL, etc.

8. **Run the migrations to create the database:**

    ```bash
    php artisan migrate
    ```

9. **Install PHP dependencies:**

    ```bash
    composer install
    ```

10. **Start the local development server:**

    ```bash
    php artisan serve
    ```

11. **Start the Vite development server (in another terminal):**

    ```bash
    npm install
    npm run dev
    ```

## API Documentation

-   Refer to the API documentation for details on available endpoints and their usage.

## Contributing

Contributions are welcome! Please follow the standard practices for making contributions.
