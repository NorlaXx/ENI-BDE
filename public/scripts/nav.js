const canvas = document.getElementById("custom_canvas");
const jsConfetti = new JSConfetti({ canvas });

// document.querySelector(".burger").addEventListener("click", function () {
//   document.querySelector("nav ul").classList.toggle("show");
// });

let konamiCode = [];
let konami = "38384040373937396665";

document.addEventListener("keydown", function (e) {
  konamiCode.push(e.keyCode);
  if (konamiCode.length > konami.length) {
    konamiCode.shift();
  }
  if (konamiCode.join("").includes(konami)) {
    playConfetti();
    konamiCode = [];
  }
});

const playConfetti = () => {
  for (let i = 0; i < 3; i++) {
    setTimeout(() => {
      const randomColors = generateRandomColors(3);
      jsConfetti.addConfetti({
        confettiRadius: 6,
        confettiNumber: 500,
        confettiColors: randomColors,
        confettiSpeed: 3,
        confettiRotation: 45,
        confettiAcceleration: 0.05,
        confettiParticles: 100,
        confettiBlast: true,
        confettiBlastRadius: 200,
        confettiBlastDirection: 45,
        confettiBlastSpeed: 5,
        confettiBlastRotation: 90,
        confettiBlastAcceleration: 0.1,
        confettiBlastParticles: 200,
      });
    }, i * 1500);
  }
};

const generateRandomColors = (count) => {
  const colors = [];
  for (let i = 0; i < count; i++) {
    const color = "#" + Math.floor(Math.random() * 16777215).toString(16);
    colors.push(color);
  }
  return colors;
};
