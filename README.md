# InterviewTask

### Project setup steps:

- Go to project root directory from terminal
- Copy and paste `.env.example` into `.env` file in root directory
  ```shell
  cp .env.example .env
  ```
- Create a database with name `intervew_task`
- Do necessary change in `.env` file (optional)
- Run following commands:
  ```shell
  composer install
  php artisan key:generate
  php artisan migrate:fresh
  npm && npm run dev
  ```
- If you need test data run following `(must be in staging server)`:
  ```shell
  php artisan db:seed
  ```
- Set third party API urls into the .env file.
- Call this [API](http://intervew-task.test/api/data?limit=2) from postman to get data
