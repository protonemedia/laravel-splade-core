# [Experimental] Laravel Splade Core

A package to use Vue 3's Composition API in Laravel Blade.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/protonemedia/laravel-splade-core.svg)](https://packagist.org/packages/protonemedia/laravel-splade-core)
[![GitHub Tests Action Status](https://github.com/protonemedia/laravel-splade-core/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/protonemedia/laravel-splade-core/actions/workflows/run-tests.yml)
[![Splade Discord Server](https://dcbadge.vercel.app/api/server/qGJ4MkMQWm?style=flat&theme=default-inverted)](https://discord.gg/qGJ4MkMQWm)
[![GitHub Sponsors](https://img.shields.io/github/sponsors/pascalbaljet)](https://github.com/sponsors/pascalbaljet)

## Sponsor Splade

We sincerely value our community support in our endeavor to create and offer free Laravel packages. If you find our package beneficial and rely on it for your professional work, we kindly request your support in [sponsoring](https://github.com/sponsors/pascalbaljet) its maintenance and development. Managing issues and pull requests is a time-intensive task, and your assistance would be greatly appreciated. Thank you for considering our request! ❤️

## Why is this experimental?

Although this package comes with an extensive test suite, it's important to note that it's in an experimental stage. It's a new package, and I haven't used it in production yet. Currently, I'm using the [original Splade v1](https://splade.dev) package for my production needs. However, in 2023 Q4, I plan to integrate this new package into two new projects. It's also worth mentioning that I haven't yet confirmed full compatibility between this package and all the features of the original Splade package.

## What does this have to do with the existing Laravel Splade package?

Laravel Splade currently offers a ton of features:

- Use Blade to build Single-Paged-Applications
- 20+ interactive components
- Extensive Form and Table components
- Modals, Slideovers, Toasts, SEO, SSR, and more

While this is great and tremendously helps to build SPAs with Laravel, it also makes it harder to maintain and extend the package. This is why I decided to split Splade into multiple packages:

- Splade Core: This package. It only contains the core functionality to use Vue 3's Composition API in Blade. No pre-built components. No markup. No CSS. Just the core functionality.
- Splade Navigation: The SPA and Navigation components from the original Splade package (Modals, Slideovers, Toasts, SEO, SSR, etc.)
- Splade UI: The UI components from the original Splade package, excluding the Form and Table components.
- Splade Form: The Form components from the original Splade package.
- Splade Table: The Table components from the original Splade package.

## Requirements

- PHP 8.1
- Laravel 10
- Vue 3.3
- Vite 4.0

## Features

- Automatic installer for new projects
- Use Vue 3's Composition API in Blade templates
- Support for Blade Components and Blade Views
- Call Blade Component methods from the frontend
- Refresh Blade Components from the frontend without reloading the page
- Use Blade Props as Vue Props
- Tap into the Js/Vue ecosystem from within Blade templates

## Limitations

- Inline Blade Components are not supported (where the template is defined in the component class and not in a separate `.blade.php` file).

## Installation

You can install the package via composer:

```bash
composer require protonemedia/laravel-splade-core
```

### Automatic Installation

For new projects, you may use the `splade:core:install` Artisan command to automatically install the package:

```bash
php artisan splade:core:install
```

This will install the JavaScript packages, create a root layout and a demo component, and add the required configuration to your `app.js` and `vite.config.js` files. After running this command, you may run `npm install` to install the JavaScript dependencies and then run `npm run dev` to start Vite.

```bash
npm install
npm run dev
```

### Manual Installation

First, you should install the companion JavaScript packages:

```bash
npm install @protonemedia/laravel-splade-core @protonemedia/laravel-splade-vite
```

Splade Core automatically generates Vue components for all your Blade templates. By default, they are stored in `resources/js/splade`. You don't have to commit these files to your repository, as they are automatically generated when you run Vite. To initialize this directory, run the following command:

```bash
php artisan splade:core:initialize-directory
```

In your main Javascript file (`app.js`), you must instantiate a new Vue app and use the Splade Core plugin. Following Laravel's convention, the `app.js` file is stored in `resources/js`, so you may pass the relative `./splade` path to the plugin options:

```js
import { createApp } from 'vue/dist/vue.esm-bundler.js'
import { SpladeCorePlugin } from '@protonemedia/laravel-splade-core'

createApp()
    .use(SpladeCorePlugin, {
        components: import.meta.glob('./splade/*.vue', { eager: true }),
    })
    .mount('#app')
```

In your `vite.config.js` file, you must add the `laravel-splade-vite` plugin. Make sure it is added before the `laravel` and `vue` plugins:

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import spladeCore from '@protonemedia/laravel-splade-vite'

export default defineConfig({
    plugins: [
        spladeCore(),
        laravel({
            ...
        }),
        vue({
            ...
        }),
    ],
})
```

Lastly, in your root layout file, you must create a root element for your Vue app, and you must include a script tag for the Splade templates stack. This must be done above the `#app` element:

```blade
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>My app</title>
        @vite("resources/js/app.js")
    </head>
    <body>
        <script> @stack('splade-templates') </script>

        <div id="app">
            {{ $slot }}
        </div>
    </body>
</html>
```

## Usage with Blade Components

You may use the default `make:component` Artisan command to generate a Blade Component:

```bash
php artisan make:component MyComponent
```

In the Blade template, you may use a `<script setup>` tag to make your component reactive:

```vue
<script setup>
const message = ref('Hello Vue!')
const uppercase = computed(() => message.value.toUpperCase())
</script>

<input v-model="message" />
<p v-text="uppercase" />
```

Besides running the Vite dev server, you don't have to do anything else. The component will be automatically compiled and registered as a Vue component.

### Echo syntax

Blade and Vue use the same curly brace syntax to render variables. This means that you can't use the `{{ message }}` syntax in your template to render a Vue variable. Technically, this is because the Blade compiler comes before the Vue compiler. There are two ways to solve this:

Use the `v-html` or `v-text` directive:

```vue
<p v-text="uppercase" />
```

Or, use the `@` symbol to escape the curly braces:

```vue
<p>@{{ uppercase }}</p>
```

### Composition API imports

In the first above, we used the `ref` and `computed` functions from Vue's Composition API. Splade Core automatically imports these functions for you. Here's a list of all the functions that are automatically imported:

- `computed`
- `nextTick`
- `onMounted`
- `reactive`
- `readonly`
- `ref`
- `watch`
- `watchEffect`
- `watchPostEffect`
- `watchSyncEffect`

### Attribute Inheritance

Just like regular Blade Components, you may use the `$attributes` variable to inherit attributes passed to the component. This also works for `v-model`:

Here's the template of the `<x-form>` component:

```vue
<script setup>
const form = ref({
    framework: 'laravel',
})
</script>

<form>
    <x-select v-model="form.framework" />
</form>
```

And here's the template of the `<x-select>` component:

```blade
<select {{ $attributes }}>
    <option value="laravel">Laravel</option>
    <option value="tailwind">Tailwind</option>
    <option value="vue">Vue</option>
</select>
```

#### Inheritance on Components with a script tag

If the component you're passing attributes to has a `<script>` tag, the attributes are passed as Vue props to the root element of the component. If you want to pass the attributes to a different element, you may use the `v-bind="$attrs"` directive:

```vue
<script setup></script>

<div class="wrapper">
    <input v-bind="$attrs" />
</div>
```

Splade Core automatically detects the custom directive.

### Element Refs

You may use the `$refs` variable to access element refs. This doesn't naturally work in Vue 3's Composition API, but it was great in Vue 2, so I decided to add it back in Splade Core:

```vue
<script setup>
onMounted(() => {
    const creditcardEl = $refs.creditcard;
});
</script>

<input ref="creditcard" />
```

### Tapping into the Vue ecosystem

Using libraries and packages from the Js/Vue ecosystem is easy. Here's an example of using [Flatpickr](https://flatpickr.js.org):

```vue
<script setup>
import flatpickr from "flatpickr";

const emit = defineEmits(["update:modelValue"]);

onMounted(() => {
    let instance = flatpickr($refs.date, {
       onChange: (selectedDates, newValue) => {
            emit("update:modelValue", newValue);
        },
    });

    instance.setDate(props.modelValue);
});
</script>

<input ref="date" />
```

Note that you can use `props.modelValue` without defining it. Splade Core automatically detects the usage of `modelValue` and adds it to the `props` object.

### Calling methods on the Blade Component

If your Blade Component has a `public` method, you may call it from the template, either in the script or in the template. Splade Core detects the HTTP Middleware of the current page and applies it to subsequent requests. The only thing you have to do is add the `Vue` attribute to the method:

```php
<?php

namespace App\View\Components;

use Illuminate\View\Component;
use ProtoneMedia\SpladeCore\Attributes\Vue;

class UserProfile extends Component
{
    #[Vue]
    public function notify(string $message)
    {
        auth()->user()->notify($message);
    }

    public function render()
    {
        return view('components.user-profile');
    }
}
```

Template:

```vue
<script setup>
const message = ref('Hey there!')
</script>

<input v-model="message" placeholder="Enter a message" />
<button @click="notify(message)">Notify User</button>
<p v-if="notify.loading">Notifying user...</p>
```

Note that you can use `notify.loading` to check if the method is currently running.

> [!WARNING]
> While the original Middleware is applied to the request, you should still validate the incoming data.

#### Blade Variables

Public properties of the Blade Component are automatically passed as Vue props. You may even update them on the frontend, and when you call a Blade Component method, the value will be updated on the backend. The only thing you have to do is add the `Vue` attribute to the property:

```php
<?php

namespace App\View\Components;

use Illuminate\View\Component;
use ProtoneMedia\SpladeCore\Attributes\Vue;

class UserProfile extends Component
{
    #[Vue]
    public string $notification = 'Hey there!'

    public function notify()
    {
        auth()->user()->notify($this->notification);
    }

    public function render()
    {
        return view('components.user-profile');
    }
}
```

Template:

```vue
<script setup></script>

<input v-model="notification" placeholder="Enter a message" />
<button @click="notify">Notify User</button>
```

> [!WARNING]
> Be careful what you define as a public property. For example, if you define an Eloquent model as a public property, it will be serialized to JSON and passed to the frontend. Be sure sensitive attributes are [hidden](https://laravel.com/docs/10.x/eloquent-serialization#hiding-attributes-from-json).

#### Callbacks

Instead of calling the method from the template, you may also call it from the script. This way, you can use `then`, `catch` and `finally`:

```vue
<script setup>
function notifyWithFixedMessage() {
    notify('Hey there!')
        .then(() => alert('User notified!'))
        .catch(() => alert('Something went wrong!'))
        .finally(() => {
            //
        })
}
</script>

<button @click="notifyWithFixedMessage">Notify User</button>
```

Alternatively, you may add global callbacks to the `notify` method:

```js
<script setup>
notify.before((data) => {

})

notify.then((response, data) => {

})

notify.catch((e) => {

})
</script>

<input v-model="message" placeholder="Enter a message" />
<button @click="notify('Hey there!')">Notify User</button>
```

### Refresh Component

To make components refreshable, you must add the `Refreshable` middleware to the route:

```php
use ProtoneMedia\SpladeCore\Http\Refreshable::class;

Route::get('/login', LoginController::class)->middleware(Refreshable::class);
```

Then, you may use the `refreshComponent` method to refresh the component. This will re-render the component and re-fetch the data.

```vue
<script setup>
const message = ref('Initial message')
</script>

<input v-model="message" placeholder="Enter a message" />
<small>User last updated: {{ auth()->user()->updated_at }}</small>
<button @click="refreshComponent">Refresh</button>
```

Similar to calling Blade Methods, you can use `refreshComponent.loading` to check if the component is currently refreshing. You may also use callbacks with `refreshComponent`.

## Usage with Blade Views

If you don't want to use Blade Components, you may also use Blade Views. Currently, reloading of Blade Views is not supported.

Just like with Blade Components, you may use a `<script setup>` tag at the top of your Blade View. If you're extending a layout, make sure to place the `<script setup>` tag inside the slot:

```vue
<x-layout>
    <script setup>
        const message = ref('Hello Vue!')
    </script>

    <input v-model="message" />
</x-layout>
```

### Including Blade Views

Note that you can only use *one* script tag per Blade View. For example, if your Blade View already has a script tag, you can't include another Blade View with a script tag using the `@include` directive. If you want to do this, convert the included Blade View to a Blade Component.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email <pascal@protone.media> instead of using the issue tracker.

## Credits

- [Pascal Baljet](https://github.com/protonemedia)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
