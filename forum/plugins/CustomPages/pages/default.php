<?php if (!defined('APPLICATION')) exit(); ?>

<h1>This is the default page in the "Custom Pages" plugin.</h1>
<p>You can find this page on your filesystem at:</p>
<code><?php echo PATH_PLUGINS . DS . 'CustomPages' . DS . 'pages' . DS . 'default.php'; ?></code>

<p>You can copy this file, rename it, and put any content you like in it. You
will be able to access your page by using it's filename in the url. For example,
if you copied this file and renamed it "shoes.php", you could access it at:
<?php echo Url('/plugin/page/shoes', TRUE); ?></p>

<h2>How to set up a custom address for your page</h2>
<p>You can make your custom pages accessible from different urls by using
<?php Anchor('route management', 'routes'); ?>. For example, if you
wanted your "shoes.php" page to be accessible at <?php echo Url('/shoes', TRUE); ?>,
you could add a new route with the following values:</p>

<table>
	<thead>
		<tr>
			<th>Route Expression: </th>
			<td>shoes</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>Target: </th>
			<td>plugin/page/shoes</td>
		</tr>
	</tbody>
</table>

<h2>How to wrap your page in a different master view</h2>
<p>You can make your page use the "admin" master view by adding "admin" in the
url, like this: <?php echo Url('plugin/page/shoes/admin'); ?></p>