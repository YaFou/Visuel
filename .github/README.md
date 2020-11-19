# Visuel
> A simple and powerful PHP template engine

## Summary
- [Getting started](#getting-started)
- [Features](#features)
  - [Syntax](#syntax)
    - [Print statement](#print-statement)
    - [Blocks](#blocks)
      - [Condition blocks](#condition-blocks)
      - [Foreach block](#foreach-block)
  - [Enable the cache](#enable-the-cache)
  - [Use a custom lexer](#use-a-custom-lexer)
- [License](#license)

## Getting started

1. Install the package with [Composer](https://getcomposer.org)
    ```shell script
    $ composer require yafou/visuel
    ```
1. Create the renderer
    ```php
    use YaFou\Visuel\Loader\FilesystemLoader;
   use YaFou\Visuel\Renderer;
   
   $loader = new FilesystemLoader(__DIR__.'/templates');
   $renderer = new Renderer($loader);
    ```  
1. Create the template in `templates/home.visuel.php`
    ```html
   <h1>{{ $message }}</h1>
   
   <ul>
       @foreach($posts as $post)
           <li>
               <a href="{{ path($post) }}">{{ $post->getName() }}</a>
           </li>
       @endforeach
    </ul>
    ```
1. Render the template
    ```php
    echo $renderer->render('home.visuel.php', [
       'message' => 'Hello world!',
       'posts' => $repository->getAll()
   ]);
    ```

## Features

### Syntax

#### Print statement
Print statement:
```php
{{ $variable }}
{{ trim($variable->method()) }}
```
Print statements use PHP, so all your methods and functions will be available.

#### Blocks
A block start with `@` and a name: `@if`, `@foreach`...

##### Condition blocks
```
@if(<condition>)
...
@endif
```

```html
@if($post->isPublic())
    {{ $post->getName() }}
@endif($post->isDraft() && $post->getAuthor() === $user)
    {{ $post->getName() }} (in draft)
@else
    You can't see this project
@endif
```

##### Foreach block
```html
@foreach($posts as $post)
    <a href="{{ path($post) }}">{{ $post->getName() }}</a>
@else
    No posts ;(
@endforeach
```

### Enable the cache
```php
use YaFou\Visuel\Renderer;
use YaFou\Visuel\Cache\FilesystemCache;

$cache = new FilesystemCache(__DIR__.'/cache');
$renderer = new Renderer($loader, $cache);
```

### Use a custom lexer
You can customize the default lexer used by Visuel:
```php
use YaFou\Visuel\Lexer;
use YaFou\Visuel\Token;
use YaFou\Visuel\Renderer;

$lexer = new Lexer([
    Token::PRINT_START => '{-',
    Token::PRINT_END => '-}'
]);

$renderer = new Renderer($loader, null, $lexer);
```
Now you can use your custom tokens:
```html
{- $message -}
```

---

However, you can create your custom lexer with the `LexerInterface`.
```php
use YaFou\Visuel\LexerInterface;
use YaFou\Visuel\Source;
use YaFou\Visuel\TokenStream;

class MyCustomLexer implements LexerInterface
{
    public function tokenize(Source $source) : TokenStream{
        return new TokenStream(<tokens>)
    }
}
```

## License
This project is under the [MIT license](https://github.com/YaFou/Visuel/blob/main/LICENSE).
