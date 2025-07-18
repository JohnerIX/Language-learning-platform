<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Language Learning Platform for East African Languages">
    <title>Learn Lugha - Language Learning Platform</title>
    <link rel="stylesheet" href="casc.css">
    <link rel="stylesheet" href="cascade.css">
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <nav class="navi">
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
                    <a href="lessons.php">Luganda lessons</a>
                    <a href="lessons1.php">Runya-kitala lessons</a>
                    <a href="lessons2.php">Luo lessons</a>
                    <a href="home.html#foot">Follow Us</a>
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
   
    <table class="lessons" id="communication">
      <caption>Communication Basics</caption>
    <thead>
      <tr>
        <th>Eng-Luganda</th>
        <th>English explanation</th>
        <th>Swahili-Eng-Luganda</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/name1.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td> <p>Name- jina (swahili) erinnya (Luganda) <br> My name is Juma- Jina langu ni Juma <br>Erinnya lyange nze Juma </p></td>
        <td>
          <audio controls>
            <source src="Audios/myname.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/country1.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          <audio controls>
        </td>
        <td> <p>Country- Nchi (Swahili)  Ensi (Luganda) <br> Which country are you coming from? <br> Unatoka nchi gani <br> Oviira munsi ki? </p></td>
        <td>
          <audio controls>
            <source src="Audios/country.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/age1.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Age- miaka (SWahili) emyaka (Luganda) <br> How old are you? <br> Una miaka mingapi? <br> Olina emyaka emeka?</p></td>
        <td>
          <audio controls>
            <source src="Audios/age.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/welcome1.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Welcome- Karibu (Swahili) Nkwanirizza (Luganda)<br>You're welcome to my house <br> Karibu nyumbani <br> Nkwanirizza mumaka gange</p></td>
        <td>
          <audio controls>
            <source src="Audios/welcome.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/man1.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
            </audio>
        </td>
        <td><p>A man- mwanaume (Swahili) omusajja (Luganda) <br> I am a man- Mimi ni mwanaume <br> ndi musajja</p></td>
        <td>
          <audio controls>
            <source src="Audios/man.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr><tr>
        <td>
          <audio controls>
            <source src="Audios/woman1.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>A woman- mwanamke (Swahili) omukyala (Luganda) <br> She is a woman- Yeye ni mwanamke <br> Ye mukazi</p></td>
        <td>
          <audio controls>
            <source src="Audios/woman.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/teacher1.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>A teacher- mwalimu (Swahili) omusomesa (Luganda) <br>A student- mwanafunzi (Swahili) omuyizi (Luganda) <br> I am a teacher- Mimi ni mwalimu <br> ndi musomesa</p></td>
        <td>
          <audio controls>
            <source src="Audios/taecher.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/me1.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Me- Mimi (Swahili) nze (Luganda) <br> You- wewe (Swahili) ggwe (Luganda) <br>Him/ her- Yeye (Swahili) oli (Luganda) </p></td>
        <td>
          <audio controls>
            <source src="Audios/meyou.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/work1.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
        <td>
          <audio controls>
            <source src="Audios/workat.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/religion1.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>A christian- mkristu (Swahili) omukristaayo (Luganda) <br> I am a christian- mimi ni mkristu <br>Ndi mukristaayo <br> Muslim- mwislam (Swahili) musilaamu (Luganda)</p></td>
        <td>
          <audio controls>
            <source src="Audios/religion.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/goodbye1.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
        <td>
          <audio controls>
            <source src="Audios/goodbye.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/th.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr><tr>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
      </tr>
      <tr>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </td>
        <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
        <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
      </tr>
    </tbody>
  </table>
  <table class="lessons" id="numbers">
    <caption>Numbers and counting</caption>
  <thead>
    <tr>
      <th>Eng-Luganda</th>
      <th>English explanation</th>
      <th>Swahili-Luganda</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>
        <audio controls>
          <source src="Audios/asante.m4a" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
      <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
      <td>
        <audio controls>
          <source src="Audios/asante.m4a" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
    </tr>
    <tr>
      <td>
        <audio controls>
          <source src="Audios/asante.m4a" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
      <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
      <td>
        <audio controls>
          <source src="Audios/asante.m4a" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
    </tr>
    <tr>
      <td>
        <audio controls>
          <source src="Audios/asante.m4a" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
      <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
      <td>
        <audio controls>
          <source src="Audios/asante.m4a" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
    </tr>
    <tr>
      <td>
        <audio controls>
          <source src="Audios/asante.m4a" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
      <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
      <td>
        <audio controls>
          <source src="Audios/asante.m4a" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
    </tr>
    
    <tr>
      <td>
        <audio controls>
          <source src="Audios/asante.m4a" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
      <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
      <td>
        <audio controls>
          <source src="Audios/asante.m4a" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
    </tr><tr>
      <td>
        <audio controls>
          <source src="Audios/asante.m4a" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
      <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
      <td>
        <audio controls>
          <source src="Audios/asante.m4a" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
    </tr>
    <tr>
      <td>
        <audio controls>
          <source src="Audios/asante.m4a" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
      <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
      <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    </tr>
    <tr>
      <td>
        <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
      </td>
      <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
      <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    </tr>
    <tr>
      <td>
        <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
      </td>
      <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
      <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    </tr>
    <tr>
      <td>
        <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
      </td>
      <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
      <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    </tr>
    <tr>
      <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
      <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
      <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    </tr>
  </tbody>
</table>

<table class="lessons" id="business">
  <caption>Business and trade</caption>
<thead>
  <tr>
    <th>Eng-Luganda</th>
    <th>English explanation</th>
    <th>Swahili-Luganda</th>
  </tr>
</thead>
<tbody>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr><tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
</tbody>
</table>

<table class="lessons" id="family">
  <caption>Family terms</caption>
<thead>
  <tr>
    <th>Eng-Luganda</th>
    <th>English explanation</th>
    <th>Swahili-Luganda</th>
  </tr>
</thead>
<tbody>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr><tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
</tbody>
</table>

<table class="lessons" id="body">
  <caption>Body Parts</caption>
<thead>
  <tr>
    <th>Eng-Luganda</th>
    <th>English explanation</th>
    <th>Swahili-Luganda</th>
  </tr>
</thead>
<tbody>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr><tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
</tbody>
</table>

<table class="lessons" id="living">
  <caption>Home & Living</caption>
<thead>
  <tr>
    <th>Eng-Luganda</th>
    <th>English explanation</th>
    <th>Swahili-Luganda</th>
  </tr>
</thead>
<tbody>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr><tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
</tbody>
</table>

<table class="lessons" id="weather">
  <caption>Weather terms</caption>
<thead>
  <tr>
    <th>Eng-Luganda</th>
    <th>English explanation</th>
    <th>Swahili-Luganda</th>
  </tr>
</thead>
<tbody>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr><tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
</tbody>
</table>

<table class="lessons" id="travel">
  <caption>Travel and Tourism</caption>
<thead>
  <tr>
    <th>Eng-Luganda</th>
    <th>English explanation</th>
    <th>Swahili-Luganda</th>
  </tr>
</thead>
<tbody>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr><tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
</tbody>
</table>

<table class="lessons" id="greetings">
  <caption>Greeting terms</caption>
<thead>
  <tr>
    <th>Eng-Luganda</th>
    <th>English explanation</th>
    <th>Swahili-Luganda- English</th>
  </tr>
</thead>
<tbody>
  <tr>
    <td>
      <audio controls>
        <source src="Audios/goodmorning1.m4a" type="audio/mpeg">
        Your browser does not support the audio element.
      </audio>
    </td>
    <td>Good morning <br> Habari z'asubuhi</td>
    <td>
      <audio controls class="audio-player">
        <source src="Audios/goodmorning.m4a" type="audio/mpeg">
        Your browser does not support the audio element.
      </audio>
    </td>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr><tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
</tbody>
</table>

<table class="lessons" id="food">
  <caption>Food and fruits</caption>
<thead>
  <tr>
    <th>Eng-Luganda</th>
    <th>English explanation</th>
    <th>Swahili-Luganda</th>
  </tr>
</thead>
<tbody>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr><tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
</tbody>
</table>

<table class="lessons" id="education">
  <caption>Education terms</caption>
<thead>
  <tr>
    <th>Eng-Luganda</th>
    <th>English explanation</th>
    <th>Swahili-Luganda</th>
  </tr>
</thead>
<tbody>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr><tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
</tbody>
</table>

<table class="lessons" id="calendar">
  <caption>Calendar and time</caption>
<thead>
  <tr>
    <th>Eng-Luganda</th>
    <th>English explanation</th>
    <th>Swahili-Luganda</th>
  </tr>
</thead>
<tbody>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr><tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
  <tr>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
    <td><p>Woking- kufanya kazi (Swahili) okukola (Luganda) <br>I work at .... Nafanya kazi kwa... <br> nkolera ku....</p></td>
    <td>
          <audio controls>
            <source src="Audios/asante.m4a" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
  </tr>
</tbody>
</table>

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
  <p>&copy; 2025 Learn Lugha ya Kiswahili na Kingereza (English)</p>
  <p>This site uses cookies from Google. <a href="https://support.google.com/chrome/answer/95647hl=en&co=GENIE.Platform%3DAndroid">Read more</a></p>
</footer>

<script src="function1.js"></script>
<script src="contact.js"></script>
<script src="audio-progress.js"></script>
</body>
</html>