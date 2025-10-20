<?php
	session_start();
	if (empty($_SESSION['user_id'])) {
		header("Location: index.php");
		exit;
	}
	require 'db.php';
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$uploadError = $_FILES['image']['error'];
		if ($uploadError !== UPLOAD_ERR_OK) {
			$_SESSION['message'] = "Kļūda, mēģiniet vēlreiz izmantojot citu bildi.";
			header("Location: profile.php");
			exit;
		}

		$image = file_get_contents($_FILES['image']['tmp_name']);

		$fileSize = $_FILES['image']['size'];
		$allowedTypes = ['image/jpeg', 'image/png'];
		if (!in_array($_FILES['image']['type'], $allowedTypes)) {
			$_SESSION['message'] = "Nepareizs datnes formāts. (Jābūt: PNG/JPEG)";
		} else if ($fileSize > 10485760) {
			$_SESSION['message'] = "Faila izmērs ir pārāk liels. (Max: 10 MB)";
		} else {
			try {
				$stmt = $conn->prepare("UPDATE profils SET picture = ? WHERE id = ?");
				$stmt->bind_param("bs", $null, $_SESSION['user_id']);
				$stmt->send_long_data(0, $image);
				$stmt->execute();
				$_SESSION['message'] = "Profila bilde veiksmīgi atjaunota.";
			} catch (Exception $e) {
				$_SESSION['message'] = "Kļūda, mēģiniet vēlreiz izmantojot citu bildi.";
			}
		}
		header("Location: profile.php");
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

			.upload-area {
				border: 2px solid #007bff;
				border-radius: 10px;
				background-color: #fff;
				border-radius: 100px;
				width: 200px;
				height: 200px;
				display: flex;
				justify-content: center;
				flex-direction: column;
				align-items: center;
				text-align: center;
				color: #007bff;
				font-size: 1rem;
				position: relative;
				cursor: pointer;
				transition: background-color 0.3s;
				margin-bottom: 10px;
				<?php
					$pfp = "profile_picture.php?id=" . $_SESSION['user_id'];
					echo("background-image: url(". $pfp .");");
				?>
				background-repeat: no-repeat;
				background-size: cover;
				background-position: center;
			}

			.upload-area.drag-over {
				background-color: #e9f5ff;
			}

			.upload-area input[type="file"] {
				display: none;
			}

			.upload-area p {
				margin: 0;
			}

			.uploaded-files {
				margin-top: 20px;
				font-size: 0.9rem;
				color: #333;
				display: flex;
				flex-direction: column;
				align-items: center;
				word-break: break-all;
			}

			.btn-3 {
				background-color: #007bff;
				color: white;
				padding: 0.8rem;
				border: none;
				border-radius: 4px;
				font-size: 1rem;
				cursor: pointer;
				transition: background-color 0.3s;
				width: 200px;
				margin-bottom: 10px;
			}
			.btn-3:hover {
				background-color: #0453a7;
			}
			.btn-color-red {
				background-color: #ff4444;
			}
			.btn-color-black {
				background-color: #383838;
			}
			.btn-color-red:hover {
				background-color: #cc4444;
			}
			.btn-color-black:hover {
				background-color: #000000;
			}

			.profile-section {
				display: flex;
				flex-direction: column;
				align-items: center;
				width: 400px;
				margin: auto;
				margin-top: 20px;
				margin-bottom: 20px;
				padding: 20px;
				border-radius: 10px;
				background-color: #fff;
				box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
			}
			
		</style>
	</head>
	<body>
		<?php
			require("header.php");
		?>
		<div id="content" style="display: flex; flex-direction: column; align-items: center;">
			<div class="profile-section">
				<form method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; align-items: center;">
					<div class="upload-area card-3-img" id="uploadArea">
						<b style="text-align: center;">+ <br>Mainīt bildi</b>
						<input type="file" id="fileInput" name="image">
					</div>
					<div class="uploaded-files" id="uploadedFiles"></div>
				</form>
				<?php
					$stmt = $conn->prepare("SELECT username, level FROM profils WHERE id = ?");
					$stmt->bind_param("i", $_SESSION['user_id']);
					$stmt->execute();
					$stmt->bind_result($username, $level);
					$stmt->fetch();
					$levelstr = "";
					switch ($level) {
						case 0:
							$levelstr = "Viesis";
							break;
						case 1:
							$levelstr = "Reģistrēts";
							break;
						default:
							$levelstr = "Administrators";
							break;
					}
					echo "<h2 style=\"margin-bottom: 0px;\">$username</h2><p>$levelstr</p>";
				?>
				<button class="btn-3" onclick="window.location.href='renew.php';">Mainīt Paroli</button>
				<button type="submit" onclick="window.location.href='delete_profile.php'; return confirm('Vai esat pārliecināts, ka gribat izdzēst kontu?');" class="btn-3 btn-color-red" name="delete-account" value="true">Dzēst Kontu</button>
				<button type="submit" onclick="window.location.href='logout.php';" class="btn-3 btn-color-black" name="sign-out" value="true">Izrakstīties</button>
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

			const uploadArea = document.getElementById('uploadArea');
			const fileInput = document.getElementById('fileInput');
			const uploadedFiles = document.getElementById('uploadedFiles');

			// Add drag and drop events
			uploadArea.addEventListener('dragover', (e) => {
				e.preventDefault();
				uploadArea.classList.add('drag-over');
			});

			uploadArea.addEventListener('dragleave', () => {
				uploadArea.classList.remove('drag-over');
			});

			uploadArea.addEventListener('drop', (e) => {
				e.preventDefault();
				uploadArea.classList.remove('drag-over');
				handleFiles(e.dataTransfer.files);
			});

			uploadArea.addEventListener('click', () => fileInput.click());
			fileInput.addEventListener('change', () => handleFiles(fileInput.files));

			function handleFiles(files) {
				const fileList = Array.from(files)
					.map((file) => `<p>${file.name} (${(file.size / 1024).toFixed(2)} KB)</p>`)
					.join('');
				uploadedFiles.innerHTML = fileList + "<button type=\"submit\" class=\"btn-3\" style=\"margin-bottom: 0px;\">Atjaunot</button>";
			}
			<?php
				if (isset($_SESSION['message'])) {
					echo "alert('" . $_SESSION['message'] . "');</script>";
					unset($_SESSION['message']);
				}
			?>
		</script>
	</body>
</html>