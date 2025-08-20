// Project Card Click Handler
document.addEventListener('DOMContentLoaded', function() {
    // Function to handle project card clicks
    function handleProjectCardClick(event) {
        const projectCard = event.currentTarget;
        
        // Don't trigger if clicking on interactive elements
        if (event.target.closest('input[type="checkbox"], button, a, .btn, .no-click')) {
            return;
        }
        
        // Get project data
        const checkbox = projectCard.querySelector('.project-checkbox');
        if (!checkbox) return;
        
        const projectId = checkbox.dataset.projectId;
        const projectName = checkbox.dataset.projectName || 'Project';
        
        // Uncheck all checkboxes first
        document.querySelectorAll('.project-checkbox').forEach(cb => {
            cb.checked = false;
        });
        
        // Check this project's checkbox
        checkbox.checked = true;
        checkbox.dispatchEvent(new Event('change'));
        
        // Open track record
        if (window.openMultipleTrackRecordModal) {
            window.openMultipleTrackRecordModal([{
                id: projectId,
                name: projectName,
                budget: 0,
                start_date: '',
                end_date: ''
            }]);
        }
    }
    
    // Add click handlers to all project cards
    function initializeProjectCardClicks() {
        document.querySelectorAll('.project-card').forEach(card => {
            // Remove any existing click handlers to prevent duplicates
            card.removeEventListener('click', handleProjectCardClick);
            // Add new click handler
            card.addEventListener('click', handleProjectCardClick);
        });
    }
    
    // Initialize on page load
    initializeProjectCardClicks();
    
    // Re-initialize when projects are loaded dynamically
    const observer = new MutationObserver(initializeProjectCardClicks);
    observer.observe(document.body, { childList: true, subtree: true });
});
