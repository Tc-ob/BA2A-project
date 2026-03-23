function setTheme(theme) {
    // Remove all themes
    document.body.classList.remove('theme-dark', 'theme-light', 'theme-sky');

    // Apply new theme
    if (theme !== 'dark') {
        document.body.classList.add('theme-' + theme);
    }

    // Update active button state if they exist on the page
    document.querySelectorAll('.theme-btn').forEach(btn => btn.classList.remove('active'));
    const activeBtn = document.getElementById('btn-' + theme);
    if (activeBtn) activeBtn.classList.add('active');

    // Persist selection
    localStorage.setItem('aurora-theme', theme);
}

// Immediately apply theme to prevent FOUC
(function() {
    const saved = localStorage.getItem('aurora-theme') || 'dark';
    setTheme(saved);
})();

// Wait for DOM to attach listeners
document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('aurora-theme') || 'dark';
    const activeBtn = document.getElementById('btn-' + saved);
    if (activeBtn) activeBtn.classList.add('active');
});
