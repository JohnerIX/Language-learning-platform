/**
 * Learn Lugha - Audio Progress Tracking System
 * This script manages audio playback progress tracking and UI updates
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize audio tracking system
    const AudioProgressTracker = {
        // Store all audio elements and their progress data
        audioTracks: [],
        
        // Overall lesson progress
        totalTracks: 0,
        completedTracks: 0,
        
        // Constants
        COMPLETION_THRESHOLD: 0.9, // 90% listened is considered complete
        
        // Initialize the tracking system
        init: function() {
            // Find all audio elements on the page
            const audioElements = document.querySelectorAll('audio');
            this.totalTracks = audioElements.length;
            
            // Setup progress tracking for each audio element
            audioElements.forEach((audio, index) => {
                // Create a unique identifier for this audio
                const audioId = audio.id || `audio-track-${index}`;
                audio.id = audioId;
                
                // Get or create track data
                const trackData = this.getTrackData(audioId);
                
                // Create custom audio player UI
                this.createCustomPlayer(audio, trackData);
                
                // Add to tracked audio list
                this.audioTracks.push({
                    element: audio,
                    id: audioId,
                    duration: audio.duration || 0,
                    currentTime: trackData.currentTime || 0,
                    progress: trackData.progress || 0,
                    completed: trackData.completed || false,
                    status: trackData.status || 'not-started'
                });
                
                // Setup event listeners
                this.setupAudioEventListeners(audio, index);
            });
            
            // Create the overall progress tracker UI
            this.createOverallProgressUI();
            
            // Update all progress displays
            this.updateAllProgress();
        },
        
        // Get saved track data from localStorage or return default values
        getTrackData: function(audioId) {
            const savedData = localStorage.getItem(`lugha-audio-${audioId}`);
            
            if (savedData) {
                return JSON.parse(savedData);
            }
            
            return {
                currentTime: 0,
                progress: 0,
                completed: false,
                status: 'not-started'
            };
        },
        
        // Save track data to localStorage
        saveTrackData: function(audioId, data) {
            localStorage.setItem(`lugha-audio-${audioId}`, JSON.stringify(data));
        },
        
        // Create custom audio player UI for an audio element
        createCustomPlayer: function(audio, trackData) {
            // Create container elements for the custom player
            const container = document.createElement('div');
            container.className = 'audio-player-container';
            container.id = `player-${audio.id}`;
            
            // Get audio metadata
            const audioTitle = audio.dataset.title || 'Audio Track';
            const audioDuration = audio.duration || 0;
            
            // Create HTML structure for the player
            container.innerHTML = `
                <div class="audio-player">
                    <div class="audio-info">
                        <span class="audio-title">${audioTitle}</span>
                        <span class="audio-duration">
                            <span class="current-time">0:00</span> / 
                            <span class="total-time">0:00</span>
                        </span>
                    </div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: ${trackData.progress * 100}%"></div>
                        <div class="progress-dot"></div>
                    </div>
                    <div class="audio-controls">
                        <button class="play-pause-btn">
                            <i class="play-icon">▶</i>
                            <i class="pause-icon" style="display:none;">⏸</i>
                        </button>
                        <div class="control-group">
                            <div class="volume-container">
                                <span class="volume-icon">🔊</span>
                                <input type="range" class="volume-slider" min="0" max="1" step="0.01" value="1">
                            </div>
                            <select class="speed-selector">
                                <option value="0.5">0.5x</option>
                                <option value="0.75">0.75x</option>
                                <option value="1" selected>1x</option>
                                <option value="1.25">1.25x</option>
                                <option value="1.5">1.5x</option>
                                <option value="2">2x</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
            
            // Insert the custom player after the audio element
            audio.parentNode.insertBefore(container, audio.nextSibling);
            
            // Hide the original audio element but keep it functional
            audio.style.display = 'none';
            
            // Set initial time if resuming
            if (trackData.currentTime > 0) {
                audio.currentTime = trackData.currentTime;
            }
        },
        
        // Setup event listeners for audio element and custom player controls
        setupAudioEventListeners: function(audio, index) {
            const container = document.getElementById(`player-${audio.id}`);
            const playPauseBtn = container.querySelector('.play-pause-btn');
            const playIcon = container.querySelector('.play-icon');
            const pauseIcon = container.querySelector('.pause-icon');
            const progressContainer = container.querySelector('.progress-container');
            const progressBar = container.querySelector('.progress-bar');
            const progressDot = container.querySelector('.progress-dot');
            const currentTimeDisplay = container.querySelector('.current-time');
            const totalTimeDisplay = container.querySelector('.total-time');
            const volumeSlider = container.querySelector('.volume-slider');
            const speedSelector = container.querySelector('.speed-selector');
            
            // Update track object reference
            const trackIndex = index;
            
            // Play/Pause button
            playPauseBtn.addEventListener('click', () => {
                if (audio.paused) {
                    this.pauseAllAudio();
                    audio.play();
                    playIcon.style.display = 'none';
                    pauseIcon.style.display = 'block';
                } else {
                    audio.pause();
                    playIcon.style.display = 'block';
                    pauseIcon.style.display = 'none';
                }
            });
            
            // Progress bar click to seek
            progressContainer.addEventListener('click', (e) => {
                const rect = progressContainer.getBoundingClientRect();
                const clickPosition = (e.clientX - rect.left) / rect.width;
                audio.currentTime = clickPosition * audio.duration;
                
                // Update track data
                this.updateTrackProgress(trackIndex, audio.currentTime, audio.duration);
            });
            
            // Volume control
            volumeSlider.addEventListener('input', () => {
                audio.volume = volumeSlider.value;
            });
            
            // Playback speed
            speedSelector.addEventListener('change', () => {
                audio.playbackRate = parseFloat(speedSelector.value);
            });
            
            // Update progress as audio plays
            audio.addEventListener('timeupdate', () => {
                const progress = audio.currentTime / audio.duration;
                
                // Update UI
                progressBar.style.width = `${progress * 100}%`;
                progressDot.style.left = `${progress * 100}%`;
                currentTimeDisplay.textContent = this.formatTime(audio.currentTime);
                
                // Update track data
                this.updateTrackProgress(trackIndex, audio.currentTime, audio.duration);
            });
            
            // When duration is available, update display
            audio.addEventListener('loadedmetadata', () => {
                totalTimeDisplay.textContent = this.formatTime(audio.duration);
                this.audioTracks[trackIndex].duration = audio.duration;
                
                // Apply saved position if available
                const trackData = this.getTrackData(audio.id);
                if (trackData.currentTime > 0) {
                    audio.currentTime = trackData.currentTime;
                    progressBar.style.width = `${(trackData.currentTime / audio.duration) * 100}%`;
                    currentTimeDisplay.textContent = this.formatTime(trackData.currentTime);
                }
            });
            
            // Handle audio end
            audio.addEventListener('ended', () => {
                playIcon.style.display = 'block';
                pauseIcon.style.display = 'none';
                
                // Mark as completed
                this.markTrackCompleted(trackIndex);
            });
            
            // Handle play event
            audio.addEventListener('play', () => {
                // Update track status if it was not started
                const track = this.audioTracks[trackIndex];
                if (track.status === 'not-started') {
                    track.status = 'in-progress';
                    this.saveTrackStatus(trackIndex);
                    this.updateTrackListUI();
                }
            });
        },
        
        // Update track progress data and save to localStorage
        updateTrackProgress: function(trackIndex, currentTime, duration) {
            const track = this.audioTracks[trackIndex];
            const progress = duration > 0 ? currentTime / duration : 0;
            
            // Update track object
            track.currentTime = currentTime;
            track.progress = progress;
            
            // Check if track should be marked as completed
            if (progress >= this.COMPLETION_THRESHOLD && !track.completed) {
                this.markTrackCompleted(trackIndex);
            }
            
            // Save to localStorage
            this.saveTrackData(track.id, {
                currentTime: currentTime,
                progress: progress,
                completed: track.completed,
                status: track.status
            });
            
            // Update overall progress display
            this.updateOverallProgress();
        },
        
        // Mark a track as completed
        markTrackCompleted: function(trackIndex) {
            const track = this.audioTracks[trackIndex];
            
            if (!track.completed) {
                track.completed = true;
                track.status = 'completed';
                this.completedTracks++;
                
                // Save updated status
                this.saveTrackStatus(trackIndex);
                
                // Update UI
                this.updateTrackListUI();
                this.updateOverallProgress();
            }
        },
        
        // Save track status to localStorage
        saveTrackStatus: function(trackIndex) {
            const track = this.audioTracks[trackIndex];
            
            this.saveTrackData(track.id, {
                currentTime: track.currentTime,
                progress: track.progress,
                completed: track.completed,
                status: track.status
            });
        },
        
        // Create UI for tracking overall lesson progress
        createOverallProgressUI: function() {
            // Create container for overall progress
            const progressSection = document.createElement('section');
            progressSection.className = 'lesson-progress-container';
            
            // Build HTML structure
            progressSection.innerHTML = `
                <div class="lesson-progress-header">
                    <h3>Lesson Progress</h3>
                    <span class="overall-progress-stat">0%</span>
                </div>
                <div class="overall-progress-bar">
                    <div class="overall-progress-fill" style="width: 0%"></div>
                </div>
                <div class="audio-track-list">
                    <!-- Track items will be inserted here -->
                </div>
            `;
            
            // Insert at the top of the main content area

            
            // Insert between navigation and lessons table
            const naviElement = document.querySelector('nav.navi');
            const lessonsTable = document.querySelector('table.lessons');

            if (naviElement && lessonsTable) {
                // Place progressSection after navi and before lessons table
                lessonsTable.parentNode.insertBefore(progressSection, lessonsTable);
            } else if (naviElement) {
                // If only navi exists, place after navi
                naviElement.parentNode.insertBefore(progressSection, naviElement.nextSibling);
            } else if (lessonsTable) {
                // If only lessons table exists, place before lessons table
                lessonsTable.parentNode.insertBefore(progressSection, lessonsTable);
            } else {
                // Fallback: insert at the top of the main content area
                const mainContent = document.querySelector('main');
                if (mainContent) {
                    mainContent.insertBefore(progressSection, mainContent.firstChild);
                } else {
                    document.body.insertBefore(progressSection, document.body.firstChild);
                }
            }
            

            
        },
        
        
        // Create list items for each audio track
        createTrackListItems: function() {
            const trackList = document.querySelector('.audio-track-list');
            
            // Clear existing items
            trackList.innerHTML = '';
            
            // Create items for each track
            this.audioTracks.forEach((track, index) => {
                const trackItem = document.createElement('div');
                trackItem.className = 'audio-track-item';
                trackItem.dataset.trackId = track.id;
                
                // Get track title from data attribute or fallback
                const trackTitle = document.getElementById(track.id).dataset.title || `Track ${index + 1}`;
                
                // Status icon class based on track status
                let statusClass = 'status-not-started';
                let statusIcon = '';
                
                if (track.status === 'completed') {
                    statusClass = 'status-completed';
                    statusIcon = '✓';
                } else if (track.status === 'in-progress') {
                    statusClass = 'status-in-progress';
                }
                
                // HTML structure for track item
                trackItem.innerHTML = `
                    <div class="audio-track-status ${statusClass}">${statusIcon}</div>
                    <div class="audio-track-info">
                        <div class="audio-track-title">${trackTitle}</div>
                        <div class="audio-track-duration">${this.formatTime(track.duration)}</div>
                    </div>
                    <div class="audio-track-progress-container">
                        <div class="audio-track-progress-bar" style="width: ${track.progress * 100}%"></div>
                    </div>
                `;
                
                // Add click event to jump to track
                trackItem.addEventListener('click', () => {
                    this.scrollToTrack(track.id);
                });
                
                trackList.appendChild(trackItem);
            });
        },
        
        // Update the track list UI based on current progress
        updateTrackListUI: function() {
            this.audioTracks.forEach((track) => {
                const trackItem = document.querySelector(`.audio-track-item[data-track-id="${track.id}"]`);
                if (!trackItem) return;
                
                // Update progress bar
                const progressBar = trackItem.querySelector('.audio-track-progress-bar');
                progressBar.style.width = `${track.progress * 100}%`;
                
                // Update status icon
                const statusIcon = trackItem.querySelector('.audio-track-status');
                statusIcon.className = `audio-track-status`;
                statusIcon.textContent = '';
                
                if (track.status === 'completed') {
                    statusIcon.classList.add('status-completed');
                    statusIcon.textContent = '✓';
                } else if (track.status === 'in-progress') {
                    statusIcon.classList.add('status-in-progress');
                } else {
                    statusIcon.classList.add('status-not-started');
                }
            });
        },
        
        // Update the overall progress display
        updateOverallProgress: function() {
            if (this.totalTracks === 0) return;
            
            // Calculate overall progress percentage
            let overallProgress = 0;
            
            // Option 1: Based on completed tracks
            if (this.completedTracks > 0) {
                overallProgress = (this.completedTracks / this.totalTracks) * 100;
            } else {
                // Option 2: Based on average progress of all tracks
                const totalProgress = this.audioTracks.reduce((sum, track) => sum + track.progress, 0);
                overallProgress = (totalProgress / this.totalTracks) * 100;
            }
            
            // Update the progress bar and text
            const progressBar = document.querySelector('.overall-progress-fill');
            const progressStat = document.querySelector('.overall-progress-stat');
            
            if (progressBar && progressStat) {
                progressBar.style.width = `${overallProgress}%`;
                progressStat.textContent = `${Math.round(overallProgress)}%`;
            }
        },
        
        // Update all progress displays
        updateAllProgress: function() {
            this.updateTrackListUI();
            this.updateOverallProgress();
        },
        
        // Scroll to a specific audio track
        scrollToTrack: function(trackId) {
            const playerContainer = document.getElementById(`player-${trackId}`);
            if (playerContainer) {
                playerContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        },
        
        // Format seconds to MM:SS
        formatTime: function(seconds) {
            if (isNaN(seconds) || !isFinite(seconds)) return '0:00';
            
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = Math.floor(seconds % 60);
            return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
        },
        
        // Pause all audio elements
        pauseAllAudio: function() {
            this.audioTracks.forEach(track => {
                if (!track.element.paused) {
                    track.element.pause();
                    
                    // Update UI controls
                    const container = document.getElementById(`player-${track.id}`);
                    const playIcon = container.querySelector('.play-icon');
                    const pauseIcon = container.querySelector('.pause-icon');
                    
                    playIcon.style.display = 'block';
                    pauseIcon.style.display = 'none';
                }
            });
        },
        
        // Reset progress for all tracks
        resetAllProgress: function() {
            if (confirm('Are you sure you want to reset all audio progress?')) {
                this.audioTracks.forEach(track => {
                    // Reset track data
                    track.currentTime = 0;
                    track.progress = 0;
                    track.completed = false;
                    track.status = 'not-started';
                    track.element.currentTime = 0;
                    
                    // Clear localStorage
                    localStorage.removeItem(`lugha-audio-${track.id}`);
                });
                
                // Reset counters
                this.completedTracks = 0;
                
                // Update UI
                this.updateAllProgress();
                this.createTrackListItems();
                
                // Reset progress bars in custom players
                document.querySelectorAll('.progress-bar').forEach(bar => {
                    bar.style.width = '0%';
                });
                
                // Reset time displays
                document.querySelectorAll('.current-time').forEach(display => {
                    display.textContent = '0:00';
                });
                
                alert('All progress has been reset.');
            }
        }
    };
    
    // Add a reset button to the lesson progress container
    function addResetButton() {
        const header = document.querySelector('.lesson-progress-header');
        if (header) {
            const resetBtn = document.createElement('button');
            resetBtn.textContent = 'Reset Progress';
            resetBtn.className = 'reset-progress-btn';
            resetBtn.style.cssText = `
                background-color: #e74c3c;
                color: white;
                border: none;
                border-radius: 4px;
                padding: 5px 10px;
                font-size: 0.8rem;
                cursor: pointer;
                margin-left: 10px;
            `;
            
            resetBtn.addEventListener('click', function() {
                AudioProgressTracker.resetAllProgress();
            });
            
            header.appendChild(resetBtn);
        }
    }
    
    // Check if the page is a lessons page (has audio elements)
    if (document.querySelectorAll('audio').length > 0) {
        // Initialize the audio progress tracker
        setTimeout(() => {
            AudioProgressTracker.init();
            addResetButton();
        }, 500); // Small delay to ensure audio metadata is loaded
    }
});