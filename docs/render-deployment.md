# Render Deployment

This project is ready to deploy as a free Render demo using Docker and Render Postgres.

## 1. Commit and Push

Push the repository to GitHub. Render deploys from a connected GitHub repository.

## 2. Create an App Key

Generate a Laravel app key locally:

```bash
php artisan key:generate --show
```

Copy the full output, including the `base64:` prefix.

## 3. Deploy the Blueprint

In Render:

1. Choose **New +**.
2. Choose **Blueprint**.
3. Connect this GitHub repository.
4. Render will read `render.yaml` and create:
   - A Docker web service.
   - A Postgres database.

Render will ask for these synced values:

| Key | Value |
| --- | --- |
| `APP_KEY` | The key generated in step 2 |
| `APP_URL` | The public Render URL, for example `https://traffic-reports.onrender.com` |

If Render gives the service a different URL, use that URL for `APP_URL`.

## 4. What Happens on Deploy

The Docker image:

1. Installs PHP production dependencies.
2. Builds Vite assets.
3. Starts Apache with Laravel's `public` directory as the document root.
4. Runs `php artisan migrate --force` on boot.
5. Caches config and views.

## Demo Limits

This is intended for client testing, not production.

- Free services may sleep after inactivity.
- Uploaded report media is stored on the service filesystem and may be lost when the free service restarts or redeploys.
- Use S3-compatible storage later if the client needs persistent media uploads.
- Mail is set to `log`, so outgoing emails are not actually sent in the demo.
