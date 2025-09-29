const MOCK_GAMES = [
  {
    id: "g1",
    title: "Snake",
    thumbnail: "https://playclassic.games/wp-content/uploads/2019/07/snake-game.png",
    url: "https://playsnake.org/",
    tags: ["Arcade", "Classic"],
    rating: 4.5,
    minutes: 5,
  },
  {
    id: "g2",
    title: "Tetris",
    thumbnail: "https://upload.wikimedia.org/wikipedia/commons/7/7c/Tetris.png",
    url: "https://tetris.com/play-tetris",
    tags: ["Puzzle", "Classic"],
    rating: 4.7,
    minutes: 10,
  },
  {
    id: "g3",
    title: "Flappy Bird",
    thumbnail: "https://play-lh.googleusercontent.com/Df1Q1oaAoIc2cVxYrGhG9L41MBMqXxL7LfDLZ13Ggw4OH8BZlPiVxHoPymURZ2DYZqM",
    url: "https://flappybird.io/",
    tags: ["Arcade", "Casual"],
    rating: 4.2,
    minutes: 3,
  },
  {
    id: "g4",
    title: "2048",
    thumbnail: "https://play2048.co/meta-og.png",
    url: "https://play2048.co/",
    tags: ["Puzzle", "Numbers"],
    rating: 4.6,
    minutes: 8,
  },
  {
    id: "g5",
    title: "Pong",
    thumbnail: "https://upload.wikimedia.org/wikipedia/commons/3/37/Pong.png",
    url: "https://pong-2.com/",
    tags: ["Arcade", "Classic"],
    rating: 4.4,
    minutes: 4,
  },
  {
    id: "g6",
    title: "Chess",
    thumbnail: "https://upload.wikimedia.org/wikipedia/commons/6/68/Chess_board_opening_position.png",
    url: "https://playpager.com/embed/chess/index.html",
    tags: ["Strategy", "Board"],
    rating: 4.8,
    minutes: 15,
  },
  {
    id: "g7",
    title: "Reversi",
    thumbnail: "https://upload.wikimedia.org/wikipedia/commons/8/88/Reversi_board.png",
    url: "https://playpager.com/embed/reversi/index.html",
    tags: ["Strategy", "Board"],
    rating: 4.3,
    minutes: 12,
  },
  {
    id: "g8",
    title: "Checkers",
    thumbnail: "https://upload.wikimedia.org/wikipedia/commons/8/8f/Checkers_board.png",
    url: "https://playpager.com/embed/checkers/index.html",
    tags: ["Strategy", "Board"],
    rating: 4.5,
    minutes: 10,
  },
  {
    id: "g9",
    title: "Tic Tac Toe",
    thumbnail: "https://upload.wikimedia.org/wikipedia/commons/9/9e/Tic-tac-toe-game-1.svg",
    url: "https://tictactoeonline.org/play/",
    tags: ["Puzzle", "Casual"],
    rating: 4.0,
    minutes: 2,
  },
  {
    id: "g10",
    title: "Sudoku",
    thumbnail: "https://upload.wikimedia.org/wikipedia/commons/e/e0/Sudoku_Puzzle_by_L2G-20050714_standardized_layout.svg",
    url: "https://www.websudoku.com/",
    tags: ["Puzzle", "Logic"],
    rating: 4.6,
    minutes: 12,
  },
];

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

function renderGames(games) {
  const grid = document.getElementById("grid");
  grid.innerHTML = "";

  if (games.length === 0) {
    grid.innerHTML = "<p>Tidak ada game yang tersedia saat ini.</p>";
    return;
  }

  games.forEach((game) => {
    const card = document.createElement("div");
    card.className = "card-game";

    card.innerHTML = `
      <div class="thumb"><img src="${game.thumbnail}" alt="${game.title}"></div>
      <div class="meta">
        <div class="title">${game.title}</div>
        <div class="tags">${game.tags.join(", ")}</div>
        <button class="btn play small" disabled>Mainkan</button>
      </div>
    `;

    grid.appendChild(card);
  });
}

renderGames(MOCK_GAMES);

document.getElementById("year").textContent = new Date().getFullYear();