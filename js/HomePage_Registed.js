

(function() {
  const canvas = document.getElementById('hero-canvas');
  const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.setSize(window.innerWidth, window.innerHeight);

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 200);
  camera.position.z = 30;

  // Particle geometry
  const count = 280;
  const positions = new Float32Array(count * 3);
  const sizes     = new Float32Array(count);

  for (let i = 0; i < count; i++) {
    positions[i*3]   = (Math.random() - 0.5) * 80;
    positions[i*3+1] = (Math.random() - 0.5) * 60;
    positions[i*3+2] = (Math.random() - 0.5) * 40;
    sizes[i] = Math.random() * 1.8 + 0.4;
  }

  const geo = new THREE.BufferGeometry();
  geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
  geo.setAttribute('size', new THREE.BufferAttribute(sizes, 1));

  const mat = new THREE.PointsMaterial({
    color: 0xc8843a,
    size: 0.35,
    transparent: true,
    opacity: 0.18,
    sizeAttenuation: true,
  });

  const points = new THREE.Points(geo, mat);
  scene.add(points);

  // Second softer layer
  const geo2 = new THREE.BufferGeometry();
  const pos2 = new Float32Array(120 * 3);
  for (let i = 0; i < 120; i++) {
    pos2[i*3]   = (Math.random() - 0.5) * 100;
    pos2[i*3+1] = (Math.random() - 0.5) * 70;
    pos2[i*3+2] = (Math.random() - 0.5) * 20 - 10;
  }
  geo2.setAttribute('position', new THREE.BufferAttribute(pos2, 3));
  const mat2 = new THREE.PointsMaterial({ color: 0xc97d7d, size: 0.22, transparent: true, opacity: 0.1 });
  const pts2 = new THREE.Points(geo2, mat2);
  scene.add(pts2);

  let scrollY = 0;
  let mouseX = 0, mouseY = 0;

  window.addEventListener('mousemove', e => {
    mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
    mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
  });

  window.addEventListener('scroll', () => { scrollY = window.scrollY; });

  window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
  });

  let t = 0;
  function animate() {
    requestAnimationFrame(animate);
    t += 0.003;

    // Scroll-driven zoom & camera parallax
    const scrollPct = scrollY / (document.body.scrollHeight - window.innerHeight);
    camera.position.z = 30 - scrollPct * 18;
    camera.position.x += (mouseX * 3 - camera.position.x) * 0.04;
    camera.position.y += (-mouseY * 2 - camera.position.y) * 0.04;

    points.rotation.y = t * 0.08 + mouseX * 0.12;
    points.rotation.x = t * 0.04 + mouseY * 0.06;
    pts2.rotation.y = -t * 0.05 + mouseX * 0.08;

    // Fade canvas as user scrolls past hero
    const heroH = window.innerHeight;
    const fade = Math.max(0, 1 - scrollY / heroH);
    canvas.style.opacity = fade;

    renderer.render(scene, camera);
  }
  animate();
})();

// ── NAVBAR SCROLL ──
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 60);

  // progress bar
  const pct = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
  document.getElementById('progress-bar').style.width = pct + '%';

  // parallax paws
  const s = window.scrollY;
  document.getElementById('paw1').style.transform = `translateY(${s * 0.18}px) rotate(${s * 0.02}deg)`;
  document.getElementById('paw2').style.transform = `translateY(${-s * 0.12}px) rotate(${-s * 0.015}deg)`;
  document.getElementById('paw3').style.transform = `translateY(${s * 0.09}px) rotate(${s * 0.01}deg)`;
});

// ── REVEAL ON SCROLL ──
const reveals = document.querySelectorAll('.reveal');
const observer = new IntersectionObserver((entries) => {
  entries.forEach((e, i) => {
    if (e.isIntersecting) {
      setTimeout(() => e.target.classList.add('visible'), 80 * [...reveals].indexOf(e.target) % 4 * 0 || 0);
      e.target.classList.add('visible');
      observer.unobserve(e.target);
    }
  });
}, { threshold: 0.12 });
reveals.forEach(r => observer.observe(r));

// ── COUNT-UP ANIMATION ──
function animateCount(el, target, duration = 1800) {
  let start = null;
  const step = ts => {
    if (!start) start = ts;
    const progress = Math.min((ts - start) / duration, 1);
    const ease = 1 - Math.pow(1 - progress, 3);
    el.textContent = Math.floor(ease * target).toLocaleString();
    if (progress < 1) requestAnimationFrame(step);
    else el.textContent = target.toLocaleString();
  };
  requestAnimationFrame(step);
}

const statObs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.currentTarget.querySelectorAll('[data-target]').forEach(el => {
        animateCount(el, +el.dataset.target);
      });
      statObs.unobserve(e.currentTarget);
    }
  });
}, { threshold: 0.4 });
document.querySelectorAll('.stats-strip').forEach(s => statObs.observe(s));

// ── HEART TOGGLE ──
function toggleHeart(btn) {
  const liked = btn.classList.toggle('liked');
  btn.textContent = liked ? '❤️' : '🤍';
}

// ── PROFILE DROPDOWN TOGGLE ──
// Fungsi untuk buka/tutup dropdown avatar
// Fungsi untuk buka/tutup dropdown avatar
function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

// Menutup dropdown secara automatik jika pengguna klik di luar avatar/menu
window.addEventListener('click', function(e) {
    const dropdown = document.getElementById('profileDropdown');
    const avatar = document.querySelector('.avatar');
    
    if (dropdown && dropdown.classList.contains('show')) {
        if (!avatar.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    }
});

