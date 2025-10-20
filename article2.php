<?php
	session_start();
	
	require("db.php");

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
		header("Location: index.php");
		exit;
	}
	
	// Process request
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title']) && !empty($_POST['text']) && !empty($_POST['new']) && !empty($_POST['id']) && empty($_POST['delete'])) {
		$_POST['new'] = filter_var($_POST['new'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

		$redirect_edit = "article2.php?id=" . $_POST['id'];
		if ($_POST['new']) {
			$redirect_edit = "article2.php?new=true";
		}

		$uploadError = $_FILES['image']['error'];
		if ($uploadError === UPLOAD_ERR_NO_FILE && !$_POST['new']) {
			goto label;
		}
		if ($uploadError !== UPLOAD_ERR_OK) {
			$_SESSION['message'] = "Kļūda, mēģiniet vēlreiz izmantojot citu bildi.";
			header("Location: " . $redirect_edit);
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
				if (!$_POST['new']) {
					label:
					if ($uploadError !== UPLOAD_ERR_NO_FILE) {
						$stmt = $conn->prepare("UPDATE raksts SET picture = ?, title = ?, text = ? WHERE id = ?");
						$stmt->bind_param("bssi", $null, $_POST['title'], $_POST['text'], $_POST['id']);
						$stmt->send_long_data(0, $image);
					} else {
						$stmt = $conn->prepare("UPDATE raksts SET title = ?, text = ? WHERE id = ?");
						$stmt->bind_param("ssi", $_POST['title'], $_POST['text'], $_POST['id']);
					}
					$stmt->execute();
					$_SESSION['message'] = "Raksts veiksmīgi atjaunots.";
				} else {
					$stmt = $conn->prepare("INSERT INTO raksts (picture, title, text, created) VALUES (?, ?, ?, NOW());");
					$stmt->bind_param("bss", $null, $_POST['title'], $_POST['text']);
					$stmt->send_long_data(0, $image);
					$stmt->execute();
					$_SESSION['message'] = "Raksts veiksmīgi izveidots.";
					$redirect_edit = "article2.php?id=" . $conn->insert_id;
				}
			} catch (Exception $e) {
				$_SESSION['message'] = "Kļūda, mēģiniet vēlreiz izmantojot citu bildi.";
			}
		}
		header("Location: " . $redirect_edit);
		exit;
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete']) && !empty($_POST['id'])) {
		$stmt = $conn->prepare("DELETE FROM raksts WHERE id = ?");
		$stmt->bind_param("i", $_POST['id']);
		$stmt->execute();
		$_SESSION['message'] = "Raksts dzēsts";
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
			
			.article-section {
				width: min(1000px, calc(100vw - 90px));
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

			.upload-area {
				border: 2px dashed #007bff;
				border-radius: 10px;
				background-color: #fff;
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
			}
		</style>
	</head>
	<body>
		<?php
			require("header.php");
		?>
		<div id="content" style="display: flex; flex-direction: column; align-items: center;">
			<?php
				if (isset($_SESSION['message'])) {
					echo "<script>alert('" . $_SESSION['message'] . "');</script>";
					unset($_SESSION['message']);
				}
			?>
			<?php
				if ($_SERVER['REQUEST_METHOD'] === 'GET' && (!empty($_GET['id']) || !empty($_GET['new']))) {
					$id = -1;
					$title = "Nosaukums";
					$text = "Teksts";
					$created = "Nav";
					$new = true;
					if (empty($_GET['new'])) {
						$new = false;
						$id = $_GET['id'];
						$stmt = $conn->prepare("SELECT title, text, created FROM raksts WHERE id = ?");
						$stmt->bind_param("i", $_GET['id']);
						$stmt->execute();
						$stmt->store_result();
				
						if ($stmt->num_rows > 0) {
							$id = $_GET['id'];
							$stmt->bind_result($title, $text, $created);
							$stmt->fetch();
						} else {
							$html = <<<html
							<div class="article-section">
								<h3>Raksts netika atrasts</h3>
							</div>
							html;
							echo($html);
							goto end;
						}
					}
					$newstr = $new ? 'true' : 'false';
					$deleteButton = $new ? "" : "<a href=\"#\" onclick=\"deleteArticle()\" class=\"a2\">Dzēst Rakstu</a>";
					$html = <<<html
						<form id="DeleteArticle" method="POST">
							<input type="hidden" name="delete" value="true">
							<input type="hidden" name="id" value="$id">
						</form>
						<form id="SubmitArticle" method="POST" enctype="multipart/form-data">
							<input type="hidden" name="new" value="$newstr">
							<input type="hidden" name="id" value="$id">
							<div class="article-section">
								<div style="display: flex; justify-content: center; gap: 20px; width: 100%;">
									<button type="button" class="btn-1" onclick="window.location.href='article.php?id=$id';" style="height: 30px; width: 80px; margin-bottom: 20px;">Raksts</button>
									<button type="button" class="btn-2" onclick="window.location.href='comments.php?id=$id';" style="height: 30px; width: 80px; margin-bottom: 20px;">Komentāri</button>
								</div>
								<div class="card-3">
									<div class="upload-area card-3-img" id="uploadArea">
										<p>+ Pievienot Bildi</p>
										<input type="file" id="fileInput" name="image">
										<div class="uploaded-files" id="uploadedFiles"></div>
									</div>
									<div class="card-3-text">
										<textarea name="title" required cols="40" rows="5" style="width: calc(100% - 10px); resize: vertical;">$title</textarea>
										<p>Izveidots: $created</p>
									</div>
								</div>
								<div>
									<textarea name="text" required cols="40" rows="5" style="width: calc(100% - 10px); resize: vertical;">$text</textarea>
									<div style="display: flex; justify-content: right;">
										$deleteButton
										<a href="#" onclick="submitArticle()" style="margin-left: 10px;">Publicēt</a>
									</div>
								</div>
							</div>
						</form>
					html;
					echo($html);
				} else {
					$html = <<<html
						<div class="article-section">
							<h3>Raksts netika atrasts</h3>
						</div>
					html;
					echo($html);
				}
				end:
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
			uploadedFiles.innerHTML = fileList;

			}

            function submitArticle() { 
                document.getElementById("SubmitArticle").submit(); 
            } 

			function deleteArticle() { 
                document.getElementById("DeleteArticle").submit(); 
            }

		</script>
	</body>
</html>