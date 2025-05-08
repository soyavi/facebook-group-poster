
Built by https://www.blackbox.ai

---

# Facebook Group Poster

## Project Overview
Facebook Group Poster is a web application that allows users to easily post content to multiple Facebook groups. With features such as message posting, image sharing, and group management, this application aims to streamline the experience of publishing content to Facebook groups for users. 

The application integrates with the Facebook Graph API for authentication and posting functionalities, leveraging OAuth 2.0 for secure user authentication.

## Installation
To install the project, follow the steps below:

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/facebook-group-poster.git
   cd facebook-group-poster
   ```

2. **Install dependencies using Composer:**
   ```bash
   composer install
   ```

3. **Set up your Facebook App:**
   - Create a Facebook app at [Facebook Developer](https://developers.facebook.com/apps/).
   - Obtain your App ID and App Secret.
   - Update the `config.php` file with your Facebook App credentials:

   ```php
   define('FB_APP_ID', 'YOUR_APP_ID');
   define('FB_APP_SECRET', 'YOUR_APP_SECRET');
   ```

4. **Ensure proper permissions:**
   - Make sure your app has permissions for `publish_to_groups` and `groups_access_member_info`.

5. **Set up your web server:**
   - Ensure your server is running PHP 7.4 or above.
   - Access the application through the designated URL (e.g., `http://localhost:8000`).

## Usage
1. Navigate to the application in your web browser.
2. Click on the "Iniciar Sesión con Facebook" button to log in.
3. Grant the required permissions to access your groups.
4. After logging in, you will be redirected to the dashboard where you can select a group and post messages or images.

## Features
- **Publish messages in multiple groups:** Quickly share content with ease.
- **Share images:** Attach images to your posts.
- **Manage your posts:** View and administer your published content efficiently.
- **User authentication:** Secure login with Facebook's OAuth 2.0.

## Dependencies
This project uses the following dependencies listed in `composer.json`:

- **facebook/graph-sdk**: The SDK for the Facebook Graph API.
  
You can install all dependencies by running:
```bash
composer install
```

## Project Structure
```
facebook-group-poster/
├── composer.json           # Dependency management file
├── config.php              # Configuration for Facebook App and Logging
├── index.php               # Entry point for the application; login page
├── login.php               # Handles Facebook OAuth login
├── callback.php            # Handles OAuth callbacks and retrieves access tokens
├── dashboard.php           # Displays user's groups and posting form
├── post.php                # Manages content posting to Facebook groups
├── logout.php              # Handles user logout
└── vendor/                 # Directory for Composer dependencies
```

## Error Logging
Errors encountered during runtime are logged in the `logs/` directory. Ensure this directory is writable by the web server to capture logs effectively.

This README provides a comprehensive overview of how to set up, run, and understand the Facebook Group Poster project. If you encounter any issues, please refer to the error logs or check the Facebook Developer documentation for guidance.