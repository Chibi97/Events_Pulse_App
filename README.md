# Events Pulse App

## Setup

Before you start docker containers, please create `.env` file and add it to the root of the directory. File should include:
```
DB_ROOT_PASSWORD=rootpassword
DB_HOST=db
DB_NAME=events_pulse_db
DB_USER=user
DB_PASSWORD=password
COOKIE_VALIDATION_SECRET=XsMHed9wIDkd3FaxYj1ZDbIkYt8S3lLV
API_URL=http://localhost:8000
API_KEY=TP1VYLNzIy8HWcggqVtIvfjvToO6zEhLNYunnxjdBnCJEKQgYnvPgyfguQZEnk3H
```

Then, create another `.env` file and place it in the `/frontend` directory. File should include:
```
VITE_API_URL=http://localhost:8080
VITE_API_KEY=TP1VYLNzIy8HWcggqVtIvfjvToO6zEhLNYunnxjdBnCJEKQgYnvPgyfguQZEnk3H
VITE_BASE_IMAGE_URL=http://localhost:9001
```

Your directory structure should look like this:
```
repo-root/
├── backend/
├── frontend/
│   ├── .env
├── .env
├── docker-compose.yml
```

Now, you can run
```bash
docker-compose up -d
```
command and both the API (in Yii2) and Vue web application should start. Additionally, `cron`, `assets` and `db` container will also start.

- The `backend` container servers Yii2 API at `http://localhost:8000`
  - It ensures `composer install`, migrations, seeders are run. It also sets a background job for queue to listen.
  - All commands specified will wait for database port to be visible, indicating that `db` container is up and running
- The `frontend` container serves Vue web application via Vite build tool and Node v20.18.1
  -  The client app will run on `http://localhost:8080`
  - The `npm run dev` command creates dependencies before the app builds
- The `assets` container servers images on `http://localhost:9001` via nginx
  - It servers already existing images located in `/backend/assets/images`
- The `cron` container installs `cron` and ensures jobs can be executed smoothly via crontab
- The `db` container ensures that database is up and running, and it uses MySQL v8.0

>  Now you are ready to test out the app and API via http://localhost:8080/


You can run backend test cases by running this command directly in the backend container
```
./vendor/bin/phpunit --testdox
```

#### I also provided postman collection at the root of the project so that you can explore the API on its own.
> When you send Postman or curl request, make sure to include `Authentication` header with API_KEY (that you can copy from /.env file).


> Besides `Authentication`, only `Content-Type` header is allowed by my `ApiCorsFilter`. The filter only allows `http://localhost:8080` to query the API.

## Database schema

I decided to use MySQL because it is well-established, reliable, and widely used relational database management system. Yii2 has great support for MySQL, making it easy to integrate with my application, resulting in optimal database operations.

I made the design simple enough and open for extension. I could see that in the future, User can be introduced and each user would have a separate cart. By having `carts` table, I made sure that multiple carts can exist in our system. Later, `user_id` column could be added to uniquely identify each User.

Events can be added to the cart which means that either a new cart is created, or existing cart is used. When adding events to the cart, I insert them to `cart_items` table that represents many-to-many relationship.

I wanted to implement a simple design yet affective DB design that would depend on data integrity. Meaning, if `current_price` is regularily updated via Yii2 jobs that crontab schedules, we are good to go.
To improve the mass updates of `events` table that happen every midnight, I made sure to implement batch updates and focus on just Basic and Vip events that are 15 days ahead (because only their prices are likely to change), or yesterday (so that we can set event's `is_for_sale` to `false` and status to 'Past' - for future analytics).
For Exclusive events, I only fetch the expired ones, since their price should increase daily only after the event's date is passed. Again, batch update is used.
The Basic and Vip events' handling is regulated by `UpdateNormalEventsJob`, and Exclusive events' are handled by `UpdateExclusiveEventsJob`.
They are both scheduleable by command `php yii scheduler/run-daily-jobs`, and cron tab is supposed to run and proccess (`php yii queue/run`) them every day after midnight.
Success of queued jobs will affect our data integrity (or `current_price`), which can definitely be improved in the future.


## Good-to-know and possible future database improvements:
- Events use enum types for `event_type` (Basic, Vip or Exclusive) and `status` (Past, Upcoming, CollectorsItem) columns because we don't need additional tables just to conform to normalization forms. This way, it is easier to retrieve event's status or type, and we have 2 less entities to keep track of.
- The `is_for_sale` and `status` columns aren't much used in the code but they are supposed to be updated by scheduled jobs, as well as the `current_price`. Later, when we make sure that data is consitent and jobs aren't failing often, we can integrate those two. For now, the event's expiration solely depends on the `date` of the event.
- The `delivery_price` could also be used to later to apply additional fees based on user's location or number of items in the cart. For now, it's hardcoded to `0`.
- The `total` is meant to keep the final price for payment, adding up `subtotal` and `delivery_price`. Since the `delivery_price` is always 0, total will always be the same as `subtotal` in current implementation.
- The `price` field is meant to keep the current_price of the event at the time of adding. That can be improved in the future so that `price` and `current_price` are regularily synced when the `current_price` is changed by the scheduled job.


## General improvements
- First of all, we need to make sure that jobs run smoothly and that data is consistent after each night (since my cron runs jobs daily, after midnight).
  - Also, if the jobs are be running concurrently (and it takes a long time to process), we can consider adding some concurrency management to avoid race conditions (like locking mechanisms).
- We could also benefit from caching mechanisms like Redis. Since events' prices change no less than a day, we can keep them in cache and only refresh the data daily.
- Logging could be improved with Sentry or some other tool
- As mentioned previously, Users can be introduced, along with JWT token authorization and sign up/sign in flows. Currently, API is only protected with one-and-only API key, but JWT would provide much flexibility.
- We could implement roles like `user` and `admin` and modify the API so that admins can have more control. I already implemented CRUD for Event resource, so later admins could have more control over the data.
- Some payment integration could be integrated so that we can process the payment and create an order.
- On the frontend, we should create pagination or fetch new data when user scrolls down, because our API already has implemented pagination.
- We need to handle edge cases that currently exist. For example, when tickets are added to the cart, and the date of the event passes, our tickets would still be in the cart. When checking out (placing an order), we could check if those prices and events are still valid and clean out the cart.
 - The `current_price` should be consistent as job is supposed to update it daily, but the prices of the cart items are not. Ideally, we would need sync up the `price` column of `cart_items` with event's `current_price` regularily. Another solution would be to delete all cart_items for some event in the event that is either expired or has `current_price` changed

