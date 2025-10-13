CREATE DATABASE IF NOT EXISTS gamebox_db;

USE gamebox_db;


CREATE TABLE IF NOT EXISTS games (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    thumbnail VARCHAR(255) NOT NULL,
    url VARCHAR(255) NOT NULL,
    tags VARCHAR(255) COMMENT 'Tags dipisahkan dengan koma',
    rating DECIMAL(2, 1) DEFAULT NULL,
    minutes INT(5) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO games (title, thumbnail, url, tags, rating, minutes) VALUES
(1, 'Snake', 'https://playsnake.org/', 'Arcade, Classic', 4.5, 5),
(2, 'Tetris', 'https://tetris.com/play-tetris', 'Puzzle, Classic', 4.7, 10),
(3, 'Flappy Bird', 'https://play-lh.googleusercontent.com/Df1Q1oaAoIc2cVxYrGhG9L41MBMqXxL7LfDLZ13Ggw4OH8BZlPiVxHoPymURZ2DYZqM', 'https://flappybird.io/', 'Arcade, Casual', 4.2, 3),
(4, '2048', 'https://play2048.co/meta-og.png', 'https://play2048.co/', 'Puzzle, Numbers', 4.6, 8),
(5, 'Pong', 'https://classicgames.me/pong/', 'Arcade, Classic', 4.4, 4), 
(6, 'Chess', 'https://www.apronus.com/chess/wbed.php', 'Strategy, Board', 4.8, 15),
(7, 'Reversi (Othello)', 'https://www.apronus.com/reversi/wbed.php', 'Strategy, Board', 4.3, 12),
(8, 'Checkers', 'https://www.apronus.com/checkers/wbed.php', 'Strategy, Board', 4.5, 10), 
(9, 'Tic Tac Toe', 'https://tictactoeonline.org/play/', 'Puzzle, Casual', 4.0, 2),
(10, 'Sudoku', 'https://www.websudoku.com/?select=1', 'Puzzle, Logic', 4.6, 12), 
(11, 'Minesweeper', 'https://minesweeper.online/start/custom', 'Puzzle, Classic', 4.3, 7),
(12, 'Solitaire (Klondike)', 'https://solitaire.com/embed/', 'Card, Classic', 4.5, 6),
(13, 'Pac-Man', 'https://html5-pacman.net/pacman/', 'Arcade, Classic', 4.7, 5),
(14, 'Bubble Shooter', 'https://www.bubbleshooter.net/', 'Arcade, Puzzle', 4.1, 8),
(15, 'Mahjong Solitaire', 'https://mahjong.com/embed/', 'Board, Puzzle', 4.4, 10),
(16, 'Space Invaders', 'https://classicgames.me/space-invaders/', 'Shooter, Arcade', 4.6, 5),
(17, 'Atari Breakout', 'https://classicgames.me/breakout/', 'Arcade, Classic', 4.2, 4),
(18, 'Connect 4', 'https://www.apronus.com/connect4/wbed.php', 'Board, Strategy', 4.1, 3),
(19, 'Crossword Puzzle', 'https://www.apronus.com/crossword/wbed.php', 'Puzzle, Word', 4.0, 7), 
(20, 'Hexxagon', 'https://www.apronus.com/hexxagon/wbed.php', 'Strategy, Board', 4.3, 15);