<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Contact Learn Lugha - Language Learning Platform for East African Languages">
    <title>Contact Us - Learn Lugha</title>
    <link rel="stylesheet" href="casc.css">
    <link rel="stylesheet" href="cascade.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

    <div class="page-header">
        <div class="container">
            <h1>Contact Us</h1>
            <p>We'd love to hear from you! Get in touch with our team for inquiries, support, or partnerships.</p>
            <p>Okumanya ebisingawo kubitukwatako, weyambise endagiriro zino wammanga</p>
            <p></p>
        </div>
    </div>

    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Information -->
                <div class="contact-info animate-on-scroll">
                    <h2><i class="fas fa-address-card"></i> Contact Information</h2>
                    <p>Feel free to reach out to us through any of the following channels:</p>
                    
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Our Location</h3>
                            <p>15 Kampala Road, Floor 3</p>
                            <p>Kampala, Uganda</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-phone-alt"></i>
                        <div>
                            <h3>Phone Numbers</h3>
                            <p>Main: +256 700 461 140</p>
                            <p>Support: +256 773 855 888</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email Addresses</h3>
                            <p>General Inquiries: <a href="mailto:info@learnlugha.com">info@learnlugha.com</a></p>
                            <p>Support: <a href="mailto:support@learnlugha.com">support@learnlugha.com</a></p>
                            <p>Partnerships: <a href="mailto:partners@learnlugha.com">partners@learnlugha.com</a></p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h3>Operating Hours</h3>
                            <p>Monday to Friday: 8:00 AM - 6:00 PM (EAT)</p>
                            <p>Saturday: 9:00 AM - 2:00 PM (EAT)</p>
                            <p>Sunday: Closed</p>
                        </div>
                    </div>
                    
                    <div class="social-contact">
                        <h3>Connect With Us</h3>
                        <div class="social-icons">
                            <a href="https://www.facebook.com/jonahkersoxhi" aria-label="Facebook" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://www.twitter.com/JxJohner" aria-label="Twitter" class="social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="https://www.instagram.com/jx_joka" aria-label="Instagram" class="social-icon"><i class="fab fa-instagram"></i></a>
                            <a href="https://www.youtube.com/@johnerkasozi" aria-label="YouTube" class="social-icon"><i class="fab fa-youtube"></i></a>
                            <a href="https://wa.me/+256700461140" aria-label="WhatsApp" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="contact-form-container animate-on-scroll">
                    <h2><i class="fas fa-paper-plane"></i> Send Us a Message</h2>
                    <p>Fill out the form below and we'll get back to you as soon as possible.</p>
                    
                    <form id="contactForm" class="contact-form" action="process_contact.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <div class="form-group">
                            <label for="name">Full Name <span class="required">*</span></label>
                            <input type="text" id="name" name="name" placeholder="Your full name" required>
                            <div class="error-message" id="nameError"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address <span class="required">*</span></label>
                            <input type="email" id="email" name="email" placeholder="Your email address" required>
                            <div class="error-message" id="emailError"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="Your phone number (optional)">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject <span class="required">*</span></label>
                            <select id="subject" name="subject" required>
                                <option value="" disabled selected>Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="support">Technical Support</option>
                                <option value="billing">Billing Question</option>
                                <option value="partnership">Partnership Opportunity</option>
                                <option value="feedback">Feedback</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="error-message" id="subjectError"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message <span class="required">*</span></label>
                            <textarea id="message" name="message" rows="5" placeholder="Type your message here..." required></textarea>
                            <div class="error-message" id="messageError"></div>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="newsletter" name="newsletter">
                            <label for="newsletter">Subscribe to our newsletter</label>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">I agree to the <a href="#" class="terms-link">terms and conditions</a> <span class="required">*</span></label>
                            <div class="error-message" id="termsError"></div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="submit-btn">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                            <button type="reset" class="reset-btn">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                        
                        <div id="formStatus" class="form-status"></div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <h2 class="section-title animate-on-scroll">Frequently Asked Questions</h2>
            
            <div class="accordion">
                <div class="accordion-item animate-on-scroll">
                    <div class="accordion-header">
                        <h3>How quickly can I expect a response?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="accordion-content">
                        <p>We aim to respond to all inquiries within 24 hours during business days. For urgent matters, we recommend calling our support line directly.</p>
                    </div>
                </div>
                
                <div class="accordion-item animate-on-scroll">
                    <div class="accordion-header">
                        <h3>Do you offer language training for businesses?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="accordion-content">
                        <p>Yes, we offer specialized corporate language training packages. Please contact our partnerships team for more information about custom solutions for your organization.</p>
                    </div>
                </div>
                
                <div class="accordion-item animate-on-scroll">
                    <div class="accordion-header">
                        <h3>I'm having technical issues with the platform. Who should I contact?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="accordion-content">
                        <p>For technical support, please email support@learnlugha.com with details of the issue you're experiencing. Screenshots are helpful. You can also use the form above and select "Technical Support" as the subject.</p>
                    </div>
                </div>
                
                <div class="accordion-item animate-on-scroll">
                    <div class="accordion-header">
                        <h3>Can I visit your office in person?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="accordion-content">
                        <p>Yes, visitors are welcome during our operating hours. We recommend scheduling an appointment beforehand to ensure the relevant team members are available to assist you.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Map Section -->
    <section class="map-section animate-on-scroll">
        <div class="container">
            <h2 class="map-title">Follow this map to find Us</h2>
            <div class="map-container">
                <!-- Replace with actual Google Maps embed code -->
                <div class="map-placeholder">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.757736372429!2d32.5786913!3d0.313293!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x177dbb9f5e8f2efb%3A0x80226f246d191d96!2sKampala%20Rd%2C%20Kampala%2C%20Uganda!5e0!3m2!1sen!2sus!4v1650450098765!5m2!1sen!2sus" 
                        width="100%" 
                        height="450" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
                <div class="directions-link">
                    <a href="https://goo.gl/maps/R1WZ2VbXY5JGLjBY6" target="_blank" class="btn">
                        <i class="fas fa-directions"></i> Get Directions
                    </a>
                </div>
            </div>
        </div>
    </section>

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