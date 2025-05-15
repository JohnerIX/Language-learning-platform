// server.js - Main API server file
const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');
const multer = require('multer');
const path = require('path');
const fs = require('fs');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.static('public')); // For serving static files

// Configure multer for file uploads
const storage = multer.diskStorage({
    destination: function (req, file, cb) {
        const uploadDir = path.join(__dirname, 'public/uploads');
        // Create directory if it doesn't exist
        if (!fs.existsSync(uploadDir)) {
            fs.mkdirSync(uploadDir, { recursive: true });
        }
        cb(null, uploadDir);
    },
    filename: function (req, file, cb) {
        // Use timestamp + original name to avoid conflicts
        cb(null, Date.now() + '-' + file.originalname);
    }
});

const upload = multer({ 
    storage: storage,
    limits: { fileSize: 5 * 1024 * 1024 }, // 5MB limit
    fileFilter: function (req, file, cb) {
        // Accept images only
        if (!file.originalname.match(/\.(jpg|jpeg|png|gif)$/)) {
            return cb(new Error('Only image files are allowed!'), false);
        }
        cb(null, true);
    }
});

// Database connection pool
const pool = mysql.createPool({
    host: 'localhost',
    user: 'root', // Change to your MySQL username
    password: 'password', // Change to your MySQL password
    database: 'user-data',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

// Test database connection
app.get('/api/test', async (req, res) => {
    try {
        const connection = await pool.getConnection();
        connection.release();
        res.json({ message: 'Database connection successful' });
    } catch (error) {
        console.error('Database connection failed:', error);
        res.status(500).json({ error: 'Database connection failed' });
    }
});

// GET user profile data
app.get('/api/profile/:userId', async (req, res) => {
    try {
        const userId = req.params.userId;
        const connection = await pool.getConnection();
        
        // Get user basic info
        const [userRows] = await connection.execute(
            'SELECT * FROM users WHERE id = ?',
            [userId]
        );
        
        if (userRows.length === 0) {
            connection.release();
            return res.status(404).json({ message: 'User not found' });
        }
        
        const user = userRows[0];
        
        // Get user stats
        const [statsRows] = await connection.execute(
            'SELECT * FROM user_stats WHERE user_id = ?',
            [userId]
        );
        
        const stats = statsRows.length > 0 ? statsRows[0] : null;
        
        // Get user subscription
        const [subscriptionRows] = await connection.execute(
            'SELECT * FROM subscriptions WHERE user_id = ?',
            [userId]
        );
        
        const subscription = subscriptionRows.length > 0 ? subscriptionRows[0] : null;
        
        // Get user languages
        const [languageRows] = await connection.execute(
            'SELECT l.name, ul.progress FROM user_languages ul JOIN languages l ON ul.language_id = l.id WHERE ul.user_id = ?',
            [userId]
        );
        
        // Get user achievements
        const [achievementRows] = await connection.execute(
            'SELECT a.name, a.icon, ua.earned_date FROM user_achievements ua JOIN achievements a ON ua.achievement_id = a.id WHERE ua.user_id = ?',
            [userId]
        );
        
        // Get user activities
        const [activityRows] = await connection.execute(
            'SELECT * FROM user_activities WHERE user_id = ? ORDER BY activity_date DESC LIMIT 5',
            [userId]
        );
        
        // Get user courses
        const [courseRows] = await connection.execute(
            'SELECT c.title, c.code, uc.progress FROM user_courses uc JOIN courses c ON uc.course_id = c.id WHERE uc.user_id = ?',
            [userId]
        );
        
        connection.release();
        
        res.json({
            user,
            stats,
            subscription,
            languages: languageRows,
            achievements: achievementRows,
            activities: activityRows,
            courses: courseRows
        });
        
    } catch (error) {
        console.error('Error fetching user profile:', error);
        res.status(500).json({ error: 'Error fetching user profile data' });
    }
});

// UPDATE user profile information
app.put('/api/profile/:userId', async (req, res) => {
    try {
        const userId = req.params.userId;
        const { firstName, lastName, email, phone, country, city, username } = req.body;
        
        const connection = await pool.getConnection();
        
        // Update user information
        await connection.execute(
            'UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, country = ?, city = ?, username = ? WHERE id = ?',
            [firstName, lastName, email, phone, country, city, username, userId]
        );
        
        connection.release();
        
        res.json({ success: true, message: 'Profile updated successfully' });
        
    } catch (error) {
        console.error('Error updating user profile:', error);
        res.status(500).json({ error: 'Error updating user profile data' });
    }
});

// UPDATE user profile picture
app.post('/api/profile/:userId/picture', upload.single('profilePicture'), async (req, res) => {
    try {
        const userId = req.params.userId;
        
        if (!req.file) {
            return res.status(400).json({ error: 'No file uploaded' });
        }
        
        const imagePath = `/uploads/${req.file.filename}`;
        
        const connection = await pool.getConnection();
        
        // Update user profile picture path
        await connection.execute(
            'UPDATE users SET profile_picture = ? WHERE id = ?',
            [imagePath, userId]
        );
        
        connection.release();
        
        res.json({ success: true, message: 'Profile picture updated successfully', imagePath });
        
    } catch (error) {
        console.error('Error updating profile picture:', error);
        res.status(500).json({ error: 'Error updating profile picture' });
    }
});

// Start the server
app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});