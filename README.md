# JeanMi Construction

A little scenario with a few web & system challenges created for the FIC 2019.

## Setup

### Requirements

Make sure you have :

* SQLite3 installed (or any RDBMS if you choose manual way but I recommand you the automated way)
* PHP >= 7.1 (might run on older versions but I'm not sure so install 7.1 or newer)
* A securely configured web application server (i.e. Apache2 for exemple)

### Automated 

Just run the bash script and you are ready to go :

```
$ ./setup.sh
```

### Manual

If the script doesn't work for you and you want to check if everything went correctly :

To create the database you can use the SQL script with the relational database managament system of your choice. I personnaly like SQLite for this kind of cases because it's very easy to setup and here you don't need to write into the database for this challenge. You need to change the connection line in *login.php* if you want to use something else than SQLite.

You can setup a SQLite database this way :

```
$ sqlite3 ./web/jeanmi-construction.db < generate-databse.sql
SQLite version 3.24.0 2018-06-04 14:10:15
Enter ".help" for usage hints.
sqlite> SELECT * FROM Users ;
1|user|35effb8d44421ae569cfaa9a2a2d76530df35d1d29afa42f5920f9bc4074771e
2|jeanmithebest|597f579d31603f69a07c6405f89e42c487553c9267e518339f325b4200f8efc7
```

*Do **not** use SHA256 to hash passwords, this is a challenge, not a good example*.

Then, change the rights on the database file you just created :

```
$ chmod 755 ./web/jeanmi-construction.db 
```

Users are as following :

| Username         | Password (before hash)                    |
|------------------|-------------------------------------------|
| admin            | yoloswag$123$aaa                          |
| jeanmithebest    | JeanmiConstructionGlobalIndustrySince2018 |

## Goal

The web part of the challenge consists in :

1. **Finding a way to access the administrator pannel (as admin)**. To do so, you have to exploit an SQL injection that is not shown on the page. You have to go to /login.php and process your SQLi. That's very basic and classical, yet, it is often presented with unashed password. I chosed on this one to present it with SHA-256 hashed passowrd and a SELECT COUNT() request. I actually did see it coded like that already on web apps. This illustrates a common situation for OWASP Top 10 2017 - A1.
2. **Finding a way to elevate your priveleges and become the super admin (basically, just become jeanmi).** You have to change your [JWT](https://jwt.io/) token to gain privileges. It's a bit harder because you have to know JWT. Those tokens are sometimes used to replace session and do session management tasks in a "stateless" way. That also sometimes goes with this kind of vulnerability where you trust the user part. This illustrates an uncommon but still very credible situation for OWASP Top 10 2017 - A2.


## Step 1

The login page is not shown. However it's super easy to find, just go to login.php and you're ready to go.

This is a SQLInjection. SQL request is (in pseudo-code):

```
SELECT COUNT(*) FROM Users WHERE username='?' AND passwd=sha256hash('?')
```

People that creates SQL requests this way think they are protected because of the structure of the SQL request. 
Actually, if you have a username, that one is ultra easy to bypass. 

Just type this in username field:

```
admin' --
```

## Creators

[Fabien Leite](https://www.github.com/fabienleite) & [Rémi Millerand (Driikolu)](https://driikolu.fr)
