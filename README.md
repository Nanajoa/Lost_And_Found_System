# Lost_And_Found_System


##Sprint 1 file structure (kindly don't change structure, you can make suggestions in the group though)
/ayera  
│── /backend (PHP API)  
│   │── database.php  # Handles database connection  
│   │── models.php  # Defines database models (Users, LostItems)  
│   │── auth.php  # Handles login & registration logic  
│   │── search.php  # Implements search functionality  
│   │── report.php  # Handles lost item reports  
│   │── item_details.php  # Retrieves details of a lost item  
│  
│── /frontend (HTML, CSS, JavaScript)  
│   │── /css  
│   │   │── styles.css  # Global styles  
│   │  
│   │── /js  
│   │   │── script.js  # Handles dynamic frontend logic  
│   │   │── auth.js  # Handles login & registration requests  
│   │   │── search.js  # Manages search functionality  
│   │   │── report.js  # Handles lost item reporting  
│  
│   │── /pages  
│   │   │── index.html  # Homepage UI  
│   │   │── login.html  # Login page UI  
│   │   │── register.html  # Registration page UI  
│   │   │── report.html  # Form for reporting lost items  
│   │   │── search_results.html  # Displays search results  
│   │   │── item_details.html  # Shows lost item details  
│  
│── .htaccess  # (For Apache, to route requests properly)  
│── config.php  # Configuration file for environment settings  
│── README.md  # Project documentation  #I will deal with the README file finally but for every page kindly give a short description about the page and the importance and how you implemented so the final essay is easier for me to compile. Put all the short descriptions of all the files you worked for in one word document and send to abambirebawayenma@gmail.com


##WorkFlow

User visits index.html → JavaScript fetches lost items from backend/search.php and displays them.
User logs in (login.html) → Form submits to backend/auth.php?action=login, and response determines if they proceed.
User reports a lost item (report.html) → Data is sent via POST to backend/report.php, which stores it in the database.
User searches for an item (search_results.html) → JavaScript fetches search results from backend/search.php?q=item.
User views item details (item_details.html) → Page fetches data from backend/item_details.php?id=123


##Purpose: 
Backend (PHP - /backend/)
database.php – Connects to MySQL database.
models.php – Defines tables like users and lost_items.
auth.php – Handles login (POST /backend/auth.php?action=login) and registration (POST /backend/auth.php?action=register).
search.php – Processes search queries (GET /backend/search.php?q=item).
report.php – Handles lost item form submissions (POST /backend/report.php).
item_details.php – Fetches details for a specific lost item (GET /backend/item_details.php?id=123).
Frontend (HTML, CSS, JavaScript - /frontend/)
index.html – Displays recent lost items and a search bar.
login.html & register.html – Form pages that send data to auth.php.
report.html – Sends lost item reports to report.php.
search_results.html – Fetches search results from search.php.
item_details.html – Displays lost item details from item_details.php.
JavaScript (Handles Frontend Logic)
auth.js – Sends login & registration requests via fetch().
search.js – Sends search queries and updates search_results.html.
report.js – Submits lost item reports via fetch().



