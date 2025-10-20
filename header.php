<?php
    require("db.php");

    $username = "Viesis";
    $pfp = "/assets/pfp.jpg";
    $url = "auth.php";
    if (!empty($_SESSION['user_id'])) {
        $stmt = $conn->prepare("SELECT username FROM profils WHERE id = ?");
        $stmt->bind_param("s", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
			$stmt->bind_result($username);
			$stmt->fetch();
            $pfp = "profile_picture.php?id=" . $_SESSION['user_id'];
        }
        $url = "profile.php";
    }

    $html = <<<HTML
        <header style="display: flex; width: 100%; background-color: #1c1c1c; height: 70px; align-items: center;">
        <a href="index.php" style="line-height: 0px"><img src="/assets/logo2.png" style="width: 60px; height: 60px; object-fit: cover; display: inline-block; margin-left: 1rem;"></a>
        <div class="regular-nav">
            <div style="display: flex; align-items: center;">
                <nav style="display: flex; gap: 2rem; margin-left: 2rem; margin-right: 2rem;">
                    <a class="header-nav" href="index.php">SĀKUMS</a>
                    <a class="header-nav" href="buj.php">BUJ</a>
                    <a class="header-nav" href="help.php">PALĪDZĪBA</a>
                </nav>
                <form action="search.php" method="GET" style="display: flex; align-items: stretch;">
                    <input type="text" name="text" placeholder="Meklēt...">
                    <button type="submit" style="background-color: #2A2A2A; border: none; border-radius: 0px 10px 10px 0px; padding: 9px 12px; cursor: pointer; border: 1px solid #666; border-left: none;">
                        <svg width="24px" height="24px" preserveAspectRatio="xMidYMid meet" viewBox="0 0 70 70">
                            <circle cx="30" cy="30" r="25"
                            stroke="white" stroke-width="5" fill="none" />
                            <polygon points="47, 47, 70, 70"
                            style="stroke:white;stroke-width:5;" />
                        </svg>
                    </button>
                </form>
            </div>
            <div style="display: flex; align-items: center;">
                <p style="display: inline-block; margin: 0px 2rem 0px 0px;">$username</p>
                <a href="$url"><img src="$pfp" style="border-radius: 50%; width: 60px; height: 60px; background-color: #888; object-fit: cover; display: inline-block; margin-right: 1rem;"></a>
            </div>
        </div>
        <div class="burger" id="burger">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <div class="mobile-nav" id="mobile-nav">
            <a href="index.php">SĀKUMS</a>
            <a href="buj.php">BUJ</a>
            <a href="help.php">PALĪDZĪBA</a>
            <form action="search.php" method="GET" style="display: flex; align-items: stretch;">
                <input type="text" name="text" placeholder="Meklēt...">
                <button type="submit" style="background-color: #2A2A2A; border: none; border-radius: 0px 10px 10px 0px; padding: 9px 12px; cursor: pointer; border: 1px solid #666; border-left: none;">
                    <svg width="24px" height="24px" preserveAspectRatio="xMidYMid meet" viewBox="0 0 70 70">
                        <circle cx="30" cy="30" r="25"
                        stroke="white" stroke-width="5" fill="none" />
                        <polygon points="47, 47, 70, 70"
                        style="stroke:white;stroke-width:5;" />
                    </svg>
                </button>
            </form>
            <div style="display: flex; align-items: center;">
                <p style="display: inline-block; margin: 0px 2rem 0px 0px;">$username</p>
                <a href="$url"><img src="$pfp" style="border-radius: 50%; width: 60px; height: 60px; background-color: #888; object-fit: cover; display: inline-block; margin-right: 1rem;"></a>
            </div>
        </div>
        </header>
    HTML;
    echo $html;
?>