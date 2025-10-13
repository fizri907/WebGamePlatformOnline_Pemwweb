const modal = document.getElementById("modal");
const iframe = document.getElementById("game-iframe");
const modalTitle = document.getElementById("modal-title");

function openGame(game) {
  iframe.src = game.url;
  modalTitle.textContent = game.title;
  modal.setAttribute("aria-hidden", "false");
}

function closeModal() {
  iframe.src = "";
  modal.setAttribute("aria-hidden", "true");
}

document.getElementById("modal-close").onclick = closeModal;
document.getElementById("modal-close-2").onclick = closeModal;
document.getElementById("modal-backdrop").onclick = closeModal; 

document.getElementById("year").textContent = new Date().getFullYear();