# Mollie Webhook Multiplexer

## What is this?

Unfortunately, Mollie can only handle one Webhook endpoint per payment transaction. This application tries to circumvent this limitation by providing a lean multiplexer, that can forward the same webhook to different applications.

You can specify the endpoints in the `endpoints.neon` file. See `endpoints.neon.dist` for an explanation of the format.

## Requirements

* Some recent version of PHP (7.2 or newer should do)
* A web server that allows URL rewrites (a `.htaccess` file for Apache 2 is included)

## Install the Application locally

* `git clone https://github.com/fjbender/mollie-webhook-multiplexer`
* `cd mollie-webhook-multiplexer`
* `composer install`
* `cp endoints.neon.dist endpoints.neon`
* Edit `endpoints.neon` to your needs
* Either use docker `docker-compose up` or `php -S localhost:8080 -t public/` to serve on http://localhost:8080 for testing

You can test the application by posting a `x-www-form-urlencoded` POST request with `id=tr_12345` as payload to `http://localhost:8080/webhooks`.

When deploying in production, have the `public/` directory served from the webserver.

## Shop configuration

If you're using any of the Mollie default plugins, chances are that they'll set the webhook URL automatically. You'll have to hack the generation of the `webhookUrl` parameter, for example in Oxid 6:

```php
    # In vendor/mollie/mollie-oxid/Application/Model/Request/Base.php
    /**
     * Return the Mollie webhook url
     *
     * @return string
     */
    protected function getWebhookUrl()
    {
        // Hack to override webhookUrl
        $url = Registry::getConfig()->getCurrentShopUrl().'index.php?cl=mollieWebhook';
        $url = str_replace('myoxidshop.example.com', 'mollie-multiplexer.com', $url);
        return $url;
        // End Hack
    }
```

## Limitations

* If there are any dynamic IDs in the webhook URL (e.g. `https://myshop.example/mollie/webhook/05591cfb-5f8f-4231-929a-09525e00104d`, this won't work. I aim to add this feature next.
* The application currently does not do any queuing of webhooks. If one forwarding endpoint breaks, the webhook is discarded for the endpoint.
* Fully synchronous: The endpoints are processed one after another. It might be that they take too long to answer, and Mollie then abandones the webhook and tries again later. This might lead to congestion. Both these point might be fixed by adding a RabbitMQ or something like that in the mix.

## License

[BSD (Berkeley Software Distribution) License](https://opensource.org/licenses/bsd-license.php).

Copyright (c) 2021, Florian Bender