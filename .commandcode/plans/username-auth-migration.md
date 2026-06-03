# Plan: Username-Based Authentication with Auto-Generated Email

## Overview
Transition the application's authentication system to use `username` for login and registration, while automatically generating the `email` address as `<username>@perpustakaan.test` on the backend.

## Implementation Steps

### 1. Database Migration
- Create a new migration to add a `username` column to the `users` table.
- The column should be `string`, `unique()`, and `after('name')`.
- The existing `email` column will remain `unique()` and `notNullable()`, but will be populated automatically.

### 2. Fortify Configuration
- Update `config/fortify.php`:
  - Change `'username' => 'email'` to `'username' => 'username'`.
  - Change `'email' => 'email'` to `'email' => 'username'` (so password reset and other flows expect the username field).

### 3. User Model
- Update `app/Models/User.php`:
  - Add `'username'` to the `#[Fillable]` attribute array.

### 4. Fortify CreateNewUser Action
- Update `app/Actions/Fortify/CreateNewUser.php`:
  - Add validation for `username`: `['required', 'string', 'max:255', 'unique:users']`.
  - Modify the `User::create` call to auto-generate the email: `'email' => strtolower($input['username']) . '@perpustakaan.test'`.
  - Ensure `username` is passed to the creation array.

### 5. Frontend: Login Page
- Update `resources/js/Pages/Auth/Login.vue`:
  - Change the "Email" input field to "Username".
  - Update the `v-model` from `form.email` to `form.username`.
  - Update labels, placeholders, and validation error bindings.

### 6. Frontend: Register Page
- Update `resources/js/Pages/Auth/Register.vue`:
  - Replace the "Email" input field with a "Username" input field.
  - Update the `v-model` to `form.username`.
  - Add a helper text below the username field: "Email akan otomatis dibuat sebagai username@perpustakaan.test".
  - Remove any manual email entry.

### 7. Database Seeder
- Update `database/seeders/UserSeeder.php`:
  - Generate a `username` using `fake()->userName()` or a predictable pattern.
  - Set the `email` to `strtolower($username) . '@perpustakaan.test'`.

## Verification
- Run `php artisan migrate` to apply the new column.
- Test registration: Ensure a user can register with just name, username, and password.
- Test login: Ensure the user can log in using the newly created username.
- Check the database: Verify the `email` column was correctly populated with `<username>@perpustakaan.test`.
- Run `php artisan db:seed` to ensure the updated seeder works without unique constraint violations.