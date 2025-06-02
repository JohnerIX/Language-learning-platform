<?php
require_once __DIR__ . '/includes/config.php'; // Ensures $conn is available

$featured_courses = [];
$fetch_error = null;
try {
    // Check if $conn is set and not null (it should be by config.php)
    if (isset($conn)) {
        $sql = "
            SELECT c.course_id, c.title, c.description, c.thumbnail_url, u.name AS tutor_name, c.is_featured
            FROM courses c
            JOIN users u ON c.tutor_id = u.user_id /* Ensure users table has user_id as primary key */
            WHERE c.status = 'published'
            ORDER BY c.is_featured DESC, c.created_at DESC
            LIMIT 6
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $featured_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $fetch_error = "Database connection is not available.";
        error_log("Error in index.php: Database connection (\$conn) not available after including config.php.");
    }
} catch (PDOException $e) {
    $fetch_error = "Error fetching courses."; // User-friendly message
    error_log("Error fetching courses in index.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Language Learning Platform for East African Languages">
    <title>Learn Lugha - Language Learning Platform</title>

     <!-- Bootstrap CSS (if needed) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="casc.css">
    <link rel="stylesheet" href="cascade.css">
    <!-- Google Translate Script -->
    <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({
        pageLanguage: 'en',
        includedLanguages: 'en,sw,lg,ach,alz,luo',
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
        autoDisplay: false
      }, 'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <style>
      /* Google Translate Styling */
      .translate-container {
        position: fixed;
        top: 90px;
        right: 20px;
        background-color: rgba(255, 255, 255, 0.9);
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        transition: all 0.3s ease;
      }
      
      .translate-title {
        font-size: 14px;
        margin-bottom: 5px;
        color: #333;
        font-weight: bold;
      }
      
      #google_translate_element {
        width: 170px;
      }
      
      #google_translate_element select {
        width: 100%;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
      }
      
      /* Hide Google Translate attribution */
      .goog-logo-link {
        display: none !important;
      }
      
      .goog-te-gadget {
        color: transparent !important;
        font-size: 0px !important;
      }
      
      .goog-te-banner-frame {
        display: none !important;
      }
      
      .goog-te-gadget .goog-te-combo {
        color: #333 !important;
        font-size: 14px !important;
      }
      
      /* Dark mode support */
      body.dark-mode .translate-container {
        background-color: rgba(51, 51, 51, 0.9);
      }
      
      body.dark-mode .translate-title {
        color: #f8f8f8;
      }
      
      body.dark-mode #google_translate_element select {
        background-color: #444;
        color: #f8f8f8;
        border-color: #666;
      }

      /* New Content Sections Styling */
      .testimonials-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 60px 0;
        position: relative;
        overflow: hidden;
      }

      .testimonials-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23000" opacity="0.02"/><circle cx="75" cy="75" r="1" fill="%23000" opacity="0.02"/></pattern></defs><rect fill="url(%23grain)" width="100" height="100"/></svg>');
        opacity: 0.5;
      }

      .testimonial-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin: 20px 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
      }

      .testimonial-card::before {
        content: '"';
        position: absolute;
        top: -20px;
        left: 20px;
        font-size: 100px;
        color:rgb(120, 133, 123);
        opacity: 0.1;
        font-family: serif;
      }

      .testimonial-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
      }

      .testimonial-text {
        font-style: italic;
        font-size: 16px;
        line-height: 1.6;
        color: #555;
        margin-bottom: 20px;
      }

      .testimonial-author {
        display: flex;
        align-items: center;
        gap: 15px;
      }

      .author-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(45deg,rgb(3, 153, 53), #28a745);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
      }

      .author-info h5 {
        margin: 0;
        color: #333;
        font-size: 16px;
      }

      .author-info small {
        color: #777;
      }

      .admin-messages-section {
        background: linear-gradient(45deg,rgb(98, 110, 98),rgb(91, 104, 91));
        color: white;
        padding: 60px 0;
        position: relative;
      }

      .admin-messages-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon points="0,0 100,0 100,80 0,100" fill="rgba(255,255,255,0.05)"/></svg>');
      }

      .admin-card {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 20px;
        padding: 40px;
        margin: 20px 0;
        transition: all 0.3s ease;
      }

      .admin-card:hover {
        background: rgba(255,255,255,0.15);
        transform: scale(1.02);
      }

      .admin-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 25px;
      }

      .admin-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(45deg, #28a745, #20c997);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 24px;
        border: 3px solid rgba(255,255,255,0.3);
      }

      .admin-title {
        color: #f8f9fa;
      }

      .admin-title h4 {
        margin: 0;
        font-size: 22px;
      }

      .admin-title small {
        color: rgba(255,255,255,0.8);
        font-size: 14px;
      }

      .admin-message {
        font-size: 16px;
        line-height: 1.7;
        color: rgba(255,255,255,0.95);
      }

      .future-plans-section {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 80px 0;
        position: relative;
        overflow: hidden;
      }

      .future-plans-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
      }

      @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }

      .plan-card {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 15px;
        padding: 30px;
        margin: 20px 0;
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
      }

      .plan-card:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-5px);
      }

      .plan-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        font-size: 24px;
      }

      .plan-title {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 15px;
      }

      .plan-description {
        font-size: 14px;
        line-height: 1.6;
        opacity: 0.9;
      }

      .stats-section {
        background: #f8f9fa;
        padding: 60px 0;
      }

      .stat-card {
        text-align: center;
        padding: 40px 20px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        margin: 15px 0;
        transition: all 0.3s ease;
      }

      .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
      }

      .stat-number {
        font-size: 48px;
        font-weight: bold;
        color:rgb(0, 255, 13);
        margin-bottom: 10px;
        display: block;
      }

      .stat-label {
        font-size: 16px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 1px;
      }

      .achievement-badge {
        display: inline-block;
        background: linear-gradient(45deg,rgb(77, 255, 7),rgb(72, 255, 0));
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        margin: 5px;
        animation: pulse 2s infinite;
      }

      @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
      }

      .section-title {
        text-align: center;
        margin-bottom: 50px;
        position: relative;
      }

      .section-title h2 {
        font-size: 36px;
        font-weight: bold;
        margin-bottom: 15px;
        position: relative;
        z-index: 2;
      }

      .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(45deg, #007bff, #28a745);
        border-radius: 2px;
      }

      /* Dark mode support for new sections */
      body.dark-mode .testimonials-section {
        background: linear-gradient(135deg, #343a40 0%, #495057 100%);
      }

      body.dark-mode .testimonial-card {
        background: #2d3748;
        color: #e2e8f0;
      }

      body.dark-mode .stats-section {
        background: #2d3748;
      }

      body.dark-mode .stat-card {
        background: #4a5568;
        color: #e2e8f0;
      }

      body.dark-mode .plan-card {
        background: rgba(0,0,0,0.3);
      }

      /* Responsive adjustments */
      @media (max-width: 768px) {
        .testimonial-card, .admin-card, .plan-card {
          margin: 15px;
          padding: 20px;
        }
        
        .section-title h2 {
          font-size: 28px;
        }
        
        .stat-number {
          font-size: 36px;
        }
      }
    </style>
</head>
<body>
    <nav>
        <div class="logo">
            <img src="images/logollp.png" alt="Learn Lugha Logo" width="160" height="60">
        </div>
        
        <div class="nav-links">
            <div class="image-container">
                <a href="home.php"><img src="images/home.jpg" alt="Home" width="35" height="35"></a>
                <div class="overlay">Home</div>
            </div>
            
            <div class="image-container">
                <a href="about.php"><img src="images/about.jpg" alt="About Us" width="35" height="35"></a>
                <div class="overlay">About</div>
            </div>
                
            <div class="dropdown-container">
                <a href="#login/signup"><img src="images/profilepic.jpg" alt="Profile" width="35" height="35"></a>
                <div class="overlay">Sign In</div>
                <div class="dropdown-content">
                    <a href="login.php">Login</a> 
                    <a href="signup.php">Sign Up</a>
                    <a href="profile.php">My profile</a>
                </div>
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
                    <a href="courses.php">Lessons</a>
                    <a href="home3.php#foot">Follow Us</a>
                    <a href="https://wa.me/+256773855888">Direct chat</a>
                </div>
            </div>
        </div>
        
        <button id="theme-toggle" class="theme-toggle" onclick="toggleDarkMode()">🌙</button>
        <div class="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <!-- Google Translate Container -->
    <div class="translate-container">
        <div class="translate-title">Translate Page:</div>
        <div id="google_translate_element"></div>
    </div>
    
    <div class="hero" id="home">
        <div class="content">
            <div class="search-container">
                <input type="text" id="search-input" placeholder="Search a topic to learn...">
                <button onclick="searchFunction()"><i class="fas fa-search"></i> Search</button>
            </div>
            <h1 class="animate-on-scroll">The Language Learning Platform</h1>
            <h4 class="animate-on-scroll">Start your journey today of learning a new language, this platform will help you be perfect<br> and fluent
                in major languages used in Uganda and East Africa. Meet different people and make <br>friends here to practice the most correct 
                pronounciation and accent.
            </h4>
        </div>
    </div>

    <div class="marque">
        <marquee behavior="scroll" direction="left">
            Subscribe to our services today and get a 20% discount valid for 1 month. 
            Hapa ni mahali pazuri sana pa kujifunza Lugha ya Kiswahili na Kingereza ambayo inatafsiriwa kutoka 
            lugha yako unajua sana
        </marquee>
    </div>

    <!-- Image Slider Container -->
    <div class="image-slider-container">
        <button class="slider-arrow prev-arrow">&#10094;</button>
        <div class="image-slider">
            <div class="slider-track">
                <div class="slider-item"><img src="images/prac1.jpg" alt="Image 1"></div>
                <div class="slider-item"><img src="images/prac2.jpg" alt="Image 2"></div>
                <div class="slider-item"><img src="images/prac3.jpg" alt="Image 3"></div>
                <div class="slider-item"><img src="images/prac4.jpg" alt="Image 4"></div>
                <div class="slider-item"><img src="images/prac6.jpg" alt="Image 5"></div>
                <div class="slider-item"><img src="images/prac5.jpg" alt="Image 6"></div>
                <div class="slider-item"><img src="images/prac7.jpg" alt="Image 7"></div>
                <div class="slider-item"><img src="images/prac8.jpg" alt="Image 8"></div>
                <div class="slider-item"><img src="images/prac9.jpg" alt="Image 9"></div>
                <div class="slider-item"><img src="images/prac10.jpg" alt="Image 10"></div>
            </div>
        </div>
        <button class="slider-arrow next-arrow">&#10095;</button>
    </div>

    <!-- Platform Statistics Section -->
    <section class="stats-section">
        <div class="container">
            <div class="section-title">
                <h2>Our Growing Community</h2>
                <p>Join thousands of learners mastering East African languages</p>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <span class="stat-number">2,500+</span>
                        <div class="stat-label">Active Students</div>
                        <div class="achievement-badge">Growing Daily</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <span class="stat-number">150+</span>
                        <div class="stat-label">Expert Tutors</div>
                        <div class="achievement-badge">Native Speakers</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <span class="stat-number">12</span>
                        <div class="stat-label">Languages</div>
                        <div class="achievement-badge">East African</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <span class="stat-number">95%</span>
                        <div class="stat-label">Success Rate</div>
                        <div class="achievement-badge">Proven Results</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Courses Section -->
    <section class="featured-courses py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Featured Courses</h2>
            <?php if ($fetch_error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($fetch_error) ?></div>
            <?php elseif (empty($featured_courses)): ?>
                <div class="alert alert-info text-center">
                    No featured courses available at the moment. Please <a href="courses.php">browse all courses</a>.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($featured_courses as $course): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <?php
                                $thumbnail_url = $course['thumbnail_url'] ?? 'images/default-course.jpg';
                                if (!empty($course['thumbnail_url']) && !filter_var($course['thumbnail_url'], FILTER_VALIDATE_URL) && strpos($course['thumbnail_url'], '/') === false) {
                                    $thumbnail_url = 'uploads/course_thumbs/' . $course['thumbnail_url'];
                                } elseif (empty($course['thumbnail_url'])) {
                                    $thumbnail_url = 'images/default-course.jpg';
                                }
                                ?>
                                <img src="<?= htmlspecialchars($thumbnail_url) ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($course['title']) ?>"
                                     style="height: 200px; object-fit: cover; background-color: #f0f0f0;"
                                     onerror="this.onerror=null; this.src='images/default-course.jpg';">
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= htmlspecialchars($course['title']) ?></h5>
                                    <?php if (!empty($course['tutor_name'])): ?>
                                        <p class="text-muted small mb-2">
                                            By <?= htmlspecialchars($course['tutor_name']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <p class="card-text flex-grow-1">
                                        <?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...
                                    </p>
                                    <div class="mt-auto">
                                        <a href="course-details.php?id=<?= $course['course_id'] ?>" class="btn btn-primary w-100">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="courses.php" class="btn btn-outline-primary">View All Courses</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- User Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-title">
                <h2>What Our Students Say</h2>
                <p>Real feedback from learners across East Africa</p>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Learn Lugha has transformed my understanding of Luganda. The native speakers helped me perfect my pronunciation, and now I can confidently communicate with my grandmother in her native language!"
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">AM</div>
                            <div class="author-info">
                                <h5>Amina Mbabazi</h5>
                                <small>University Student, Kampala</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "The Kiswahili course exceeded my expectations. The interactive lessons and cultural context made learning enjoyable. I completed my 3-month subscription and immediately renewed for a full year!"
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">JK</div>
                            <div class="author-info">
                                <h5>James Kiprotich</h5>
                                <small>Business Owner, Nairobi</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "As a researcher focusing on East African cultures, this platform provided authentic language learning with cultural nuances. The Luo lessons were particularly comprehensive and well-structured."
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">SM</div>
                            <div class="author-info">
                                <h5>Sarah Mwangi</h5>
                                <small>Cultural Researcher, Dar es Salaam</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "The mobile app feature and offline downloads were game-changers for me. I could practice during my commute and the speech recognition helped improve my accent significantly."
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">RN</div>
                            <div class="author-info">
                                <h5>Robert Nyong</h5>
                                <small>Software Developer, Kigali</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "The community aspect is amazing! I've made friends from different countries while learning Runya-kitara. The cultural exchange through language learning is truly valuable."
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">FW</div>
                            <div class="author-info">
                                <h5>Fatuma Wanjiku</h5>
                                <small>Teacher, Mombasa</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Administrator Messages Section -->
    <section class="admin-messages-section">
        <div class="container">
            <div class="section-title">
                <h2 style="color: white;">Messages from Leadership</h2>
                <p style="color: rgba(255,255,255,0.8);">Insights from our platform administrators</p>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="admin-card">
                        <div class="admin-header">
                            <div class="admin-avatar">JE</div>
                            <div class="admin-title">
                                <h4>Johner Kasozi</h4>
                                <small>Founder & CEO</small>
                            </div>
                        </div>
                        <div class="admin-message">
                            "Welcome to Learn Lugha! Our journey began with a simple vision: to preserve and promote the rich linguistic heritage of East Africa. Over the past two years, we've grown from a small team of language enthusiasts to a thriving community of over 2,500 learners. I'm incredibly proud of how our platform has helped people reconnect with their roots, advance their careers, and build bridges across cultures. As we expand into 2025, we remain committed to providing authentic, high-quality language education that honors our diverse traditions while embracing modern learning technologies."
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="admin-card">
                        <div class="admin-header">
                            <div class="admin-avatar">DR</div>
                            <div class="admin-title">
                                <h4>Dr. Rebecca Nalwanga</h4>
                                <small>Head of Academic Affairs</small>
                            </div>
                        </div>
                        <div class="admin-message">
                            "As an educator and linguist with over 15 years of experience in East African languages, I've witnessed the transformative power of quality language education. At Learn Lugha, we've carefully designed our curriculum to balance linguistic accuracy with cultural authenticity. Our team of native-speaking tutors undergoes rigorous training to ensure they can effectively share not just vocabulary and grammar, but the cultural context that makes each language truly come alive. We're not just teaching languages – we're preserving cultural heritage for future generations."
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Future Plans Section -->
    <section class="future-plans-section">
        <div class="container">
            <div class="section-title">
                <h2 style="color: white;">Our Vision for the Future</h2>
                <p style="color: rgba(255,255,255,0.8);">Exciting developments coming to Learn Lugha</p>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="plan-card">
                        <div class="plan-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="plan-title">Mobile App Launch</div>
                        <div class="plan-description">
                            Our dedicated mobile app will launch in Q2 2025, featuring offline learning capabilities, speech recognition technology, and gamified lessons. Practice anywhere, anytime with seamless synchronization across all your devices.
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="plan-card">
                        <div class="plan-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="plan-title">AI Learning Assistant</div>
                        <div class="plan-description">
                            Introducing an AI-powered language tutor trained specifically on East African languages. Get personalized feedback, pronunciation coaching, and adaptive learning paths tailored to your progress and learning style.
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="plan-card">
                        <div class="plan-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="plan-title">Certification Program</div>
                        <div class="plan-description">
                            Partner with regional universities to offer accredited language proficiency certificates. Perfect for professionals, students, and anyone seeking official recognition of their language skills.
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="plan-card">
                        <div class="plan-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="plan-title">Community Expansion</div>
                        <div class="plan-description">
                            Establish physical learning centers in major East African cities, hosting cultural events, language immersion workshops, and connecting learners with native speaker communities.
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="plan-card">
                        <div class="plan-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="plan-title">Digital Library</div>
                        <div class="plan-description">
                            Launch a comprehensive digital library featuring traditional stories, contemporary literature, and historical documents in multiple East African languages, complete with audio narrations.
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="plan-card">
                        <div class="plan-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="plan-title">Teacher Training</div>
                        <div class="plan-description">
                            Develop a comprehensive teacher training program to expand our network of qualified instructors and establish Learn Lugha methodology in schools across the region.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="sub-info">
        <div class="cont"><h2>Subscription Information</h2> </div>
        <div class="info-columns">
            <div class="info-column">
                <h4>Our subscription plans</h4>
                <p>1. Monthly - $3 or UGX 10,000. <br> 2. Quarterly (3 months) - $9 or UGX 30,000 <br> 3. Annually - $30 or UGX 100,000</p>
            </div>
            <div class="info-column">
                <h4>Package Offers</h4>
                <p>Once you pay for a subscription, you will be given access to meet and contact advanced content 
                    to learn more. You are able to chat with different native language speakers to help you perfect their accent.
            </div>
            <div class="info-column">
                <h4>Account management</h4>
                <p>You can always contact our team to help you manage your account for issues like making payments, change a preferred language
                    or report any issue that may arise. We have offers to learners who wish to join our team.
                </p>
            </div>
        </div>
    </section>

    <section class="info-section">
        <div class="info-content">
            <h2>Plan Your Learning today</h2>
            <p>The main local languages used in East Africa are sometimes considered hard to some people due to the complexity in grasping them</p>
            <p>We try to come up with different methods of helping each other learn and become fluent in the major local, national and official languages. </p>
            <hr>
            <h2>Ready to start learning?</h2>
            <p>Join thousands of students mastering East African languages</p>
            <a href="login.php"><button class="subscribe-btn">Subscribe Now</button> </a>
        </div>
    </section>

    <div class="marque">
        <marquee behavior="scroll" direction="left">
            Subscribe to our services today and get a 20% discount valid for 1 month. 
            Hapa ni mahali pazuri sana pa kujifunza Lugha
        </marquee>
    </div>

    <!-- Live Chat Button -->
    <div class="live-chat-button">
        <a href="https://wa.me/+256700461140" target="_blank">
            <img src="images/Whatsapp.jpg" alt="Chat with us on WhatsApp">
        </a>
    </div>

    <!-- Direct Call Button -->
    <div class="direct-call-button">
        <a href="tel:+256700461140">
            <img src="images/contact.jpg" alt="Call us directly">
        </a>
    </div>
    
    <footer class="footer" id="foot">
        <table class="borderless-table">
            <thead>
              <tr>
                <th>Quick links</th>
                <th>Follow us on</th>
                <th>Quick downloads</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                    <a href="home.php">Home </a> <br>
                    <a href="contact.php">Contact Us </a> <br>
                    <a href="about.php">About Us </a> <br>
                    <a href="connect.php">Connect </a> <br> 
                    <a href="menu.php">Main menu </a> <br>
                    <a href="login.php">Subscribe now </a>
                </td>
                <td>
                    <div class="social-links">
                        <a href="https://www.youtube.com/@johnerkasozi" aria-label="YouTube">
                            <img src="images/Youtube.jpg" alt="Youtube">
                        </a>
                        <a href="https://wa.me/+256700461140" aria-label="WhatsApp">
                            <img src="images/Whatsapp.jpg" alt="Whatsapp">
                        </a>
                        <a href="https://www.facebook.com/jonahkersoxhi" aria-label="Facebook">
                            <img src="images/Facebook.png" alt="Facebook">
                        </a>
                        <a href="https://www.twitter.com/JxJohner" aria-label="Twitter">
                            <img src="images/Twitter.jpg" alt="Twitter">
                        </a>
                        <a href="https://www.instagram.com/jx_joka" aria-label="Instagram">
                            <img src="images/Insta.png" alt="Instagram">
                        </a>
                    </div>
                </td>
                <td>
                    <a href="#" class="download-btn">Language Guide PDF</a> <br>
                    <a href="#" class="download-btn">Mobile App</a> <br>
                    <a href="#" class="download-btn">Learning Calendar</a>
                </td>
              </tr>
              <tr>
                <td>-</td>
                <td>Contact us to read about terms and conditions.</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
        <p>&copy; <?php echo date("Y"); ?> Learn Lugha ya Kiswahili na Kingereza (English)</p>
        <p>This site uses cookies from Google. <a href="https://support.google.com/chrome/answer/95647hl=en&co=GENIE.Platform%3DAndroid">Read more</a></p>
    </footer>

    <script src="function1.js"></script>
    <script src="contact.js"></script>
    
    <script>
        // Enhanced JavaScript for new features
        document.addEventListener('DOMContentLoaded', function() {
            // Animate statistics counters
            const counters = document.querySelectorAll('.stat-number');
            const animateCounter = (counter) => {
                const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;
                
                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        counter.textContent = counter.textContent.replace(/\d+/, target);
                        clearInterval(timer);
                    } else {
                        counter.textContent = counter.textContent.replace(/\d+/, Math.floor(current));
                    }
                }, 16);
            };
            
            // Intersection Observer for counter animation
            const observerOptions = {
                threshold: 0.5,
                once: true
            };
            
            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const counter = entry.target.querySelector('.stat-number');
                        if (counter) {
                            setTimeout(() => animateCounter(counter), 200);
                        }
                    }
                });
            }, observerOptions);
            
            document.querySelectorAll('.stat-card').forEach(card => {
                counterObserver.observe(card);
            });
            
            // Add scroll animations for testimonials and admin cards
            const scrollElements = document.querySelectorAll('.testimonial-card, .admin-card, .plan-card');
            const scrollObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            scrollElements.forEach(element => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(30px)';
                element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                scrollObserver.observe(element);
            });
        });
    </script>
</body>
</html>