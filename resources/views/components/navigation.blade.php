<!-- Enhanced Navigation Component -->
<nav class="w-full flex items-center justify-between px-8 py-3 fixed top-0 left-0 z-50 border-b border-white/30 backdrop-blur-sm" style="background: linear-gradient(90deg, #064e3b 0%, #065f46 50%, #10b981 100%);">
    <!-- Logo/Brand -->
    <div class="flex items-center">
        <div class="flex items-center space-x-3">
            <a href="/dashboard" class="hover:scale-105 transition-all duration-200">
                @include('components.logo', ['size' => 'md', 'background' => true])
            </a>
            <div>
                <a href="/dashboard" class="text-xl font-extrabold text-white drop-shadow tracking-wide hover:text-green-100 transition-colors duration-200">Budget Control</a>
                <p class="text-xs text-green-100 opacity-80">{{ $pageTitle ?? 'Project Management' }}</p>
            </div>
        </div>
    </div>

    <!-- Desktop Navigation -->
    <div class="hidden md:flex items-center space-x-6">
        <!-- Main Navigation Links -->
        <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <a href="/projects" class="nav-item {{ request()->is('projects*') && !request()->is('projects/archive') && !request()->is('projects/recently-deleted') ? 'active' : '' }}">
            Projects
        </a>    

        <a href="/monthly-assignments" class="nav-item {{ request()->is('monthly-assignments*') ? 'active' : '' }}">
            Teams
        </a>



        <a href="/projects/archive" class="nav-item {{ request()->is('projects/archive') ? 'active' : '' }}">
            Archive
        </a>

        @if(auth()->user()->is_admin)
            <a href="/projects/recently-deleted" class="nav-item {{ request()->is('projects/recently-deleted') ? 'active' : '' }}">
                Recently Deleted
            </a>
        @endif

        <!-- User Menu -->
        <div class="relative ml-4 flex-shrink-0 h-10" style="min-width: fit-content;">
            <button id="accountDropdownBtn" class="nav-item dropdown-toggle flex items-center min-w-0 h-10">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-sm mr-2 flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="hidden sm:inline">Account</span>
                <svg id="accountDropdownArrow" class="w-4 h-4 ml-1 transition-transform duration-300 ease-in-out flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
            <div id="accountDropdownMenu" class="dropdown-menu hidden" style="width: 280px !important; border-radius: 16px !important; background: white !important;">
                <!-- Header Section -->
                <div class="px-4 py-3 bg-green-300 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-3">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 text-sm">
                                {{ auth()->user()->is_admin ? 'Admin User' : auth()->user()->name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ auth()->user()->is_admin ? 'Administrator' : 'User' }}
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Personal Account Actions (All Users) -->
                <div class="py-2 bg-white">
                    <a href="#" class="dropdown-item py-3 px-4 hover:bg-gray-50 transition-colors duration-200" onclick="showMyProfile()">
                        <svg class="w-4 h-4 mr-3 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-gray-700">My Profile</span>
                    </a>
                    <a href="#" class="dropdown-item py-3 px-4 hover:bg-gray-50 transition-colors duration-200" onclick="showChangePassword()">
                        <svg class="w-4 h-4 mr-3 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-2a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-gray-700">Change Password</span>
                    </a>
                </div>

                <!-- Admin Actions (Admin Only) -->
                @if(auth()->user()->is_admin)
                <div class="border-t border-gray-200"></div>
                <div class="py-2 bg-white">
                    <a href="{{ route('users.index') }}" class="dropdown-item py-3 px-4 hover:bg-blue-50 transition-colors duration-200" onclick="closeAccountDropdown()">
                        <svg class="w-4 h-4 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                        <span class="font-medium text-blue-700">User Management</span>
                    </a>
                    <a href="#" class="dropdown-item py-3 px-4 hover:bg-blue-50 transition-colors duration-200" onclick="showSystemSettings()">
                        <svg class="w-4 h-4 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-blue-700">System Settings</span>
                    </a>
                </div>
                @endif

                <!-- Sign Out -->
                <div class="border-t border-gray-200 bg-white">
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="dropdown-item w-full text-left py-3 px-4 hover:bg-red-50 transition-colors duration-200 border-none bg-transparent">
                            <svg class="w-4 h-4 mr-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium text-red-700">Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Button -->
    <div class="md:hidden">
        <button id="mobileMenuBtn" class="nav-item">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>
</nav>

<!-- Mobile Menu -->
<div id="mobileMenu" class="fixed inset-0 z-40 hidden md:hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeMobileMenu()"></div>
    <div class="fixed top-0 right-0 w-80 h-full bg-white shadow-xl transform translate-x-full transition-transform duration-300" id="mobileMenuPanel">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Menu</h2>
                <button onclick="closeMobileMenu()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-2">
                <a href="/dashboard" class="mobile-nav-item {{ request()->is('dashboard') ? 'bg-green-100' : '' }}">
                    Dashboard
                </a>

                <a href="/projects" class="mobile-nav-item {{ request()->is('projects*') && !request()->is('projects/archive') && !request()->is('projects/recently-deleted') ? 'bg-green-100' : '' }}">
                    Projects
                </a>

                <a href="/monthly-assignments" class="mobile-nav-item {{ request()->is('monthly-assignments*') ? 'bg-green-100' : '' }}">
                    Teams
                </a>

                <a href="/projects/archive" class="mobile-nav-item {{ request()->is('projects/archive') ? 'bg-green-100' : '' }}">
                    Archive
                </a>

                @if(auth()->user()->is_admin)
                    <a href="/projects/recently-deleted" class="mobile-nav-item {{ request()->is('projects/recently-deleted') ? 'bg-green-100' : '' }}">
                        Recently Deleted
                    </a>
                @endif

                <div class="border-t pt-4 mt-6">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="mobile-nav-item w-full text-left">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Simple Navigation Styles */
.nav-item {
    @apply flex items-center px-4 py-2 rounded-lg text-white font-medium transition-all duration-200;
}

.nav-item:hover {
    @apply bg-white bg-opacity-20;
}

.nav-item.active {
    @apply bg-white bg-opacity-25;
}

.dropdown-toggle {
    @apply cursor-pointer;
    min-width: fit-content;
    flex-shrink: 0;
}

/* Simple Navigation Layout */
nav {
    position: fixed !important;
    width: 100% !important;
}

.nav-item {
    white-space: nowrap;
    flex-shrink: 0;
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
    transform: translateX(4px) !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
    color: #1f2937 !important;
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
// Mobile menu functionality
function openMobileMenu() {
    document.getElementById('mobileMenu').classList.remove('hidden');
    setTimeout(() => {
        document.getElementById('mobileMenuPanel').classList.remove('translate-x-full');
    }, 10);
}

function closeMobileMenu() {
    document.getElementById('mobileMenuPanel').classList.add('translate-x-full');
    setTimeout(() => {
        document.getElementById('mobileMenu').classList.add('hidden');
    }, 300);
}

document.getElementById('mobileMenuBtn').addEventListener('click', openMobileMenu);

// Account dropdown functionality
const accountDropdownBtn = document.getElementById('accountDropdownBtn');
const accountDropdownMenu = document.getElementById('accountDropdownMenu');
const accountDropdownArrow = document.getElementById('accountDropdownArrow');

function toggleAccountDropdown() {
    const isHidden = accountDropdownMenu.classList.contains('hidden');

    if (isHidden) {
        accountDropdownMenu.classList.remove('hidden');
        accountDropdownMenu.classList.add('show');
        accountDropdownArrow.style.transform = 'rotate(180deg)';
    } else {
        accountDropdownMenu.classList.remove('show');
        accountDropdownMenu.classList.add('hidden');
        accountDropdownArrow.style.transform = 'rotate(0deg)';
    }
}

function closeAccountDropdown() {
    accountDropdownMenu.classList.remove('show');
    accountDropdownMenu.classList.add('hidden');
    accountDropdownArrow.style.transform = 'rotate(0deg)';
}

accountDropdownBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    toggleAccountDropdown();
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!accountDropdownBtn.contains(e.target) && !accountDropdownMenu.contains(e.target)) {
        closeAccountDropdown();
    }
});

// Keyboard navigation support
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeMobileMenu();
        closeAccountDropdown();
    }
});

// Account Settings Functions
function showMyProfile() {
    // Close the dropdown first
    closeAccountDropdown();

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
}

function showChangePassword() {
    // Close the dropdown first
    closeAccountDropdown();

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
                    <button type="button" onclick="this.closest('.fixed').remove()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    `;
    document.body.appendChild(modal);

    // Handle form submission
    document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
            const response = await fetch('/change-password', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            if (response.ok) {
                alert('Password changed successfully!');
                modal.remove();
            } else {
                const error = await response.json();
                alert(error.message || 'Error changing password');
            }
        } catch (error) {
            alert('Error changing password. Please try again.');
        }
    });
}



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

