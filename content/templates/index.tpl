{css file="index.css"}
{js file="index.js"}

<div class="pagecontent">{$page.content|pagecontent}</div>

<div>Сейчас {$now|writeDate:'d.m'} {$now|writeTime:'H:i'}</div>
