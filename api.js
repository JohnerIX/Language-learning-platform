// api.js - Contains functions for API communication
const API_BASE_URL = 'http://localhost:3000/api';

// Function to get user profile data
async function fetchUserProfile(userId) {
    try {
        const response = await fetch(`${API_BASE_URL}/profile/${userId}`);
        if (!response.ok) {
            throw new Error('Failed to fetch profile data');
        }
        return await response.json();
    } catch (error) {
        console.error('Error fetching profile data:', error);
        throw error;
    }
}

// Function to update user profile
async function updateUserProfile(userId, profileData) {
    try {
        const response = await fetch(`${API_BASE_URL}/profile/${userId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(profileData)
        });
        if (!response.ok) {
            throw new Error('Failed to update profile data');
        }
        return await response.json();
    } catch (error) {
        console.error('Error updating profile data:', error);
        throw error;
    }
}

// Function to upload profile picture
async function uploadProfilePicture(userId, fileInput) {
    try {
        const formData = new FormData();
        formData.append('profilePicture', fileInput.files[0]);
        
        const response = await fetch(`${API_BASE_URL}/profile/${userId}/picture`, {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error('Failed to upload profile picture');
        }
        
        return await response.json();
    } catch (error) {
        console.error('Error uploading profile picture:', error);
        throw error;
    }
}

// profile.js - Updated JavaScript for the profile page
document.addEventListener('DOMContentLoaded', async function() {
    // For demonstration, hardcoding user ID as 1
    // In a real app, you'd get this from the session/login
    const userId = 1;
    
    try {
        // Load user profile data from API
        const profileData = await fetchUserProfile(userId);
        populateProfileData(profileData);
    } catch (error) {
        showError('Failed to load profile data. Please try again later.');
    }
    
    // Handle profile picture upload
    document.getElementById('profile-pic-upload').addEventListener('change', async function(e) {
        if (e.target.files && e.target.files[0]) {
            try {
                const result = await uploadProfilePicture(userId, this);
                document.getElementById('profile-picture').src = result.imagePath;
                showSuccess('Profile picture updated successfully!');
            } catch (error) {
                showError('Failed to upload profile picture. Please try again.');
            }
        }
    });
    
    // Other event listeners
    setupEventListeners(userId);
});

// Populate profile data from API response
function populateProfileData(data) {
    // Basic user info
    document.getElementById('profile-name').textContent = `${data.user.first_name} ${data.user.last_name}`;
    document.getElementById('profile-username').textContent = `@${data.user.username}`;
    document.getElementById('profile-picture').src = data.user.profile_picture;
    
    // Form fields
    document.getElementById('first-name').value = data.user.first_name;
    document.getElementById('last-name').value = data.user.last_name;
    document.getElementById('email').value = data.user.email;
    document.getElementById('phone').value = data.user.phone;
    document.getElementById('country').value = data.user.country;
    document.getElementById('city').value = data.user.city;
    document.getElementById('username').value = data.user.username;
    document.getElementById('preferred-language').value = data.user.interface_language;
    document.getElementById('notification-preference').value = data.user.notification_preference;
    
    // Stats
    if (data.stats) {
        document.getElementById('lessons-completed').textContent = data.stats.lessons_completed;
        document.getElementById('current-streak').textContent = data.stats.current_streak;
        document.getElementById('total-points').textContent = data.stats.total_points;
    }
    
    // Subscription
    if (data.subscription) {
        document.getElementById('subscription-type').textContent = data.subscription.type;
        document.getElementById('subscription-status').textContent = data.subscription.status;
        document.getElementById('renewal-date').textContent = formatDate(data.subscription.renewal_date);
    }
    
    // Languages
    const languagesContainer = document.querySelector('.sidebar-section:nth-of-type(1)');
    languagesContainer.innerHTML = '<h3>My Languages</h3>';
    
    data.languages.forEach(language => {
        const langHTML = `
            <div class="language-progress">
                <div class="language-name">
                    <span>${language.name}</span>
                    <span>${Math.round(language.progress)}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${language.progress}%"></div>
                </div>
            </div>
        `;
        languagesContainer.innerHTML += langHTML;
    });
    
    // Achievements
    const achievementsContainer = document.querySelector('.achievements');
    achievementsContainer.innerHTML = '';
    
    data.achievements.forEach(achievement => {
        const achievementHTML = `
            <div class="achievement">
                <div class="achievement-icon">${achievement.icon}</div>
                <div class="achievement-name">${achievement.name}</div>
            </div>
        `;
        achievementsContainer.innerHTML += achievementHTML;
    });
    
    // Activities
    const activitiesContainer = document.querySelector('.recent-activity');
    activitiesContainer.innerHTML = '';
    
    data.activities.forEach(activity => {
        const activityHTML = `
            <div class="activity-item">
                <div class="activity-icon">${activity.icon}</div>
                <div class="activity-details">
                    <div class="activity-description">${activity.description}</div>
                    <div class="activity-time">${formatDateTime(activity.activity_date)}</div>
                </div>
            </div>
        `;
        activitiesContainer.innerHTML += activityHTML;
    });
    
    // Courses
    const coursesContainer = document.querySelector('.profile-section:nth-of-type(3)');
    coursesContainer.innerHTML = '<h3>My Courses</h3>';
    
    data.courses.forEach(course => {
        const courseHTML = `
            <div class="course-item">
                <div class="course-icon">${course.code}</div>
                <div class="course-details">
                    <div class="course-title">${course.title}</div>
                    <div class="course-progress">Progress: ${Math.round(course.progress)}% complete</div>
                    <a href="lessons.html#${course.code.toLowerCase()}" class="course-continue">Continue Learning</a>
                </div>
            </div>
        `;
        coursesContainer.innerHTML += courseHTML;
    });
}

// Setup event listeners for interactive elements
function setupEventListeners(userId) {
    // Toggle edit mode
    let isEditMode = false;
    const editButton = document.querySelector('.edit-profile');
    
    function toggleEditMode() {
        isEditMode = !isEditMode;
        
        const formInputs = document.querySelectorAll('#profile-form .form-control');
        formInputs.forEach(input => {
            input.disabled = !isEditMode;
        });
        
        document.getElementById('edit-buttons').style.display = isEditMode ? 'block' : 'none';
        editButton.textContent = isEditMode ? 'Editing...' : 'Edit Profile';
        editButton.style.display = isEditMode ? 'none' : 'block';
    }
    
    editButton.addEventListener('click', toggleEditMode);
    
    // Save changes
    document.querySelector('.save-changes').addEventListener('click', async function() {
        try {
            const profileData = {
                firstName: document.getElementById('first-name').value,
                lastName: document.getElementById('last-name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                country: document.getElementById('country').value,
                city: document.getElementById('city').value,
                username: document.getElementById('username').value
            };
            
            await updateUserProfile(userId, profileData);
            
            // Update visible profile data
            document.getElementById('profile-name').textContent = `${profileData.firstName} ${profileData.lastName}`;
            document.getElementById('profile-username').textContent = `@${profileData.username}`;
            
            // Return to view mode
            toggleEditMode();
            
            showSuccess('Profile updated successfully!');
        } catch (error) {
            showError('Failed to update profile. Please try again.');
        }
    });
    
    // Cancel edit
    document.querySelector('.cancel-edit').addEventListener('click', function() {
        // Reset form by reloading data
        fetchUserProfile(userId).then(data => {
            document.getElementById('first-name').value = data.user.first_name;
            document.getElementById('last-name').value = data.user.last_name;
            document.getElementById('email').value = data.user.email;
            document.getElementById('phone').value = data.user.phone;
            document.getElementById('country').value = data.user.country;
            document.getElementById('city').value = data.user.city;
            document.getElementById('username').value = data.user.username;
        });
        
        // Return to view mode
        toggleEditMode();
    });
}

// Helper function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Helper function to format date and time
function formatDateTime(dateTimeString) {
    const date = new Date(dateTimeString);
    const now = new Date();
    const diffDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) {
        // Today
        return `Today, ${date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}`;
    } else if (diffDays === 1) {
        // Yesterday
        return `Yesterday, ${date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}`;
    } else {
        // Other days
        return formatDate(dateTimeString);
    }
}

// Show success message to user
function showSuccess(message) {
    alert(message); // In a real app, use a more elegant notification system
}

// Show error message to user
function showError(message) {
    alert(message); // In a real app, use a more elegant notification system
}