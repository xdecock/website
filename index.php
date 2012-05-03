<?php
/* Page Name detection */
$pageName = 'index';
if (isset($_GET['page']) && strlen($_GET['page'])) {
	$pageName=basename($_GET['page']);
}
/* Headers */
header('Content-Type: text/html; charset=utf-8');
/* Check For Page Existance */
$contentFile=dirname(__FILE__).'/content/'.$pageName;
$existsPHP = file_exists($contentFile.'.php');
$existsTxt = false;
if (!$existsPHP) {
	$existsTxt = file_exists($contentFile.'.txt');
} 
if (!$existsTxt && !$existsPHP) {
	// 404
	header('HTTP/1.1 404 Page Not Found');
	include (dirname(__FILE__).'/errors/404.php');
	die();
}
/* Grab Config */
$config = $headVariables = array();
include (__DIR__.'/config.default.php');
if (file_exists(include (__DIR__.'/config.php'))) {
	include (__DIR__.'/config.php');
}
/* Grab Metas */
$metaFile=$contentFile.'.meta.php';
if ($existsPHP) {
	$mode = 'PHP';
	$contentFile .= '.php';
} else {
	$mode = 'Markdown';
	$contentFile .= '.txt';
}
if (file_exists($metaFile)) {
	include($metaFile);
	$lastModified = max (filemtime($metaFile), filemtime($contentFile));
} else {
	$lastModified = filemtime($contentFile);
}
header('Last-Modified: '.date(DATE_RFC1123, $lastModified));
?>
<!DOCTYPE html>
<html lang="<?php echo $headVariables['lang'];?>">
	<head>
        <meta charset="utf-8">
        <meta http-equiv="content-type"  content="text/html; charset=utf-8">
    	<title><?php echo htmlentities( $headVariables['title'], ENT_QUOTES, 'UTF-8');?></title>
        <!-- CSS / JS -->
        <!--[if lt IE 9]>
			<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<link rel="stylesheet" type="text/css" href="css/<?php echo $config['stylesheet']; ?>.css" />
		
        <!-- Metas -->
        <meta name="description" content="<?php echo htmlentities( $headVariables['description'], ENT_QUOTES, 'UTF-8');?>">
        <meta name="author" content="<?php echo htmlentities( $headVariables['author'], ENT_QUOTES, 'UTF-8');?>">
		<meta name="keywords" content="<?php
			$keywords=array();
			foreach ($headVariables['keywords'] as $v) {
				$keywords[]=htmlentities( $v, ENT_QUOTES, 'UTF-8');
			}
			echo implode(', ', $keywords);
        ?>">
        <!-- Favicon, canonical, ... -->
        <link rel="canonical" href="http://<?php echo $config['website']?>/<?php echo $pageName?>">
		<link rel="icon" type="image/ico" href="/favicon.ico" />        
    </head>
    <body>
    	<?php include(dirname(__FILE__).'/includes/header.php'); ?>
        <div id="page">
    		<?php
    		if ($mode == 'PHP') {
    			include($contentFile);
    		} else {
    			$md = new Markdown_Parser();
    			echo $md->transform(file_get_contents($contentFile));
    		}
    		?>
        </div>
    	<?php include(dirname(__FILE__).'/includes/footer.php'); ?>
    </body>
</html>