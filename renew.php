<?php
	session_start();
	if (empty($_SESSION['user_id'])) {
		header("Location: index.php");
		exit;
	}

	require 'db.php';
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
		$password = htmlspecialchars($_POST['password']);
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

		$stmt2 = $conn->prepare("UPDATE profils SET password=? WHERE id=?");
		$stmt2->bind_param("ss", $password, $_SESSION['user_id']);
		$stmt2->execute();

		$_SESSION['message'] = "Parole atjaunota";
		header("Location: index.php");
		$stmt->close();
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
			
			.auth-container {
				width: min(350px, calc(100% - 60px));
				margin: 2rem auto;
				background-color: #fff;
				padding: 1rem;
				border-radius: 8px;
				box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
				display: flex;
				flex-direction: column;
				align-items: center;
				margin: auto;
			}

			.auth-container label {
				margin-bottom: 0.5rem;
				font-weight: bold;
				color: #555;
			}

			.auth-container input {
				padding: 0.8rem;
				font-size: 1rem;
				margin-bottom: 1rem;
				border: 1px solid #ddd;
				border-radius: 4px;
				outline: none;
				transition: border-color 0.3s;
			}

			.auth-container input:focus {
				border-color: #007bff;
			}

			.auth-form button {
				background-color: #007bff;
				color: white;
				padding: 0.8rem;
				border: none;
				border-radius: 4px;
				font-size: 1rem;
				cursor: pointer;
				transition: background-color 0.3s;
			}

			.auth-form button:hover {
				background-color: #0056b3;
			}
			
		</style>
	</head>
	<body>
		<?php
			require("header.php");
		?>
		<div id="content" style="display: flex; flex-direction: column; align-items: center;">
			<div class="auth-container">
				<h2>Atjaunot Paroli</h2>
				<form class="auth-form" id="authForm" style="display: flex; flex-direction: column; justify-content: center;" method="POST">
					<div style="display: flex; flex-direction: column; justify-content: center;">
						<label for="password">Parole</label>
						<input type="password" id="password" name="password" placeholder="Ievadiet jauno paroli" required>
					</div>
					<br>
					<button type="submit" style="margin-bottom: 20px;">Atjaunot</button>
				</form>
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