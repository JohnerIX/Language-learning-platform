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
                    <a href="lessons.php">Luganda lessons</a>
                    <a href="lessons1.php">Runya-kitala lessons</a>
                    <a href="lessons2.php">Luo lessons</a>
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

    <section class="categories" id="topics">
        <div class="cont"><h2>Beginner level lessons</h2> </div>
        
        <div class="gallery-grid">
            <div class="gallery-item">
                <a href="lessons.php#basics"><img src="images/comm6.png" alt="Communication Basics" loading="lazy"> </a>
                <div class="caption">Communication Basics</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#numbers"><img src="images/counting.jpg" alt="Numbers and Counting" loading="lazy"></a>
                <div class="caption">Numbers and Counting</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#business"><img src="images/comm5.webp" alt="Business and Trade" loading="lazy"> </a>
                <div class="caption">Business and Trade</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#family"><img src="images/fam1.jpeg" alt="Family Terms" loading="lazy"></a>
                <div class="caption">Family Terms</div>
            </div>
        </div>
    </section>
    
    <section class="categories" id="topics">
        <div class="gallery-grid">
            <div class="gallery-item">
                <a href="lessons.php#greetings"><img src="images/tenses.jpeg" alt="Greetings" loading="lazy"></a>
                <div class="caption">Tenses</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#food"><img src="images/fruits.jpeg" alt="Food and Fruits" loading="lazy"></a>
                <div class="caption">Food and Fruits</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#education"><img src="images/health1.jpeg" alt="Education" loading="lazy"> </a>
                <div class="caption">Health terms</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#calendar"><img src="images/greet1.jpeg" alt="Calendar and Time" loading="lazy"></a>
                <div class="caption">Feelings & expressions</div>
            </div>
        </div>
    </section>

    <section class="categories" id="topics">
        <div class="cont"><h2>Advanced level lessons</h2> </div>
        <div class="gallery-grid">
            <div class="gallery-item">
                <a href="lessons.php#body"><img src="images/kitchen.jpeg" alt="Body Parts" loading="lazy"></a>
                <div class="caption">Kitchen terms</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#living"><img src="images/home3.jpeg" alt="Home and Living" loading="lazy"></a>
                <div class="caption">Home and Living</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#weather"><img src="images/comm8.jpg" alt="Weather Terms" loading="lazy"></a>
                <div class="caption">Weather Terms</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#travel"><img src="images/tour.jpeg" alt="Travel and Tourism" loading="lazy"></a>
                <div class="caption">Travel and Tourism</div>
            </div>
        </div>
    </section>
    
    <section class="categories" id="topics">
        <div class="gallery-grid">
            <div class="gallery-item">
                <a href="lessons.php#basics"><img src="images/comm6.png" alt="Communication Basics" loading="lazy"> </a>
                <div class="caption">Communication Basics</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#numbers"><img src="images/home3.jpeg" alt="Numbers and Counting" loading="lazy"></a>
                <div class="caption">Hospitality </div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#business"><img src="images/trade.jpeg" alt="Business and Trade" loading="lazy"> </a>
                <div class="caption">Business and Trade</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#family"><img src="images/fam2.jpeg" alt="Family Terms" loading="lazy"></a>
                <div class="caption">Family Terms</div>
            </div>
        </div>
    </section>

    <section class="categories" id="topics">
        <div class="cont"><h2>Professional level lessons</h2> </div>
        <div class="gallery-grid">
            <div class="gallery-item">
                <a href="lessons.php#greetings"><img src="images/greet2.jpeg" alt="Greetings" loading="lazy"></a>
                <div class="caption">Greetings</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#food"><img src="images/fruits.jpeg" alt="Food and Fruits" loading="lazy"></a>
                <div class="caption">Food and Fruits</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#education"><img src="images/school.jpeg" alt="Education" loading="lazy"> </a>
                <div class="caption">Education</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#calendar"><img src="images/calendar2.jpeg" alt="Calendar and Time" loading="lazy"></a>
                <div class="caption">Calendar and Time</div>
            </div>
        </div>
    </section>
    
    <section class="categories" id="topics">
        <div class="gallery-grid">
            <div class="gallery-item">
                <a href="lessons.php#greetings"><img src="images/greet2.jpeg" alt="Greetings" loading="lazy"></a>
                <div class="caption">Greetings</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#food"><img src="images/fruits.jpeg" alt="Food and Fruits" loading="lazy"></a>
                <div class="caption">Food and Fruits</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#education"><img src="images/school.jpeg" alt="Education" loading="lazy"> </a>
                <div class="caption">Education</div>
            </div>
            
            <div class="gallery-item">
                <a href="lessons.php#calendar"><img src="images/calendar2.jpeg" alt="Calendar and Time" loading="lazy"></a>
                <div class="caption">Calendar and Time</div>
            </div>
        </div>
    </section>
    
    <section class="sub-info">
        <div class="cont"><h2>Subscription Information</h2> </div>
        <div class="info-columns">
            <div class="info-column">
                <h4>Our subscription plans</h4>
                <p>1. MOnthly - $3 or UGX 10,000. <br> 2. Quaterly (3 months) - $9 or UGX 30,000 <br> 3. Anually - $30 or UGX 100,000</p>
            </div>
            <div class="info-column">
                <h4>Package Offers</h4>
                <p>Once you pay for a subsription, you will be given access to meet and contact advanced content 
                    to learn more. You are able to chat with different native language speakers to help you perfect their accent.
            </div>
            <div class="info-column">
                <h4>Account management</h4>
                <p>You can always contact our team to help you manage your account for issues like making payements, change a preffered language
                    or report any issue that may arise. We have offers to learners who with to join our team.
                </p>
            </div>
        </div>
    </section>

    <section class="info-section">
        <div class="info-content">
            <h2>Plan Your Learning today</h2>
            <p>The main local languages used in EastAfrica are sometimes considered hard to some people due to the complexity in grasping them</p>
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
</body>
</html>