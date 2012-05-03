<?php
header('Content-Type: application/xml');
$headVariables = $config = array();
include (__DIR__.'/config.default.php');
if (file_exists(include (__DIR__.'/config.php'))) {
	include (__DIR__.'/config.php');
}
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php
	$dp = opendir(dirname(__FILE__).'/content/');
	$tplList = array();
	$tpl_name="";
	while ($fn = readdir($dp)) {
		if (preg_match('/\.php$/', $fn)) {
			$pageName = basename($fn, '.php');
			$baseFile = dirname(__FILE__).'/content/'.$pageName;
			$contentFile = $baseFile.'.php';
		} elseif (preg_match ('/\.txt$/', $fn)) {
			$pageName = basename($fn, '.txt');
			$baseFile = dirname(__FILE__).'/content/'.$pageName;
			$contentFile = $baseFile.'.txt';
		} else {
			continue;
		}
		$metaFile = $baseFile.'.meta.php';
		$siteMaps['priority']=0.5;
		$siteMaps['changefreq']='weekly';
		if (file_exists($metaFile)) {
			include($metaFile);
			$lastModified = max (filemtime($metaFile), filemtime($contentFile));
		} else {
			$lastModified = filemtime($contentFile);
		}
		$siteMaps['lastmod'] = date('c', $lastModified);
		?>
		<url>
			<loc>http://<?php echo $_SERVER['HTTP_HOST']?>/<?php echo htmlspecialchars($pageName).'.php';?></loc>
			<priority><?php echo $siteMaps['priority'];?></priority>
			<changefreq><?php echo $siteMaps['changefreq']; ?></changefreq>
			<lastmod><?php echo $siteMaps['lastmod']; ?></lastmod>
		</url>
		<?php
	}
?>
</urlset>
