<?php

require(__DIR__."/lib/SplClassLoader.php");

$loader = new SplClassLoader('ListGenerator', __DIR__.'/lib');
$loader->register();

$html = '
<h2 class="sg-index" id="sg-item-1">Second level 1 a</h2>
<h3 class="sg-index">Third level 1 b</h3>
<h3 class="sg-index">Third level 1 c</h3>
<h4 class="sg-index">Fourth level 1 d</h4>
<h2 class="sg-index" id="sg-item-2">Second level 2 a</h2>
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
