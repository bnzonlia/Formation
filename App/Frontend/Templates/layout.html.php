<?php
/**
 * @var $user    \OCFram\User
 * @var $content string Contenu de la page à afficher
 *                      **
 * @var $User    \OCFram\User Session utilisateur
 * @var $content string Contenu de la page générée
 * @var $menu_a array[] Panneau menu personnalisé en fonction de l'authentification
 * @var $flash string Affichage du flash (message à l'utilisateur)
 * @var $h1_link_a string[] Liste des liens à afficher
 */


?>
<!DOCTYPE html>
<html>
	<head>
		<title>
			<?= isset( $title ) ? $title : 'Mon super site' ?>
		</title>
		
		<meta http-equiv="Content-Type" content="text/html; charset= UTF-8" />
		
		<link rel="stylesheet" href="/css/Envision.css" type="text/css" />
	</head>
	
	<body>
		<div id="wrap">
			<header>
				<?php foreach ($h1_link_a as $element_a): ?>
					<h1><a href="<?= $element_a['url']?>"><?= $element_a['label'] ?></a></h1>
				<?php endforeach; ?>
				<p>Comment ça, il n'y a presque rien ?</p>
			</header>
			
			<nav>
				<ul>
					<?php foreach ($menu_a as $element_a): ?>
						<li><a href="<?= $element_a['url'] ?>"><?= $element_a['label']?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
			
			<div id="content-wrap">
				<section id="main">
					<?php if ( $User->hasFlash() ) {
						echo '<p style="text-align: center;">', $User->getFlash(), '</p>';
					} ?>
					
					<?= $content ?>
				</section>
			</div>
			
			<footer></footer>
		</div>
	</body>
</html>