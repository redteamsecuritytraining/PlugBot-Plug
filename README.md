<img src="http://www.redteamsecure.com/images/labs/pb.png"/>


<h1>Synopsis</h1>

PlugBot project is a security research project by <a href="http://www.redteamsecure.com">RedTeam Security</a>, led by Jeremiah Talamantes. It is designed to be a proof-of-concept / experimental foray into the development of software that could potentially support the concept of a hardware botnet. The project is made up of two components, PlugBot Bot and PlugBot Command & Control. The hardware component to this project is intended to be single-board computers, such as: Raspberry Pi, Beaglebone, Cubox, etc.

<h1>Motivation</h1>

Jeremiah began developing the concept in early 2010 upon the surge of <a href="http://en.wikipedia.org/wiki/Plug_computer">plug computers</a> into the tech market. Although the development ceased not soon after, the research aspect continued into his dissertation and finally came to life again in early 2015.

<h1>Bot Installation</h2>

Carry out the following steps to install:

<ol>
	<li>Copy the code into your web server's web root</li>
	<li>Open application/config/config.php and change the encryption_key to arbitrary/random characters (line 227)</li>
	<li>Open application/config/database.php and set your database's hostname, username and password (lines 51 to 53)</li>
	<li>Ensure the system requirements below are installed</li>
</ol>

<b>Bot System Requirements</b>

<ul>
	<li>Linux OS (tested on Debian)</li>
	<li>Apache2</li>
	<li>PHP5 (php5-curl)</li>
	<li>MySQL</li>
	<li>Perl</li>
	<li>Python</li>
	<li>Bash</li>
	<li>Flip</li>
	<li>cron</li>
	<li>Tor</li>
	<li>cURL, wput, wget</li>
</ul>

<h1>Login</h1>

The default username is <b>admin</b> and the default password is <b>admin</b>.

<h1>Contributors</h1>

Jeremiah is an information security consultant, not a developer. Therefore much help is needed to improve the project all around. If you're proficient in PHP / CodeIgniter and want to contribute, contact jeremiah[at]redteamsecure[dot]com. Help is greatly needed!

<h1>License</h1>

Non-commercial use, share/contribute and provide credit. <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International Public License.</a>
