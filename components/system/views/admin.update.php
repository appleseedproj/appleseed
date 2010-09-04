<h1>Admin System Update Title</h1> 
 
<p>Admin System Update Description</p> 
 
<form id='update' name='update' method='POST' action='_admin/control/update/'> 
  
    <label for='Server'>Update Server Location</label> 
    <select name="Server"></select>
    
    <label for='version'>Version</label> 
    <select class='version' name='gVERSION'  > 
	</select> 
    
    <label for="backupdirectory">Backup Directory</label> 
    <input  type="text" name="BackupDirectory" class="backupdirectory" maxlength="255" value="" /> 
       
     <button   type="submit" name="Task" value="Continue">Continue</button>   
 </form>
