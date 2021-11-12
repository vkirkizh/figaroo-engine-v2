{html}
{html_head}
{/html_head}
{html_body}
{start_javascript}
{config_load file='main.cfg'}

<div id="layout">

<div id="header">
<h1>Панель управления сайтом</h1>
</div>

<div id="breadcrumbs">{breadcrumbs}</div>

<div id="wrapper">

<div id="sidebar">
<div class="widget menu">
	<ul>
		<li><a href="{$URL}">На сайт</a></li>
		<li><a href="{$URL}admin/">Панель управления</a></li>
	</ul>
</div>
</div>

<div id="content">
{include file=$PAGE_TEMPLATE}
</div>

</div>

<div id="empty"></div>
</div>

<div id="footer">
Figaroo Engine v.2.2
</div>

{insert_javascript}

{/html_body}
{/html}
