<section id="login">
    <div class="container">
      <h3 class="text-center text-uppercase"> Connect to access admin panel </h3>
      <div class="row">
        <div class="col-lg-8 mx-auto">
          <form name="credentials" method="POST" action="login.php">
            <div class="control-group">
              <div class="form-group floating-label-form-group controls mb-0 pb-2">
                <label>Username</label>
                <input class="form-control" name="login" id="username" type="text" placeholder="Username" required="required" data-validation-required-message="Please enter your username.">
                <p class="help-block text-danger"></p>
              </div>
            </div>
            <div class="control-group">
              <div class="form-group floating-label-form-group controls mb-0 pb-2">
                <label>Password</label>
                <input class="form-control" name="password" id="email" type="password" placeholder="Password" required="required" data-validation-required-message="Please enter your password.">
                <p class="help-block text-danger"></p>
              </div>
            </div>
            <br>
            <div id="success"></div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-xl" id="sendMessageButton">Send</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>