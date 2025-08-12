<!-- Enhanced Sidebar Navigation Component -->
<!-- Sidebar -->
<div id="sidebar" class="fixed left-0 top-0 h-full w-64 z-50 transform transition-transform duration-300 ease-in-out flex flex-col shadow-xl rounded-xl" style="background:rgb(10, 145, 100)">
    <!-- Logo/Brand Section -->
    <div class="p-4 border-b border-white/20">
        <div class="flex items-center space-x-3"
            style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);"
            >
            <a href="javascript:void(0);" onclick="toggleSidebarByLogo()" class="hover:scale-105 transition-all duration-200 flex-shrink-0" title="Toggle Navigation">
                @include('components.logo', ['size' => 'sm', 'background' => true])
            </a>
            <div class="flex-1 min-w-0"
                style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);">
                <a href="/dashboard" class="text-lg font-extrabold text-white drop-shadow tracking-wide hover:text-green-100 transition-colors duration-200 block truncate">Budget Control</a>
                <p class="text-xs text-green-100 opacity-80 truncate">{{ $pageTitle ?? 'Project Management' }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 px-6 py-4 overflow-y-auto">
        <nav class="space-y-3"
            style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);">
            <!-- Main Navigation Links -->
            <a href="/dashboard" class="sidebar-nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="/projects" class="sidebar-nav-item {{ request()->is('projects*') && !request()->is('projects/archive') && !request()->is('projects/recently-deleted') ? 'active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span>Projects</span>
            </a>

            <a href="/monthly-assignments" class="sidebar-nav-item {{ request()->is('monthly-assignments*') ? 'active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Teams</span>
            </a>

            <a href="/projects/archive" class="sidebar-nav-item {{ request()->is('projects/archive') ? 'active' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 8l6 6 6-6"></path>
                </svg>
                <span>Archive</span>
            </a>

            @if(auth()->user()->is_admin)
                <a href="/projects/recently-deleted" class="sidebar-nav-item {{ request()->is('projects/recently-deleted') ? 'active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>Recently Deleted</span>
                </a>
            @endif
        </nav>
    </div>

    <!-- User Account Section -->
    <div class="border-t border-white/20 p-6 mt-auto"
        style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);"
        >
        <!-- User Info -->
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-white font-semibold text-base mr-4">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1">
                <div class="font-semibold text-white text-sm">
                    {{ auth()->user()->is_admin ? 'Admin User' : auth()->user()->name }}
                </div>
                <div class="text-xs text-green-100 opacity-80">
                    {{ auth()->user()->is_admin ? 'Administrator' : 'User' }}
                </div>
            </div>
        </div>

        <!-- Account Actions -->
        <div class="space-y-2">
            <a href="#" class="sidebar-nav-item text-sm" onclick="showMyProfile()">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>My Profile</span>
            </a>

            <a href="#" class="sidebar-nav-item text-sm" onclick="showChangePassword()">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
                <span>Change Password</span>
            </a>

            @if(auth()->user()->is_admin)
                <a href="{{ route('users.index') }}" class="sidebar-nav-item text-sm">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <span>User Management</span>
                </a>

                <a href="{{ route('system-settings.index') }}" class="sidebar-nav-item text-sm">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>System Settings</span>
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="sidebar-nav-item text-sm w-full text-left">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Mobile Menu Button -->
<button id="mobileMenuBtn" class="fixed top-4 left-4 z-50 md:hidden bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-2 text-white">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<!-- Sidebar Toggle Button for Desktop -->
<button id="sidebarToggle" class="fixed top-4 left-4 z-50 hidden md:block bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-2 text-white">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<!-- Sidebar Overlay for Mobile -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>


<style>
/* Sidebar Navigation Styles */
.sidebar-nav-item {
    @apply flex items-center justify-start px-4 py-4 text-white font-medium transition-all duration-200 w-full;
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: flex-start !important;
    white-space: nowrap;
    min-height: 48px;
    line-height: 1.2;
    margin: 0 -24px;
    padding-left: 28px;
    padding-right: 28px;
    border-radius: 0;
}

.sidebar-nav-item {
    transition: transform 0.2s ease-in-out, background 0.2s, border-left 0.2s, padding-left 0.2s !important;
}

.sidebar-nav-item:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-4px) !important;
    border-left: 4px solid rgba(255, 255, 255, 0.8);
    padding-left: 24px;
}

/* Ensure perfect vertical centering */
.sidebar-nav-item * {
    vertical-align: middle;
}

.sidebar-nav-item.active {
    background: rgba(255, 255, 255, 0.25);
    border-left: 4px solid white;
    padding-left: 24px;
}

/* Ensure icons stay beside text */
.sidebar-nav-item svg {
    flex-shrink: 0;
    min-width: 20px;
    min-height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar-nav-item span {
    flex: 1;
    display: flex;
    align-items: center;
    line-height: 1.2;
}

/* Sidebar Layout */
#sidebar {
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

/* Mobile sidebar hidden by default */
@media (max-width: 768px) {
    #sidebar {
        transform: translateX(-100%);
    }

    #sidebar.open {
        transform: translateX(0);
    }
}

/* Main content adjustment */
body {
    @apply transition-all duration-300;
}

/* Ensure content doesn't overlap sidebar */
.main-content {
    @apply transition-all duration-300;
}

/* Remove duplicate rule - using the main .dropdown-menu rule below */

.dropdown-menu {
    position: absolute !important;
    top: calc(100% + 8px) !important;
    right: 0 !important;
    width: 280px !important;
    min-width: 280px !important;
    max-width: 280px !important;
    background: white !important;
    background-color: white !important;
    border-radius: 16px !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    border: 1px solid #e5e7eb !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    z-index: 50 !important;
    overflow: hidden !important;
    display: block !important;
    transform-origin: top right !important;
}

.dropdown-menu.hidden {
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
    transform: translateY(-8px) scale(0.95) !important;
}

.dropdown-menu.show {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    transform: translateY(0) scale(1) !important;
}

.dropdown-item {
    @apply flex items-center justify-start w-full text-gray-700 text-sm text-left border-none;
    text-decoration: none !important;
    background: white !important;
    background-color: white !important;
    display: flex !important;
    align-items: center !important;
    padding: 12px 20px !important;
    margin: 2px 8px !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
}

.dropdown-item:hover {
    background: #f8fafc !important;
    background-color: #f8fafc !important;
    transform: translateX(-4px) !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
    color: #1f2937 !important;
    transition: transform 0.2s ease-in-out, background 0.2s, box-shadow 0.2s, color 0.2s !important;
}

.dropdown-item:hover svg {
    color: #059669 !important;
    transform: scale(1.1) !important;
}

.dropdown-item:active {
    transform: translateX(2px) scale(0.98) !important;
}

.dropdown-divider {
    @apply border-t border-gray-200 my-2;
}

/* Mobile Navigation Styles */
.mobile-nav-item {
    @apply flex items-center w-full px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-150;
}

.mobile-nav-section {
    @apply space-y-2;
}

.mobile-nav-header {
    @apply font-semibold text-gray-800 px-4 py-2;
}

.mobile-nav-subitem {
    @apply block px-8 py-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors duration-150;
}
</style>

<script>
// Sidebar functionality
let sidebarOpen = true;

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const isMobile = window.innerWidth < 768;

    if (isMobile) {
        // Mobile behavior
        if (sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
            overlay.classList.add('hidden');
        } else {
            sidebar.classList.add('open');
            overlay.classList.remove('hidden');
        }
    } else {
        // Desktop behavior - toggle sidebar width
        const mainContent = document.getElementById('mainContent');
        if (sidebarOpen) {
            sidebar.style.transform = 'translateX(-256px)';
            if (mainContent) mainContent.style.marginLeft = '0';
        } else {
            sidebar.style.transform = 'translateX(0)';
            if (mainContent) mainContent.style.marginLeft = '256px';
        }
        sidebarOpen = !sidebarOpen;
    }
}

function toggleSidebarByLogo() {
    // Only trigger on desktop
    if (window.innerWidth >= 768) {
        toggleSidebar();
    } else {
        // On mobile, clicking logo navigates to dashboard
        window.location.href = '/dashboard';
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    sidebar.classList.remove('open');
    overlay.classList.add('hidden');
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', toggleSidebar);
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // Set initial main content margin for desktop
    if (window.innerWidth >= 768) {
        const mainContent = document.getElementById('mainContent');
        if (mainContent) mainContent.style.marginLeft = '256px';
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        const isMobile = window.innerWidth < 768;
        const sidebar = document.getElementById('sidebar');

        const mainContent = document.getElementById('mainContent');
        if (isMobile) {
            if (mainContent) mainContent.style.marginLeft = '0';
            sidebar.style.transform = '';
        } else {
            if (mainContent) mainContent.style.marginLeft = sidebarOpen ? '256px' : '24px';
            sidebar.classList.remove('open');
            document.getElementById('sidebarOverlay').classList.add('hidden');
        }
    });
});

// Keyboard navigation support
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeSidebar();
    }
});

// Account Settings Functions
function showMyProfile() {
    // Create and show account settings modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md relative animate-fadeInUp">
            <button onclick="this.closest('.fixed').remove()" class="absolute top-3 right-3 text-gray-600 hover:text-red-600 text-3xl font-bold hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200">&times;</button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                <span class="mr-3">⚙️</span> Account Settings
            </h2>
            <form class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Full Name</label>
                    <input type="text" value="{{ auth()->user()->name }}" class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-green-500" readonly>
                    <p class="text-xs text-gray-500 mt-1">Contact admin to change your name</p>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Email Address</label>
                    <input type="email" value="{{ auth()->user()->email }}" class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-green-500" readonly>
                    <p class="text-xs text-gray-500 mt-1">Contact admin to change your email</p>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Account Type</label>
                    <input type="text" value="{{ auth()->user()->is_admin ? 'Administrator' : 'Regular User' }}" class="w-full rounded-lg px-4 py-2 border border-gray-300 bg-gray-50" readonly>
                </div>
                <div class="flex justify-end pt-4">
                    <button type="button" onclick="this.closest('.fixed').remove()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                        Close
                    </button>
                </div>
            </form>
        </div>
    `;
    document.body.appendChild(modal);

    // Keyboard shortcut to close modal
    function closeModalOnEsc(event) {
        if (event.key === 'Escape') {
            modal.remove();
            document.removeEventListener('keydown', closeModalOnEsc);
        }
    }
    document.addEventListener('keydown', closeModalOnEsc);

    // Also clean up event if closed by button
    modal.querySelector('button').addEventListener('click', function() {
        document.removeEventListener('keydown', closeModalOnEsc);
    });
} 

function showChangePassword() {
    // Create and show change password modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md relative animate-fadeInUp">
            <button onclick="this.closest('.fixed').remove()" class="absolute top-3 right-3 text-gray-600 hover:text-red-600 text-3xl font-bold hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200">&times;</button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                <span class="mr-3">🔑</span> Change Password
            </h2>
            <form id="changePasswordForm" class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Current Password</label>
                    <input type="password" name="current_password" class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-green-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">New Password</label>
                    <input type="password" name="new_password" class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-green-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-green-500" required>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" id="cancelBtn" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn" class="relative bg-green-600 hover:bg-green-700 text-white px-8 py-2 rounded-lg font-semibold transition-colors flex items-center justify-center min-w-[120px]">
                        <span id="submitText">Update Password</span>
                        <span id="submitSpinner" class="hidden ml-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    `;
    document.body.appendChild(modal);

    // Keyboard shortcut to close modal
    function closeModalOnEsc(event) {
        if (event.key === 'Escape') {
            modal.remove();
            document.removeEventListener('keydown', closeModalOnEsc);
        }
    }
    document.addEventListener('keydown', closeModalOnEsc);

    // Also clean up event if closed by button
    modal.querySelector('button').addEventListener('click', function() {
        document.removeEventListener('keydown', closeModalOnEsc);
    });

    // Reusable toast notification function
    function showToast(message, type = 'info', duration = 5000) {
        const toast = document.createElement('div');
        const isSuccess = type === 'success';
        
        toast.className = `fixed top-6 right-6 z-50 ${isSuccess ? 'bg-green-600' : 'bg-red-600'} text-white px-6 py-4 rounded-xl shadow-2xl flex items-center space-x-3 animate-fadeInUp`;
        
        toast.innerHTML = `
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${isSuccess 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                }
            </svg>
            <span class="font-semibold">${isSuccess ? 'Success!' : 'Error'}</span>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" class="ml-3 text-white hover:opacity-75 focus:outline-none">&times;</button>
        `;
        
        document.body.appendChild(toast);
        
        // Auto-remove after duration
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.add('opacity-0', 'translate-x-8', 'transition-all', 'duration-300');
                setTimeout(() => toast.remove(), 300);
            }
        }, duration);
        
        return toast;
    }

    // Handle form submission
    document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('#submitBtn');
        const submitText = form.querySelector('#submitText');
        const submitSpinner = form.querySelector('#submitSpinner');
        const cancelBtn = form.querySelector('#cancelBtn');
        const formData = new FormData(form);
        
        // Disable form and show loading state
        form.querySelectorAll('input, button').forEach(el => el.disabled = true);
        submitText.textContent = 'Updating...';
        submitSpinner.classList.remove('hidden');
        
        try {
            const response = await fetch('/change-password', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData,
                // Add timeout to prevent hanging
                signal: AbortSignal.timeout(30000) // 30 seconds timeout
            });

            const result = await response.json();
            
            if (response.ok) {
                showToast('Password changed successfully!', 'success');
                // Small delay to show success message before closing
                setTimeout(() => modal.remove(), 1000);
            } else {
                showToast(result.message || 'Error changing password', 'error');
                // Re-enable form on error
                form.querySelectorAll('input, button').forEach(el => el.disabled = false);
                submitText.textContent = 'Update Password';
                submitSpinner.classList.add('hidden');
            }
        } catch (error) {
            const errorMsg = error.name === 'TimeoutError' 
                ? 'Request timed out. Please try again.'
                : 'Error changing password. Please try again.';
                
            showToast(errorMsg, 'error');
            // Re-enable form on error
            form.querySelectorAll('input, button').forEach(el => el.disabled = false);
            submitText.textContent = 'Update Password';
            submitSpinner.classList.add('hidden');
        }
    });
}



// Legacy function - now using dedicated page
function showSystemSettings() {
    closeAccountDropdown();

    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md relative animate-fadeInUp">
            <button onclick="this.closest('.fixed').remove()" class="absolute top-3 right-3 text-gray-600 hover:text-red-600 text-3xl font-bold hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200">&times;</button>
            <h2 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
                <span class="mr-3">⚙️</span> System Settings
            </h2>

            <form id="systemSettingsForm" class="space-y-4">
                <!-- Basic Settings -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
                        <input type="text" value="Budget Control System" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Quick Actions</h3>
                    <div class="space-y-2">
                        <button type="button" onclick="window.location.href='{{ route('users.index') }}'" class="w-full text-left p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors text-sm">
                            <div class="font-medium text-blue-800">👥 Manage Users</div>
                        </button>
                        <button type="button" onclick="clearCache()" class="w-full text-left p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors text-sm">
                            <div class="font-medium text-orange-800">🗑️ Clear Cache</div>
                        </button>
                    </div>
                </div>

                <!-- Settings Toggles -->
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">System Options</h3>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm text-gray-700">Allow user registration</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm text-gray-700">Require email verification</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm text-gray-700">Maintenance mode</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="this.closest('.fixed').remove()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    `;
    document.body.appendChild(modal);

    // Load current settings when form opens
    loadSystemSettings();

    // Handle form submission
    document.getElementById('systemSettingsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveSystemSettings();
    });
}

// System maintenance functions
function clearCache() {
    if (confirm('Are you sure you want to clear the application cache?')) {
        fetch('/admin/system-settings/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(error => {
            console.error('Error clearing cache:', error);
            alert('Error clearing cache. Please try again.');
        });
    }
}



// Load system settings from API
function loadSystemSettings() {
    fetch('/admin/system-settings', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(settings => {
        // Set form values with a small delay to ensure DOM is ready
        setTimeout(() => {
            const form = document.getElementById('systemSettingsForm');
            if (form) {
                const textInput = form.querySelector('input[type="text"]');
                const checkboxes = form.querySelectorAll('input[type="checkbox"]');

                if (textInput) textInput.value = settings.app_name || 'Budget Control System';
                if (checkboxes[0]) checkboxes[0].checked = settings.allow_user_registration !== false;
                if (checkboxes[1]) checkboxes[1].checked = settings.require_email_verification === true;
                if (checkboxes[2]) checkboxes[2].checked = settings.maintenance_mode === true;
            }
        }, 100);
    })
    .catch(error => {
        console.error('Error loading settings:', error);
        // Fallback to defaults if API fails
        setTimeout(() => {
            const form = document.getElementById('systemSettingsForm');
            if (form) {
                const textInput = form.querySelector('input[type="text"]');
                const checkboxes = form.querySelectorAll('input[type="checkbox"]');

                if (textInput) textInput.value = 'Budget Control System';
                if (checkboxes[0]) checkboxes[0].checked = true;
                if (checkboxes[1]) checkboxes[1].checked = false;
                if (checkboxes[2]) checkboxes[2].checked = false;
            }
        }, 100);
    });
}

// Save system settings to API
function saveSystemSettings() {
    try {
        const form = document.getElementById('systemSettingsForm');
        const formData = {
            app_name: form.querySelector('input[type="text"]').value,
            allow_user_registration: form.querySelectorAll('input[type="checkbox"]')[0].checked,
            require_email_verification: form.querySelectorAll('input[type="checkbox"]')[1].checked,
            maintenance_mode: form.querySelectorAll('input[type="checkbox"]')[2].checked
        };

        fetch('/admin/system-settings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Close the modal
                const modal = form.closest('.fixed');
                if (modal) {
                    modal.remove();
                }
            } else {
                alert('Error saving settings: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error saving settings:', error);
            alert('Error saving settings. Please try again.');
        });
    } catch (error) {
        console.error('Error saving settings:', error);
        alert('Error saving settings. Please try again.');
    }
}
</script>