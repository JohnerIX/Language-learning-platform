<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Practice page for Learn Lugha Language Learning Platform">
    <title>Practice - Learn Lugha</title>
    <link rel="stylesheet" href="casc.css">
    <link rel="stylesheet" href="cascade.css">
    <link rel="stylesheet" href="practice.css">
</head>
<body>
    <nav>
        <div class="logo">
            <img src="images/logollp.png" alt="Learn Lugha Logo" width="200" height="80">
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
                    <a href="login.php">Login/SignUp</a> 
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
                
            <div class="dropdown-container">
                <a href="#menu"><img src="images/menu.jpg" alt="Menu" width="35" height="35" class="image-container"></a>
                <div class="overlay">Menu</div>
                <div class="dropdown-content">
                    <a href="contact.php">Help</a> 
                    <a href="courses.php">Lessons</a>
                    <a href="home3.html#foot">Follow Us</a>
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

    <main class="practice-container">
        <section class="content">
            <h1 class="animate-on-scroll">Practice & Improve Your Skills</h1>
            <h4 class="animate-on-scroll">Take your language learning to the next level with interactive exercises, community support, and personalized guidance.
            </h4>
        </section>

        <!-- Verbs of the Day Section -->
        <section class="verbs-day-section">
            <h2>Verbs of the Day</h2>
            <div class="verb-cards">
                <div class="verb-card">
                    <div class="verb-header">
                        <h3>Kucheza</h3>
                        <span class="language-tag">Swahili</span>
                    </div>
                    <p class="verb-meaning">To play</p>
                    <div class="verb-examples">
                        <p><strong>Example:</strong> Watoto wanacheza uwanja wa michezo.</p>
                        <p><strong>Translation:</strong> The children are playing in the playground.</p>
                    </div>
                    <div class="verb-conjugation">
                        <p><strong>Present:</strong> Ninacheza (I play)</p>
                        <p><strong>Past:</strong> Nilicheza (I played)</p>
                        <p><strong>Future:</strong> Nitacheza (I will play)</p>
                    </div>
                </div>

                <div class="verb-card">
                    <div class="verb-header">
                        <h3>Kusoma</h3>
                        <span class="language-tag">Swahili</span>
                    </div>
                    <p class="verb-meaning">To read/study</p>
                    <div class="verb-examples">
                        <p><strong>Example:</strong> Ninasoma kitabu kizuri.</p>
                        <p><strong>Translation:</strong> I am reading a good book.</p>
                    </div>
                    <div class="verb-conjugation">
                        <p><strong>Present:</strong> Anasoma (He/She reads)</p>
                        <p><strong>Past:</strong> Alisoma (He/She read)</p>
                        <p><strong>Future:</strong> Atasoma (He/She will read)</p>
                    </div>
                </div>

                <div class="verb-card">
                    <div class="verb-header">
                        <h3>Kuimba</h3>
                        <span class="language-tag">Swahili</span>
                    </div>
                    <p class="verb-meaning">To sing</p>
                    <div class="verb-examples">
                        <p><strong>Example:</strong> Tuliimba wimbo mzuri.</p>
                        <p><strong>Translation:</strong> We sang a beautiful song.</p>
                    </div>
                    <div class="verb-conjugation">
                        <p><strong>Present:</strong> Tunaimba (We sing)</p>
                        <p><strong>Past:</strong> Tuliimba (We sang)</p>
                        <p><strong>Future:</strong> Tutaimba (We will sing)</p>
                    </div>
                </div>
            </div>
            <button class="refresh-verbs">Load New Verbs</button>
        </section>

        <div class="practice-features">
            <!-- Quiz Section -->
            <section class="feature-box quiz-section">
                <div class="feature-icon">
                    <img src="images/calendar.jpeg" alt="Quiz Icon" width="60" height="60">
                </div>
                <h2>Attempt a Quiz</h2>
                <p>Test your knowledge with our interactive quizzes designed for different proficiency levels.</p>
                
                <div class="quiz-options">
                    <div class="quiz-level">
                        <h3>Choose Your Level:</h3>
                        <div class="level-buttons">
                            <button class="level-btn" data-level="beginner">Beginner</button>
                            <button class="level-btn" data-level="intermediate">Intermediate</button>
                            <button class="level-btn" data-level="advanced">Advanced</button>
                        </div>
                    </div>
                    
                    <div class="quiz-language">
                        <h3>Select Language:</h3>
                        <select id="quiz-language-select">
                            <option value="Swahili">Swahili</option>
                            <option value="Luganda">Luganda</option>
                            <option value="Luo">Luo</option>
                            <option value="Runya-kitala">Runya-kitala</option>
                        </select>
                    </div>
                    
                    <div class="quiz-topic">
                        <h3>Choose Topic:</h3>
                        <select id="quiz-topic-select">
                            <option value="vocabulary">Vocabulary</option>
                            <option value="grammar">Grammar</option>
                            <option value="conversation">Conversation</option>
                            <option value="culture">Cultural Knowledge</option>
                        </select>
                    </div>
                </div>
                
                <button class="start-quiz-btn">Start Quiz</button>
            </section>

            <!-- Apply for Test Section -->
            <section class="feature-box test-section">
                <div class="feature-icon">
                    <img src="images/calendar2.jpeg" alt="Test Icon" width="60" height="60">
                </div>
                <h2>Apply for a Test</h2>
                <p>Ready to certify your language skills? Apply for our official proficiency tests.</p>
                
                <div class="test-options">
                    <div class="test-types">
                        <h3>Available Certifications:</h3>
                        <ul class="test-list">
                            <li>
                                <input type="radio" id="basic-cert" name="test-type" value="basic">
                                <label for="basic-cert">Basic Proficiency (A1-A2)</label>
                            </li>
                            <li>
                                <input type="radio" id="intermediate-cert" name="test-type" value="intermediate">
                                <label for="intermediate-cert">Intermediate Proficiency (B1-B2)</label>
                            </li>
                            <li>
                                <input type="radio" id="advanced-cert" name="test-type" value="advanced">
                                <label for="advanced-cert">Advanced Proficiency (C1-C2)</label>
                            </li>
                            <li>
                                <input type="radio" id="business-cert" name="test-type" value="business">
                                <label for="business-cert">Business Language Certificate</label>
                            </li>
                            <li>
                                <input type="radio" id="teaching-cert" name="test-type" value="teaching">
                                <label for="teaching-cert">Teaching Qualification</label>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="test-dates">
                        <h3>Upcoming Test Dates:</h3>
                        <select id="test-date-select">
                            <option value="">Select a date</option>
                            <option value="2025-05-15">May 15, 2025</option>
                            <option value="2025-06-02">June 2, 2025</option>
                            <option value="2025-06-20">June 20, 2025</option>
                            <option value="2025-07-10">July 10, 2025</option>
                        </select>
                    </div>
                    
                    <div class="test-format">
                        <h3>Test Format:</h3>
                        <select id="test-format-select">
                            <option value="online">Online Proctored</option>
                            <option value="center">Test Center</option>
                        </select>
                    </div>
                </div>
                
                <button class="apply-test-btn">Apply Now</button>
            </section>
        </div>

        <div class="practice-features">
            <!-- Community Groups Section -->
            <section class="feature-box groups-section">
                <div class="feature-icon">
                    <img src="images/group-icon.png" alt="Group Icon" width="60" height="60">
                </div>
                <h2>Join Related Groups</h2>
                <p>Connect with fellow language learners in our community groups for practice and cultural exchange.</p>
                
                <div class="groups-container">
                    <div class="group-card">
                        <div class="group-info">
                            <h3>Swahili Conversation Club</h3>
                            <p>Practice speaking with weekly conversation sessions</p>
                            <div class="group-meta">
                                <span>154 members</span>
                                <span>Weekly meetings</span>
                            </div>
                        </div>
                        <button class="join-group-btn">Join Group</button>
                    </div>
                    
                    <div class="group-card">
                        <div class="group-info">
                            <h3>East African Literature Circle</h3>
                            <p>Read and discuss books in various East African languages</p>
                            <div class="group-meta">
                                <span>87 members</span>
                                <span>Bi-weekly meetings</span>
                            </div>
                        </div>
                        <button class="join-group-btn">Join Group</button>
                    </div>
                    
                    <div class="group-card">
                        <div class="group-info">
                            <h3>Cultural Exchange Forum</h3>
                            <p>Share and learn about East African cultures and traditions</p>
                            <div class="group-meta">
                                <span>209 members</span>
                                <span>Daily discussions</span>
                            </div>
                        </div>
                        <button class="join-group-btn">Join Group</button>
                    </div>
                </div>
                
                <a href="#" class="view-more-link">View All Groups</a>
            </section>

            <!-- WhatsApp Channels Section -->
            <section class="feature-box whatsapp-section">
                <div class="feature-icon">
                    <img src="images/whatsapp-icon.png" alt="WhatsApp Icon" width="60" height="60">
                </div>
                <h2>Follow WhatsApp Channels</h2>
                <p>Stay updated with daily language tips, audio lessons, and practice materials.</p>
                
                <div class="channels-container">
                    <div class="channel-card">
                        <div class="channel-icon">
                            <img src="images/daily-tips.png" alt="Daily Tips Channel" width="50" height="50">
                        </div>
                        <div class="channel-info">
                            <h3>Daily Language Tips</h3>
                            <p>Get bite-sized tips and vocabulary every day</p>
                            <span>5,600+ followers</span>
                        </div>
                        <a href="https://whatsapp.com/channel/learn-lugha-daily" class="follow-channel-btn">Follow</a>
                    </div>
                    
                    <div class="channel-card">
                        <div class="channel-icon">
                            <img src="images/audio-lessons.png" alt="Audio Lessons Channel" width="50" height="50">
                        </div>
                        <div class="channel-info">
                            <h3>Audio Pronunciation</h3>
                            <p>Listen to native speakers pronounce challenging words</p>
                            <span>3,200+ followers</span>
                        </div>
                        <a href="https://whatsapp.com/channel/learn-lugha-audio" class="follow-channel-btn">Follow</a>
                    </div>
                    
                    <div class="channel-card">
                        <div class="channel-icon">
                            <img src="images/practice-exercises.png" alt="Practice Exercises Channel" width="50" height="50">
                        </div>
                        <div class="channel-info">
                            <h3>Daily Practice Exercises</h3>
                            <p>Quick exercises to test your knowledge</p>
                            <span>4,100+ followers</span>
                        </div>
                        <a href="https://whatsapp.com/channel/learn-lugha-practice" class="follow-channel-btn">Follow</a>
                    </div>
                    
                    <div class="channel-card">
                        <div class="channel-icon">
                            <img src="images/cultural-insights.png" alt="Cultural Insights Channel" width="50" height="50">
                        </div>
                        <div class="channel-info">
                            <h3>Cultural Insights</h3>
                            <p>Learn about traditions, customs, and cultural context</p>
                            <span>3,800+ followers</span>
                        </div>
                        <a href="https://whatsapp.com/channel/learn-lugha-culture" class="follow-channel-btn">Follow</a>
                    </div>
                </div>
            </section>
        </div>

        <!-- Personal Tutor Section -->
        <section class="tutor-section">
            <h2>Request a Personal Tutor</h2>
            <p class="section-intro">Get personalized guidance from our experienced language tutors to accelerate your learning journey.</p>
            
            <div class="tutor-container">
                <div class="tutor-benefits">
                    <h3>Benefits of Personal Tutoring:</h3>
                    <ul class="benefits-list">
                        <li>Customized learning plan based on your goals</li>
                        <li>One-on-one conversation practice with native speakers</li>
                        <li>Personalized feedback on pronunciation and grammar</li>
                        <li>Flexible scheduling to fit your availability</li>
                        <li>Progress tracking and regular assessments</li>
                    </ul>
                </div>
                
                <div class="tutor-form">
                    <h3>Request Your Tutor</h3>
                    <form id="tutor-request-form">
                        <div class="form-group">
                            <label for="language-preference">Preferred Language:</label>
                            <select id="language-preference" name="language" required>
                                <option value="">Select a language</option>
                                <option value="swahili">Swahili</option>
                                <option value="luganda">Luganda</option>
                                <option value="Luo">Luo</option>
                                <option value="Runya-kitala">Runya-kitala</option>
                                
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="proficiency-level">Your Current Level:</label>
                            <select id="proficiency-level" name="proficiency" required>
                                <option value="">Select your level</option>
                                <option value="beginner">Beginner</option>
                                <option value="elementary">Elementary</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="learning-goals">Learning Goals:</label>
                            <textarea id="learning-goals" name="goals" rows="3" placeholder="What do you want to achieve? (e.g., Conversational fluency, Business language, etc.)" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="session-frequency">Preferred Session Frequency:</label>
                            <select id="session-frequency" name="frequency" required>
                                <option value="">Select frequency</option>
                                <option value="once-weekly">Once a week</option>
                                <option value="twice-weekly">Twice a week</option>
                                <option value="thrice-weekly">Three times a week</option>
                                <option value="daily">Daily sessions</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="preferred-days">Preferred Days:</label>
                            <div class="checkbox-group">
                                <label><input type="checkbox" name="days" value="monday"> Monday</label>
                                <label><input type="checkbox" name="days" value="tuesday"> Tuesday</label>
                                <label><input type="checkbox" name="days" value="wednesday"> Wednesday</label>
                                <label><input type="checkbox" name="days" value="thursday"> Thursday</label>
                                <label><input type="checkbox" name="days" value="friday"> Friday</label>
                                <label><input type="checkbox" name="days" value="saturday"> Saturday</label>
                                <label><input type="checkbox" name="days" value="sunday"> Sunday</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="additional-notes">Additional Notes:</label>
                            <textarea id="additional-notes" name="notes" rows="2" placeholder="Any specific requirements or preferences for your tutor?"></textarea>
                        </div>
                        
                        <button type="submit" class="request-tutor-btn">Submit Request</button>
                    </form>
                </div>
            </div>
            
            <div class="tutor-showcase">
                <h3>Our Featured Tutors</h3>
                <div class="tutors-gallery">
                    <div class="tutor-profile">
                        <img src="images/tutor1.jpg" alt="Tutor: Maria Njeri" width="120" height="120">
                        <h4>Maria Njeri</h4>
                        <p class="tutor-languages">Swahili, English</p>
                        <div class="tutor-rating">
                            <span class="stars">★★★★★</span>
                            <span class="rating-count">(42 reviews)</span>
                        </div>
                    </div>
                    
                    <div class="tutor-profile">
                        <img src="images/tutor2.jpg" alt="Tutor: David Mukasa" width="120" height="120">
                        <h4>David Mukasa</h4>
                        <p class="tutor-languages">Luganda, Swahili, English</p>
                        <div class="tutor-rating">
                            <span class="stars">★★★★★</span>
                            <span class="rating-count">(38 reviews)</span>
                        </div>
                    </div>
                    
                    <div class="tutor-profile">
                        <img src="images/tutor3.jpg" alt="Tutor: Amina Hassan" width="120" height="120">
                        <h4>Amina Hassan</h4>
                        <p class="tutor-languages">Swahili, Arabic, English</p>
                        <div class="tutor-rating">
                            <span class="stars">★★★★★</span>
                            <span class="rating-count">(53 reviews)</span>
                        </div>
                    </div>
                    
                    <div class="tutor-profile">
                        <img src="images/tutor4.jpg" alt="Tutor: Samuel Kamau" width="120" height="120">
                        <h4>Samuel Kamau</h4>
                        <p class="tutor-languages">Luo, Swahili, English</p>
                        <div class="tutor-rating">
                            <span class="stars">★★★★☆</span>
                            <span class="rating-count">(29 reviews)</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

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
                <td><a href="home.php">Home </a> <br>
                    <a href="contact.php">Contact Us </a> <br>
                    <a href="about.php">About Us </a> <br>
                    <a href="connect.php">Connect </a> <br> 
                    <a href="menu.php">Main menu </a> <br>
                    <a href="login.php">Subscribe now </a></td>
                <td><div class="social-links">
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
                </div></td>
                <td><a href="#" class="download-link">Vocabulary Lists</a><br>
                    <a href="#" class="download-link">Practice Worksheets</a><br>
                    <a href="#" class="download-link">Mobile App</a></td>
              </tr>
              <tr>
                <td>-</td>
                <td>Contact us to read about terms and conditions.</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
        <p>&copy; 2025 Learn Lugha ya Kiswahili na Kingereza (English)</p>
        <p>This site uses cookies from Google. <a href="https://support.google.com/chrome/answer/95647hl=en&co=GENIE.Platform%3DAndroid">Read more</a></p>
    </footer>

    <script src="function1.js"></script>
    <script src="practice.js"></script>
</body>
</html>