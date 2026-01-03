-- database/seeds.sql

USE empire_plaza_spin;

-- Insert prize configurations (matching your Node.js prizes array)
INSERT INTO prize_config (prize, weight) VALUES
('Better Luck Next Time', 83),
('Pepsi 200ml', 8),
('5% OFF', 2),
('Watermelon Juice', 5),
('Gift', 2),
('10% OFF', 0),
('Free Full Mandi', 0);
