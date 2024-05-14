# Hengram - Your simple social media

Hengram is a simple instagram clone where in the website a user can see other user's posts by following the user. User can also register a private account so other users that are not following could not see the posts.

## 💻 Tech Stacks

Here are the technology used to create this project

1. Laravel 11
2. Vue.ts 3

## 🔐 Setup

First you need to clone this repository

### Backend Setup

1. Run `composer install --ignore-platform-reqs`
2. Copy `.env.example` to `.env`
3. Add `FILESYSTEM_DISK=public` key to `.env`
4. Run `php artisan key:generate`
5. Set your database credentials in `.env`
6. Run `php artisan migrate`
7. Run `php artisan db:seed`
8. Run `php artisan serve`
9. Access the API at `http://localhost:8000`

### Frontend Setup

1. Run `npm install`
2. Run `npm run dev`
3. Access the website at provided network
