
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recently Deleted Projects - Budget Control</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap" rel="stylesheet">
</head>
<body style="background: #064e3b;;" class="min-h-screen">
    @include('components.navigation')

    <style>
        /* Reset any default margins/padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0 !important;
            padding: 0 !important;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
            opacity: 0;
            transform: translateY(40px);
            animation: fadeInUp 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }
        .glass-card.card-delay-1 { animation-delay: 0.1s; }
        .glass-card.card-delay-2 { animation-delay: 0.25s; }
        .glass-card.card-delay-3 { animation-delay: 0.4s; }
        .glass-card:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 12px 32px 0 #ef444455;
            background: rgba(255,255,255,0.22);
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .techy-btn {
            background: linear-gradient(45deg, #1f2937, #374151);
            border: 1px solid #4b5563;
            transition: all 0.3s ease;
        }
        .techy-btn:hover {
            background: linear-gradient(45deg, #374151, #4b5563);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        /* Restore button states */
        .restore-project-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .restore-project-btn:disabled:hover {
            background-color: #059669 !important;
            transform: none !important;
        }

        /* Smooth transitions for project cards */
        .deleted-project-card {
            transition: all 0.3s ease;
        }

        /* Shake animation for deletion effect */
        @keyframes shake {
            0%, 100% { transform: translateX(0) scale(1.05); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px) scale(1.05); }
            20%, 40%, 60%, 80% { transform: translateX(5px) scale(1.05); }
        }

        /* Enhanced fade-in animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }

        /* Centered notification styles */
        .notification-center {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            min-width: 300px;
            max-width: 500px;
            animation: notificationFadeIn 0.3s ease-out;
        }

        @keyframes notificationFadeIn {
            from {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        /* Custom confirmation dialog styles */
        .custom-confirm {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }

        .custom-confirm-dialog {
            background: white;
            border-radius: 12px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            animation: confirmDialogFadeIn 0.3s ease-out;
        }

        @keyframes confirmDialogFadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>

    <!-- Main Content -->
    <div class="main-content px-4 pt-6 pb-10 transition-all duration-300" style="margin-left: 256px;" id="mainContent">
    <div class="max-w-7xl mx-auto">
        <!-- Info Banner -->
        <div class="glass-card card-delay-1 p-6 mb-8">
            <div class="flex items-center space-x-4">
                <div class="text-4xl">⚠️</div>
                <div>
                    <h2 class="text-xl font-bold text-white mb-2">Recently Deleted Projects</h2>
                    <p class="text-red-100">Projects deleted within the last 30 days. After 30 days, projects are permanently deleted and cannot be recovered.</p>
                </div>
            </div>
        </div>

    <div class="container mx-auto">
        <!-- Page Header -->
        <div class="glass-card card-delay-1 p-6 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Recently Deleted Projects</h1>
                <p class="text-green-100">Projects deleted within the last 30 days. You can restore or permanently delete them.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="glass-card card-delay-2 p-4 mb-6 bg-green-500 bg-opacity-20">
                <div class="flex items-center space-x-2">
                    <span class="text-2xl">✅</span>
                    <span class="text-white font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Deleted Projects Grid -->
        @if($deletedProjects->count() > 0)
            <div id="deletedProjectsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($deletedProjects as $project)
                    <div class="glass-card card-delay-{{ $loop->index % 3 + 1 }} p-6 deleted-project-card">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2">{{ $project->name }}</h3>
                                <div class="text-red-200 text-sm">
                                    <div>Budget: ₱{{ number_format($project->budget, 2) }}</div>
                                    <div>Spent: ₱{{ number_format($project->totalSpent(), 2) }}</div>
                                    <div>Deleted: {{ $project->deleted_at->format('M d, Y') }}</div>
                                    <div class="text-yellow-200 font-semibold">
                                        Expires: {{ $project->deleted_at->addDays(30)->format('M d, Y') }}
                                        ({{ $project->deleted_at->addDays(30)->diffForHumans() }})
                                    </div>
                                </div>
                            </div>
                            <div class="text-3xl opacity-50"></div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2 mt-4">
                            <button class="restore-project-btn flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                                    data-project-id="{{ $project->id }}"
                                    data-project-name="{{ $project->name }}">
                                 Restore
                            </button>
                            <button class="force-delete-project-btn flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                                    data-project-id="{{ $project->id }}"
                                    data-project-name="{{ $project->name }}">
                                Delete Forever
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="glass-card card-delay-1 text-center py-16">
                <div class="text-8xl mb-6"></div>
                <h3 class="text-3xl font-bold text-white mb-4">No Recently Deleted Projects</h3>
                <p class="text-red-200 text-lg mb-8">All your projects are safe and sound!</p>
                <a href="/projects" class="techy-btn inline-flex items-center px-6 py-3 rounded-lg text-white font-semibold">
                    ← Back to Projects
                </a>
            </div>
        @endif
    </div>

    <script>
        // Custom centered confirmation dialog
        function showCenteredConfirm(message, onConfirm, onCancel = null) {
            const overlay = document.createElement('div');
            overlay.className = 'custom-confirm';

            overlay.innerHTML = `
                <div class="custom-confirm-dialog">
                    <div class="flex items-center mb-4">
                        <div class="text-3xl mr-3">⚠️</div>
                        <h3 class="text-lg font-semibold text-gray-800">Confirm Action</h3>
                    </div>
                    <p class="text-gray-600 mb-6">${message}</p>
                    <div class="flex justify-end space-x-3">
                        <button class="cancel-btn px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button class="confirm-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                            Confirm
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(overlay);

            // Handle confirm button
            overlay.querySelector('.confirm-btn').addEventListener('click', () => {
                document.body.removeChild(overlay);
                if (onConfirm) onConfirm();
            });

            // Handle cancel button and overlay click
            const cancelHandler = () => {
                document.body.removeChild(overlay);
                if (onCancel) onCancel();
            };

            overlay.querySelector('.cancel-btn').addEventListener('click', cancelHandler);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) cancelHandler();
            });
        }

        // Custom centered notification with blurred background
        function showCenteredNotification(message, type = 'success', duration = 1000) {
            // Create backdrop overlay with blur
            const overlay = document.createElement('div');
            overlay.className = 'notification-overlay';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.3);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                z-index: 9998;
                opacity: 0;
                transition: opacity 0.3s ease;
            `;

            const notification = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';

            notification.className = `notification-center ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg`;
            notification.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) scale(0.8);
                z-index: 9999;
                min-width: 300px;
                max-width: 500px;
                transition: all 0.3s ease;
                opacity: 0;
            `;
            notification.innerHTML = `
                <div class="flex items-center justify-center">
                    <span class="text-xl mr-3">${icon}</span>
                    <span class="font-medium">${message}</span>
                </div>
            `;

            document.body.appendChild(overlay);
            document.body.appendChild(notification);

            // Animate in
            requestAnimationFrame(() => {
                overlay.style.opacity = '1';
                notification.style.opacity = '1';
                notification.style.transform = 'translate(-50%, -50%) scale(1)';
            });

            // Auto remove after duration
            setTimeout(() => {
                overlay.style.opacity = '0';
                notification.style.opacity = '0';
                notification.style.transform = 'translate(-50%, -50%) scale(0.8)';

                setTimeout(() => {
                    if (document.body.contains(overlay)) {
                        document.body.removeChild(overlay);
                    }
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, duration);
        }

        // Restore project buttons
        document.querySelectorAll('.restore-project-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.projectId;
                const name = this.dataset.projectName;
                showCenteredConfirm(
                    `Are you sure you want to restore "${name}"?`,
                    () => restoreProject(id)
                );
            });
        });

        // Force delete project buttons
        document.querySelectorAll('.force-delete-project-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.projectId;
                const name = this.dataset.projectName;
                showCenteredConfirm(
                    `Are you sure you want to PERMANENTLY delete "${name}"? This action cannot be undone!`,
                    () => forceDeleteProject(id)
                );
            });
        });

        // Restore project function
        async function restoreProject(id) {
            // Disable the restore button to prevent double-clicks
            const restoreBtn = document.querySelector(`[data-project-id="${id}"].restore-project-btn`);
            if (restoreBtn) {
                restoreBtn.disabled = true;
                restoreBtn.textContent = '⏳ Restoring...';
            }

            try {
                const response = await fetch(`/projects/${id}/restore`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                });

                if (response.ok) {
                    const data = await response.json();

                    // Show centered success message
                    showCenteredNotification(data.message, 'success', 1000);

                    // Enhanced visual effects for restore
                    const projectCard = document.querySelector(`[data-project-id="${id}"]`).closest('.deleted-project-card');
                    if (projectCard) {
                        // Success animation sequence
                        projectCard.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                        projectCard.style.background = 'rgba(34, 197, 94, 0.2)'; // Green glow
                        projectCard.style.borderColor = '#22c55e';
                        projectCard.style.transform = 'scale(1.05)';
                        projectCard.style.boxShadow = '0 10px 30px rgba(34, 197, 94, 0.3)';

                        // Success overlay removed - no check animation

                        // Fade out animation
                        setTimeout(() => {
                            projectCard.style.opacity = '0';
                            projectCard.style.transform = 'scale(0.8) translateY(-20px)';
                        }, 1000);

                        // Remove card from DOM
                        setTimeout(() => {
                            projectCard.remove();

                            // Check if no more cards exist, show empty state
                            const remainingCards = document.querySelectorAll('.deleted-project-card');
                            if (remainingCards.length === 0) {
                                const grid = document.getElementById('deletedProjectsGrid');
                                if (grid) {
                                    grid.innerHTML = `
                                        <div class="col-span-full glass-card text-center py-16 animate-fadeInUp">
                                            <div class="text-8xl mb-6">🎉</div>
                                            <h3 class="text-3xl font-bold text-white mb-4">All Projects Restored!</h3>
                                            <p class="text-green-200 text-lg mb-8">Great job! All deleted projects have been restored.</p>
                                            <a href="/projects" class="techy-btn inline-flex items-center px-6 py-3 rounded-lg text-white font-semibold">
                                                ← Back to Projects
                                            </a>
                                        </div>
                                    `;
                                }
                            }
                        }, 1600);
                    }
                } else {
                    const errorData = await response.json().catch(() => ({}));
                    const errorMessage = errorData.message || 'Error restoring project. Please try again.';

                    // Show centered error message
                    showCenteredNotification(errorMessage, 'error', 4000);

                    setTimeout(() => {
                        errorMsg.remove();
                    }, 4000);

                    // Re-enable the button
                    if (restoreBtn) {
                        restoreBtn.disabled = false;
                        restoreBtn.textContent = '↩️ Restore';
                    }
                }
            } catch (error) {
                console.error('Error:', error);

                // Show centered error message
                showCenteredNotification('Network error. Please check your connection and try again.', 'error', 4000);

                // Re-enable the button
                if (restoreBtn) {
                    restoreBtn.disabled = false;
                    restoreBtn.textContent = '↩️ Restore';
                }
            }
        }

        // Force delete project function
        async function forceDeleteProject(id) {
            // Disable the delete button to prevent double-clicks
            const deleteBtn = document.querySelector(`[data-project-id="${id}"].force-delete-project-btn`);
            if (deleteBtn) {
                deleteBtn.disabled = true;
                deleteBtn.textContent = '🗑️ Deleting...';
                deleteBtn.style.background = '#7f1d1d';
            }

            try {
                const response = await fetch(`/projects/${id}/force-delete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: '_method=DELETE'
                });

                if (response.ok) {
                    const data = await response.json();

                    // Show centered deletion message (red color for deletion)
                    showCenteredNotification(data.message, 'error', 1000);

                    // Enhanced visual effects for permanent deletion
                    const projectCard = document.querySelector(`[data-project-id="${id}"]`).closest('.deleted-project-card');
                    if (projectCard) {
                        // Destruction animation sequence
                        projectCard.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
                        projectCard.style.background = 'rgba(239, 68, 68, 0.2)'; // Red glow
                        projectCard.style.borderColor = '#ef4444';
                        projectCard.style.transform = 'scale(1.05)';
                        projectCard.style.boxShadow = '0 10px 30px rgba(239, 68, 68, 0.4)';

                        // Add destruction icon overlay
                        const destructionOverlay = document.createElement('div');
                        destructionOverlay.className = 'absolute inset-0 flex items-center justify-center bg-red-500 bg-opacity-30 rounded-2xl';
                        destructionOverlay.innerHTML = `
                            <div class="text-6xl text-red-400 animate-pulse">
                                💥
                            </div>
                        `;
                        projectCard.style.position = 'relative';
                        projectCard.appendChild(destructionOverlay);

                        // Shake animation
                        setTimeout(() => {
                            projectCard.style.animation = 'shake 0.5s ease-in-out';
                        }, 200);

                        // Disintegration effect
                        setTimeout(() => {
                            projectCard.style.opacity = '0';
                            projectCard.style.transform = 'scale(0.3) rotate(10deg)';
                        }, 1000);

                        // Remove card from DOM
                        setTimeout(() => {
                            projectCard.remove();

                            // Check if no more cards exist, show empty state
                            const remainingCards = document.querySelectorAll('.deleted-project-card');
                            if (remainingCards.length === 0) {
                                const grid = document.getElementById('deletedProjectsGrid');
                                if (grid) {
                                    grid.innerHTML = `
                                        <div class="col-span-full glass-card text-center py-16 animate-fadeInUp">
                                            <div class="text-8xl mb-6">🧹</div>
                                            <h3 class="text-3xl font-bold text-white mb-4">All Clean!</h3>
                                            <p class="text-red-200 text-lg mb-8">No recently deleted projects remaining.</p>
                                            <a href="/projects" class="techy-btn inline-flex items-center px-6 py-3 rounded-lg text-white font-semibold">
                                                ← Back to Projects
                                            </a>
                                        </div>
                                    `;
                                }
                            }
                        }, 1800);
                    }
                } else {
                    const errorData = await response.json().catch(() => ({}));
                    const errorMessage = errorData.message || 'Error deleting project permanently. Please try again.';
                    showCenteredNotification(errorMessage, 'error', 4000);

                    // Re-enable the button
                    if (deleteBtn) {
                        deleteBtn.disabled = false;
                        deleteBtn.textContent = 'Delete Forever';
                        deleteBtn.style.background = '#dc2626';
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showCenteredNotification('Network error. Please check your connection and try again.', 'error', 4000);

                // Re-enable the button
                if (deleteBtn) {
                    deleteBtn.disabled = false;
                    deleteBtn.textContent = 'Delete Forever';
                    deleteBtn.style.background = '#dc2626';
                }
            }
        }
    </script>
    </div> <!-- Close container -->
    </div> <!-- Close main content -->
</body>
</html>