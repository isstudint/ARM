// Register GSAP plugins
gsap.registerPlugin(TextPlugin, ScrollTrigger, CustomBounce, SlowMo, RoughEase);

// Main timeline for controlling all animations
let masterTimeline = gsap.timeline();

// Initialize all animations when page loads
document.addEventListener('DOMContentLoaded', function() {
    initHeroAnimations();
    initCardAnimations();
    initScoreboardAnimations();
    initTextAnimations();
    initShapeAnimations();
    initPhysicsAnimations();
    initScrollAnimations();
    initControlPanel();
});

function initHeroAnimations() {
    // Hero background gradient animation
    gsap.to('.hero-bg', {
        backgroundPosition: '100% 100%',
        duration: 8,
        ease: 'none',
        repeat: -1,
        yoyo: true
    });

    // Hero title animation with stagger
    gsap.fromTo('.hero-title .word', {
        y: 100,
        opacity: 0,
        rotationX: 90
    }, {
        y: 0,
        opacity: 1,
        rotationX: 0,
        duration: 1.2,
        stagger: 0.3,
        ease: CustomBounce.create("myBounce", {strength: 0.7, squash: 3}),
        delay: 0.5
    });

    // Subtitle fade in
    gsap.to('.hero-subtitle', {
        opacity: 1,
        y: 0,
        duration: 1,
        delay: 2,
        ease: 'power2.out'
    });

    // CTA button pulse
    gsap.to('.cta-button', {
        scale: 1.05,
        duration: 1.5,
        repeat: -1,
        yoyo: true,
        ease: 'power2.inOut',
        delay: 3
    });

    // Basketball floating animation
    gsap.to('.basketball', {
        y: -20,
        rotation: 360,
        duration: 2,
        repeat: -1,
        yoyo: true,
        ease: 'power2.inOut'
    });

    // Particles animation
    gsap.set('.particle', {
        x: () => Math.random() * window.innerWidth,
        y: () => Math.random() * window.innerHeight,
        scale: () => Math.random() * 0.5 + 0.5
    });

    gsap.to('.particle', {
        y: '+=100',
        x: '+=50',
        duration: () => Math.random() * 3 + 2,
        repeat: -1,
        yoyo: true,
        ease: 'none',
        stagger: 0.2
    });
}

function initCardAnimations() {
    // Cards entrance animation
    gsap.fromTo('.team-card', {
        y: 100,
        opacity: 0,
        rotationY: 45,
        scale: 0.8
    }, {
        y: 0,
        opacity: 1,
        rotationY: 0,
        scale: 1,
        duration: 1,
        stagger: 0.2,
        ease: 'back.out(1.7)',
        scrollTrigger: {
            trigger: '.cards-section',
            start: 'top 80%'
        }
    });

    // Card hover interactions
    document.querySelectorAll('.team-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            gsap.to(card, {
                y: -10,
                rotationY: 10,
                scale: 1.05,
                duration: 0.3,
                ease: 'power2.out'
            });
            
            gsap.to(card.querySelector('.card-bg'), {
                opacity: 1,
                duration: 0.3
            });

            // Animate stats
            gsap.fromTo(card.querySelectorAll('.wins, .losses'), {
                scale: 0.8,
                opacity: 0
            }, {
                scale: 1,
                opacity: 1,
                duration: 0.4,
                stagger: 0.1,
                ease: 'back.out(1.7)'
            });
        });

        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                y: 0,
                rotationY: 0,
                scale: 1,
                duration: 0.3,
                ease: 'power2.out'
            });
            
            gsap.to(card.querySelector('.card-bg'), {
                opacity: 0,
                duration: 0.3
            });
        });

        // Click animation
        card.addEventListener('click', () => {
            gsap.to(card, {
                rotation: 360,
                scale: 1.2,
                duration: 0.6,
                ease: 'back.out(1.7)',
                onComplete: () => {
                    gsap.to(card, {
                        rotation: 0,
                        scale: 1,
                        duration: 0.3
                    });
                }
            });
        });
    });
}

function initScoreboardAnimations() {
    // Scoreboard entrance
    gsap.fromTo('.scoreboard', {
        scale: 0,
        opacity: 0,
        rotationX: 180
    }, {
        scale: 1,
        opacity: 1,
        rotationX: 0,
        duration: 1.2,
        ease: 'back.out(1.7)',
        scrollTrigger: {
            trigger: '.scoreboard-section',
            start: 'top 80%'
        }
    });

    // Score animation button
    document.querySelector('.animate-score-btn').addEventListener('click', animateScores);
}

function animateScores() {
    const scoreA = document.getElementById('scoreA');
    const scoreB = document.getElementById('scoreB');
    
    // Random scores
    const finalScoreA = Math.floor(Math.random() * 50) + 50;
    const finalScoreB = Math.floor(Math.random() * 50) + 50;
    
    // Create counting animation
    const tl = gsap.timeline();
    
    tl.to(scoreA, {
        textContent: finalScoreA,
        duration: 2,
        ease: 'power2.out',
        snap: { textContent: 1 },
        onUpdate: function() {
            gsap.to(scoreA, {
                scale: 1.2,
                duration: 0.1,
                yoyo: true,
                repeat: 1
            });
        }
    })
    .to(scoreB, {
        textContent: finalScoreB,
        duration: 2,
        ease: 'power2.out',
        snap: { textContent: 1 },
        onUpdate: function() {
            gsap.to(scoreB, {
                scale: 1.2,
                duration: 0.1,
                yoyo: true,
                repeat: 1
            });
        }
    }, '-=1.5');

    // Add glow effect to winner
    tl.call(() => {
        const winner = finalScoreA > finalScoreB ? scoreA : scoreB;
        gsap.to(winner, {
            textShadow: '0 0 30px #FFD700, 0 0 40px #FFD700',
            scale: 1.3,
            duration: 0.5,
            yoyo: true,
            repeat: 3
        });
    });
}

function initTextAnimations() {
    const messages = [
        "Welcome to ARM Basketball League!",
        "Experience cutting-edge animations...",
        "Powered by GSAP technology...",
        "Real-time scores and updates...",
        "Never miss a moment of the game!"
    ];
    
    let currentMessage = 0;
    
    function typeMessage() {
        const text = messages[currentMessage];
        
        gsap.to('.typing-text', {
            text: text,
            duration: text.length * 0.05,
            ease: 'none',
            onComplete: () => {
                setTimeout(() => {
                    gsap.to('.typing-text', {
                        text: '',
                        duration: 0.5,
                        ease: 'none',
                        onComplete: () => {
                            currentMessage = (currentMessage + 1) % messages.length;
                            typeMessage();
                        }
                    });
                }, 2000);
            }
        });
    }
    
    // Start typing when section is in view
    ScrollTrigger.create({
        trigger: '.text-section',
        start: 'top 80%',
        onEnter: typeMessage,
        once: true
    });
}

function initShapeAnimations() {
    // Court entrance animation
    gsap.fromTo('.basketball-court', {
        scale: 0,
        opacity: 0,
        rotation: 180
    }, {
        scale: 1,
        opacity: 1,
        rotation: 0,
        duration: 1.5,
        ease: 'back.out(1.7)',
        scrollTrigger: {
            trigger: '.shapes-section',
            start: 'top 80%'
        }
    });

    // Morph button functionality
    document.querySelector('.morph-btn').addEventListener('click', morphCourt);
}

function morphCourt() {
    const court = document.querySelector('.basketball-court');
    const centerCircle = document.querySelector('.center-circle');
    const threePointLine = document.querySelector('.three-point-line');
    
    const tl = gsap.timeline();
    
    tl.to(court, {
        rotation: 360,
        scale: 1.2,
        duration: 1,
        ease: 'power2.inOut'
    })
    .to(centerCircle, {
        r: 60,
        stroke: '#FFD700',
        duration: 0.5,
        ease: 'elastic.out(1, 0.3)'
    }, '-=0.5')
    .to(threePointLine, {
        rx: 20,
        stroke: '#FF6B6B',
        strokeWidth: 4,
        duration: 0.5,
        ease: 'bounce.out'
    }, '-=0.3')
    .to([centerCircle, threePointLine], {
        r: 30,
        rx: 50,
        stroke: '#fff',
        strokeWidth: 2,
        duration: 1,
        ease: 'elastic.out(1, 0.3)'
    })
    .to(court, {
        rotation: 0,
        scale: 1,
        duration: 1,
        ease: 'back.out(1.7)'
    });
}

function initPhysicsAnimations() {
    // Physics section entrance
    gsap.fromTo('.basketball-game', {
        y: 100,
        opacity: 0
    }, {
        y: 0,
        opacity: 1,
        duration: 1,
        ease: 'power2.out',
        scrollTrigger: {
            trigger: '.physics-section',
            start: 'top 80%'
        }
    });

    // Shoot button functionality
    document.querySelector('.shoot-btn').addEventListener('click', shootBall);
}

function shootBall() {
    const ball = document.getElementById('physicsball');
    const hoop = document.querySelector('.hoop');
    
    const tl = gsap.timeline();
    
    // Ball shooting animation with physics
    tl.to(ball, {
        x: 300,
        y: -200,
        rotation: 720,
        duration: 1.5,
        ease: 'power2.out'
    })
    .to(ball, {
        y: -150,
        duration: 0.5,
        ease: 'bounce.out',
        onComplete: () => {
            // Success animation
            gsap.to('.rim', {
                scale: 1.2,
                backgroundColor: '#FFD700',
                duration: 0.3,
                yoyo: true,
                repeat: 3
            });
            
            // Confetti effect
            createConfetti();
        }
    })
    .to(ball, {
        x: 50,
        y: 0,
        rotation: 0,
        duration: 1,
        ease: 'bounce.out',
        delay: 1
    });
}

function createConfetti() {
    const colors = ['#FFD700', '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4'];
    const container = document.querySelector('.basketball-game');
    
    for (let i = 0; i < 20; i++) {
        const confetti = document.createElement('div');
        confetti.style.cssText = `
            position: absolute;
            width: 10px;
            height: 10px;
            background: ${colors[Math.floor(Math.random() * colors.length)]};
            top: 50px;
            right: 80px;
            border-radius: 50%;
            pointer-events: none;
        `;
        
        container.appendChild(confetti);
        
        gsap.to(confetti, {
            x: (Math.random() - 0.5) * 200,
            y: Math.random() * 200 + 100,
            rotation: Math.random() * 360,
            opacity: 0,
            duration: Math.random() * 2 + 1,
            ease: 'power2.out',
            onComplete: () => confetti.remove()
        });
    }
}

function initScrollAnimations() {
    // Parallax scrolling effect
    gsap.to('.hero-bg', {
        yPercent: -50,
        ease: 'none',
        scrollTrigger: {
            trigger: '.hero',
            start: 'top bottom',
            end: 'bottom top',
            scrub: true
        }
    });

    // Section reveal animations
    gsap.utils.toArray('section').forEach((section, i) => {
        gsap.fromTo(section, {
            y: 50,
            opacity: 0.8
        }, {
            y: 0,
            opacity: 1,
            duration: 1,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: section,
                start: 'top 85%',
                end: 'top 20%',
                scrub: 1
            }
        });
    });
}

function initControlPanel() {
    const controlButtons = document.querySelectorAll('.control-btn');
    
    controlButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const action = e.target.dataset.action;
            
            switch(action) {
                case 'replay':
                    replayAllAnimations();
                    break;
                case 'pause':
                    gsap.globalTimeline.pause();
                    break;
                case 'resume':
                    gsap.globalTimeline.resume();
                    break;
            }
            
            // Button click feedback
            gsap.to(btn, {
                scale: 0.9,
                duration: 0.1,
                yoyo: true,
                repeat: 1,
                ease: 'power2.out'
            });
        });
    });
}

function replayAllAnimations() {
    // Refresh ScrollTrigger and replay all animations
    ScrollTrigger.refresh();
    
    // Reset and replay hero animations
    gsap.set('.hero-title .word', { y: 100, opacity: 0, rotationX: 90 });
    gsap.set('.hero-subtitle', { opacity: 0 });
    
    initHeroAnimations();
    
    // Restart other animations
    gsap.to('.basketball-court', {
        rotation: 720,
        scale: 1.5,
        duration: 2,
        ease: 'power2.inOut',
        onComplete: () => {
            gsap.to('.basketball-court', {
                rotation: 0,
                scale: 1,
                duration: 1,
                ease: 'back.out(1.7)'
            });
        }
    });
}

// Basketball click interaction
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('basketball')) {
        gsap.to(e.target, {
            rotation: '+=360',
            scale: 1.5,
            duration: 0.6,
            ease: 'back.out(1.7)',
            onComplete: () => {
                gsap.to(e.target, {
                    scale: 1,
                    duration: 0.3
                });
            }
        });
    }
});

// Add smooth scrolling
document.addEventListener('wheel', (e) => {
    e.preventDefault();
    
    gsap.to(window, {
        scrollTo: window.pageYOffset + e.deltaY,
        duration: 0.8,
        ease: 'power2.out'
    });
}, { passive: false });

// Mouse trail effect
let mouseTrail = [];
document.addEventListener('mousemove', (e) => {
    const trail = document.createElement('div');
    trail.style.cssText = `
        position: fixed;
        width: 6px;
        height: 6px;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 50%;
        pointer-events: none;
        z-index: 9999;
        left: ${e.clientX}px;
        top: ${e.clientY}px;
    `;
    
    document.body.appendChild(trail);
    mouseTrail.push(trail);
    
    gsap.to(trail, {
        scale: 0,
        opacity: 0,
        duration: 0.5,
        ease: 'power2.out',
        onComplete: () => {
            trail.remove();
            mouseTrail = mouseTrail.filter(t => t !== trail);
        }
    });
    
    if (mouseTrail.length > 20) {
        const oldest = mouseTrail.shift();
        oldest.remove();
    }
});
