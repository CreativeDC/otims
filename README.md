# Introduction
The Online Textbooks Inventory Management System (OTIMS) is a web and mobile application initially developed for the Ministry of Education of Afghanistan to digitize the current manual paper-based textbooks distribution process.
At the request of USAID-Afghanistan, Afghan Children Read (ACR) conducted a rapid assessment of the Ministry’s distribution system, and, in view of its findings, introduced an Online Textbook Inventory Management System (OTIMS) that is digitized, efficient, and cost-effective for replacing the manual system.

The interface of OTIMS on the web and mobile are identical. The only difference being that the mobile application is an APK file which can be installed on Android devices while the web version must be accessed using the URL.

This note serves as a concise but comprehensive guide for developers to understand the technologies used for developing OTIMS as well as to help set up the development environment to run and debug OTIMS. This note may also be used to help set up the production environment.

# Demo Installation
You can access a fully working demo installation of OTIMS at http://108.175.5.232 . For user accounts, please refer to the documents/Accounts.txt file in the repository. 

# Programming Languages, Frameworks, and Tools Used
OTIMS has been developed using several languages, frameworks, and tools. The frontend of OTIMS Web has been developed using Vue.js Framework, which is a JavaScript, while OTIMS Mobile has been developed using Vue.JS Framework and Cordova Framework.

It is important to note that the frontend for web and mobile both share the same source code base when developing and deploying. The only difference is the compilation or build process for web and mobile. OTIMS Web must be built using Vue.JS build scripts while OTIMS Mobile must be built using Cordova build scripts.

The backend for OTIMS is developed using Laravel, which is a PHP framework used for developing APIs or full-fledged web applications. It is important to note that both the mobile and web version frontends share the same source code base for backend as well.

The primary database for OTIMS is designed and developed using MySQL.

# Setting up the Development Environment
To run and debug OTIMS backend (Laravel/API) on a local machine for development purposes, a XAMP stack must be installed. A “XAMP” stack is a group of open-source software that is typically installed together to enable a server to host dynamic websites and web apps. This term is an acronym which represents the Linux/Windows/Mac (X) operating system, with the Apache web server. The application data is stored in a MySQL database, and dynamic content is processed by PHP.

You may install WAMP or XAMPP depending on your operating system and preference, however, we highly recommend XAMPP. Although the installation process is fairly simple and straightforward, instructions on downloading and installing XAMPP are available on its official website: https://www.apachefriends.org.

To run and debug the OTIMS frontend, you will need to install the following:
1.	Download and Install Node.JS from https://nodejs.org. The installation is fairly simple and instructions are available on its official website.

2.	Install Vue CLI using instructions on its official website: https://cli.vuejs.org/guide/installation.html. The commands mentioned should be run using command line on Windows or terminal on Linux.

3.	Install Cordova using the instructions on its official website: https://cordova.apache.org/#getstarted. Please note that you will have to install Cordova and not create any projects.

4.	Download and install Android studio using the instructions on its official website: https://developer.android.com/studio. The installation process is simple and straightforward.

Please note that it is important to install Android Studio to be able to build the APK for Android devices. If you only need to debug or build for the web, you do not need to install Cordova and Android Studio.

# Running the Web Application Locally on a Browser
To run the web application locally, please follow the following steps:

1.	Run XAMPP and make sure you start Apache and MySQL.

2.	Navigate to PhpMyAdmin and import the OTIMS database script.

3.	Open the .env file from the OTIMS backend source code folder and make sure the database name and credentials match with your database on PhpMyAdmin.

4.	Open command line and navigate to the OTIMS backend source code folder and then run the following command to start the API: php artisan serve –host=127.0.0.1 

5.	Open another instance of command line and navigate to the source code folder of OTIMS frontend and run the following command to start the web frontend: npm run dev

6.	Step 4 and Step 5 will give you URLs once the command is done processing and you can navigate to them using your web browser to access the frontend, or the backend if needed.

Please note that any changes you make to the frontend or the backend code are automatically reflected in the open instance in the browser and you do not need to run the commands every time you make changes. The changes are instant.

# Running the Mobile Application on a Simulator
To run the mobile application locally on a simulator, please make sure that you have installed a simulator in your Android studio and then follow the following steps:

1.	Run XAMPP and make sure you start Apache and MySQL.

2.	Open command line and navigate to the OTIMS backend source code folder and then run the following command to start the API: php artisan serve –host=127.0.0.1 

3.	Open another instance of command line and navigate to the source code folder of OTIMS frontend and run the following command to start the mobile frontend: cordova run android or cordova emulate android

4.	The mobile application will be started on a simulator device on your computer. It may take a bit longer when running it for the first time.

# Building the Application for Web and Mobile
To build the application for deployment for web, please navigate to the source code folder from command line and run the following command: npm run build. This command will build the frontend for web and you can then deploy it to the server by coping / pasting all the files from the folder www\dist.

To build the application for Android, please navigate to the source code folder from command line and run the following command: cordova build android. The command will generate a .apk file which can be used to install OTIMS on any android device. The .apk will be located in platforms\android\app\build\outputs\apk\debug folder.

Please note that you have to provide the API URL in the main.js file inside the frontend folder.
