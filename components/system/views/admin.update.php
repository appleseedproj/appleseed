<h1>Admin System Update Title</h1> 
 
<p>Admin System Update Description</p> 
 
<form id="update" name="update" method="POST"> 
  
 	<fieldset>
 	   <label for="Server">Update Server Location</label> 
 	   <select name="Server"></select>
    
 	   <label for="NewServer">Add Server</label> 
 	   <input  type="text" name="NewServer" class="NewServer" maxlength="255" value="" />
 	   <button id="add-server" ype="submit" name="Task" value="AddServer">Add Server Button</button>   
 	   
 	   <label for="version">Version</label> 
 	   <select class="version" name="Version"> 
		</select> 
    
    	<label for="BackupDirectory">Backup Directory</label> 
    	<input  type="text" name="BackupDirectory" class="BackupDirectory" maxlength="255" value="" /> 
       
    </fieldset>
    <button type="submit" name="Task" value="Continue">Continue</button>   
 </form>
