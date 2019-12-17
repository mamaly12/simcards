# Simcards Top-up

REQUIREMENTS
============
• Symfony 4.3. (https://symfony.com/download)

• Apache WEB server, version 2.0 or higher. ( http://httpd.apache.org/download.cgi )

• PHP 7.2. ( http://www.php.net/ )

• MySQL 8. ( http://dev.mysql.com/downloads/ )

• IDE (like PhpStorm). (https://www.jetbrains.com/phpstorm/download/)

INSTALLATION
============
1- Download and Extract the Source Code.

2- Create DataBase:
   - in the terminal of the IDE type the following commands:

		a. composer update
		
		b. according to env file of the root of the project DATABASE_URL=mysql://apaa:apaa@127.0.0.1:3306/simcard
		
		create a user with password which has access to the database: DATABASE_URL=mysql://user_db:user_password@127.0.0.1:3306/dbName
		
		c. php bin/console doctrin:database:create

3- Create Table:
    -in the terminal of the IDE type these commands to create tables for the entities of the project:
         
        a. php bin/console doctrine:migrations:migrate
        
User Guides
============
1- The project consists of two parts: 

    1-1- Web part
  
    1-2- REST API part

**1-1 Web part:**
      
      a. users can register 
      
      b. THE FIRST USER WHO REGISTER WILL GIVEN ADMIN_ROLE. (column roles is implemented inside the user table)
      
      c. PLEASE NOTE THAT ONLY ADMIN CAN DO THE FOLLOWING ACTIONS:
      
           I)   DELETE USER
		   
	  d. users can top-up simcards
        
 **1-2 REST API part:**  
      
        a.  In the registration process a unique token is created for each user in order to identify the senders of the REST requests.
        
            I)   token is stored in token column of the user table 
            
            II)  Each user can see only his/her own token in the user list of web version
            
            // TODO  separte table with the userId column, user token column, and also expiration time column must be implemented, etc.
            
            // TODO  After each Login Token must be created
        
        b.  URLs of the REST REQUESTS MUST START WITH /api/. Such as the followign sample url:
        
            `HOMEURL/api/REST_REQUEST`
            
        c.  User token must be send as a token parameter for each REST request. 
        
            `example: HOMEURL/api/deleteUser?token=sadff&userId=2`
            
        d.  the following shows the list of valid REST API Requests:
        
            I)    create user: `HOMEURL/api/createUser?token=****&name=***&email=****&password=****`
            
            II)   delete user:  `HOMEURL/api/deleteUser?token=****&userId=***`
            
            III)  view user list:  `HOMEURL/api/viewUsers?token=****`
            
            IV)   get balance:  `HOMEURL/api/getBalance?token=*******&number=****`
            
            V)    add balance:  `HOMEURL/api/addBalance?token=*********&number=******&currency=*****&amount=*****`
            
