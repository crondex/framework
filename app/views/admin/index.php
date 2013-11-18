<h1>Create a new user</h1>
<p>
<form action="admin/newuser" method="POST">
Username:<br>
<input type="text" name="user" size="60"><br>
Password:<br>
<input type="password" name="pass" size="60"><br>
<input type="submit" value="Create user">
</form>
<h1>Log in</h1>
<p>
<form action="admin/login" method="POST">
Username:<br>
<input type="text" name="user" size="60"><br>
Password:<br>
<input type="password" name="pass" size="60"><br>
<input type="submit" value="Log in">
</form>
<h1>Change password</h1>
<p>
<form action="admin/passwd" method="POST">
Username:<br>
<input type="text" name="user" size="60"><br>
Current password:<br>
<input type="password" name="pass" size="60"><br>
New password:<br>
<input type="password" name="newpass" size="60"><br>
<input type="submit" value="Change password">
</form>
