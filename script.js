const yourNameInput = document.getElementById('yourName');
const partnerNameInput = document.getElementById('partnerName');
const calculateBtn = document.getElementById('calculateBtn');
const lovePercent = document.getElementById('lovePercent');
const centerHeart = document.getElementById('centerHeart');
const yourShareResult = document.getElementById('yourShareResult');
const partnerShareResult = document.getElementById('partnerShareResult');
const resultScreen = document.getElementById('resultScreen');
const mainContent = document.getElementById('mainContent');
const yourNameResult = document.getElementById('yourNameResult');
const partnerNameResult = document.getElementById('partnerNameResult');
const resetBtn = document.getElementById('resetBtn');
const canvas = document.getElementById('particleCanvas');
const ctx = canvas.getContext('2d');

const particleCount = 5000;
const particles = [];
let targets = [];
let isHeartMode = false;

function resizeCanvas() {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
}

function heartPoint(t) {
  const x = 16 * Math.pow(Math.sin(t), 3);
  const y = 13 * Math.cos(t) - 5 * Math.cos(2 * t) - 2 * Math.cos(3 * t) - Math.cos(4 * t);
  return { x: x * 12, y: -y * 12 };
}

function makeTargetsHeart() {
  const points = [];
  for (let i = 0; i < particleCount; i++) {
    const t = (i / particleCount) * Math.PI * 2;
    const p = heartPoint(t);
    points.push({
      x: canvas.width / 2 + p.x + (Math.random() - 0.5) * 18,
      y: canvas.height / 2 + p.y + (Math.random() - 0.5) * 18
    });
  }
  return points;
}

function makeTargetsScatter() {
  const points = [];
  for (let i = 0; i < particleCount; i++) {
    points.push({
      x: Math.random() * canvas.width,
      y: Math.random() * canvas.height
    });
  }
  return points;
}

function initParticles() {
  particles.length = 0;
  const colors = [330, 0, 15, 45, 260, 300, 320];
  for (let i = 0; i < particleCount; i++) {
    const hue = colors[Math.floor(Math.random() * colors.length)];
    particles.push({
      x: Math.random() * canvas.width,
      y: Math.random() * canvas.height,
      vx: 0,
      vy: 0,
      hue: hue + Math.random() * 20
    });
  }
  targets = makeTargetsScatter();
}

function updateTargets(useHeart) {
  isHeartMode = useHeart;
  targets = useHeart ? makeTargetsHeart() : makeTargetsScatter();
}

function drawParticles() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.globalCompositeOperation = 'lighter';

  for (let i = 0; i < particleCount; i++) {
    const particle = particles[i];
    const target = targets[i];

    const dx = target.x - particle.x;
    const dy = target.y - particle.y;
    particle.vx += dx * 0.0035;
    particle.vy += dy * 0.0035;
    particle.vx *= 0.88;
    particle.vy *= 0.88;
    particle.x += particle.vx;
    particle.y += particle.vy;

    const size = isHeartMode ? 2.2 : 1.6;
    const baseAlpha = 0.5 + Math.random() * 0.35;
    const saturation = 85 + Math.random() * 15;
    const lightness = 65 + Math.random() * 20;
    ctx.fillStyle = `hsla(${particle.hue}, ${saturation}%, ${lightness}%, ${baseAlpha})`;
    ctx.beginPath();
    ctx.arc(particle.x, particle.y, size, 0, Math.PI * 2);
    ctx.fill();
  }
}

function animate() {
  requestAnimationFrame(animate);
  drawParticles();
}

function calculateLove() {
  const yourName = yourNameInput.value.trim();
  const partnerName = partnerNameInput.value.trim();

  if (!yourName || !partnerName) {
    alert('Please enter both names to calculate love percentage.');
    return;
  }

  const combined = yourName.toLowerCase() + partnerName.toLowerCase();
  const totalScore = [...combined].reduce((sum, char) => sum + char.charCodeAt(0), 0);
  const loveScore = totalScore % 101;
  const yourShare = Math.round((loveScore * yourName.length) / (yourName.length + partnerName.length));
  const partnerShare = Math.max(0, loveScore - yourShare);

  yourNameResult.textContent = yourName;
  partnerNameResult.textContent = partnerName;
  lovePercent.textContent = loveScore;
  if (yourShareResult) yourShareResult.textContent = `${yourShare}%`;
  if (partnerShareResult) partnerShareResult.textContent = `${partnerShare}%`;
  updateHeartColor(loveScore);

  mainContent.classList.add('hidden');
  resultScreen.classList.remove('hidden');
  resultScreen.classList.add('visible');
  updateTargets(true);
}

function updateHeartColor(score) {
  if (!centerHeart) return;
  centerHeart.classList.remove('love-high', 'love-medium', 'love-low', 'love-weak');
  if (score >= 80) {
    centerHeart.classList.add('love-high');
  } else if (score >= 50) {
    centerHeart.classList.add('love-medium');
  } else if (score >= 30) {
    centerHeart.classList.add('love-low');
  } else {
    centerHeart.classList.add('love-weak');
  }
}

function resetCalculator() {
  mainContent.classList.remove('hidden');
  resultScreen.classList.add('hidden');
  resultScreen.classList.remove('visible');
  updateTargets(false);
}

window.addEventListener('resize', () => {
  resizeCanvas();
  initParticles();
});

calculateBtn.addEventListener('click', calculateLove);
resetBtn.addEventListener('click', resetCalculator);

window.onload = () => {
  resizeCanvas();
  initParticles();
  animate();
  if (!resultScreen.classList.contains('hidden')) {
    const score = parseInt(lovePercent.textContent, 10);
    if (!Number.isNaN(score)) {
      updateHeartColor(score);
    }
  }
};
