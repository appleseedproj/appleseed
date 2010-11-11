Appleseed v0.7.7 Beta (Twelfth Release)

Nov 11 2010

New In This Release:
--------------------

Privacy UI, Newsfeeds, "Page" posts.

See _release/changelog.txt

--------------------
About Appleseed:

This is the thirteenth release of the Appleseed code base.  Appleseed is 
social networking software, similar to Friendster or Myspace.  

Appleseed is distributed social networking, which connects friends on 
different websites in a decentralized fashion.

Appleseed is being released under the GNU General Public License.

More information is available at http://opensource.appleseedproject.org

--------------------
Requirements:

1. Apache 1.2 or 2.0

2. PHP5
   a. GD image manipulation library.

3. MySQL 5+
   b. InnoDB Transactional Database

--------------------
Install Instructions

1. Move this directory into the htdocs/ or public_html/ directory where
   appleseed will be run from.  Appleseed cannot be installed in a 
   subdirectory.  Subdomains, however, are fine.

2. Create a mysql database and username/password.  

3. Point your browser to your website, and follow the install script
   instructions.
   
   
--------------------
Upgrade Instructions

(This can be a little tricky, and these steps only work for upgrading
from 0.7.7)

1. Make a backup of your web directory and db tables!
2. Untar the release into your web directory, overwriting existing files
3. Create the file configurations/local/local.conf with your site settings in this format:

inherit="default"
enabled="true"
db="database"
un="username"
pw="password"
pre="asd_"
host="localhost"
url="http://localhost"

5. Copy htaccess.original to .htaccess
6. Run the queries in _release/update-0.7.7.sql on your database, replacing "#__" with your prefix.

ie, 

drop table #__systemStrings;

becomes

drop table asd_systemStrings;
