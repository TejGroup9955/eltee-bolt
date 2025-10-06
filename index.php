<?php
include_once('configuration.php');
?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
  @import url('https://fonts.googleapis.com/css?family=Raleway:400,700');

body {
  background: #c0c0c0; 
  font-family: Raleway, sans-serif;
  color: #666;
}

.login {
  margin: 90px auto;
  padding: 40px 50px;
  max-width: 300px;
  border-radius: 5px;
  background: #fff;
  box-shadow: 1px 1px 1px #666;
}
  .login input, .login select {
    width: 100%;
    display: block;
    box-sizing: border-box;
    margin: 10px 0;
    padding: 8px 10px;
    font-size: 14px;
    border-radius: 1px; 
    font-family: Raleway, sans-serif;
  }

.login input[type=text],
.login input[type=password],
.login select {
  border: 1px solid #c0c0c0;
  transition: .2s;
}

.login input[type=text]:hover, .login select:hover {
  border-color:rgb(12, 12, 12);
  outline: none;
  transition: all .2s ease-in-out;
} 

.login input[type=submit] {
  border: none;
  background: #8e1b24;
  color: white;
  font-weight: bold;  
  transition: 0.2s;
  margin: 20px 0px;
}

.login input[type=submit]:hover {
  background: #8e1b24;  
}

  .login h2 {
    margin: 20px 0 0; 
    color: #8e1b24;
    font-size: 25px;
  }

.login p {
  margin-bottom: 40px;
}

.links {
  display: table;
  width: 100%;  
  box-sizing: border-box;
  border-top: 1px solid #1555a4;
  margin-bottom: 10px;
}

.links a {
  color:#1555a4;
  font-weight:bold;
  display: table-cell;
  padding-top: 10px;
}

.links a:first-child {
  text-align: left;
}

.links a:last-child {
  text-align: right;
}

  .login h2,
  .login p,
  .login a {
    text-align: center;  
    margin-bottom:20px;  
  }

.login a {
  text-decoration: none;  
  font-size: .8em;
}

.login a:visited {
  color: inherit;
}

.login a:hover {
  text-decoration: underline;
}
</style>

    <form class="login" id="formAuthentication">
        <div class="row" style="display:flex;justify-content:center;">
            <img src="production/images/logo.png" style="width:150px;float:center;">
        </div>
        <h2 >Welcome, User!</h2>
        <!-- <p>Please log in</p> -->
        <select class="form-control" id="txtyear" name="txtyear" required>
                <?php
                    $rstyear = mysqli_query($connect,"select * from financial_year order by year_id desc");
                    while($rwyear = mysqli_fetch_assoc($rstyear))
                    {
                    $year_id = $rwyear['year_id'];
                    $year_name = $rwyear['year_name'];
                    echo "<option value='$year_id'>$year_name</option>";
                    }
                ?>
        </select>
        <input type="text" placeholder="User Name" name="txtusername" id="txtusername" required/>
        <input type="password" placeholder="Password" name="txtpassword" id="txtpassword" required />
        <input type="submit" id="submit" value="Log In" >
        <!-- <div class="links">
            <a href="#">Forgot password</a>
            <a href="#">Register</a>
        </div> -->
    </form>
      
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      $(document).ready(function(){
          $("#formAuthentication").submit(function(e){
              e.preventDefault();
              $.post("component.php",{
              year_id:$("#txtyear").val(),
              username:$("#txtusername").val(),
              password:$("#txtpassword").val(),
              Flag:"Login"
              },function(data,success)
              {
                var res = JSON.parse(data);
                if(res.message =="Login Successfully")
                {
                  Swal.fire({
                      icon: 'success',
                      title: 'Success',
                      text: 'Login Successfully',
                  });
                //   if(res.role_name=="Admin")
                //   {
                    setTimeout(() => {
                      window.location.href="production/index.php";
                    }, 1000);
                //   } 
                }
                else
                {
                  Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: res.message,
                  });
                }
              });
          });
      }); 
  </script>
