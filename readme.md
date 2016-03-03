# ListGenerator

Generates unordered list from headings

## Example

```php
require(__DIR__."/lib/SplClassLoader.php");

$loader = new SplClassLoader('ListGenerator', __DIR__.'/lib');
$loader->register();

$html = '
<h2 class="sg-index">Second level 1 a <a href="#" data-target="target-0">link</a></h2>
<h3 class="sg-index">Third level 1 b</h3>
<h3 class="sg-index">Third level 1 c</h3>
<h4 class="sg-index">Fourth level 1 d</h4>
<h2 class="sg-index">Second level 2 a <a href="#" data-target="target-1">link</a></h2>
<h3 class="sg-index">Third level 2 b</h3>
<h4 class="sg-index">Fourth level 2 c</h4>
<h3 class="sg-index">Third level 2 d</h3>
<h3 class="sg-index">Third level 2 e</h3>
<h4 class="sg-index">Fourth level 2 f</h4>
<h5 class="sg-index">Fifth level 2 g</h5>
<h2 class="sg-index">Second level 3 h</h2>
';


$list = new ListGenerator\ListGenerator(new DomDocument(), $html, "//*[contains(@class, 'sg-index')]");
$list->initialize();
$output = $list->output();

echo $output;
```

### Output

```html
<ul>
  <li class="list-level-2">
    <a href="#">Second level 1 a link</a>
    <ul>
      <li class="list-level-3"><a href="#">Third level 1 b</a></li>
      <li class="list-level-3">
        <a href="#">Third level 1 c</a>
        <ul>
          <li class="list-level-4"><a href="#">Fourth level 1 d</a></li>
        </ul>
      </li>
    </ul>
  </li>
  <li class="list-level-2">
    <a href="#">Second level 2 a link</a>
    <ul>
      <li class="list-level-3">
        <a href="#">Third level 2 b</a>
        <ul>
          <li class="list-level-4"><a href="#">Fourth level 2 c</a></li>
        </ul>
      </li>
      <li class="list-level-3"><a href="#">Third level 2 d</a></li>
      <li class="list-level-3">
        <a href="#">Third level 2 e</a>
        <ul>
          <li class="list-level-4">
            <a href="#">Fourth level 2 f</a>
          <ul>
            <li class="list-level-5"><a href="#">Fifth level 2 g</a></li>
          </ul>
        </li>
      </ul>
    </li>
  </ul>
  </li>
  <li class="list-level-2"><a href="#">Second level 3 h</a></li>
</ul>
```

