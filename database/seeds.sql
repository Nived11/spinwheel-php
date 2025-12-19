-- database/seeds.sql

USE empire_plaza_spin;

-- Insert prize configurations (matching your Node.js prizes array)
INSERT INTO prize_config (prize, weight) VALUES
('Better Luck Next Time', 74),
('Pepsi 200ml', 10),
('5% OFF', 5),
('Watermelon Juice', 5),
('Gift', 3),
('10% OFF', 2),
('Free Full Mandi', 1);
