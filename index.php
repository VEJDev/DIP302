<?php
	session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Preses Apvienība</title>
		<link rel="icon" type="image/x-icon" href="/assets/logo2.ico">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Inconsolata&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
		<style>
			body {
				margin: 0px;
				font-family: "Inter", serif;
				background-color: #ddd;
			}
			header input {
				background-color: #2A2A2A;
                border: 1px solid #666;
                color: #FFF;
                outline: none;
                padding: 12px;
                border-radius: 10px 0px 0px 10px;
                font-family: "Inter", serif;
                font-size: 1rem;
			}
			header {
				font-size: 1.3rem;
				color: #bebebe;
				border-bottom: 1px solid #fff;
				position: fixed;
				z-index: 100;
			}
			header a:link {
				transition: 0.3s;
				color: #fff;
				text-decoration: none;
			}
			
			header a:visited {
				transition: 0.3s;
				color: #fff;
				text-decoration: none;
			}

			header a:hover {
				transition: 0.3s;
				color: #fb5;
				text-decoration: none;
			}

			header a:active {
				transition: 0.3s;
				color: #fff;
				text-decoration: none;
			}
			
            footer {
                background-color: #1c1c1c;
                font-size: 0.8rem;
				padding: 0.2rem;
				color: #bbb;
                text-align: center;
				border-top: 1px solid #fff;
            }
			#content {
				padding-top: 70px;
				min-height: calc(100vh - 118px);
			}
			
			.add-btn {
				background-color: #1BE930;
				border: 1px dashed #fff;
				border-radius: 10px;
				color: #fff;
				cursor: pointer;
			}

			.card-1 {
				width: 25%;
				max-width: 400px;
				margin: 20px;
				margin-bottom: auto;
				padding: 10px;
				padding-top: 0px;
				border-radius: 10px;
				background-color: #fff;
				min-height: 500px;
				display: flex;
				flex-direction: column;
				align-items: center;
			}

			.card-2 {
				display: flex;
				gap: 10px;
				margin-bottom: 10px;
				margin-right: auto;
			}

			.card-2-img {
				width: 100px;
				height: 100px;
				object-fit: cover;
				border-radius: 10px;
			}

			.card-2-text {
				display: flex;
				flex-direction: column;
				justify-content: space-between;
				word-break: break-all;
			}

			.card-2-text h2 {
				margin: 0px;
				text-align: left;
			}

			.card-2-text p {
				margin: 0px;
			}

			#container-1 {
				display: flex;
				width: 100%;
				margin-bottom: 20px;
				justify-content: center;
			}

			.burger {
				display: none;
				flex-direction: column;
				gap: 5px;
				cursor: pointer;
				margin-right: 20px;
			}

			.burger div {
				width: 25px;
				height: 3px;
				background-color: white;
			}

			.mobile-nav {
				display: none;
				flex-direction: column;
				align-items: center;
				background-color: #333;
				position: absolute;
				top: 70px;
				left: 0;
				width: 100%;
				padding: 1rem;
				gap: 1rem;
			}

			.mobile-nav.active {
				display: flex;
			}

			.regular-nav {
				display: flex;
				justify-content: space-between;
				width: 100%;
			}

			@media only screen and (max-width: 1000px) {
				#container-1 {
					flex-direction: column;
					align-items: center;
				}
				.card-1 {
					width: 400px;
				}
				.burger {
					display: flex;
				}
				.regular-nav {
					display: none;
				}
				header {
					justify-content: space-between;
				}
			}

			@media (min-width: 1000px) {
				.mobile-nav {
					display: none !important;
				}
			}

			a:link {
				transition: 0.3s;
				color: #44f;
				text-decoration: none;
			}
			
			a:visited {
				transition: 0.3s;
				color: #44f;
				text-decoration: none;
			}

			a:hover {
				transition: 0.3s;
				color: #99f;
				text-decoration: none;
			}

			a:active {
				transition: 0.3s;
				color: #44f;
				text-decoration: none;
			}
		</style>
		<?php
			if (isset($_SESSION['message'])) {
				echo "<script>alert('" . $_SESSION['message'] . "');</script>";
				unset($_SESSION['message']);
			}
		?>
	</head>
	<body>
		<?php
			require("header.php");
		?>
		<div id="content" style="display: flex; flex-direction: column; align-items: center;">
			<div id="container-1">
				<div class="card-1">
					<h1>Jaunākaie</h1>
					<?php
						$stmt = $conn->prepare("SELECT title, created, id FROM raksts ORDER BY created DESC LIMIT 20");
						$stmt->execute();
						$stmt->bind_result($title, $created, $id);
						while ($stmt->fetch()) {
							$html = <<<HTML
								<div class="card-2">
									<img src="article_picture.php?id=$id" class="card-2-img">
									<div class="card-2-text">
										<a href="article.php?id=$id"><h2>$title</h2></a>
										<p>Izveidots: $created</p>
									</div>
								</div>
							HTML;
							echo $html;
						}
					?>
				</div>
				<div class="card-1">
					<h1>Aktuāli</h1>
					<?php
						$stmt = $conn->prepare("SELECT title, created, id FROM raksts ORDER BY last_access DESC LIMIT 20");
						$stmt->execute();
						$stmt->bind_result($title, $created, $id);
						while ($stmt->fetch()) {
							$html = <<<HTML
								<div class="card-2">
									<img src="article_picture.php?id=$id" class="card-2-img">
									<div class="card-2-text">
										<a href="article.php?id=$id"><h2>$title</h2></a>
										<p>Izveidots: $created</p>
									</div>
								</div>
							HTML;
							echo $html;
						}
					?>
				</div>
				<div class="card-1">
					<h1>Populārākie</h1>
					<?php
						$stmt = $conn->prepare("SELECT title, created, id FROM raksts ORDER BY views DESC LIMIT 20");
						$stmt->execute();
						$stmt->bind_result($title, $created, $id);
						while ($stmt->fetch()) {
							$html = <<<HTML
								<div class="card-2">
									<img src="article_picture.php?id=$id" class="card-2-img">
									<div class="card-2-text">
										<a href="article.php?id=$id"><h2>$title</h2></a>
										<p>Izveidots: $created</p>
									</div>
								</div>
							HTML;
							echo $html;
						}
					?>
				</div>
			</div>
			<?php
				$level = 0;
				if (!empty($_SESSION['user_id'])) {
					$stmt = $conn->prepare("SELECT level FROM profils WHERE id = ?");
					$stmt->bind_param("s", $_SESSION['user_id']);
					$stmt->execute();
					$stmt->store_result();
					if ($stmt->num_rows > 0) {
						$stmt->bind_result($level);
						$stmt->fetch();
					}
				}
				if ($level == 2) {
					echo("<button class=\"add-btn\" onclick=\"window.location.href='article2.php?new=true';\" style=\"height: 100px; width: 400px; margin-bottom: 20px;\">+ Pievienot Rakstu</button>");
				}
			?>
		</div>
		<footer>
			<script>document.write("<p>© 2024-" + new Date().getFullYear() + " Preses Apvienība</p>")</script>
		</footer>
		<script>
			const burger = document.getElementById('burger');
			const mobileNav = document.getElementById('mobile-nav');

			burger.addEventListener('click', () => {
				mobileNav.classList.toggle('active');
			});
		</script>
	</body>
</html>