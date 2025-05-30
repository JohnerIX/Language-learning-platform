<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true
    ]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Languages Learn Platform' ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Bootstrap CSS (if needed) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Navigation Styles */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1000;
        }
        
        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .image-container {
            position: relative;
            display: inline-block;
            text-align: center;
        }
        
        .overlay {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            opacity: 0;
            transition: opacity 0.3s;
            white-space: nowrap;
        }
        
        .image-container:hover .overlay {
            opacity: 1;
        }
        
        .dropdown-container {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
        }
        
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
        }
        
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        
        .dropdown-container:hover .dropdown-content {
            display: block;
        }
        
        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }
        
        .menu-toggle span {
            height: 3px;
            width: 25px;
            background-color: #333;
            margin: 3px 0;
            transition: 0.4s;
        }
        
        .theme-toggle {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 5px 10px;
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .nav-links {
                position: fixed;
                top: 80px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 80px);
                background: white;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
                padding-top: 30px;
                transition: 0.5s;
                gap: 30px;
            }
            
            .nav-links.active {
                left: 0;
            }
            
            .menu-toggle {
                display: flex;
            }
            
            .dropdown-content {
                position: static;
                box-shadow: none;
                display: none;
            }
            
            .dropdown-container:hover .dropdown-content,
            .dropdown-container.active .dropdown-content {
                display: block;
            }
        }
    </style>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <nav>
        <div class="logo">
            <img src="images/logollp.png" alt="Learn Lugha Logo" width="160" height="60">
        </div>
        
        <div class="nav-links" id="navLinks">
            <div class="image-container">
                <a href="index.php"><img src="images/home.jpg" alt="Home" width="35" height="35"></a>
                <div class="overlay">Home</div>
            </div>
            
            <div class="image-container">
                <a href="about.php"><img src="images/about.jpg" alt="About Us" width="35" height="35"></a>
                <div class="overlay">About</div>
            </div>
                
            <div class="dropdown-container">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php"><img src="<?= $_SESSION['profile_pic'] ?? 'images/profilepic.jpg' ?>" alt="Profile" width="35" height="35"></a>
                    <div class="overlay">Profile</div>
                    <div class="dropdown-content">
                        <a href="profile.php">My Profile</a>
                        <a href="logout.php">Logout</a>
                        <?php if ($_SESSION['user_role'] === 'learner'): ?>
                            <a href="dashboard.php">My Learning</a>
                        <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                            <a href="admin-dashboard.php">Admin Dashboard</a>
                        <?php elseif ($_SESSION['user_role'] === 'tutor'): ?>
                            <a href="tutor-dashboard.php">Tutor Dashboard</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <a href="login.php"><img src="images/profilepic.jpg" alt="Login" width="35" height="35"></a>
                    <div class="overlay">Sign In</div>
                    <div class="dropdown-content">
                        <a href="signup.php">SignUp</a>
                        <a href="login.php">Login</a>
                    </div>
                <?php endif; ?>
            </div>
                
            <div class="image-container">
                <a href="contact.php"><img src="images/contact.jpg" alt="Contact" width="35" height="35"></a>
                <div class="overlay">Contact</div>
            </div>    
                
            <div class="image-container">
                <a href="connect.php"><img src="images/connz.png" alt="Connect" width="35" height="35"></a>
                <div class="overlay">Connect</div>
            </div>
                
            <div class="dropdown-container" id="menu">
                <a href="#menu"><img src="images/menu.jpg" alt="Menu" width="35" height="35" class="image-container"></a>
                <div class="overlay">Menu</div>
                <div class="dropdown-content">
                    <a href="contact.php">Help</a> 
                    <a href="lessons.php?language=luganda">Luganda lessons</a>
                    <a href="lessons.php?language=runyankole">Runyankole lessons</a>
                    <a href="lessons.php?language=luo">Luo lessons</a>
                    <a href="index.php#foot">Follow Us</a>
                    <a href="https://wa.me/+256773855888">Direct chat</a>
                </div>
            </div>
        </div>
        
        <button id="theme-toggle" class="theme-toggle" onclick="toggleDarkMode()">🌙</button>
        <div class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');
        
        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
        
        // Close menu when clicking a link (mobile)
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                menuToggle.classList.remove('active');
            });
        });
        
        // Dark mode toggle
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const themeToggle = document.getElementById('theme-toggle');
            if (document.body.classList.contains('dark-mode')) {
                themeToggle.textContent = '☀️';
                localStorage.setItem('darkMode', 'enabled');
            } else {
                themeToggle.textContent = '🌙';
                localStorage.setItem('darkMode', 'disabled');
            }
        }
        
        // Check for saved dark mode preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            document.getElementById('theme-toggle').textContent = '☀️';
        }
    </script>