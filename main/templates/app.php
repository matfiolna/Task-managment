<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="templates/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="templates/css/app.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script type="text/javascript" src="templates/css/bootstrap.min.js"></script>
</head>

<div class="container-fluid enter-app">
    <div class="col-sm-6 col-xs-12 firstscreen"><p class="col-xs-12 header">Login</p><form id="logIn" calss="col-xs-12">
        <p class="message"></p>
        <label class="col-sm-6 col-xs-12"><input type="email" name="email" placeholder="email" required></label>
        <label class="col-sm-6 col-xs-12"><input type="password" name="password" placeholder="password" required></label>
        <p><button class="col-xs-12">Logg in</button></p>
    </form></div>
    <div class="col-sm-6 col-xs-12 firstscreen"><p class="col-xs-12 header">Or register</p><form id="register" action="" calss="col-xs-12">
        <p class="message"></p>
        <label class="col-sm-6 col-xs-12"><input name='email' type="email" placeholder="email" required></label>
        <label class="col-sm-6 col-xs-12"><input name='password' type="password" placeholder="password" required></label>
        <label class="col-sm-6 col-xs-12"><input name='password1' type="password" placeholder="confirm passwor" required></label>
        <label class="col-sm-6 col-xs-12"><input name='nickname' type="text" placeholder="user-nam" required></label>
        <p><button type="submit" class="col-xs-12">Register</button></p>
    </form></div>
</div>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active" onclick="displayTasks(this)"><a href='#'>Tasks</a></li>
        <li onclick="displayArchive(this)"><a href='#'>Archive</a></li>
        <li onclick="logOut()"><a href='#'>Log out</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="row">
  <div class="row-wraper" id="hello-user"></div>
  <div class="row-wraper" id="tasks">
    <form id="addTask">
      <textarea name="text" id="" cols="30" rows="1" placeholder="task description"></textarea>
      <button>Create new task</button>
      <div id="more-options">
        <div id="display-options">
          <span class="glyphicon glyphicon-chevron-down"></span>
        </div>
        <label>high priority task?<input type="checkbox" name="priority"></label>
        <label>finish to<input type="date" name="finish_to"></label>
      </div>
    </form>
    <div class="tasks">
    </div>
    <!-- <div class="task">task text</div> -->
  </div>
  <div class="row-wraper" id="archive">
    <div class="tasks">
    </div>
  </div>
</div>

<script type="text/javascript" src="templates/js/app.js"></script>