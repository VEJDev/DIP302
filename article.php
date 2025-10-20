<?php
	session_start();
	// Katrs lietotājs ik pēc 24 stundām aplūkojot rakstu audzē raksta popularitāti un aktualitāti
	require("db.php");

	if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['id'])) {
		if(!isset($_COOKIE[$_GET['id']])) {
			$stmt = $conn->prepare("UPDATE raksts SET last_access=NOW(), views=views+1 WHERE id=?");
			$stmt->bind_param("i", $_GET['id']);
			$stmt->execute();
			setcookie($_GET['id'], "true", time() + 86400, "/");
		}
	}
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
			
			.article-section {
				width: min(1000px, calc(100% - 60px));
				margin: 2rem auto;
				background-color: #fff;
				padding: 1rem;
				border-radius: 8px;
				box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
			}

			.article-section h2 {
				text-align: center;
				margin-bottom: 1rem;
			}

			.btn-1 {
				font-family: "Inter", serif;
				background-color: #fff;
				box-shadow: 0 0px 6px rgba(0, 0, 0, 0.3);
				border: none;
				border-radius: 10px;
				color: #373737;
				cursor: pointer;
			}
			.btn-2 {
				font-family: "Inter", serif;
				background-color: #eee;
				box-shadow: 0 0px 6px rgba(0, 0, 0, 0.3);
				border: none;
				border-radius: 10px;
				color: #373737;
				cursor: pointer;
			}
			.card-3 {
				word-wrap: break-word;
				word-break: break-all;
				text-align-last: left;
				display: flex;
			}
			.card-3 h2 {
				text-align: left;
			}
			.card-3-img {
				width: min(700px, calc(100% - 250px));
				aspect-ratio: 16 / 9;
				object-fit: cover;
			}
			.card-3-text {
				margin-left: 10px;
			}
			
		</style>
	</head>
	<body>
		<?php
			require("header.php");
		?>
		<div id="content" style="display: flex; flex-direction: column; align-items: center;">
			<div class="article-section">
				<?php

					if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['id'])) {
						
						$stmt = $conn->prepare("SELECT title, text, created FROM raksts WHERE id = ?");
						$stmt->bind_param("i", $_GET['id']);
						$stmt->execute();
						$stmt->store_result();
				
						if ($stmt->num_rows > 0) {
							$id = $_GET['id'];
							$picture = "article_picture.php?id=" . $id;
							$stmt->bind_result($title, $text, $created);
							$stmt->fetch();

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
							$edit = "";
							if ($level == 2) {
								$edit = "<a href=\"article2.php?id=" . $id . "\">Rediģēt Rakstu</a>";
							}

							$html = <<<html
								<div style="display: flex; justify-content: center; gap: 20px; width: 100%;">
									<button class="btn-1" onclick="window.location.href='article.php?id=$id';" style="height: 30px; width: 80px; margin-bottom: 20px;">Raksts</button>
									<button class="btn-2" onclick="window.location.href='comments.php?id=$id';" style="height: 30px; width: 80px; margin-bottom: 20px;">Komentāri</button>
								</div>
								<div class="card-3">
									<img class="card-3-img" src="$picture">
									<div class="card-3-text">
										<h2>$title</h2>
										<p>Izveidots: $created</p>
										$edit
									</div>
								</div>
								<p>$text</p>
							html;
							echo($html);
						} else {
							echo("<h3>Raksts netika atrasts</h3>");
						}
					} else {
						echo("<h3>Raksts netika atrasts</h3>");
					}
				?>
			</div>
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

			document.querySelectorAll('.qa-item .question').forEach(question => {
				question.addEventListener('click', () => {
					const parent = question.parentElement;
					parent.classList.toggle('active');
				});
			});
		</script>
	</body>
</html>