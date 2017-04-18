<?php
/**
 * @var $user    \OCFram\User
 * @var $content string Contenu de la page à afficher
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
				<h1><a href="/">Mon super site</a></h1>
				<p>Comment ça, il n'y a presque rien ?</p>
			</header>
			
			<nav>
				<ul>
					<?php if ( $User->isAuthenticated()==false ) { ?>
						<li><a href="/">Accueil</a></li>
						<li><a href="/connexion/">Connexion</a></li>
						<li><a href="/inscription/">S'inscrire</a></li>
					<?php } ?>
					<?php if ( $User->isAuthenticated() ) { ?>
						<li><a href="/">Accueil</a></li>
						<li><a href="/admin/">Admin</a></li>
						<li><a href="/admin/news-insert.html">Ajouter une news</a></li>
						<li><a href="/admin/logout.php">Se deconnecter</a></li>
					<?php } ?>
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