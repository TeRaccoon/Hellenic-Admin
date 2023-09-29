# Helenic ERP

Hellenic-ERP is a modern ERP system built on a LAMP stack infrastructure, featuring dynamically generated pages based on customizable templates. It provides an array of essential functionalities, including automatic invoice calculations, stock take management, and warehouse management, among others.


## Installation

To set up Hellenic-ERP on your local development environment or server, follow these steps:

 - Download the code from the repository and extract it into your web server's root location.

 - Ensure that you have PHP and MySQL installed on your system with the following minimum versions:

    MySQL version: 8.0.33 or above

    PHP version: 8.1.2 or above

Open your MySQL command-line interface and log in using your credentials and create a new database named "hellenic":
```sql
CREATE DATABASE hellenic;
```
Exit the MySQL command-line interface.

Navigate to the location where you have the "dumpy.sql" file from the repository.

Import the database into the newly created "hellenic" database using the following command:
```bash
mysql -u root -p hellenic < dumpy.sql
```
