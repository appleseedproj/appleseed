<script type="text/javascript" src="/components/debug/assets/javascript/login.js"></script>

<section id="login">
	<nav class='tabs'> 
		<ul> 
			<li id="login_local_button"><a href='/login/'>Local</a></li> 
			<li id="login_remote_button"><a href='/login/remote/'>Remote</a></li> 
		</ul> 
	</nav>

  <form id='userinfo' name='userinfo' action='login/' method='post'> 
   <input type='hidden' name='gACTION' value='LOGIN' /> 
 
   <p id='username'>Username</p>
   <span class='gusername'><input  type='text' name='gUSERNAME' class='username' maxlength='64' value="" /></span>   
   <p id='password'>Password</p>
   <span class='gpass'><input  type='password' name='gPASS' class='pass' maxlength='16' value="" /></span>   
   <p id='rememberme'>Remember Me</p>
   <input id='remember' type='checkbox' name='gREMEMBER'  checked='checked' /> 
 
    <button   type='submit' name='gACTION' value='LOGIN'>Login</button>    <button   type='submit' name='gACTION' value='FORGOT'>Forgot Password</button> 
  </form>
 
  <form id='userinfo' name='userinfo' action='login/remote/' method='post'> 
   <span class='glocation'><input  type='text' name='gLOCATION' class='location' maxlength='128' value="" /></span>   
   <button   type='submit' name='gACTION' value='REMOTE LOGIN'>Remote Login</button> 
  </form>
  
  <h1>Join This Site</h1> 
 
 
 <p class='content'> 
   Create a new account by filling out the information below.  Once the account is created, you can log in and set up your profile, and upload a profile photo and user icons. </p> <!-- .content --> 
 
 <form name='join' id='join' action='join/' method='post'> 
  <input type='hidden' name='gACTION' id='gACTION' value='join'> 
 
   <p id='fullname'> 
    Full Name   </p> <!-- .fullname --> 
   <span class='gfullname'><input  type='text' name='gFULLNAME' class='fullname' maxlength='16' value="" /> 
</span>   
   <p id='username'> 
    Username   </p> <!-- .username --> 
   <span class='gusername'><input  type='text' name='gUSERNAME' class='username' maxlength='16' value="" /> 
</span>   
   <p id='email'> 
    Email   </p> <!-- .email --> 
   <span class='gemail'><input  type='text' name='gEMAIL' class='email' maxlength='128' value="" /> 
</span>   
   <p id='password'> 
    Password   </p> <!-- .password --> 
   <span class='gpass'><input  type='password' name='gPASS' class='pass' maxlength='16' value="" /> 
</span> 
   <p id='confirm'> 
    Confirm Password   </p> <!-- .confirm --> 
   <span class='gconfirm'><input  type='password' name='gCONFIRM' class='confirm' maxlength='16' value="" /> 
</span>   
 
      
    <button   type='submit' name='gACTION' value='SUBMIT'>Submit</button> 
 </form> <!-- #join --> 
 
</section>

