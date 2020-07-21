# Laravel renderless components

Blade components are great, aren't they? The one thing I was still missing from them is a way to create [renderless components](https://adamwathan.me/renderless-components-in-vuejs). Blade renders top-down, which means you can't lazy-evaluate child content. Imagine being able to define and use a component like this:

```html
<ul class="space-y-2">
  @foreach ($items as $item)
  <li class="text-gray-900">{{ $render($item, $loop->count) }}</li>
  @endforeach
</ul>
```

```html
<x-list :items="$items">
  {{ $item }} (of {{ $count }})
</x-list>
```

So, I built a way to do it. You need to define your PHP component to look like this:

```php
namespace App\View\Components;

use RenderlessComponents\RenderlessComponent;

class List extends RenderlessComponent
{
    public array $items = [];

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function view(): string
    {
        return 'components.tailwind-list';
    }

    public function viewParams(): array
    {
        // think of this as the array of params
        // you usually give as the second argument
        // of the view() function
        return [
            'items' => $this->items,
        ];
    }

    public function renderParams(): string
    {
        // think of this as the list of arguments
        // you give to the render function
        return '$params, $count';
    }
}
```

## Usage

Add the library to your project:

```
composer require assertchris/laravel-renderless-components
```

It automatically registers itself, so the only thing left is to extend `RenderlessComponents\RenderlessComponent` instead of `Illuminate\View\Component` and implement the abstract methods.

## Caveats

- I've changed how the lazy-evaluated content is cached, so that this library works alongside `facade/ignition`. On local (or on any other env, if the file doesn't already exist); the lazy-evaluated content is cached in the `storage/framework/views/components` folder. If your view isn't updating, you might be in an environment other than `local`. You can change the environment or remove the cache files in that folder.
