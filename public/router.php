<?php
require_once '../core/controller/autoload.php';

$html = new HtmlLayout();
echo $html->page_header();
?>

<div id="app"></div>

<script type="module" src="resources/js/router.js?v=4"></script>

<?php
echo $html->page_footer();
?>
