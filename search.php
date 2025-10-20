<?php
	session_start();
	require 'db.php';

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

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['level']) && !empty($_POST['id']) && $level == 2) {
		$stmt2 = $conn->prepare("UPDATE profils SET level=? WHERE id=?");
		$lvl = $_POST['level'] - 1;
		$stmt2->bind_param("ii", $lvl, $_POST['id']);
		$stmt2->execute();
		$_SESSION['message'] = "Lietotājs tiesības tika izmainītas.";
		header("Location: index.php");
		exit;
	}
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete']) && !empty($_POST['id']) && $level == 2) {
		$stmt = $conn->prepare("DELETE FROM profils WHERE id = ?");
		$stmt->bind_param("i", $_POST['id']);
		$stmt->execute();
		$_SESSION['message'] = "Lietotājs tika dzēsts.";
		header("Location: index.php");
		exit;
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

			.a2:link {
				transition: 0.3s;
				color: #FF0000;
				text-decoration: none;
			}
			
			.a2:visited {
				transition: 0.3s;
				color: #FF0000;
				text-decoration: none;
			}

			.a2:hover {
				transition: 0.3s;
				color: #FF4444;
				text-decoration: none;
			}

			.a2:active {
				transition: 0.3s;
				color: #FF0000;
				text-decoration: none;
			}
			
			.search-section {
				width: min(800px, calc(100% - 60px));
				margin: 2rem auto;
				background-color: #fff;
				padding: 1rem;
				border-radius: 8px;
				box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
			}

			.search-section h2 {
				text-align: center;
				margin-bottom: 1rem;
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
			
			.card-2-text button {
				background-color: #007bff;
				color: white;
				padding: 0.8rem;
				border: none;
				border-radius: 4px;
				font-size: 1rem;
				cursor: pointer;
				transition: background-color 0.3s;
				width: 200px;
			}

			.card-2-text button:hover {
				background-color: #0056b3;
			}

			select {
				width: 100%;
				padding: 10px;
				font-size: 1rem;
				border: 1px solid #ccc;
				border-radius: 5px;
				outline: none;
				transition: border-color 0.3s;
				cursor: pointer;
			}

			select:focus {
				border-color: #007bff;
			}
			.permission-form {
				display:flex;
				gap: 10px;
			}
			
		</style>
	</head>
	<body>
		<?php
			require("header.php");
		?>
		<div id="content" style="display: flex; flex-direction: column; align-items: center;">
			<div class="search-section">
				<h2>Meklēšanas rezultāti</h2>
				<?php
					if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['text'])) {
						if (str_starts_with($_GET['text'], 'lietotājs:')) {
							// Check permissions
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
							if ($level != 2) {
								$_SESSION['message'] = "Jums nav piekļuves šai funkcijai.";
								header("Location: index.php");
								exit;
							}
							$text = "%" . substr($_GET['text'], 12) . "%";
							$stmt = $conn->prepare("SELECT username, created, id, level FROM profils WHERE username LIKE ?");
							$stmt->bind_param("s", $text);
							$stmt->execute();
							$stmt->store_result();
							if ($stmt->num_rows == 0) {
								echo "<p>Nekas netika atrasts</p>";
							} else {
								$stmt->bind_result($username, $created, $id, $user_level);
								while ($stmt->fetch()) {
									$isAdmin = "<option value=\"3\"" . ($user_level == 2 ? " selected=\"selected\"" : "") . ">Administrators</option>";
									$isRegistered = "<option value=\"2\"" . ($user_level == 1 ? " selected=\"selected\"" : "") . ">Reģistrējies</option>";
									$isGuest = "<option value=\"1\"" . ($user_level == 0 ? " selected=\"selected\"" : "") . ">Viesis</option>";

									$stmt = $conn->prepare("SELECT text, article_id FROM komentars WHERE profile_id = ?");
									$stmt->bind_param("i", $id);
									$stmt->execute();
									$stmt->store_result();
									$commentStr = "";
									if ($stmt->num_rows == 0) {
										$commentStr = "<p>Nav komentāru</p>";
									} else {
										$stmt->bind_result($text, $article_id);
										while ($stmt->fetch()) {
											$commentStr = $commentStr . "<p>" . $text . " <a href=\"comments.php?id=" . $article_id . "\">Raksts #" . $article_id . "</a></p>\n";
										}
									}
									$html = <<<HTML
										<div style="word-break: break-all;">
											<div class="card-2">
												<img class="card-2-img" src="profile_picture.php?id=$id">
												<div class="card-2-text">
													<h2>$username</h2>
													<form class="permission-form" method="post">
														<input type="hidden" name="id" value="$id">
														<select id="permission-level" name="level">
															$isAdmin
															$isRegistered
															$isGuest
														</select>
														<button type="submit">Atjaunot</button>
													</form>
													<div style="display: flex; gap: 10px;">
														<p>Izveidots: 11. Okt, 2024</p>
														<form id="DeleteUser" method="post">
															<input type="hidden" name="delete" value="true">
															<input type="hidden" name="id" value="$id">
														</form>
														<a href="#" class="a2" onclick="deleteUser();">Dzēst lietotāju</a>
													</div>
												</div>
											</div>
											<h4>Komentāri</h4>
											$commentStr
										</div>
									HTML;
									echo $html;
								}
							}
						} else {
							$text = "%" . $_GET['text'] . "%";
							$stmt = $conn->prepare("SELECT title, created, id FROM raksts WHERE title LIKE ?");
							$stmt->bind_param("s", $text);
							$stmt->execute();
							$stmt->store_result();
							if ($stmt->num_rows == 0) {
								echo "<p>Nekas netika atrasts</p>";
							} else {
								$stmt->bind_result($title, $created, $id);
								while ($stmt->fetch()) {
									$html = <<<HTML
										<div class="card-2">
											<img class="card-2-img" src="article_picture.php?id=$id">
											<div class="card-2-text">
												<a href="article.php?id=$id"><h2>$title</h2></a>
												<p>Izveidots: $created</p>
											</div>
										</div>
									HTML;
									echo $html;
								}
							}
						}
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
			function deleteUser() { 
                document.getElementById("DeleteUser").submit(); 
            }
		</script>
	</body>
</html>