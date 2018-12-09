CREATE TABLE Users(
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    username    VARCHAR(255),
    passwd      VARCHAR(255)
);

INSERT INTO Users(username, passwd) VALUES("admin", "35effb8d44421ae569cfaa9a2a2d76530df35d1d29afa42f5920f9bc4074771e");
INSERT INTO Users(username, passwd) VALUES("jeanmithebest", "597f579d31603f69a07c6405f89e42c487553c9267e518339f325b4200f8efc7");